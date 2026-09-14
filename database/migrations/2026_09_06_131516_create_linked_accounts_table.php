<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('linked_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // 'bank' or 'card' — which half of the Link Account page this
            // came from. Only the columns for that half are ever filled in;
            // the other half stays null on that row.
            $table->string('type', 4);

            // ---- bank half ----
            $table->string('bank_name')->nullable();
            $table->string('account_holder_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('routing_number')->nullable();

            // ---- card half ----
            // Deliberately no CVV column here at all — unlike the Cards Ledger
            // itself issues (cards table), a linked card is someone else's
            // bank's card. The CVV is only ever needed once, to prove the
            // person entering it actually has the card in hand right now, so
            // LinkedAccountController validates its format and then simply
            // never writes it anywhere.
            $table->string('card_number')->nullable();
            $table->string('card_name')->nullable();
            $table->string('card_expiry', 5)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('linked_accounts');
    }
};
