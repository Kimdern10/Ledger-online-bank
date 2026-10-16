<?php

namespace App\Http\Controllers;

use App\Mail\StatementMail;
use App\Models\AdminBalanceAdjustment;
use App\Support\TransactionFeed;
use App\Support\TransferCode;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class HistoryController extends Controller
{
    /**
     * How many of the newest transactions History shows. Older ones are
     * only available by email, through Get a statement.
     */
    private const LATEST = 10;

    /**
     * Real transaction history: every kind of money movement this user has
     * (transfers, withdrawals, top-ups, admin balance changes and demo card
     * purchases — see App\Support\TransactionFeed), newest first — only the
     * newest LATEST of them. Card purchases have no receipt page, so they show as plain,
     * non-clickable rows in history.blade.php. There are no money in / out
     * totals here; the monthly statement email has them.
     */
    public function index(Request $request): View
    {
        $all = collect(TransactionFeed::for($request->user()));

        // Only the newest few — older ones come by email (Get a statement).
        $latest = $all->take(self::LATEST)->values();

        // Grouped by real calendar buckets — Today / Yesterday / This week
        // / then by month for anything older — built directly from
        // $item['created_at'], not hardcoded like the old mockup was.
        $groups = [];
        foreach ($latest as $item) {
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
            'hasOlder' => $all->count() > self::LATEST,
            'latestCount' => self::LATEST,
        ]);
    }

    /**
     * A receipt for an admin balance change ("Deposit"/"Debit" in History)
     * — reuses the exact same send-receipt.blade.php template
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

        // Same "DEB"/"CRD" + row id format AdminController::adjustBalance()
        // puts in the transaction email, so the receipt and the email show
        // the same reference. Display-only, never stored.
        $reference = ($isCredit ? 'CRD' : 'DEB').$adjustment->id;

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
        $rows[] = ['label' => __('receipt.row_reference'), 'value' => $reference];
        $rows[] = ['label' => __('receipt.row_date'), 'value' => $adjustment->created_at->format('M j, Y \a\t g:i A')];

        return view('send-receipt', [
            'title' => $isCredit ? __('receipt.title_deposit_received') : 'Debit',
            'amountSign' => $isCredit ? '+' : '-',
            'subVerb' => $isCredit ? 'from' : 'by',
            'recipientName' => $isCredit ? ($adjustment->sender_name ?: 'Ledger') : 'Ledger',
            'amount' => (float) $adjustment->amount,
            'reference' => $reference,
            'rows' => $rows,
            'banner' => $isCredit ? __('receipt.banner_adjustment_credit') : __('receipt.banner_adjustment_debit'),
            'icon' => 'check',
            'hideSendAnother' => true,
        ]);
    }

    /**
     * "Get a statement" on History: emails the statement for the From/To
     * dates the customer picked to the email address on the account — never
     * to an address from the form, so a statement can't be sent anywhere
     * else. Statements aren't shown in the app at all. Sent right away (not
     * queued) because the customer is waiting for it; limited to a few
     * requests (see routes/web.php).
     */
    public function emailStatement(Request $request): RedirectResponse
    {
        $user = $request->user();
        $opened = $user->created_at->toDateString();
        $today = now()->toDateString();

        $data = $request->validate([
            'from' => ['required', 'date_format:Y-m-d', 'after_or_equal:'.$opened, 'before_or_equal:'.$today],
            'to' => ['required', 'date_format:Y-m-d', 'after_or_equal:from', 'before_or_equal:'.$today],
        ], [
            'from.after_or_equal' => __('history.statement_too_early'),
            'from.before_or_equal' => __('history.statement_future'),
            'to.before_or_equal' => __('history.statement_future'),
            'to.after_or_equal' => __('history.statement_to_before_from'),
        ]);

        $from = CarbonImmutable::createFromFormat('!Y-m-d', $data['from']);
        $to = CarbonImmutable::createFromFormat('!Y-m-d', $data['to']);

        if ($from->diffInDays($to) > 366) {
            return back()->withErrors(['to' => __('history.statement_too_long')])->withInput();
        }

        $mail = new StatementMail($user, $from->format('Y-m-d'), $to->format('Y-m-d'), requested: true);
        Mail::to($user->email)->sendNow($mail);

        return back()->with('statementSent', __('history.statement_sent', [
            'period' => $mail->statement()->periodLabel(),
            'email' => TransferCode::maskedEmail($user->email),
        ]));
    }
}
