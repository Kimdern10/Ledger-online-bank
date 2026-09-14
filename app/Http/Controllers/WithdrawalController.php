<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\EnforcesTierLimits;
use App\Http\Controllers\Concerns\RequiresTransactionPin;
use App\Mail\TransactionMail;
use App\Models\AppNotification;
use App\Models\LinkedAccount;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;
use RuntimeException;

class WithdrawalController extends Controller
{
    use EnforcesTierLimits, RequiresTransactionPin;

    /**
     * Card withdrawals are "instant" but cost $1.50 — same fee shown on
     * withdraw.blade.php's fee breakdown before this page was wired to the
     * database. Bank withdrawals have no fee, but (like a real bank) take
     * 1–3 business days to actually land — see the note left as-is on the
     * page. Either way the sender's Ledger balance moves right now; this
     * constant only affects what shows on the receipt.
     */
    private const CARD_WITHDRAWAL_FEE = 1.50;

    /**
     * Submits the Withdraw form. The full $amount always leaves the
     * sender's Ledger balance — the fee (card destinations only) comes out
     * of that same amount rather than being charged on top, matching the
     * "You'll receive $198.50" breakdown already shown on the page.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'amount' => (float) str_replace(',', '', (string) $request->input('amount', '0')),
        ]);

        $data = $request->validate([
            'destination_type' => ['required', 'in:bank,card'],
            'linked_account_id' => ['required', 'integer'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:999999.99'],
        ]);

        $user = $request->user();

        $linkedAccount = LinkedAccount::where('id', $data['linked_account_id'])
            ->where('user_id', $user->id)
            ->where('type', $data['destination_type'])
            ->first();

        if (! $linkedAccount) {
            return back()->withInput()->with(
                'withdrawError',
                'Select a linked '.($data['destination_type'] === 'card' ? 'card' : 'bank account').' to withdraw to.'
            );
        }

        // See RequiresTransactionPin — checked once the destination itself
        // is confirmed valid, right before any balance actually moves.
        if ($blocked = $this->blockedByTransactionPin($request, $user, 'withdrawError')) {
            return $blocked;
        }

        if ($blocked = $this->blockedByTierLimit($request, $user, (float) $data['amount'], 'withdrawError')) {
            return $blocked;
        }

        $amount = (float) $data['amount'];
        $fee = $data['destination_type'] === 'card' ? self::CARD_WITHDRAWAL_FEE : 0.0;
        $withdrawal = null;
        $balanceAfter = null;

        try {
            DB::transaction(function () use ($user, $linkedAccount, $amount, $fee, $data, &$withdrawal, &$balanceAfter) {
                // lockForUpdate so two withdrawals submitted at nearly the
                // same moment can't both read the same starting balance and
                // both pass a balance check that should have stopped the
                // second one — same guard TransferController uses.
                $freshUser = User::whereKey($user->id)->lockForUpdate()->first();

                if ((float) $freshUser->balance < $amount) {
                    throw new RuntimeException('insufficient_funds');
                }

                $freshUser->balance = (float) $freshUser->balance - $amount;
                $freshUser->save();
                $balanceAfter = (float) $freshUser->balance;

                $withdrawal = Withdrawal::create([
                    'user_id' => $user->id,
                    'linked_account_id' => $linkedAccount->id,
                    'reference' => 'WD'.now()->format('ymd').strtoupper(Str::random(6)),
                    'destination_type' => $data['destination_type'],
                    'destination_label' => $linkedAccount->displayLabel(),
                    'destination_last4' => $linkedAccount->last4(),
                    'amount' => $amount,
                    'fee' => $fee,
                    'status' => 'completed',
                ]);
            });
        } catch (RuntimeException $e) {
            if ($e->getMessage() === 'insufficient_funds') {
                return back()->withInput()->with('withdrawError', 'Insufficient funds for this withdrawal.');
            }

            throw $e;
        }

        // See TransferController::storeLedgerTransfer() for why this is
        // gated by notify_transactions_email and wrapped in try/catch — the
        // withdrawal above already succeeded and the balance already moved.
        try {
            // !== false (not just a truthy check): a missing/never-set
            // column reads back as null, and null should still mean "send
            // it" since the preference defaults to on — only an explicit
            // false (the user actually switched it off) should skip this.
            if ($user->notify_transactions_email !== false) {
                $rows = [
                    ['label' => 'Withdraw to', 'value' => $withdrawal->destination_label.' •••• '.$withdrawal->destination_last4],
                ];

                if ($fee > 0) {
                    $rows[] = ['label' => 'Fee', 'value' => '$'.number_format($fee, 2)];
                }

                $rows[] = ['label' => 'Date', 'value' => $withdrawal->created_at->format('M j, Y \a\t g:i A')];

                Mail::to($user->email)->send(new TransactionMail(
                    user: $user,
                    title: 'Withdrawal',
                    amountSign: '-',
                    amount: $amount,
                    reference: $withdrawal->reference,
                    rows: $rows,
                    newBalance: $balanceAfter,
                ));
            }
        } catch (\Throwable $e) {
            Log::error('Failed to send withdrawal notification email: '.$e->getMessage());
        }

        AppNotification::notify(
            $user,
            'Withdrawal',
            'You withdrew $'.number_format($amount, 2).' to '.$withdrawal->destination_label.'.',
            route('withdraw.receipt', $withdrawal),
        );

        return redirect()->route('withdraw.receipt', $withdrawal);
    }

    /**
     * Same bank-debit-style receipt Send Money uses (send-receipt.blade.php)
     * — see TransferController::receipt() for the pattern this mirrors.
     */
    public function receipt(Request $request, Withdrawal $withdrawal): View
    {
        abort_unless($withdrawal->user_id === $request->user()->id, 404);

        $rows = [
            ['label' => __('receipt.row_withdraw_to'), 'value' => $withdrawal->destination_label.' •••• '.$withdrawal->destination_last4],
        ];

        if ((float) $withdrawal->fee > 0) {
            $rows[] = ['label' => __('receipt.row_fee'), 'value' => '$'.number_format((float) $withdrawal->fee, 2)];
            $rows[] = ['label' => __('receipt.row_youll_receive'), 'value' => '$'.number_format((float) $withdrawal->amount - (float) $withdrawal->fee, 2)];
        }

        $rows[] = ['label' => __('receipt.row_status'), 'value' => $withdrawal->statusLabel()];
        $rows[] = ['label' => __('receipt.row_reference'), 'value' => $withdrawal->reference];
        $rows[] = ['label' => __('receipt.row_date'), 'value' => $withdrawal->created_at->format('M j, Y \a\t g:i A')];

        return view('send-receipt', [
            'title' => __('receipt.title_withdrawal'),
            'amountSign' => '-',
            'subVerb' => 'to',
            'recipientName' => $withdrawal->destination_label,
            'amount' => (float) $withdrawal->amount,
            'reference' => $withdrawal->reference,
            'rows' => $rows,
            'banner' => $withdrawal->destination_type === 'bank'
                ? __('receipt.banner_withdrawal_bank_days')
                : null,
            // Always hidden — "Send another" only makes sense right after
            // sending money, and this receipt is never reached from Send.
            'hideSendAnother' => true,
        ]);
    }
}
