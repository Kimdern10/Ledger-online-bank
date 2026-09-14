<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * One boolean per feature the admin "Feature access" toggles control:
     * Send, Pay Bills, Link Account, Withdraw, Top Up, and Cards. All
     * default to true so nobody's access silently changes the moment this
     * migration runs — an admin has to deliberately flip one off.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('can_send')->default(true)->after('account_balance');
            $table->boolean('can_pay_bills')->default(true)->after('can_send');
            $table->boolean('can_link_account')->default(true)->after('can_pay_bills');
            $table->boolean('can_withdraw')->default(true)->after('can_link_account');
            $table->boolean('can_top_up')->default(true)->after('can_withdraw');
            $table->boolean('can_manage_cards')->default(true)->after('can_top_up');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'can_send',
                'can_pay_bills',
                'can_link_account',
                'can_withdraw',
                'can_top_up',
                'can_manage_cards',
            ]);
        });
    }
};
