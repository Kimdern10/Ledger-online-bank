<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Mail\WelcomeMail;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => $this->passwordRules(),
        ])->validate();

        // checking_account_number / savings_account_number aren't set
        // here — see User::assignAccountNumbersForType() — since which
        // one(s) to generate isn't known until the onboarding wizard's
        // account-type step.
        $user = User::create([
            'first_name' => $input['first_name'],
            'last_name' => $input['last_name'],
            'middle_name' => $input['middle_name'] ?? null,
            'email' => $input['email'],
            'phone' => $input['phone'] ?? null,
            'password' => $input['password'],
        ]);

        // A brand-new account starts with Send money and Add money (Receive)
        // turned on — every other feature (Pay Bills, Link Account, Withdraw,
        // Top Up, Manage Cards, Scan) stays off until an admin deliberately
        // enables it from the user's admin page (see
        // AdminController::toggleFeature()). Add money is on by default
        // because every user needs a way to receive funds into their own
        // account; an admin can still switch it off for a specific user from
        // the Feature access panel. These eight columns aren't in User's
        // #[Fillable(...)] list, so they can't be passed into User::create()
        // above — they're set the same way toggleFeature() itself sets them,
        // by assigning the property directly and saving.
        $user->can_send = true;
        $user->can_pay_bills = false;
        $user->can_link_account = false;
        $user->can_withdraw = false;
        $user->can_top_up = false;
        $user->can_manage_cards = false;
        $user->can_receive = true;
        $user->can_scan = false;
        $user->save();

        // Wrapped in try/catch on purpose — a broken mail configuration
        // (wrong SMTP credentials, mailer down, etc.) should never turn a
        // successful registration into a 500 error page. The account is
        // already created and saved above by the time this runs; failing
        // to send a welcome email is unfortunate, not a reason to lose the
        // account that was just made. See TransactionMail's call sites for
        // the same reasoning applied to Send/Withdraw/Top Up.
        try {
            Mail::to($user->email)->send(new WelcomeMail($user));
        } catch (\Throwable $e) {
            Log::error('Failed to send welcome email: '.$e->getMessage());
        }

        return $user;
    }
}
