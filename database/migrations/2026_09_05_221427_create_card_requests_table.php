<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('card_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            // 'physical' or 'virtual'
            $table->string('type', 8);
            // 'pending', 'approved', or 'declined'
            $table->string('status', 10)->default('pending');
            $table->text('decline_reason')->nullable();
            // The admin who approved/declined this — null while still pending.
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            // The real card that got issued once this was approved — null
            // until then, and always null for a declined request.
            $table->foreignId('card_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('card_requests');
    }
};
