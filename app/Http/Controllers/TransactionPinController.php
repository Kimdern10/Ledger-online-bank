<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class TransactionPinController extends Controller
{
    /**
     * One page handles both "create your first PIN" and "change your PIN"
     * — see transaction-pin.blade.php, which reads
     * auth()->user()->hasTransactionPin() itself to decide which form to
     * show (a change form asks for the current PIN first; a first-time
     * form doesn't, since there's nothing to check it against yet).
     */
    public function edit(): View
    {
        return view('transaction-pin');
    }

    /**
     * Stored hashed, exactly like the login password — see the migration
     * that added this column. Never mass-assigned (transaction_pin isn't in
     * User's #[Fillable(...)] list on purpose): set here via direct
     * property assignment, the same pattern every other sensitive column in
     * this app already uses.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        $hasExistingPin = $user->hasTransactionPin();

        $rules = [
            'pin' => ['required', 'digits:4', 'confirmed'],
        ];

        if ($hasExistingPin) {
            $rules['current_pin'] = ['required', 'digits:4'];
        }

        $data = $request->validate($rules);

        if ($hasExistingPin && ! Hash::check($data['current_pin'], $user->transaction_pin)) {
            return back()->with('pinError', 'Your current PIN is incorrect.');
        }

        $user->transaction_pin = Hash::make($data['pin']);
        $user->save();

        // Back to this same page (not Settings) so the confirmation shows up
        // right where the person just was, rather than needing Settings to
        // grow its own flash-message toast just for this.
        return redirect()->route('setting.transaction-pin')->with(
            'status',
            $hasExistingPin
                ? 'Transaction PIN updated.'
                : "Transaction PIN created — you'll be asked for it any time you send, withdraw, or top up."
        );
    }
}
