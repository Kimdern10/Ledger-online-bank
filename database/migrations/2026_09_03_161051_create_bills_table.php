<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // One of the known categories (Rent / Mortgage, Credit Card,
            // Medical Insurance, Taxes, Student Loan) or a custom name the
            // user typed in for "Other" — pay-bills.blade.php picks an icon
            // based on this string, falling back to a generic one for
            // anything it doesn't recognize.
            $table->string('category');
            $table->string('biller');
            $table->decimal('amount', 10, 2);
            $table->date('due_date');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};
