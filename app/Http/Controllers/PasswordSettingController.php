<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class PasswordSettingController extends Controller
{
    public function edit(): View
    {
        return view('password-setting');
    }

    /**
     * Requires the current password first, exactly like changing the
     * Transaction PIN requires the current PIN — see
     * TransactionPinController::update() for the same pattern. Assigning
     * to $user->password directly (rather than $user->update([...])) still
     * goes through the 'password' => 'hashed' cast on User, so this never
     * stores it in plain text — the cast hashes it the moment it's set,
     * same as every other place a password gets written in this app.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (! Hash::check($data['current_password'], $user->password)) {
            return back()->with('passwordError', 'Your current password is incorrect.');
        }

        $user->password = $data['password'];
        $user->save();

        return redirect()->route('setting.password')->with('status', 'Your password was changed.');
    }
}
