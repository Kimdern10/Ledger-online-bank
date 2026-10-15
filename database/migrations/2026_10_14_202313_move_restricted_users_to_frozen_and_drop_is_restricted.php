<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * "Restrict transactions" and "Freeze wallet" did nearly the same thing
     * (sign in allowed, money blocked), so Restrict is gone and Freeze wallet
     * covers it — see User::isWalletFrozen(). Anyone who was restricted and
     * otherwise active becomes frozen, so nobody loses their block.
     */
    public function up(): void
    {
        DB::table('users')
            ->where('is_restricted', true)
            ->where('account_status', 'active')
            ->update(['account_status' => 'frozen']);

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_restricted');
        });
    }

    /**
     * Brings the column back (all false). Who was restricted before can't be
     * told apart from who was frozen, so nobody is moved back.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_restricted')->default(false)->after('account_status');
        });
    }
};
