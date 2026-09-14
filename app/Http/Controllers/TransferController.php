<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\EnforcesTierLimits;
use App\Http\Controllers\Concerns\RequiresTransactionPin;
use App\Mail\TransactionMail;
use App\Models\AppNotification;
use App\Models\ExternalTransfer;
use App\Models\InternationalTransfer;
use App\Models\Transfer;
use App\Models\User;
use App\Support\CurrencyRates;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;
use RuntimeException;

class TransferController extends Controller
{
    use EnforcesTierLimits, RequiresTransactionPin;

    /**
     * Handles the Send Money form (send.blade.php). Two of the page's
     * three "Send to" tabs are real:
     *
     *  - Ledger: looks the recipient up by account number in our own
     *    database — see storeLedgerTransfer().
     *  - Another bank: no lookup exists for this one and never will — no
     *    legitimate API can return an account holder's name from a bare
     *    account + routing number (see the long comment that used to live
     *    on isValidAbaRoutingNumber() before it was removed). The sender
     *    types the account number, routing number, and recipient name
     *    themselves, same as a real bank's wire/ACH form, and Ledger sends
     *    it through using exactly what was typed — no checksum or format
     *    validation beyond "this field is required". See
     *    storeExternalTransfer().
     *
     *  - International bank: same idea as "Another bank" — no live
     *    verification of the account holder's name is possible — but with
     *    its own set of fields (country, SWIFT/BIC, currency) and its own
     *    table (international_transfers), since a domestic transfer never
     *    has those. Can optionally be filled in from the admin-managed
     *    Bank directory (see App\Models\Bank) instead of typed by hand. See
     *    storeInternationalTransfer().
     *
     * "Linked bank" has no real rails behind it at all and is rejected
     * outright. "Schedule" under When IS real: instead of moving money
     * right away, it creates the same Transfer/ExternalTransfer/
     * InternationalTransfer row with status = 'scheduled' and no balance
     * movement yet — see scheduleLedgerTransfer()/scheduleExternalTransfer()
     * /scheduleInternationalTransfer() below — and
     * `php artisan transfers:process-scheduled` (run daily via the
     * scheduler — see routes/console.php) is what actually moves the money
     * once the date arrives.
     */
    public function store(Request $request): RedirectResponse
    {
        // The amount field on the page displays with thousands commas
        // ("5,500") for readability — strip that before it's validated as
        // a plain number.
        $request->merge([
            'amount' => (float) str_replace(',', '', (string) $request->input('amount', '0')),
        ]);

        $data = $request->validate([
            'mode' => ['required', 'in:ledger,bank,international,linked'],
            'when' => ['required', 'in:now,later'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:999999.99'],
            // Tomorrow at the earliest — "today" is what Send now is for,
            // so this keeps the two modes from overlapping in meaning.
            'scheduled_date' => ['required_if:when,later', 'nullable', 'date', 'after:today'],
            'recipient_name' => ['required_if:mode,ledger', 'nullable', 'string', 'max:150'],
            'recipient_account_number' => ['required_if:mode,ledger', 'nullable', 'string', 'max:20'],
            'bank_name' => ['required_if:mode,bank', 'nullable', 'string', 'max:150'],
            'bank_recipient_name' => ['required_if:mode,bank', 'nullable', 'string', 'max:150'],
            'bank_account_number' => ['required_if:mode,bank', 'nullable', 'string', 'max:20'],
            'bank_routing_number' => ['required_if:mode,bank', 'nullable', 'string', 'max:20'],
            'intl_bank_name' => ['required_if:mode,international', 'nullable', 'string', 'max:150'],
            'intl_recipient_name' => ['required_if:mode,international', 'nullable', 'string', 'max:150'],
            'intl_country' => ['required_if:mode,international', 'nullable', 'string', 'max:100'],
            'intl_swift_code' => ['required_if:mode,international', 'nullable', 'string', 'max:20'],
            'intl_account_number' => ['required_if:mode,international', 'nullable', 'string', 'max:34'],
            'intl_currency' => ['required_if:mode,international', 'nullable', 'string', 'max:3'],
            'category' => ['nullable', 'string', 'max:60'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        if ($data['mode'] === 'linked') {
            return back()->withInput()->with(
                'sendError',
                "Sending to a linked card isn't available yet — choose Ledger or Another bank instead."
            );
        }

        // See RequiresTransactionPin — checked last, right before anything
        // actually happens (money moving now, or a scheduled row being
        // created), so a user still gets pointed at whatever's wrong with
        // the form itself first rather than being asked for a PIN on a
        // submission that was never going through anyway.
        if ($blocked = $this->blockedByTransactionPin($request, $request->user(), 'sendError')) {
            return $blocked;
        }

        if ($data['when'] === 'later') {
            return match ($data['mode']) {
                'bank' => $this->scheduleExternalTransfer($request, $data),
                'international' => $this->scheduleInternationalTransfer($request, $data),
                default => $this->scheduleLedgerTransfer($request, $data),
            };
        }

        // Only "send now" ever actually moves money the moment this
        // request is handled, so only it is checked against today's tier
        // limit — see EnforcesTierLimits. A scheduled transfer doesn't move
        // anything yet (ProcessScheduledTransfers does, on the day it
        // runs), so there's nothing to count against *today's* limit here.
        if ($blocked = $this->blockedByTierLimit($request, $request->user(), (float) $data['amount'], 'sendError')) {
            return $blocked;
        }

        return match ($data['mode']) {
            'bank' => $this->storeExternalTransfer($request, $data),
            'international' => $this->storeInternationalTransfer($request, $data),
            default => $this->storeLedgerTransfer($request, $data),
        };
    }

    /**
     * Ledger-to-Ledger: real recipient, found by account number, both
     * balances actually move in one atomic step.
     */
    private function storeLedgerTransfer(Request $request, array $data): RedirectResponse
    {
        $sender = $request->user();

        // Looked up by account number only — the "Recipient's name" field
        // is auto-filled by the same lookup as you type (see lookup()
        // below), not something the sender can freely type here.
        $recipient = User::where('is_admin', false)
            ->where(function ($query) use ($data) {
                $query->where('checking_account_number', $data['recipient_account_number'])
                    ->orWhere('savings_account_number', $data['recipient_account_number']);
            })
            ->first();

        if (! $recipient) {
            return back()->withInput()->with('sendError', "We couldn't find a Ledger account with that number.");
        }

        if ($recipient->id === $sender->id) {
            return back()->withInput()->with('sendError', "You can't send money to your own account.");
        }

        $amount = (float) $data['amount'];
        $transfer = null;
        $senderBalanceAfter = null;
        $recipientBalanceAfter = null;

        try {
            DB::transaction(function () use ($sender, $recipient, $amount, $data, &$transfer, &$senderBalanceAfter, &$recipientBalanceAfter) {
                // lockForUpdate so two transfers submitted at nearly the
                // same moment can't both read the same starting balance
                // and both pass a balance check that should have stopped
                // the second one.
                $freshSender = User::whereKey($sender->id)->lockForUpdate()->first();

                if ((float) $freshSender->balance < $amount) {
                    throw new RuntimeException('insufficient_funds');
                }

                $freshSender->balance = (float) $freshSender->balance - $amount;
                $freshSender->save();
                $senderBalanceAfter = (float) $freshSender->balance;

                $freshRecipient = User::whereKey($recipient->id)->lockForUpdate()->first();
                $freshRecipient->balance = (float) $freshRecipient->balance + $amount;
                $freshRecipient->save();
                $recipientBalanceAfter = (float) $freshRecipient->balance;

                $transfer = Transfer::create([
                    'reference' => 'LDG'.now()->format('ymd').strtoupper(Str::random(6)),
                    'sender_id' => $sender->id,
                    'recipient_id' => $recipient->id,
                    'amount' => $amount,
                    // Set explicitly (not just relying on the column's DB
                    // default) because this line is the one place that's
                    // actually true: the balance moves right above this,
                    // inside the same DB transaction, so by the time this
                    // row exists the transfer really is done.
                    'status' => 'completed',
                    'category' => $data['category'] ?? null,
                    'note' => $data['note'] ?? null,
                ]);
            });
        } catch (RuntimeException $e) {
            if ($e->getMessage() === 'insufficient_funds') {
                return back()->withInput()->with('sendError', 'Insufficient funds for this transfer.');
            }

            throw $e;
        }

        // Both sides get their own email — the sender sees "Money sent" to
        // the recipient, the recipient sees "Money received" from the
        // sender — same split send.receipt() already renders on screen.
        // Gated per-user by notify_transactions_email (see
        // NotificationSettingController) and wrapped in try/catch: the
        // transfer above already succeeded and both balances already
        // moved, so a broken mail server should never turn a completed
        // transfer into a 500 error page.
        try {
            // !== false (not just a truthy check): a missing/never-set
            // column reads back as null, and null should still mean "send
            // it" since the preference defaults to on — only an explicit
            // false (the user actually switched it off) should skip this.
            if ($sender->notify_transactions_email !== false) {
                Mail::to($sender->email)->send(new TransactionMail(
                    user: $sender,
                    title: 'Money sent',
                    amountSign: '-',
                    amount: $amount,
                    reference: $transfer->reference,
                    rows: [
                        ['label' => 'To', 'value' => $recipient->name],
                        ['label' => 'Date', 'value' => $transfer->created_at->format('M j, Y \a\t g:i A')],
                    ],
                    newBalance: $senderBalanceAfter,
                ));
            }

            if ($recipient->notify_transactions_email !== false) {
                Mail::to($recipient->email)->send(new TransactionMail(
                    user: $recipient,
                    title: 'Money received',
                    amountSign: '+',
                    amount: $amount,
                    reference: $transfer->reference,
                    rows: [
                        ['label' => 'From', 'value' => $sender->name],
                        ['label' => 'Date', 'value' => $transfer->created_at->format('M j, Y \a\t g:i A')],
                    ],
                    newBalance: $recipientBalanceAfter,
                ));
            }
        } catch (\Throwable $e) {
            Log::error('Failed to send transfer notification email: '.$e->getMessage());
        }

        // Bell notifications — always created regardless of
        // notify_transactions_email (see AppNotification::notify()'s own
        // comment for why that email-only preference doesn't gate this).
        AppNotification::notify(
            $sender,
            'Money sent',
            'You sent $'.number_format($amount, 2)." to {$recipient->name}.",
            route('send.receipt', $transfer),
        );
        AppNotification::notify(
            $recipient,
            'Money received',
            'You received $'.number_format($amount, 2)." from {$sender->name}.",
            route('send.receipt', $transfer),
        );

        return redirect()->route('send.receipt', $transfer);
    }

    /**
     * Ledger-to-Ledger, but scheduled: same recipient lookup as
     * storeLedgerTransfer(), but nothing about the balance moves yet — this
     * just records the row with status = 'scheduled' and the date it
     * should go out. No insufficient-funds check happens here either;
     * whether the money is actually there gets checked for real by
     * ProcessScheduledTransfers on the day it runs, since a balance
     * checked now could easily be different by the scheduled date.
     */
    private function scheduleLedgerTransfer(Request $request, array $data): RedirectResponse
    {
        $sender = $request->user();

        $recipient = User::where('is_admin', false)
            ->where(function ($query) use ($data) {
                $query->where('checking_account_number', $data['recipient_account_number'])
                    ->orWhere('savings_account_number', $data['recipient_account_number']);
            })
            ->first();

        if (! $recipient) {
            return back()->withInput()->with('sendError', "We couldn't find a Ledger account with that number.");
        }

        if ($recipient->id === $sender->id) {
            return back()->withInput()->with('sendError', "You can't send money to your own account.");
        }

        $amount = (float) $data['amount'];

        $transfer = Transfer::create([
            'reference' => 'LDG'.now()->format('ymd').strtoupper(Str::random(6)),
            'sender_id' => $sender->id,
            'recipient_id' => $recipient->id,
            'amount' => $amount,
            'status' => 'scheduled',
            'scheduled_for' => $data['scheduled_date'],
            'category' => $data['category'] ?? null,
            'note' => $data['note'] ?? null,
        ]);

        return redirect()->route('send')->with('sendSuccess', sprintf(
            'Your transfer of $%s to %s is scheduled for %s. You can cancel it any time before then from History.',
            number_format($amount, 2),
            $recipient->name,
            $transfer->scheduled_for->format('M j, Y'),
        ));
    }

    /**
     * "Another bank": there is no recipient to look up or credit inside
     * our own database — the money is genuinely leaving Ledger. This
     * mirrors what a real bank's own ledger does for an outgoing ACH/wire:
     * debit the sender for exactly the amount entered, record exactly what
     * was typed in (account number, routing number, recipient name), and
     * stop there — there's no external ACH/wire rail actually wired up
     * (that needs a payment processor like Dwolla or Plaid Transfer, a
     * business agreement, and compliance review that's out of scope here),
     * so nothing pretends to have "arrived" anywhere. No fee is charged.
     */
    private function storeExternalTransfer(Request $request, array $data): RedirectResponse
    {
        $sender = $request->user();
        $amount = (float) $data['amount'];

        $externalTransfer = null;
        $senderBalanceAfter = null;

        try {
            DB::transaction(function () use ($sender, $amount, $data, &$externalTransfer, &$senderBalanceAfter) {
                $freshSender = User::whereKey($sender->id)->lockForUpdate()->first();

                if ((float) $freshSender->balance < $amount) {
                    throw new RuntimeException('insufficient_funds');
                }

                $freshSender->balance = (float) $freshSender->balance - $amount;
                $freshSender->save();
                $senderBalanceAfter = (float) $freshSender->balance;

                $externalTransfer = ExternalTransfer::create([
                    'reference' => 'LDG'.now()->format('ymd').strtoupper(Str::random(6)),
                    'sender_id' => $sender->id,
                    'amount' => $amount,
                    'status' => 'completed',
                    'fee' => 0,
                    'recipient_name' => $data['bank_recipient_name'],
                    'bank_name' => $data['bank_name'],
                    'account_number' => $data['bank_account_number'],
                    'routing_number' => $data['bank_routing_number'],
                    'category' => $data['category'] ?? null,
                    'note' => $data['note'] ?? null,
                ]);
            });
        } catch (RuntimeException $e) {
            if ($e->getMessage() === 'insufficient_funds') {
                return back()->withInput()->with('sendError', 'Insufficient funds for this transfer.');
            }

            throw $e;
        }

        // See storeLedgerTransfer() above for why this is gated by
        // notify_transactions_email and wrapped in try/catch.
        try {
            // !== false (not just a truthy check): a missing/never-set
            // column reads back as null, and null should still mean "send
            // it" since the preference defaults to on — only an explicit
            // false (the user actually switched it off) should skip this.
            if ($sender->notify_transactions_email !== false) {
                Mail::to($sender->email)->send(new TransactionMail(
                    user: $sender,
                    title: 'Money sent',
                    amountSign: '-',
                    amount: $amount,
                    reference: $externalTransfer->reference,
                    rows: [
                        ['label' => 'To', 'value' => $externalTransfer->recipient_name],
                        ['label' => 'Bank', 'value' => $externalTransfer->bank_name],
                        ['label' => 'Date', 'value' => $externalTransfer->created_at->format('M j, Y \a\t g:i A')],
                    ],
                    newBalance: $senderBalanceAfter,
                ));
            }
        } catch (\Throwable $e) {
            Log::error('Failed to send external transfer notification email: '.$e->getMessage());
        }

        AppNotification::notify(
            $sender,
            'Money sent',
            'You sent $'.number_format($amount, 2)." to {$externalTransfer->recipient_name}.",
            route('send.receipt.external', $externalTransfer),
        );

        return redirect()->route('send.receipt.external', $externalTransfer);
    }

    /**
     * Same idea as scheduleLedgerTransfer(), for "Another bank" — records
     * the row with status = 'scheduled', no balance movement or funds
     * check until ProcessScheduledTransfers actually runs it.
     */
    private function scheduleExternalTransfer(Request $request, array $data): RedirectResponse
    {
        $sender = $request->user();
        $amount = (float) $data['amount'];

        $externalTransfer = ExternalTransfer::create([
            'reference' => 'LDG'.now()->format('ymd').strtoupper(Str::random(6)),
            'sender_id' => $sender->id,
            'amount' => $amount,
            'status' => 'scheduled',
            'scheduled_for' => $data['scheduled_date'],
            'fee' => 0,
            'recipient_name' => $data['bank_recipient_name'],
            'bank_name' => $data['bank_name'],
            'account_number' => $data['bank_account_number'],
            'routing_number' => $data['bank_routing_number'],
            'category' => $data['category'] ?? null,
            'note' => $data['note'] ?? null,
        ]);

        return redirect()->route('send')->with('sendSuccess', sprintf(
            'Your transfer of $%s to %s is scheduled for %s. You can cancel it any time before then from History.',
            number_format($amount, 2),
            $externalTransfer->recipient_name,
            $externalTransfer->scheduled_for->format('M j, Y'),
        ));
    }

    /**
     * "International bank" — same reasoning as storeExternalTransfer()
     * above (there's no recipient to look up or credit inside our own
     * database, and no legitimate API can verify an account holder's name
     * from what's typed here), just with its own fields and its own table.
     * No fee is charged, same as the domestic version.
     */
    private function storeInternationalTransfer(Request $request, array $data): RedirectResponse
    {
        $sender = $request->user();
        $amount = (float) $data['amount'];

        $internationalTransfer = null;
        $senderBalanceAfter = null;

        try {
            DB::transaction(function () use ($sender, $amount, $data, &$internationalTransfer, &$senderBalanceAfter) {
                $freshSender = User::whereKey($sender->id)->lockForUpdate()->first();

                if ((float) $freshSender->balance < $amount) {
                    throw new RuntimeException('insufficient_funds');
                }

                $freshSender->balance = (float) $freshSender->balance - $amount;
                $freshSender->save();
                $senderBalanceAfter = (float) $freshSender->balance;

                // Locked in now, at the moment the money actually leaves —
                // see CurrencyRates for why this is a fixed reference table
                // rather than a live rate, and why both columns are
                // nullable (currency not in the table -> nothing to show).
                $currency = strtoupper($data['intl_currency']);
                $rate = CurrencyRates::rate($currency);

                $internationalTransfer = InternationalTransfer::create([
                    'reference' => 'LDG'.now()->format('ymd').strtoupper(Str::random(6)),
                    'sender_id' => $sender->id,
                    'amount' => $amount,
                    'status' => 'completed',
                    'fee' => 0,
                    'recipient_name' => $data['intl_recipient_name'],
                    'bank_name' => $data['intl_bank_name'],
                    'country' => $data['intl_country'],
                    'swift_code' => strtoupper($data['intl_swift_code']),
                    'account_number' => $data['intl_account_number'],
                    'currency' => $currency,
                    'exchange_rate' => $rate,
                    'converted_amount' => $rate === null ? null : round($amount * $rate, 2),
                    'category' => $data['category'] ?? null,
                    'note' => $data['note'] ?? null,
                ]);
            });
        } catch (RuntimeException $e) {
            if ($e->getMessage() === 'insufficient_funds') {
                return back()->withInput()->with('sendError', 'Insufficient funds for this transfer.');
            }

            throw $e;
        }

        // See storeLedgerTransfer() above for why this is gated by
        // notify_transactions_email and wrapped in try/catch.
        try {
            if ($sender->notify_transactions_email !== false) {
                Mail::to($sender->email)->send(new TransactionMail(
                    user: $sender,
                    title: 'Money sent',
                    amountSign: '-',
                    amount: $amount,
                    reference: $internationalTransfer->reference,
                    rows: [
                        ['label' => 'To', 'value' => $internationalTransfer->recipient_name],
                        ['label' => 'Bank', 'value' => $internationalTransfer->bank_name],
                        ['label' => 'Country', 'value' => $internationalTransfer->country],
                        ['label' => 'Date', 'value' => $internationalTransfer->created_at->format('M j, Y \a\t g:i A')],
                    ],
                    newBalance: $senderBalanceAfter,
                ));
            }
        } catch (\Throwable $e) {
            Log::error('Failed to send international transfer notification email: '.$e->getMessage());
        }

        AppNotification::notify(
            $sender,
            'Money sent',
            'You sent $'.number_format($amount, 2)." to {$internationalTransfer->recipient_name}.",
            route('send.receipt.international', $internationalTransfer),
        );

        return redirect()->route('send.receipt.international', $internationalTransfer);
    }

    /**
     * Same idea as scheduleExternalTransfer() above, for "International
     * bank" — records the row with status = 'scheduled', no balance
     * movement or funds check until ProcessScheduledTransfers actually
     * runs it.
     */
    private function scheduleInternationalTransfer(Request $request, array $data): RedirectResponse
    {
        $sender = $request->user();
        $amount = (float) $data['amount'];

        // Locked in at schedule time, same as storeInternationalTransfer()
        // above — the rate quoted when this was scheduled is what shows on
        // the receipt later, not whatever the table says by the time it
        // actually goes out.
        $currency = strtoupper($data['intl_currency']);
        $rate = CurrencyRates::rate($currency);

        $internationalTransfer = InternationalTransfer::create([
            'reference' => 'LDG'.now()->format('ymd').strtoupper(Str::random(6)),
            'sender_id' => $sender->id,
            'amount' => $amount,
            'status' => 'scheduled',
            'scheduled_for' => $data['scheduled_date'],
            'fee' => 0,
            'recipient_name' => $data['intl_recipient_name'],
            'bank_name' => $data['intl_bank_name'],
            'country' => $data['intl_country'],
            'swift_code' => strtoupper($data['intl_swift_code']),
            'account_number' => $data['intl_account_number'],
            'currency' => $currency,
            'exchange_rate' => $rate,
            'converted_amount' => $rate === null ? null : round($amount * $rate, 2),
            'category' => $data['category'] ?? null,
            'note' => $data['note'] ?? null,
        ]);

        return redirect()->route('send')->with('sendSuccess', sprintf(
            'Your transfer of $%s to %s is scheduled for %s. You can cancel it any time before then from History.',
            number_format($amount, 2),
            $internationalTransfer->recipient_name,
            $internationalTransfer->scheduled_for->format('M j, Y'),
        ));
    }

    /**
     * Cancels a still-pending scheduled Ledger-to-Ledger transfer — the
     * "Cancel" button next to a scheduled row in History (see
     * HistoryController). Scoped to the sender only (a recipient has
     * nothing to cancel — the money was never taken from them), and only
     * while it's still actually scheduled: once ProcessScheduledTransfers
     * has run it, cancelling would try to undo a transfer that already
     * completed, which isn't what this button is for.
     */
    public function cancelScheduled(Request $request, Transfer $transfer): RedirectResponse
    {
        abort_unless($transfer->sender_id === $request->user()->id, 404);

        if ($transfer->status !== 'scheduled') {
            return back()->with('sendError', 'That transfer already went out and can no longer be cancelled.');
        }

        $transfer->update(['status' => 'cancelled']);

        return back()->with('sendSuccess', 'Scheduled transfer cancelled — nothing was sent.');
    }

    /**
     * Same as cancelScheduled() above, for a scheduled "Another bank"
     * transfer.
     */
    public function cancelScheduledExternal(Request $request, ExternalTransfer $externalTransfer): RedirectResponse
    {
        abort_unless($externalTransfer->sender_id === $request->user()->id, 404);

        if ($externalTransfer->status !== 'scheduled') {
            return back()->with('sendError', 'That transfer already went out and can no longer be cancelled.');
        }

        $externalTransfer->update(['status' => 'cancelled']);

        return back()->with('sendSuccess', 'Scheduled transfer cancelled — nothing was sent.');
    }

    /**
     * Same as cancelScheduled()/cancelScheduledExternal() above, for a
     * scheduled "International bank" transfer.
     */
    public function cancelScheduledInternational(Request $request, InternationalTransfer $internationalTransfer): RedirectResponse
    {
        abort_unless($internationalTransfer->sender_id === $request->user()->id, 404);

        if ($internationalTransfer->status !== 'scheduled') {
            return back()->with('sendError', 'That transfer already went out and can no longer be cancelled.');
        }

        $internationalTransfer->update(['status' => 'cancelled']);

        return back()->with('sendSuccess', 'Scheduled transfer cancelled — nothing was sent.');
    }

    /**
     * Backs the live "type an account number, see the name" lookup on the
     * Ledger tab of send.blade.php — the same account-number match
     * storeLedgerTransfer() uses, just exposed as its own endpoint so the
     * page can call it as someone types, before they've submitted
     * anything. Never reveals anything beyond a name: no balance, no
     * email, nothing else about the account, and only a match/no-match
     * shape either way — a wrong account number and someone else's own
     * account number both come back simply "not found" (never "that's not
     * a real account" vs. "that's yours"), so this can't be used to
     * enumerate which numbers are real, beyond the standard throttle
     * already applied on the route.
     *
     * This ONLY works because Ledger accounts are rows in our own
     * database. There is no equivalent for "Another bank" — no legitimate
     * API can return an account holder's name from a bare account +
     * routing number — see storeExternalTransfer() above for what that tab
     * does instead.
     */
    public function lookup(Request $request): JsonResponse
    {
        $accountNumber = trim((string) $request->query('account_number', ''));

        if ($accountNumber === '') {
            return response()->json(['found' => false]);
        }

        $recipient = User::where('is_admin', false)
            ->where(function ($query) use ($accountNumber) {
                $query->where('checking_account_number', $accountNumber)
                    ->orWhere('savings_account_number', $accountNumber);
            })
            ->first();

        if (! $recipient || $recipient->id === $request->user()->id) {
            return response()->json(['found' => false]);
        }

        return response()->json([
            'found' => true,
            'name' => $recipient->name,
        ]);
    }

    /**
     * Live "recipient receives approximately" preview for the
     * International tab (see send.blade.php's onIntlAmountOrCurrencyInput())
     * — recalculated as the sender types the amount or changes the
     * currency, before anything is actually submitted. Purely a preview:
     * the real rate that ends up on the transfer is looked up again,
     * fresh, at the moment storeInternationalTransfer()/
     * scheduleInternationalTransfer() actually creates the row, not
     * carried over from this call.
     */
    public function convertPreview(Request $request): JsonResponse
    {
        $currency = strtoupper(trim((string) $request->query('currency', '')));
        $amount = (float) str_replace(',', '', (string) $request->query('amount', '0'));

        $rate = $currency !== '' ? CurrencyRates::rate($currency) : null;

        if ($rate === null || $amount <= 0) {
            return response()->json(['known' => false]);
        }

        return response()->json([
            'known' => true,
            'currency' => $currency,
            'rate' => $rate,
            'converted' => round($amount * $rate, 2),
        ]);
    }

    /**
     * The Ledger-to-Ledger bank-debit-style confirmation page. Scoped to
     * whoever was actually part of the transfer — sender OR recipient —
     * so History (see HistoryController) can link a received transfer to
     * a real receipt too, not just the ones you sent. abort_unless still
     * keeps a third party from guessing another pair's transfer id in the
     * URL. Which branch runs below depends on which side of the transfer
     * the person looking at it is on — a sender sees "Payment sent" with
     * the recipient's details, the recipient sees "Payment received" with
     * the sender's — both built into the same normalized $rows shape
     * send-receipt.blade.php renders, just with different labels.
     */
    public function receipt(Request $request, Transfer $transfer): View
    {
        $viewer = $request->user();
        abort_unless($transfer->sender_id === $viewer->id || $transfer->recipient_id === $viewer->id, 404);
        $transfer->load('sender', 'recipient');

        // History links here with ?from=history — "Send another" makes
        // sense right after you've just sent money, not while browsing
        // back through old transactions, so it's hidden in that context.
        // See HistoryController::index() for where this gets set.
        $hideSendAnother = $request->query('from') === 'history';

        // A transfer isn't always 'completed' any more — History's Cancel
        // button now links straight to this same receipt while it's still
        // 'scheduled', and it can also be viewed afterwards as 'cancelled'
        // /'failed' (see HistoryController and ProcessScheduledTransfers).
        // This is what keeps the page from claiming money already moved
        // ("Payment sent", a green checkmark) when it hasn't.
        [$statusIcon, $statusBanner] = match ($transfer->status) {
            'scheduled' => ['clock', __('receipt.banner_scheduled', ['date' => $transfer->scheduled_for->format('M j, Y')])],
            'cancelled' => ['x', __('receipt.banner_cancelled')],
            'failed' => ['x', __('receipt.banner_failed')],
            default => ['check', null],
        };

        // Titles are picked directly off $transfer->status (not built by
        // concatenating statusLabel() into a sentence) so every language
        // gets a naturally-worded full title instead of an
        // English-word-order "Transfer " + lowercased-label mashup — see
        // lang/{locale}/receipt.php's transfer_* keys.
        $transferTitle = fn (string $completedKey) => match ($transfer->status) {
            'scheduled' => __('receipt.title_transfer_scheduled'),
            'cancelled' => __('receipt.title_transfer_cancelled'),
            'failed' => __('receipt.title_transfer_failed'),
            default => __($completedKey),
        };

        if ($transfer->recipient_id === $viewer->id) {
            $rows = [
                ['label' => __('receipt.row_from'), 'value' => $transfer->sender->name],
                // "Bank" here is always Ledger itself — a received Ledger
                // transfer never came from anywhere else — but it's shown
                // explicitly rather than assumed, matching the "Bank" row
                // an external transfer's receipt already has. The value is
                // the credit union's proper name, so it's deliberately not
                // run through __() — see lang note on brand names.
                ['label' => __('receipt.row_bank'), 'value' => 'Ledger Federal Credit Union'],
                ['label' => __('receipt.row_status'), 'value' => $transfer->statusLabel()],
            ];

            if ($transfer->category) {
                $rows[] = ['label' => __('receipt.row_category'), 'value' => $transfer->category];
            }
            if ($transfer->note) {
                $rows[] = ['label' => __('receipt.row_note'), 'value' => $transfer->note];
            }

            $rows[] = ['label' => __('receipt.row_reference'), 'value' => $transfer->reference];
            $rows[] = ['label' => __('receipt.row_date'), 'value' => $transfer->created_at->format('M j, Y \a\t g:i A')];

            return view('send-receipt', [
                'title' => $transferTitle('receipt.title_payment_received'),
                'amountSign' => '+',
                'subVerb' => 'from',
                'recipientName' => $transfer->sender->name,
                'amount' => (float) $transfer->amount,
                'reference' => $transfer->reference,
                'rows' => $rows,
                'banner' => $statusBanner,
                'icon' => $statusIcon,
                'hideSendAnother' => $hideSendAnother,
            ]);
        }

        $recipientAccount = $transfer->recipient->checking_account_number
            ?? $transfer->recipient->savings_account_number;

        $rows = [
            ['label' => __('receipt.row_recipient'), 'value' => $transfer->recipient->name],
        ];

        if ($recipientAccount) {
            $rows[] = ['label' => __('receipt.row_account_number'), 'value' => '•••• '.substr($recipientAccount, -4)];
        }

        $rows[] = ['label' => __('receipt.row_from'), 'value' => $transfer->sender->name];
        $rows[] = ['label' => __('receipt.row_status'), 'value' => $transfer->statusLabel()];

        if ($transfer->status === 'scheduled') {
            $rows[] = ['label' => __('receipt.row_scheduled_for'), 'value' => $transfer->scheduled_for->format('M j, Y')];
        }

        if ($transfer->category) {
            $rows[] = ['label' => __('receipt.row_category'), 'value' => $transfer->category];
        }
        if ($transfer->note) {
            $rows[] = ['label' => __('receipt.row_note'), 'value' => $transfer->note];
        }

        $rows[] = ['label' => __('receipt.row_reference'), 'value' => $transfer->reference];
        $rows[] = ['label' => __('receipt.row_date'), 'value' => $transfer->created_at->format('M j, Y \a\t g:i A')];

        return view('send-receipt', [
            'title' => $transferTitle('receipt.title_payment_sent'),
            'amountSign' => '-',
            'subVerb' => 'to',
            'recipientName' => $transfer->recipient->name,
            'amount' => (float) $transfer->amount,
            'reference' => $transfer->reference,
            'rows' => $rows,
            'banner' => $statusBanner,
            'icon' => $statusIcon,
            'hideSendAnother' => $hideSendAnother,
        ]);
    }

    /**
     * The "Another bank" version of the receipt above — same template,
     * built from ExternalTransfer instead of Transfer.
     */
    public function externalReceipt(Request $request, ExternalTransfer $externalTransfer): View
    {
        abort_unless($externalTransfer->sender_id === $request->user()->id, 404);
        $externalTransfer->load('sender');

        // See receipt() above for why this exists — an external transfer
        // can now be viewed here while still 'scheduled', or afterwards as
        // 'cancelled'/'failed', not just 'completed'.
        [$statusIcon, $statusBanner] = match ($externalTransfer->status) {
            'scheduled' => ['clock', __('receipt.banner_scheduled', ['date' => $externalTransfer->scheduled_for->format('M j, Y')])],
            'cancelled' => ['x', __('receipt.banner_cancelled')],
            'failed' => ['x', __('receipt.banner_failed')],
            default => ['check', __('receipt.banner_transfer_sent')],
        };

        $rows = [
            ['label' => __('receipt.row_recipient'), 'value' => $externalTransfer->recipient_name],
            ['label' => __('receipt.row_bank'), 'value' => $externalTransfer->bank_name],
            ['label' => __('receipt.row_account_number'), 'value' => '•••• '.substr($externalTransfer->account_number, -4)],
            ['label' => __('receipt.row_routing_number'), 'value' => '•••'.substr($externalTransfer->routing_number, -3)],
            ['label' => __('receipt.row_from'), 'value' => $externalTransfer->sender->name],
            ['label' => __('receipt.row_status'), 'value' => $externalTransfer->statusLabel()],
        ];

        if ($externalTransfer->status === 'scheduled') {
            $rows[] = ['label' => __('receipt.row_scheduled_for'), 'value' => $externalTransfer->scheduled_for->format('M j, Y')];
        }

        if ($externalTransfer->category) {
            $rows[] = ['label' => __('receipt.row_category'), 'value' => $externalTransfer->category];
        }
        if ($externalTransfer->note) {
            $rows[] = ['label' => __('receipt.row_note'), 'value' => $externalTransfer->note];
        }

        $rows[] = ['label' => __('receipt.row_reference'), 'value' => $externalTransfer->reference];
        $rows[] = ['label' => __('receipt.row_date'), 'value' => $externalTransfer->created_at->format('M j, Y \a\t g:i A')];

        return view('send-receipt', [
            'title' => match ($externalTransfer->status) {
                'scheduled' => __('receipt.title_transfer_scheduled'),
                'cancelled' => __('receipt.title_transfer_cancelled'),
                'failed' => __('receipt.title_transfer_failed'),
                default => __('receipt.title_payment_sent'),
            },
            'recipientName' => $externalTransfer->recipient_name,
            'amount' => (float) $externalTransfer->amount,
            'reference' => $externalTransfer->reference,
            'rows' => $rows,
            'banner' => $statusBanner,
            'icon' => $statusIcon,
            // Same "hide it when browsing back through History" rule as
            // receipt() above.
            'hideSendAnother' => $request->query('from') === 'history',
        ]);
    }

    /**
     * The "International bank" version of the receipt above — same
     * template, built from InternationalTransfer instead of
     * ExternalTransfer.
     */
    public function internationalReceipt(Request $request, InternationalTransfer $internationalTransfer): View
    {
        abort_unless($internationalTransfer->sender_id === $request->user()->id, 404);
        $internationalTransfer->load('sender');

        [$statusIcon, $statusBanner] = match ($internationalTransfer->status) {
            'scheduled' => ['clock', __('receipt.banner_scheduled', ['date' => $internationalTransfer->scheduled_for->format('M j, Y')])],
            'cancelled' => ['x', __('receipt.banner_cancelled')],
            'failed' => ['x', __('receipt.banner_failed')],
            default => ['check', __('receipt.banner_international_transfer_sent')],
        };

        $rows = [
            ['label' => __('receipt.row_recipient'), 'value' => $internationalTransfer->recipient_name],
            ['label' => __('receipt.row_bank'), 'value' => $internationalTransfer->bank_name],
            ['label' => __('receipt.row_country'), 'value' => $internationalTransfer->country],
            ['label' => __('receipt.row_swift_bic'), 'value' => $internationalTransfer->swift_code],
            ['label' => __('receipt.row_account_iban'), 'value' => '•••• '.substr($internationalTransfer->account_number, -4)],
            ['label' => __('receipt.row_currency'), 'value' => $internationalTransfer->currency],
        ];

        // Only shown when a rate was actually locked in at send/schedule
        // time (see storeInternationalTransfer()/scheduleInternationalTransfer())
        // — a USD transfer, or a currency App\Support\CurrencyRates doesn't
        // carry a rate for, just doesn't get these two rows. The "1 USD ="
        // notation itself is left untranslated — a universal financial
        // convention, same reasoning as leaving SWIFT/BIC codes untranslated.
        if ($internationalTransfer->converted_amount !== null) {
            $rows[] = ['label' => __('receipt.row_exchange_rate'), 'value' => '1 USD = '.rtrim(rtrim(number_format((float) $internationalTransfer->exchange_rate, 4), '0'), '.').' '.$internationalTransfer->currency];
            $rows[] = ['label' => __('receipt.row_recipient_receives_approx'), 'value' => $internationalTransfer->convertedAmountDisplay()];
        }

        $rows[] = ['label' => __('receipt.row_from'), 'value' => $internationalTransfer->sender->name];
        $rows[] = ['label' => __('receipt.row_status'), 'value' => $internationalTransfer->statusLabel()];

        if ($internationalTransfer->status === 'scheduled') {
            $rows[] = ['label' => __('receipt.row_scheduled_for'), 'value' => $internationalTransfer->scheduled_for->format('M j, Y')];
        }

        if ($internationalTransfer->category) {
            $rows[] = ['label' => __('receipt.row_category'), 'value' => $internationalTransfer->category];
        }
        if ($internationalTransfer->note) {
            $rows[] = ['label' => __('receipt.row_note'), 'value' => $internationalTransfer->note];
        }

        $rows[] = ['label' => __('receipt.row_reference'), 'value' => $internationalTransfer->reference];
        $rows[] = ['label' => __('receipt.row_date'), 'value' => $internationalTransfer->created_at->format('M j, Y \a\t g:i A')];

        return view('send-receipt', [
            'title' => match ($internationalTransfer->status) {
                'scheduled' => __('receipt.title_transfer_scheduled'),
                'cancelled' => __('receipt.title_transfer_cancelled'),
                'failed' => __('receipt.title_transfer_failed'),
                default => __('receipt.title_payment_sent'),
            },
            'recipientName' => $internationalTransfer->recipient_name,
            'amount' => (float) $internationalTransfer->amount,
            'reference' => $internationalTransfer->reference,
            'rows' => $rows,
            'banner' => $statusBanner,
            'icon' => $statusIcon,
            'hideSendAnother' => $request->query('from') === 'history',
        ]);
    }
}
