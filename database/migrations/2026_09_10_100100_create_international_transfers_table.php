<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A separate table from `external_transfers` even though the shape is
     * close — an international transfer carries a country, a SWIFT/BIC
     * code, and a currency that a domestic "Another bank" transfer never
     * has, and keeping them apart means neither table carries columns that
     * only make sense for the other. Same "record exactly what the sender
     * typed, no verification against the account holder's name" model as
     * external_transfers — see that table's own migration and
     * TransferController's docblocks for why.
     */
    public function up(): void
    {
        Schema::create('international_transfers', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('amount', 15, 2);
            $table->decimal('fee', 10, 2)->default(0);
            // 'completed' by default, same reasoning as transfers/
            // external_transfers' own status column — a real row is written
            // only after the balance has already moved. 'scheduled',
            // 'cancelled', and 'failed' are the other real values (see
            // scheduleInternationalTransfer()/cancelScheduledInternational()
            // in TransferController).
            $table->string('status')->default('completed');
            $table->date('scheduled_for')->nullable();
            $table->string('recipient_name');
            $table->string('bank_name');
            $table->string('country');
            $table->string('swift_code');
            $table->string('account_number');
            $table->string('currency', 3)->default('USD');
            $table->string('category')->nullable();
            $table->string('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('international_transfers');
    }
};
