<?php

namespace App\Http\Controllers\Concerns;

use App\Models\ExternalTransfer;
use App\Models\InternationalTransfer;
use App\Models\Transfer;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Shared by everything that moves real money out of the account —
 * TransferController (Send Money's "now" branch) and WithdrawalController
 * each call blockedByTierLimit() right after their own
 * blockedByTransactionPin() check, same "checked last, right before
 * anything actually happens" placement that trait already uses.
 * ProcessScheduledTransfers calls movedTodayBy() directly instead (it has
 * no request/redirect to build a blockedByTierLimit()-style response
 * around) right before executing a due scheduled row, so a scheduled
 * transfer can't be used to skip the check that an immediate one would
 * have hit.
 *
 * "Today's total" (see movedTodayBy() below) is every dollar that's actually
 * left the account today across all four rails: Ledger-to-Ledger, Another
 * bank (domestic/"USA"), International bank, and Withdraw. All four use the
 * same tier limit — there's no separate, looser limit for international
 * transfers, and there never should be one added quietly per-rail; any new
 * way of moving money out needs to be added to movedTodayBy() too, or it
 * silently bypasses every tier's daily cap.
 *
 * The limit itself, and what each account tier means, lives in one place —
 * App\Support\AccountTier — so raising or lowering a tier's daily allowance
 * later never means hunting through controllers for a hardcoded number.
 */
trait EnforcesTierLimits
{
    /**
     * Returns null when there's still room under today's limit. Otherwise
     * returns the redirect the controller should return immediately, with
     * $errorKey flashed the same way blockedByTransactionPin() already
     * does — each controller's own error key ('sendError' / 'withdrawError')
     * so the page shows it exactly where every other rejection already
     * appears.
     *
     * Admins never move money through these flows in the first place, but
     * this still skips the check for one if it's ever called on one, rather
     * than silently applying a Tier 1 limit to an account that was never
     * meant to have one.
     */
    protected function blockedByTierLimit(Request $request, User $user, float $amount, string $errorKey): ?RedirectResponse
    {
        if ($user->isAdmin()) {
            return null;
        }

        $limit = $user->tierDailyLimit();
        $movedToday = $this->movedTodayBy($user);

        if ($movedToday + $amount > $limit) {
            $remaining = max(0, $limit - $movedToday);

            $tier = $user->tier();
            $hint = match ($tier) {
                1 => ' Verify your identity in Settings to raise it.',
                2 => ' Verify your address in Settings to raise it further.',
                default => '',
            };

            return back()->withInput()->with($errorKey, sprintf(
                '%s daily limit is $%s — you have $%s left today.%s',
                $user->tierLabel(),
                number_format($limit, 2),
                number_format($remaining, 2),
                $hint,
            ));
        }

        return null;
    }

    /**
     * Every dollar that's actually left $user's account today (all times in
     * the app server's own timezone, same as `today()` everywhere else) —
     * completed Ledger-to-Ledger, Another bank (domestic), International
     * bank, and Withdraw, summed together. A scheduled transfer only counts
     * once it actually executes and its status flips to 'completed' (see
     * ProcessScheduledTransfers, which calls this same method before
     * executing a due row, for the same reason blockedByTierLimit() above
     * calls it before an immediate one) — while it's still just sitting
     * there as 'scheduled', no money has moved yet and it shouldn't count
     * against anything.
     */
    protected function movedTodayBy(User $user): float
    {
        $movedToday = (float) Transfer::where('sender_id', $user->id)
            ->where('status', 'completed')
            ->whereDate('created_at', today())
            ->sum('amount');

        $movedToday += (float) ExternalTransfer::where('sender_id', $user->id)
            ->where('status', 'completed')
            ->whereDate('created_at', today())
            ->sum('amount');

        $movedToday += (float) InternationalTransfer::where('sender_id', $user->id)
            ->where('status', 'completed')
            ->whereDate('created_at', today())
            ->sum('amount');

        $movedToday += (float) Withdrawal::where('user_id', $user->id)
            ->where('status', 'completed')
            ->whereDate('created_at', today())
            ->sum('amount');

        return $movedToday;
    }
}
