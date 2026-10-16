<?php

namespace App\Support;

use App\Models\Card;
use App\Models\User;

/**
 * Every transaction of a customer's, normalized to one shape and sorted
 * newest first — what History shows (HistoryController::index()) and what
 * the monthly statement email lists (App\Support\MonthlyStatement).
 *
 * Included: Ledger transfers sent and received, Another bank and
 * International transfers, withdrawals, top-ups, admin balance changes
 * (all of which move the balance), and demo card purchases (which don't —
 * 'affects_balance' => false keeps them out of money in/out totals).
 *
 * @phpstan-type HistoryItem array{type: string, name: mixed, label: mixed, amount_raw: float, amount_display: string, created_at: mixed, url: string|null, affects_balance: bool}
 */
final class TransactionFeed
{
    /**
     * @return array<int, HistoryItem>
     */
    public static function for(User $user): array
    {

        // Only 'completed' rows — the money that actually moved.
        $sent = $user->sentTransfers()->where('status', 'completed')->with('recipient')->get()->map(function ($transfer) {
            return [
                'type' => 'sent',
                'name' => $transfer->recipient->name,
                'label' => $transfer->category ?: 'Sent',
                'amount_raw' => -1 * (float) $transfer->amount,
                'amount_display' => '–$'.number_format((float) $transfer->amount, 2),
                'created_at' => $transfer->created_at,
                // ?from=history tells the receipt page to hide "Send
                // another" — that button only makes sense right after
                // actually sending money, not while browsing back through
                // old transactions. See TransferController::receipt().
                'url' => route('send.receipt', ['reference' => $transfer->reference, 'from' => 'history']),
                'affects_balance' => true,
            ];
        });

        $received = $user->receivedTransfers()->where('status', 'completed')->with('sender')->get()->map(function ($transfer) {
            return [
                'type' => 'received',
                'name' => $transfer->sender->name,
                'label' => $transfer->category ?: 'Received',
                'amount_raw' => (float) $transfer->amount,
                'amount_display' => '+$'.number_format((float) $transfer->amount, 2),
                'created_at' => $transfer->created_at,
                'url' => route('send.receipt', ['reference' => $transfer->reference, 'from' => 'history']),
                'affects_balance' => true,
            ];
        });

        $external = $user->externalTransfers()->where('status', 'completed')->get()->map(function ($transfer) {
            return [
                'type' => 'sent',
                'name' => $transfer->recipient_name,
                'label' => $transfer->category ?: 'Sent',
                'amount_raw' => -1 * (float) $transfer->amount,
                'amount_display' => '–$'.number_format((float) $transfer->amount, 2),
                'created_at' => $transfer->created_at,
                'url' => route('send.receipt', ['reference' => $transfer->reference, 'from' => 'history']),
                'affects_balance' => true,
            ];
        });

        $international = $user->internationalTransfers()->where('status', 'completed')->get()->map(function ($transfer) {
            return [
                'type' => 'sent',
                'name' => $transfer->recipient_name,
                'label' => $transfer->category ?: 'Sent internationally',
                'amount_raw' => -1 * (float) $transfer->amount,
                'amount_display' => '–$'.number_format((float) $transfer->amount, 2),
                'created_at' => $transfer->created_at,
                'url' => route('send.receipt', ['reference' => $transfer->reference, 'from' => 'history']),
                'affects_balance' => true,
            ];
        });

        $withdrawals = $user->withdrawals()->get()->map(function ($withdrawal) {
            return [
                'type' => 'sent',
                'name' => $withdrawal->destination_label,
                'label' => $withdrawal->destination_type === 'card' ? 'Card withdrawal' : 'Bank withdrawal',
                'amount_raw' => -1 * (float) $withdrawal->amount,
                'amount_display' => '–$'.number_format((float) $withdrawal->amount, 2),
                'created_at' => $withdrawal->created_at,
                'url' => route('withdraw.receipt', $withdrawal),
                'affects_balance' => true,
            ];
        });

        $topUps = $user->topUps()->get()->map(function ($topUp) {
            return [
                'type' => 'received',
                'name' => $topUp->source_label,
                'label' => $topUp->source_type === 'card' ? 'Card top-up' : 'Bank top-up',
                'amount_raw' => (float) $topUp->amount,
                'amount_display' => '+$'.number_format((float) $topUp->amount, 2),
                'created_at' => $topUp->created_at,
                'url' => route('top-up.receipt', $topUp),
                'affects_balance' => true,
            ];
        });

        $adminAdjustments = $user->adminBalanceAdjustments()
            // Only adjustments that actually hit the real $user->balance.
            // Back before "Account balance" was removed from the admin
            // panel (see AdminController::adjustBalance()'s comment),
            // some older rows here have balance_pot = 'account' — those
            // moved the old, unused second number, never your real
            // balance, so showing them as a "Deposit" here would be
            // showing money that was never actually added.
            ->where('balance_pot', 'available')
            ->get()
            ->map(function ($adj) {
                $isCredit = $adj->direction === 'credit';

                return [
                    'type' => $isCredit ? 'received' : 'sent',
                    'name' => $isCredit ? ($adj->sender_name ?: 'Bank deposit') : 'Ledger',
                    'label' => $isCredit ? 'Deposit' : 'Debit',
                    'amount_raw' => $isCredit ? (float) $adj->amount : -1 * (float) $adj->amount,
                    'amount_display' => ($isCredit ? '+$' : '–$').number_format((float) $adj->amount, 2),
                    'created_at' => $adj->created_at,
                    // Now has a real receipt — see adjustmentReceipt()
                    // below. This used to be null (no page existed to view
                    // beyond what the row itself already said).
                    'url' => route('history.adjustment.receipt', $adj),
                    'affects_balance' => true,
                ];
            });

        $cardPurchases = Card::where('user_id', $user->id)
            ->with('transactions')
            ->get()
            ->flatMap(fn ($card) => $card->transactions)
            ->map(function ($tx) {
                return [
                    'type' => 'sent',
                    'name' => $tx->merchant,
                    'label' => $tx->category ?: 'Card purchase',
                    'amount_raw' => -1 * (float) $tx->amount,
                    'amount_display' => '–$'.number_format((float) $tx->amount, 2),
                    'created_at' => $tx->occurred_at,
                    'url' => null,
                    // Demo spending data for the Card page — never actually
                    // debited $user->balance, so it's shown but left out of
                    // the Money in/out totals below.
                    'affects_balance' => false,
                ];
            });

        return $sent->concat($received)->concat($external)->concat($international)
            ->concat($withdrawals)->concat($topUps)->concat($adminAdjustments)
            ->concat($cardPurchases)
            ->sortByDesc('created_at')->values()->all();
    }
}
