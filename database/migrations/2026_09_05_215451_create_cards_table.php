<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('card_number', 16)->unique();
            $table->string('cardholder_name');
            // 'physical' or 'virtual'
            $table->string('type', 8);
            // 'active', 'frozen', or 'closed'
            $table->string('status', 8)->default('active');
            $table->unsignedTinyInteger('expiry_month');
            $table->unsignedSmallInteger('expiry_year');
            $table->string('cvv', 4);
            // Demo project only: stored in plain text so "View PIN" can show
            // it straight back to the customer, the same way this page
            // already reveals the full card number and CVV client-side. A
            // real bank would never store or display a PIN like this — this
            // app isn't handling real card data.
            $table->string('pin', 4)->nullable();
            $table->decimal('spending_limit', 10, 2)->nullable();
            $table->boolean('contactless_enabled')->default(true);
            $table->boolean('online_payments_enabled')->default(true);
            $table->boolean('reported_lost_stolen')->default(false);
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cards');
    }
};
