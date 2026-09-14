<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A record of every Credit/Debit an admin has ever made from the
     * per-user admin page — so the "sender details" fields on that panel
     * actually get saved somewhere instead of just being decorative. Not
     * the same thing as the full transactions/history feature that's still
     * paused; this only logs admin-initiated balance changes.
     */
    public function up(): void
    {
        Schema::create('admin_balance_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('admin_id')->constrained('users')->cascadeOnDelete();
            $table->string('direction'); // credit | debit
            $table->string('balance_pot'); // account | available
            $table->decimal('amount', 15, 2);
            $table->string('sender_name')->nullable();
            $table->string('sender_account_name')->nullable();
            $table->string('sender_account_number')->nullable();
            $table->string('sender_bank_name')->nullable();
            $table->string('bank_address')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_balance_adjustments');
    }
};
