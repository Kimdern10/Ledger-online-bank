<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('top_ups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Same nullable + nullOnDelete reasoning as withdrawals.linked_account_id.
            $table->foreignId('linked_account_id')->nullable()->constrained()->nullOnDelete();

            $table->string('reference')->unique();

            // 'bank' or 'card' — which Top Up tab this came from.
            $table->string('source_type', 4);

            // Snapshot of the linked account used, same idea as
            // withdrawals.destination_label/destination_last4.
            $table->string('source_label');
            $table->string('source_last4', 4);

            $table->decimal('amount', 10, 2);

            // Card top-ups show a processing fee on the receipt, but it's
            // informational only — it's what the linked card would have been
            // charged, not money Ledger keeps, so it never reduces $amount
            // credited to the user's balance. See TopUpController::store().
            $table->decimal('fee', 10, 2)->default(0);
            $table->string('status', 12)->default('completed');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('top_ups');
    }
};
