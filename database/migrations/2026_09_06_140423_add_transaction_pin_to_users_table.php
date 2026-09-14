<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Hashed exactly like the login password (see
            // TransactionPinController) — never stored or shown in plain
            // text, unlike the card PIN elsewhere in this app, since this
            // one is what actually stands between a stolen session and real
            // money moving. Null means "no PIN set yet", which is what makes
            // Send/Withdraw/Top Up ask the user to create one before their
            // first transaction goes through.
            $table->string('transaction_pin')->nullable()->after('account_balance');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('transaction_pin');
        });
    }
};
