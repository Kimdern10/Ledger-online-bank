<?php

namespace App\Console\Commands;

use App\Http\Controllers\Concerns\EnforcesTierLimits;
use App\Mail\TransactionMail;
use App\Models\AppNotification;
use App\Models\ExternalTransfer;
use App\Models\InternationalTransfer;
use App\Models\Transfer;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * The other half of the "Schedule" option on send.blade.php.
 * TransferController::scheduleLedgerTransfer()/scheduleExternalTransfer()
 * only ever create a Transfer/ExternalTransfer row with status =
 * 'scheduled' — nothing about the balance moves at that point. This
 * command is what actually moves the money once the scheduled date
 * arrives: run it daily (see routes/console.php) and it finds every
 * scheduled row whose date has come, executes it with the exact same
 * locking/balance logic TransferController uses for an immediate send, and
 * sends the same "Money sent"/"Money received" emails.
 *
 * Registered via Schedule::command(self::class)->daily() in
 * routes/console.php — the scheduler itself still has to actually be
 * running for that to fire (see this command's own doc comment in
 * routes/console.php for how to do that locally). You can also always run
 * `php artisan transfers:process-scheduled` by hand any time, e.g. to test
 * this without waiting for the scheduler.
 *
 * Scheduling a transfer deliberately skips the tier daily-limit check (see
 * TransferController::store()'s own comment on why — nothing has moved yet,
 * so there's nothing to count against *that* day's limit). But the money
 * still has to clear the sender's tier limit for *the day it actually goes
 * out*, same as an immediate send would — otherwise scheduling would be a
 * free way around the whole limit. So each execute*Transfer() method below
 * checks EnforcesTierLimits::movedTodayBy() right alongside its existing
 * balance check, and fails the row (same as insufficient funds) if sending
 * it now would push the sender over today's limit.
 */
class ProcessScheduledTransfers extends Command
{
    use EnforcesTierLimits;

    protected $signature = 'transfers:process-scheduled';

    protected $description = 'Executes every Ledger/Another-bank/International transfer whose scheduled date has arrived';

    public function handle(): int
    {
        $this->processLedgerTransfers();
        $this->processExternalTransfers();
        $this->processInternationalTransfers();

        return self::SUCCESS;
    }

    private function processLedgerTransfers(): void
    {
        $due = Transfer::where('status', 'scheduled')
            ->whereDate('scheduled_for', '<=', now()->toDateString())
            ->get();

        foreach ($due as $transfer) {
            $this->executeLedgerTransfer($transfer);
        }
    }

