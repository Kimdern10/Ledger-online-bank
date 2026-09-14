<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileSettingController extends Controller
{
    /**
     * Self-service version of the same fields AdminController::
     * updateProfile() edits on the admin side — first/last/middle name,
     * email, and phone are the only ones a user can change themselves.
     * Employment & finances (occupation, income, net worth, etc.) stays
     * admin-only, same as it already is today — those came from the
     * onboarding wizard, not something meant to be casually edited later.
     */
    public function edit(): View
    {
        return view('profile-setting');
    }

    /**
     * first_name/last_name/middle_name/email/phone are already in User's
     * #[Fillable(...)] list, so update() below is a normal mass-assignment
     * — no direct property assignment needed here, unlike the admin-only
     * columns elsewhere on this model.
     *
     * Changing email here does NOT reset email_verified_at, matching the
     * same deliberate simplification AdminController::updateProfile()
     * already documents — this isn't an oversight, it's staying consistent
     * with the one other place email gets changed.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:30'],
        ]);

        $user->update([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'middle_name' => $data['middle_name'] ?? null,
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
        ]);

        return redirect()->route('setting.profile')->with('status', 'Your profile was updated.');
    }
}
