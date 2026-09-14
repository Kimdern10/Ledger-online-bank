<?php

namespace App\Http\Controllers;

use App\Models\AdminBalanceAdjustment;
use App\Models\Card;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HistoryController extends Controller
{
    /**
     * Real transaction history — every kind of real money movement this
     * user has, all normalized to the same shape and merged into one
     * timeline:
     *
     *  - Transfer (sent + received), ExternalTransfer (sent), and
     *    InternationalTransfer (sent) — Send Money's three kinds of
     *    transfer.
     *  - Withdrawal — Withdraw, always money out.
     *  - TopUp — Top Up, always money in.
     *  - AdminBalanceAdjustment — an admin crediting or debiting this
     *    user's balance directly (see AdminController::adjustBalance()).
     *    This is exactly the "money in" that was missing before: an admin
     *    credit moves the same real $user->balance everything else reads
     *    from, but until now it never showed up anywhere for the customer
     *    to see.
     *
     * Each of the above actually moves $user->balance, so all of them
     * count toward the Money in / Money out totals at the top of the page.
     *
     * CardTransaction rows are included too, so card purchases show up in
     * the feed the way "card withdrwal" asked for — but they're seed/demo
     * spending data for the Card page (see Card::transactions()) and don't
     * touch $user->balance at all in this app, so they're deliberately
     * left OUT of the Money in / Money out totals. Counting them there
     * would make "money out" bigger than what actually left the balance,
     * which would just trade one confusing number for another. They also
     * have no receipt page of their own (there's nothing to link to, the
     * way a Transfer or Withdrawal has), so they render as plain,
     * non-clickable rows in history.blade.php.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        // Only 'completed' rows here — a 'scheduled' transfer hasn't
        // touched the balance yet (see the block below), and 'cancelled'/
        // 'failed' ones never did, so none of those belong in the same
        // bucket as a transfer that actually happened.
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
                'url' => route('send.receipt', ['transfer' => $transfer, 'from' => 'history']),
                'affects_balance' => true,
                'cancel_route' => null,
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
                'url' => route('send.receipt', ['transfer' => $transfer, 'from' => 'history']),
                'affects_balance' => true,
                'cancel_route' => null,
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
                'url' => route('send.receipt.external', ['externalTransfer' => $transfer, 'from' => 'history']),
                'affects_balance' => true,
                'cancel_route' => null,
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
                'url' => route('send.receipt.international', ['internationalTransfer' => $transfer, 'from' => 'history']),
                'affects_balance' => true,
                'cancel_route' => null,
            ];
        });

        // Scheduled Ledger-to-Ledger transfers that haven't gone out yet
        // (or didn't) — see TransferController::scheduleLedgerTransfer()
        // and ProcessScheduledTransfers. None of these have moved the
        // balance, so affects_balance stays false the same way a card
        // purchase's does below; a still-'scheduled' one gets a Cancel
        // link, the other two states are just shown for the record.
        $scheduledLedger = $user->sentTransfers()
            ->whereIn('status', ['scheduled', 'cancelled', 'failed'])
            ->with('recipient')
            ->get()
            ->map(function ($transfer) {
                return [
                    'type' => 'sent',
                    'name' => $transfer->recipient->name,
                    'label' => match ($transfer->status) {
                        'scheduled' => 'Scheduled for '.$transfer->scheduled_for->format('M j, Y'),
                        'cancelled' => 'Cancelled',
                        default => 'Failed — insufficient funds on the day',
                    },
                    'amount_raw' => -1 * (float) $transfer->amount,
                    'amount_display' => '–$'.number_format((float) $transfer->amount, 2),
                    'created_at' => $transfer->created_at,
                    // Not a plain 'url' — a scheduled row still needs to
                    // stay a non-clickable row (see history.blade.php) so
                    // the Cancel button below can sit inside it; 'view_url'
                    // is what that row's own onclick uses to open the same
                    // receipt page instead, now that receipt() understands
                    // scheduled/cancelled/failed transfers too.
                    'url' => null,
                    'view_url' => route('send.receipt', ['transfer' => $transfer, 'from' => 'history']),
                    'affects_balance' => false,
                    'cancel_route' => $transfer->status === 'scheduled'
                        ? route('send.scheduled.cancel', $transfer)
                        : null,
                ];
            });

        $scheduledExternal = $user->externalTransfers()
            ->whereIn('status', ['scheduled', 'cancelled', 'failed'])
            ->get()
            ->map(function ($transfer) {
                return [
                    'type' => 'sent',
                    'name' => $transfer->recipient_name,
                    'label' => match ($transfer->status) {
                        'scheduled' => 'Scheduled for '.$transfer->scheduled_for->format('M j, Y'),
                        'cancelled' => 'Cancelled',
                        default => 'Failed — insufficient funds on the day',
                    },
                    'amount_raw' => -1 * (float) $transfer->amount,
                    'amount_display' => '–$'.number_format((float) $transfer->amount, 2),
                    'created_at' => $transfer->created_at,
                    // See $scheduledLedger above for why this is 'view_url',
                    // not 'url'.
                    'url' => null,
                    'view_url' => route('send.receipt.external', ['externalTransfer' => $transfer, 'from' => 'history']),
                    'affects_balance' => false,
                    'cancel_route' => $transfer->status === 'scheduled'
                        ? route('send.scheduled.cancel.external', $transfer)
                        : null,
                ];
            });

        $scheduledInternational = $user->internationalTransfers()
            ->whereIn('status', ['scheduled', 'cancelled', 'failed'])
            ->get()
            ->map(function ($transfer) {
                return [
                    'type' => 'sent',
                    'name' => $transfer->recipient_name,
                    'label' => match ($transfer->status) {
                        'scheduled' => 'Scheduled for '.$transfer->scheduled_for->format('M j, Y'),
                        'cancelled' => 'Cancelled',
                        default => 'Failed — insufficient funds on the day',
                    },
                    'amount_raw' => -1 * (float) $transfer->amount,
                    'amount_display' => '–$'.number_format((float) $transfer->amount, 2),
                    'created_at' => $transfer->created_at,
                    // See $scheduledLedger above for why this is 'view_url',
                    // not 'url'.
                    'url' => null,
                    'view_url' => route('send.receipt.international', ['internationalTransfer' => $transfer, 'from' => 'history']),
                    'affects_balance' => false,
                    'cancel_route' => $transfer->status === 'scheduled'
                        ? route('send.scheduled.cancel.international', $transfer)
                        : null,
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
                'cancel_route' => null,
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
                'cancel_route' => null,
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
                    'label' => $isCredit ? 'Deposit' : 'Adjustment',
                    'amount_raw' => $isCredit ? (float) $adj->amount : -1 * (float) $adj->amount,
                    'amount_display' => ($isCredit ? '+$' : '–$').number_format((float) $adj->amount, 2),
                    'created_at' => $adj->created_at,
                    // Now has a real receipt — see adjustmentReceipt()
                    // below. This used to be null (no page existed to view
                    // beyond what the row itself already said).
                    'url' => route('history.adjustment.receipt', $adj),
                    'affects_balance' => true,
                    'cancel_route' => null,
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
                    'cancel_route' => null,
                ];
            });

        $all = $sent->concat($received)->concat($external)->concat($international)
            ->concat($withdrawals)->concat($topUps)->concat($adminAdjustments)
            ->concat($cardPurchases)->concat($scheduledLedger)->concat($scheduledExternal)
            ->concat($scheduledInternational)
            ->sortByDesc('created_at')->values();

        // Money in/out are always computed from the FULL merged feed above,
        // never just the current page below — otherwise paging back through
        // older history would make these totals silently shrink.
        $balanceMoving = $all->where('affects_balance', true);
        $moneyIn = (float) $balanceMoving->where('amount_raw', '>', 0)->sum('amount_raw');
        $moneyOut = abs((float) $balanceMoving->where('amount_raw', '<', 0)->sum('amount_raw'));

        // $all is already sorted newest-first and isn't a real DB query (it's
        // a merge of several different models' rows), so — same as
        // AdminSupportController::index() — pagination has to happen on the
        // in-memory collection instead of a ->paginate() call.
        $perPage = 20;
        $page = (int) $request->query('page', 1);

        $transactions = new \Illuminate\Pagination\LengthAwarePaginator(
            $all->forPage($page, $perPage)->values(),
            $all->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()],
        );

        // Grouped by real calendar buckets — Today / Yesterday / This week
        // / then by month for anything older — built directly from
        // $item['created_at'], not hardcoded like the old mockup was. Only
        // the current page's items are grouped; the date buckets are just a
        // display grouping within that page, not a separate pagination axis.
        $groups = [];
        foreach ($transactions->items() as $item) {
            $date = $item['created_at'];

            $groupLabel = match (true) {
                $date->isToday() => 'Today',
                $date->isYesterday() => 'Yesterday',
                $date->greaterThanOrEqualTo(now()->subDays(7)) => 'This week',
                default => $date->format('F Y'),
            };

            $item['meta'] = ($date->isToday() ? $date->format('g:i A') : $date->format('M j')).' · '.$item['label'];

            $groups[$groupLabel][] = $item;
        }

        return view('history', [
            'groups' => $groups,
            'moneyIn' => $moneyIn,
            'moneyOut' => $moneyOut,
            'transactions' => $transactions,
        ]);
    }

    /**
     * A receipt for an admin balance adjustment ("Deposit"/"Adjustment" in
     * History) — reuses the exact same send-receipt.blade.php template
     * TransferController::receipt() does, just built from an
     * AdminBalanceAdjustment instead of a Transfer. This is the one History
     * row type that was never something the user submitted themselves, so
     * there's no "sender's own copy" perspective to branch on the way
     * receipt() does — just one view, from the customer's side.
     */
    public function adjustmentReceipt(Request $request, AdminBalanceAdjustment $adjustment): View
    {
        abort_unless($adjustment->user_id === $request->user()->id, 404);

        $isCredit = $adjustment->direction === 'credit';

        $rows = [];

        if ($isCredit && $adjustment->sender_name) {
            $rows[] = ['label' => __('receipt.row_from'), 'value' => $adjustment->sender_name];
        }
        if ($isCredit && $adjustment->sender_account_name) {
            $rows[] = ['label' => __('receipt.row_account_name'), 'value' => $adjustment->sender_account_name];
        }
        if ($isCredit && $adjustment->sender_account_number) {
            $rows[] = ['label' => __('receipt.row_account_number'), 'value' => '•••• '.substr($adjustment->sender_account_number, -4)];
        }
        if ($isCredit && $adjustment->sender_bank_name) {
            $rows[] = ['label' => __('receipt.row_bank'), 'value' => $adjustment->sender_bank_name];
        }

        $rows[] = ['label' => __('receipt.row_status'), 'value' => __('receipt.status_completed')];
        $rows[] = ['label' => __('receipt.row_reference'), 'value' => 'ADJ'.$adjustment->id];
        $rows[] = ['label' => __('receipt.row_date'), 'value' => $adjustment->created_at->format('M j, Y \a\t g:i A')];

        return view('send-receipt', [
            'title' => $isCredit ? __('receipt.title_deposit_received') : __('receipt.title_balance_adjustment'),
            'amountSign' => $isCredit ? '+' : '-',
            'subVerb' => $isCredit ? 'from' : 'by',
            'recipientName' => $isCredit ? ($adjustment->sender_name ?: 'Ledger') : 'Ledger',
            'amount' => (float) $adjustment->amount,
            'reference' => 'ADJ'.$adjustment->id,
            'rows' => $rows,
            'banner' => $isCredit ? __('receipt.banner_adjustment_credit') : __('receipt.banner_adjustment_debit'),
            'icon' => 'check',
            'hideSendAnother' => true,
        ]);
    }
}
