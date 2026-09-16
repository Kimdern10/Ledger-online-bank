<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Str;

/**
 * The actual "permanently delete" step for a self-deleted account — run
 * automatically by App\Console\Commands\PurgeDeletedAccounts 30 days after
 * account_deleted_at, or immediately by an admin via
 * AdminController::purgeAccountNow() (the Trash page's "Delete now"
 * button). Moved here from User::anonymizeForPermanentDeletion() rather
 * than into either caller directly, since — unlike User::restoreFromTrash()
 * (which only ever had the one controller caller and was inlined straight
 * into it) — a console command can't reasonably depend on a controller
 * class, and duplicating this scrub logic in two places would be its own
 * bug waiting to happen the first time only one copy got updated.
 *
 * Deliberately does NOT delete the row itself: every transfer, card, bill,
 * and support message this account ever touched uses ON DELETE CASCADE back
 * to users (see the migrations), so actually deleting the row would
 * silently wipe those records too — including the OTHER side of any
 * transfer, e.g. erasing a transfer from a recipient's history just because
 * the sender's account got purged. Scrubbing every personal and financial
 * field in place keeps all of that history intact while making sure
 * nothing identifying is left on the account itself.
 */
class AccountAnonymizer
{
    public static function scrub(User $user): void
    {
        $user->first_name = 'Deleted';
        $user->last_name = 'User';
        $user->middle_name = null;
        $user->email = 'deleted-user-'.$user->id.'@ledger.invalid';
        $user->phone = null;
        $user->password = Str::random(40);
        $user->transaction_pin = null;
        $user->verification_code = null;
        $user->verification_code_expires_at = null;
        $user->password_reset_code = null;
        $user->password_reset_code_expires_at = null;
        $user->two_factor_secret = null;
        $user->two_factor_recovery_codes = null;
        $user->two_factor_confirmed_at = null;
        $user->remember_token = null;
        $user->account_number = null;
        $user->routing_number = null;
        $user->swift_code = null;
        $user->checking_account_number = null;
        $user->savings_account_number = null;
        $user->permanently_deleted_at = now();
        $user->save();

        if ($user->profile) {
            $user->profile->update([
                'next_of_kin_name' => null,
                'next_of_kin_phone' => null,
                'next_of_kin_email' => null,
                'next_of_kin_address' => null,
                'gender' => null,
                'date_of_birth' => null,
                'country' => null,
                'state' => null,
                'city' => null,
                'zip_code' => null,
                'home_address' => null,
                'employment_status' => null,
                'occupation' => null,
                'industry' => null,
                'annual_income' => null,
                'expected_annual_income' => null,
                'main_source_of_income' => null,
                'net_worth' => null,
            ]);
        }
    }
}
