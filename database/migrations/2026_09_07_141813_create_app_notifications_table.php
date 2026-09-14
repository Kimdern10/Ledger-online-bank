<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Named app_notifications, not notifications: Laravel's own
        // Notifiable trait (already used by User) reserves "notifications"
        // for its own polymorphic table (notifiable_type/notifiable_id,
        // uuid id, data json) — a table this app has never actually
        // published or used. Using a different name here means adding this
        // feature can never collide with that one if it's ever needed
        // later, instead of two unrelated schemas fighting over one table.
        Schema::create('app_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('body')->nullable();
            $table->string('url')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('app_notifications');
    }
};
