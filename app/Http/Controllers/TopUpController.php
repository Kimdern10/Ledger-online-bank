<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\RequiresTransactionPin;
use App\Mail\TransactionMail;
use App\Models\AppNotification;
use App\Models\LinkedAccount;
use App\Models\TopUp;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TopUpController extends Controller
{
    use RequiresTransactionPin;

    /**
     * Same 2.9% shown on top-up.blade.php's fee breakdown before this page
     * was wired to the database — see store() for why it never reduces the
     * amount actually credited.
     */
    private const CARD_TOPUP_FEE_RATE = 0.029;

    /**
     * Submits the Top Up form. The full $amount is always credited to the
     * user's Ledger balance — a card top-up's fee is what the linked card
     * would have been charged on top (matching the "Total charged $102.90"
     * breakdown already on the page), not money that comes out of the
     * $100 that actually lands in Ledger.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'amount' => (float) str_replace(',', '', (string) $request->input('amount', '0')),
        ]);

        $data = $request->validate([
            'source_type' => ['required', 'in:bank,card'],
            'linked_account_id' => ['required', 'integer'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:999999.99'],
        ]);

        $user = $request->user();

        $linkedAccount = LinkedAccount::where('id', $data['linked_account_id'])
            ->where('user_id', $user->id)
            ->where('type', $data['source_type'])
            ->first();

        if (! $linkedAccount) {
            return back()->withInput()->with(
                'topUpError',
                'Select a linked '.($data['source_type'] === 'card' ? 'card' : 'bank account').' to top up from.'
            );
        }

        // See RequiresTransactionPin — checked once the source itself is
        // confirmed valid, right before the balance actually moves.
        if ($blocked = $this->blockedByTransactionPin($request, $user, 'topUpError')) {
            return $blocked;
        }

        $amount = (float) $data['amount'];
        $fee = $data['source_type'] === 'card'
            ? round($amount * self::CARD_TOPUP_FEE_RATE, 2)
            : 0.0;
        $topUp = null;
        $balanceAfter = null;

        DB::transaction(function () use ($user, $linkedAccount, $amount, $fee, $data, &$topUp, &$balanceAfter) {
            // lockForUpdate here too, for the same reason as everywhere else
            // that touches balance — keeps two near-simultaneous top-ups
            // from clobbering each other's read of the starting balance.
            $freshUser = User::whereKey($user->id)->lockForUpdate()->first();
            $freshUser->balance = (float) $freshUser->balance + $amount;
            $freshUser->save();
            $balanceAfter = (float) $freshUser->balance;

            $topUp = TopUp::create([
                'user_id' => $user->id,
                'linked_account_id' => $linkedAccount->id,
                'reference' => 'TU'.now()->format('ymd').strtoupper(Str::random(6)),
                'source_type' => $data['source_type'],
                'source_label' => $linkedAccount->displayLabel(),
                'source_last4' => $linkedAccount->last4(),
                'amount' => $amount,
                'fee' => $fee,
                'status' => 'completed',
            ]);
        });

        // See TransferController::storeLedgerTransfer() for why this is
        // gated by notify_transactions_email and wrapped in try/catch — the
        // top-up above already succeeded and the balance already moved.
        try {
            // !== false (not just a truthy check): a missing/never-set
            // column reads back as null, and null should still mean "send
            // it" since the preference defaults to on — only an explicit
            // false (the user actually switched it off) should skip this.
            if ($user->notify_transactions_email !== false) {
                $rows = [
                    ['label' => 'Top up from', 'value' => $topUp->source_label.' •••• '.$topUp->source_last4],
                ];

                if ($fee > 0) {
                    $rows[] = ['label' => 'Card processing fee', 'value' => '$'.number_format($fee, 2)];
                }

                $rows[] = ['label' => 'Date', 'value' => $topUp->created_at->format('M j, Y \a\t g:i A')];

                Mail::to($user->email)->send(new TransactionMail(
                    user: $user,
                    title: 'Top-up',
                    amountSign: '+',
                    amount: $amount,
                    reference: $topUp->reference,
                    rows: $rows,
                    newBalance: $balanceAfter,
                ));
            }
        } catch (\Throwable $e) {
            Log::error('Failed to send top-up notification email: '.$e->getMessage());
        }

        AppNotification::notify(
            $user,
            'Top-up',
            'You topped up $'.number_format($amount, 2).' from '.$topUp->source_label.'.',
            route('top-up.receipt', $topUp),
        );

        return redirect()->route('top-up.receipt', $topUp);
    }

    /**
     * Same bank-debit-style receipt Send Money uses (send-receipt.blade.php)
     * — see TransferController::receipt() for the pattern this mirrors.
     */
    public function receipt(Request $request, TopUp $topUp): View
    {
        abort_unless($topUp->user_id === $request->user()->id, 404);

        $rows = [
            ['label' => __('receipt.row_top_up_from'), 'value' => $topUp->source_label.' •••• '.$topUp->source_last4],
        ];

        if ((float) $topUp->fee > 0) {
            $rows[] = ['label' => __('receipt.row_card_processing_fee'), 'value' => '$'.number_format((float) $topUp->fee, 2)];
        }

        $rows[] = ['label' => __('receipt.row_status'), 'value' => $topUp->statusLabel()];
        $rows[] = ['label' => __('receipt.row_reference'), 'value' => $topUp->reference];
        $rows[] = ['label' => __('receipt.row_date'), 'value' => $topUp->created_at->format('M j, Y \a\t g:i A')];

        return view('send-receipt', [
            'title' => __('receipt.title_topup'),
            'amountSign' => '+',
            'subVerb' => 'from',
            'recipientName' => $topUp->source_label,
            'amount' => (float) $topUp->amount,
            'reference' => $topUp->reference,
            'rows' => $rows,
            'banner' => (float) $topUp->fee > 0
                ? __('receipt.banner_topup_fee_note')
                : null,
            'hideSendAnother' => true,
        ]);
    }
}
