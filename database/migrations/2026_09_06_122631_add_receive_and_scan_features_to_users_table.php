<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * "Receive" (a customer's account number/QR code for getting money)
     * and "Scan" (scan-to-pay) had no Feature access toggle at all before
     * this — unlike Send, Pay Bills, Link Account, Withdraw, Top Up, and
     * Cards, they were reachable by anyone with no auth check whatsoever.
     * Both default to true here so existing users keep working exactly as
     * before; a brand-new account gets these set to false in code instead
     * (see CreateNewUser::create()), same as every other feature but Send.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('can_receive')->default(true)->after('can_manage_cards');
            $table->boolean('can_scan')->default(true)->after('can_receive');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['can_receive', 'can_scan']);
        });
    }
};
