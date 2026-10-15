<?php

namespace App\Http\Controllers\Concerns;

use App\Models\User;
use App\Support\TransferCode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Shared by every controller that actually moves real money — currently
 * TransferController, WithdrawalController, and TopUpController. Each one
 * calls blockedByTransactionPin() right after its own field/destination
 * validation passes, and bails out with whatever it gets back instead of
 * touching a single balance.
 *
 * What's checked is the one-time 4-digit code just emailed to the user
 * (App\Support\TransferCode) — a new one for every transfer — not a fixed
 * PIN. It still arrives in the `transaction_pin` field, so the shared
 * confirmation pop-up (openPinModal() in dashboard.js) works unchanged.
 */
trait RequiresTransactionPin
{
    /**
     * Returns null when it's fine to proceed. Otherwise returns the
     * redirect the controller should return immediately, flashing
     * $errorKey (each controller's own error key — 'sendError' /
     * 'withdrawError' / 'topUpError') with a plain message.
     */
    protected function blockedByTransactionPin(Request $request, User $user, string $errorKey): ?RedirectResponse
    {
        $code = trim((string) $request->input('transaction_pin', ''));

        if (! TransferCode::verify($user, $code)) {
            // Shown as a SweetAlert pop-up (layouts/app.blade.php), not in the
            // page's error box. $errorKey stays in the signature for callers.
            return back()->withInput()->with('transferCodeError', __('transfer_code.incorrect'));
        }

        return null;
    }
}
