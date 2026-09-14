<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Two separate, prefixed account numbers per user — filled
            // automatically by User::booted(), same pattern as
            // account_number. Every checking account number starts "74",
            // every savings account number starts "50", so the two are
            // visually distinguishable at a glance — a common pattern real
            // banks use internally (their own private prefix scheme, not a
            // national standard — see account_number's own comment).
            // The original account_number column is left untouched.
            $table->string('checking_account_number', 10)->nullable()->unique()->after('swift_code');
            $table->string('savings_account_number', 10)->nullable()->unique()->after('checking_account_number');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['checking_account_number', 'savings_account_number']);
        });
    }
};