    private function executeLedgerTransfer(Transfer $transfer): void
    {
        $sender = null;
        $recipient = null;
        $senderBalanceAfter = null;
        $recipientBalanceAfter = null;
        $insufficientFundsFor = null;
        $limitExceededFor = null;

        try {
            DB::transaction(function () use ($transfer, &$sender, &$recipient, &$senderBalanceAfter, &$recipientBalanceAfter, &$insufficientFundsFor, &$limitExceededFor) {
                // Re-lock and re-check the row itself, in case a cancel
                // request landed between the query above and this
                // transaction actually starting.
                $freshTransfer = Transfer::whereKey($transfer->id)->lockForUpdate()->first();

                if (! $freshTransfer || $freshTransfer->status !== 'scheduled') {
                    return;
                }

                $freshSender = User::whereKey($freshTransfer->sender_id)->lockForUpdate()->first();
                $freshRecipient = User::whereKey($freshTransfer->recipient_id)->lockForUpdate()->first();

                if (! $freshSender || ! $freshRecipient) {
                    $freshTransfer->update(['status' => 'failed']);

                    return;
                }

                if ((float) $freshSender->balance < (float) $freshTransfer->amount) {
                    $freshTransfer->update(['status' => 'failed']);
                    $insufficientFundsFor = $freshSender;

                    return;
                }

                if (! $freshSender->isAdmin() && $this->movedTodayBy($freshSender) + (float) $freshTransfer->amount > $freshSender->tierDailyLimit()) {
                    $freshTransfer->update(['status' => 'failed']);
                    $limitExceededFor = $freshSender;

                    return;
                }

                $freshSender->balance = (float) $freshSender->balance - (float) $freshTransfer->amount;
                $freshSender->save();

                $freshRecipient->balance = (float) $freshRecipient->balance + (float) $freshTransfer->amount;
                $freshRecipient->save();

                $freshTransfer->update(['status' => 'completed']);

                $sender = $freshSender;
                $recipient = $freshRecipient;
                $senderBalanceAfter = (float) $freshSender->balance;
                $recipientBalanceAfter = (float) $freshRecipient->balance;
            });
        } catch (\Throwable $e) {
            Log::error('Failed to execute scheduled transfer #'.$transfer->id.': '.$e->getMessage());

            return;
        }

        // Unlike a missing-user failure (too rare/unrecoverable a case to
        // usefully notify about — there may be no valid sender left to
        // tell), insufficient funds is the realistic case a user actually
        // needs to hear about: their scheduled payment silently didn't go
        // out. No email for this one (the original TransactionMail system
        // only ever covered successes) — just the bell, so it's still
        // visible without adding a whole new email template for one edge
        // case.
        if ($insufficientFundsFor) {
            AppNotification::notify(
                $insufficientFundsFor,
                'Scheduled transfer failed',
                'Your scheduled transfer of $'.number_format((float) $transfer->amount, 2)." didn't go through — insufficient funds.",
                route('history'),
            );
        }

        if ($limitExceededFor) {
            AppNotification::notify(
                $limitExceededFor,
                'Scheduled transfer failed',
                'Your scheduled transfer of $'.number_format((float) $transfer->amount, 2)." didn't go through — it would put you over your {$limitExceededFor->tierLabel()} daily limit. It'll need to be sent again on a day with enough room left.",
                route('history'),
            );
        }

        // Null here means the transaction returned early above (already
        // cancelled, marked failed, etc.) — nothing more to do.
        if (! $sender || ! $recipient) {
            return;
        }

        $this->info("Executed scheduled transfer #{$transfer->id}: {$sender->email} -> {$recipient->email}");

        try {
            if ($sender->notify_transactions_email !== false) {
                Mail::to($sender->email)->send(new TransactionMail(
                    user: $sender,
                    title: 'Money sent',
                    amountSign: '-',
                    amount: (float) $transfer->amount,
                    reference: $transfer->reference,
                    rows: [
                        ['label' => 'To', 'value' => $recipient->name],
                        ['label' => 'Date', 'value' => now()->format('M j, Y \a\t g:i A')],
                    ],
                    newBalance: $senderBalanceAfter,
                ));
            }

            if ($recipient->notify_transactions_email !== false) {
                Mail::to($recipient->email)->send(new TransactionMail(
                    user: $recipient,
                    title: 'Money received',
                    amountSign: '+',
                    amount: (float) $transfer->amount,
                    reference: $transfer->reference,
                    rows: [
                        ['label' => 'From', 'value' => $sender->name],
                        ['label' => 'Date', 'value' => now()->format('M j, Y \a\t g:i A')],
                    ],
                    newBalance: $recipientBalanceAfter,
                ));
            }
        } catch (\Throwable $e) {
            Log::error('Failed to send scheduled-transfer notification email: '.$e->getMessage());
        }

        AppNotification::notify(
            $sender,
            'Money sent',
            'Your scheduled transfer of $'.number_format((float) $transfer->amount, 2)." to {$recipient->name} went out.",
            route('send.receipt', $transfer),
        );
        AppNotification::notify(
            $recipient,
            'Money received',
            'You received $'.number_format((float) $transfer->amount, 2)." from {$sender->name}.",
            route('send.receipt', $transfer),
        );
    }

    private function processExternalTransfers(): void
    {
        $due = ExternalTransfer::where('status', 'scheduled')
            ->whereDate('scheduled_for', '<=', now()->toDateString())
            ->get();

        foreach ($due as $externalTransfer) {
            $this->executeExternalTransfer($externalTransfer);
        }
    }

