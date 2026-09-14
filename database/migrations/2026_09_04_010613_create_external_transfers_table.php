<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A separate table from `transfers` on purpose — a Ledger-to-Ledger
     * transfer has a real recipient_id pointing at another row in `users`;
     * a transfer to "another bank" doesn't, because there's no such thing
     * as looking up or verifying who owns an arbitrary external account
     * (see TransferController's docblocks for why — no API does this
     * without the account owner authenticating themselves). So this table
     * just stores what the sender typed — bank name, account/routing
     * number, the name they entered — the same way a real bank's own
     * external-transfer form does, with no promise any of it was verified.
     */
    public function up(): void
    {
        Schema::create('external_transfers', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('amount', 15, 2);
            $table->decimal('fee', 10, 2)->default(0);
            $table->string('recipient_name');
            $table->string('bank_name');
            $table->string('account_number');
            $table->string('routing_number');
            $table->string('category')->nullable();
            $table->string('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('external_transfers');
    }
};
