<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationSettingController extends Controller
{
    public function edit(): View
    {
        return view('notification-setting');
    }

    /**
     * Saves the three preferences for real — but nothing in this app
     * actually sends a transaction/security/promotion email or push
     * notification today (Fortify's password-reset and verification-code
     * emails are the only real ones, and neither reads these columns).
     * These are just ready for whenever that gets built, rather than
     * pretending the toggles already do something.
     *
     * Checkboxes only appear in $request at all when checked, so a missing
     * key means "off" — that's why this doesn't use $request->validate()
     * with a boolean rule, which would fail on the ones left unchecked.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $user->notify_transactions_email = $request->boolean('notify_transactions_email');
        $user->notify_security_email = $request->boolean('notify_security_email');
        $user->notify_promotions_email = $request->boolean('notify_promotions_email');
        $user->save();

        return redirect()->route('setting.notifications')->with('status', 'Notification preferences saved.');
    }
}
