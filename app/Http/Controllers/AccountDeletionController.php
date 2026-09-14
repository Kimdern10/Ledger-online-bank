<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AccountDeletionController extends Controller
{
    public function edit(): View
    {
        return view('delete-account');
    }

    /**
     * Two checks before anything happens, same order a real bank would use:
     * the wallet has to be at $0 first (so deleting can't be used to make
     * money just disappear — it has to be moved out via Withdraw/Send
     * first, same as everywhere else in this app), then the password has
     * to be confirmed, same as changing it.
     *
     * "Delete" here means the same thing Disable already means everywhere
     * else in this app (see AdminController::updateStatus() and
     * User::canSignIn()) — account_status becomes 'disabled', which blocks
     * sign-in immediately and forever, without touching a single row of
     * their transfers, cards, bills, or history. account_deleted_at is the
     * only new thing: it's what lets an admin later tell "this user chose
     * to leave" apart from "an admin disabled this," even though both look
     * identical to canSignIn(). Nothing is actually erased — if this needs
     * to be a real, permanent, un-reversible deletion instead, that's a
     * bigger decision than this endpoint makes on its own.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();

        $request->validate([
            'password' => ['required', 'string'],
        ]);

        if ((float) $user->balance > 0) {
            return back()->with(
                'deleteError',
                'Your balance must be $0 before you can delete your account. Withdraw or send the remaining $'.number_format((float) $user->balance, 2).' first.'
            );
        }

        if (! Hash::check($request->input('password'), $user->password)) {
            return back()->with('deleteError', 'Your password is incorrect.');
        }

        $user->account_status = 'disabled';
        $user->account_deleted_at = now();
        $user->save();

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Your account has been deleted. Contact support if this was a mistake.');
    }
}
