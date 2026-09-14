<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('withdrawals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Nullable + nullOnDelete on purpose: if the linked account is
            // later removed, this row (and its receipt) should keep existing
            // — it's a record of money that already moved — so it only loses
            // the link back to the (now gone) linked account, not itself.
            $table->foreignId('linked_account_id')->nullable()->constrained()->nullOnDelete();

            $table->string('reference')->unique();

            // 'bank' or 'card' — which Withdraw tab this came from.
            $table->string('destination_type', 4);

            // Snapshot of the linked account at the moment of withdrawal
            // (e.g. "Citibank" / "Ledger Visa"), so the receipt and history
            // still read correctly even after the linked account above is
            // unlinked.
            $table->string('destination_label');
            $table->string('destination_last4', 4);

            $table->decimal('amount', 10, 2);
            $table->decimal('fee', 10, 2)->default(0);
            $table->string('status', 12)->default('completed');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('withdrawals');
    }
};