    private function executeExternalTransfer(ExternalTransfer $externalTransfer): void
    {
        $sender = null;
        $senderBalanceAfter = null;
        $insufficientFundsFor = null;
        $limitExceededFor = null;

        try {
            DB::transaction(function () use ($externalTransfer, &$sender, &$senderBalanceAfter, &$insufficientFundsFor, &$limitExceededFor) {
                $freshExternal = ExternalTransfer::whereKey($externalTransfer->id)->lockForUpdate()->first();

                if (! $freshExternal || $freshExternal->status !== 'scheduled') {
                    return;
                }

                $freshSender = User::whereKey($freshExternal->sender_id)->lockForUpdate()->first();

                if (! $freshSender || (float) $freshSender->balance < (float) $freshExternal->amount) {
                    $freshExternal->update(['status' => 'failed']);

                    if ($freshSender) {
                        $insufficientFundsFor = $freshSender;
                    }

                    return;
                }

                if (! $freshSender->isAdmin() && $this->movedTodayBy($freshSender) + (float) $freshExternal->amount > $freshSender->tierDailyLimit()) {
                    $freshExternal->update(['status' => 'failed']);
                    $limitExceededFor = $freshSender;

                    return;
                }

                $freshSender->balance = (float) $freshSender->balance - (float) $freshExternal->amount;
                $freshSender->save();

                $freshExternal->update(['status' => 'completed']);

                $sender = $freshSender;
                $senderBalanceAfter = (float) $freshSender->balance;
            });
        } catch (\Throwable $e) {
            Log::error('Failed to execute scheduled external transfer #'.$externalTransfer->id.': '.$e->getMessage());

            return;
        }

        // See executeLedgerTransfer() above for why only the insufficient-
        // funds case gets a notification, and why no email exists for it.
        if ($insufficientFundsFor) {
            AppNotification::notify(
                $insufficientFundsFor,
                'Scheduled transfer failed',
                'Your scheduled transfer of $'.number_format((float) $externalTransfer->amount, 2)." to {$externalTransfer->recipient_name} didn't go through — insufficient funds.",
                route('history'),
            );
        }

        if ($limitExceededFor) {
            AppNotification::notify(
                $limitExceededFor,
                'Scheduled transfer failed',
                'Your scheduled transfer of $'.number_format((float) $externalTransfer->amount, 2)." to {$externalTransfer->recipient_name} didn't go through — it would put you over your {$limitExceededFor->tierLabel()} daily limit. It'll need to be sent again on a day with enough room left.",
                route('history'),
            );
        }

        if (! $sender) {
            return;
        }

        $this->info("Executed scheduled external transfer #{$externalTransfer->id}: {$sender->email}");

        try {
            if ($sender->notify_transactions_email !== false) {
                Mail::to($sender->email)->send(new TransactionMail(
                    user: $sender,
                    title: 'Money sent',
                    amountSign: '-',
                    amount: (float) $externalTransfer->amount,
                    reference: $externalTransfer->reference,
                    rows: [
                        ['label' => 'To', 'value' => $externalTransfer->recipient_name],
                        ['label' => 'Bank', 'value' => $externalTransfer->bank_name],
                        ['label' => 'Date', 'value' => now()->format('M j, Y \a\t g:i A')],
                    ],
                    newBalance: $senderBalanceAfter,
                ));
            }
        } catch (\Throwable $e) {
            Log::error('Failed to send scheduled-external-transfer notification email: '.$e->getMessage());
        }

        AppNotification::notify(
            $sender,
            'Money sent',
            'Your scheduled transfer of $'.number_format((float) $externalTransfer->amount, 2)." to {$externalTransfer->recipient_name} went out.",
            route('send.receipt.external', $externalTransfer),
        );
    }

