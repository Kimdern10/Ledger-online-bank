<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // active | frozen | suspended | disabled — see User::canSignIn().
            // Stored as a plain string rather than a DB enum so adding a new
            // status later is just a PHP change, not another migration.
            $table->string('account_status')->default('active')->after('is_admin');

            // The reference admin UI shows two different numbers: "Available
            // balance" (what's already tracked as `balance` everywhere else
            // in the app — dashboard, receive, pay-bills) and "Account
            // balance" (the total on the books, which can differ once holds
            // or pending amounts exist). Ledger doesn't have a holds/pending
            // system, so for now this is just a second, independently
            // admin-adjustable number — not automatically derived from
            // anything.
            $table->decimal('account_balance', 15, 2)->default(0)->after('balance');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['account_status', 'account_balance']);
        });
    }
};
