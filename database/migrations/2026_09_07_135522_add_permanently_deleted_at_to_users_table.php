<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Set once PurgeDeletedAccounts (or an admin's "Delete now" button
            // on the Trash page) actually scrubs this account's personal data
            // — see User::anonymizeForPermanentDeletion(). Separate from
            // account_deleted_at (set the moment the user chooses Delete
            // Account) so the Trash page can tell "deleted, still
            // recoverable" apart from "already permanently gone."
            $table->timestamp('permanently_deleted_at')->nullable()->after('account_deleted_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('permanently_deleted_at');
        });
    }
};
