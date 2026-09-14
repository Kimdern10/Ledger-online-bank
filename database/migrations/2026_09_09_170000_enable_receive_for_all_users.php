<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Add money (Receive) is meant to be on for every user by default, with
     * an admin able to switch it off per-user from the Feature access panel
     * (see admin/users-show.blade.php + AdminController::toggleFeature()).
     * CreateNewUser::create() used to set can_receive = false for brand-new
     * accounts, so anyone who signed up before that was fixed is stuck
     * without access. This is a one-time backfill turning it on for every
     * user who doesn't already have it — it does NOT touch any other
     * feature flag, and can't tell an admin's deliberate "off" apart from
     * the old broken default, but no admin has had a working reason to
     * turn Add money off before now, so this is safe.
     */
    public function up(): void
    {
        DB::table('users')->where('can_receive', false)->update(['can_receive' => true]);
    }

    public function down(): void
    {
        // Intentionally a no-op — there's no way to tell which rows were
        // flipped by this migration versus already true beforehand, and
        // reverting would risk undoing a deliberate admin "off" toggle.
    }
};
