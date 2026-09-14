<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * One row per completed Ledger-to-Ledger transfer — this is what
     * TransferController::store() writes to, and what the receipt page
     * (send-receipt.blade.php) reads back. Deliberately doesn't touch
     * anything related to "another bank" or "linked card" sends — those
     * aren't real yet (see TransferController), so there's nothing for
     * this table to record for them.
     */
    public function up(): void
    {
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();

            // A human-readable confirmation code for the receipt — not
            // just the numeric id, which would look and be a bit too
            // guessable/exposed for something shown on-screen.
            $table->string('reference')->unique();

            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('recipient_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('amount', 15, 2);
            $table->string('category')->nullable();
            $table->string('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transfers');
    }
};
