<?php

namespace App\Http\Controllers\Concerns;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * Shared by every controller that actually moves real money — currently
 * TransferController, WithdrawalController, and TopUpController. Each one
 * calls blockedByTransactionPin() right after its own field/destination
 * validation passes, and bails out with whatever it gets back instead of
 * touching a single balance.
 */
trait RequiresTransactionPin
{
    /**
     * Returns null when it's fine to proceed. Otherwise returns the
     * redirect the controller should return immediately:
     *
     *  - No PIN created yet: flashes 'pinRequired' (a plain boolean flag,
     *    not text) so the page can show its own "create one in Settings"
     *    banner with a real link — see send/withdraw/top-up.blade.php.
     *  - Wrong PIN entered: flashes $errorKey (each controller's own error
     *    key — 'sendError' / 'withdrawError' / 'topUpError') with a plain
     *    message, same as every other rejection those already show.
     */
    protected function blockedByTransactionPin(Request $request, User $user, string $errorKey): ?RedirectResponse
    {
        if (! $user->hasTransactionPin()) {
            return back()->withInput()->with('pinRequired', true);
        }

        $pin = (string) $request->input('transaction_pin', '');

        if ($pin === '' || ! Hash::check($pin, $user->transaction_pin)) {
            return back()->withInput()->with($errorKey, 'Incorrect transaction PIN.');
        }

        return null;
    }
}
