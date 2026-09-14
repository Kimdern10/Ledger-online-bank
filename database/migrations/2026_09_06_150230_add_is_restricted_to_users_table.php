<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Deliberately separate from account_status (active / frozen /
            // suspended / disabled) — those all block sign-in entirely (see
            // User::canSignIn()). A restricted account can still sign in
            // and see its dashboard; it just can't move money. See
            // EnsureAccountIsNotRestricted, which is what actually enforces
            // this on Send/Withdraw/Top Up/Pay Bills/Scan.
            $table->boolean('is_restricted')->default(false)->after('account_status');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_restricted');
        });
    }
};