    private function processInternationalTransfers(): void
    {
        $due = InternationalTransfer::where('status', 'scheduled')
            ->whereDate('scheduled_for', '<=', now()->toDateString())
            ->get();

        foreach ($due as $internationalTransfer) {
            $this->executeInternationalTransfer($internationalTransfer);
        }
    }

    private function executeInternationalTransfer(InternationalTransfer $internationalTransfer): void
    {
        $sender = null;
        $senderBalanceAfter = null;
        $insufficientFundsFor = null;
        $limitExceededFor = null;

        try {
            DB::transaction(function () use ($internationalTransfer, &$sender, &$senderBalanceAfter, &$insufficientFundsFor, &$limitExceededFor) {
                $freshInternational = InternationalTransfer::whereKey($internationalTransfer->id)->lockForUpdate()->first();

                if (! $freshInternational || $freshInternational->status !== 'scheduled') {
                    return;
                }

                $freshSender = User::whereKey($freshInternational->sender_id)->lockForUpdate()->first();

                if (! $freshSender || (float) $freshSender->balance < (float) $freshInternational->amount) {
                    $freshInternational->update(['status' => 'failed']);

                    if ($freshSender) {
                        $insufficientFundsFor = $freshSender;
                    }

                    return;
                }

                if (! $freshSender->isAdmin() && $this->movedTodayBy($freshSender) + (float) $freshInternational->amount > $freshSender->tierDailyLimit()) {
                    $freshInternational->update(['status' => 'failed']);
                    $limitExceededFor = $freshSender;

                    return;
                }

                $freshSender->balance = (float) $freshSender->balance - (float) $freshInternational->amount;
                $freshSender->save();

                $freshInternational->update(['status' => 'completed']);

                $sender = $freshSender;
                $senderBalanceAfter = (float) $freshSender->balance;
            });
        } catch (\Throwable $e) {
            Log::error('Failed to execute scheduled international transfer #'.$internationalTransfer->id.': '.$e->getMessage());

            return;
        }

        if ($insufficientFundsFor) {
            AppNotification::notify(
                $insufficientFundsFor,
                'Scheduled transfer failed',
                'Your scheduled transfer of $'.number_format((float) $internationalTransfer->amount, 2)." to {$internationalTransfer->recipient_name} didn't go through — insufficient funds.",
                route('history'),
            );
        }

        if ($limitExceededFor) {
            AppNotification::notify(
                $limitExceededFor,
                'Scheduled transfer failed',
                'Your scheduled transfer of $'.number_format((float) $internationalTransfer->amount, 2)." to {$internationalTransfer->recipient_name} didn't go through — it would put you over your {$limitExceededFor->tierLabel()} daily limit. It'll need to be sent again on a day with enough room left.",
                route('history'),
            );
        }

        if (! $sender) {
            return;
        }

        $this->info("Executed scheduled international transfer #{$internationalTransfer->id}: {$sender->email}");

        try {
            if ($sender->notify_transactions_email !== false) {
                Mail::to($sender->email)->send(new TransactionMail(
                    user: $sender,
                    title: 'Money sent',
                    amountSign: '-',
                    amount: (float) $internationalTransfer->amount,
                    reference: $internationalTransfer->reference,
                    rows: [
                        ['label' => 'To', 'value' => $internationalTransfer->recipient_name],
                        ['label' => 'Bank', 'value' => $internationalTransfer->bank_name],
                        ['label' => 'Country', 'value' => $internationalTransfer->country],
                        ['label' => 'Date', 'value' => now()->format('M j, Y \a\t g:i A')],
                    ],
                    newBalance: $senderBalanceAfter,
                ));
            }
        } catch (\Throwable $e) {
            Log::error('Failed to send scheduled-international-transfer notification email: '.$e->getMessage());
        }

        AppNotification::notify(
            $sender,
            'Money sent',
            'Your scheduled transfer of $'.number_format((float) $internationalTransfer->amount, 2)." to {$internationalTransfer->recipient_name} went out.",
            route('send.receipt.international', $internationalTransfer),
        );
    }
}
