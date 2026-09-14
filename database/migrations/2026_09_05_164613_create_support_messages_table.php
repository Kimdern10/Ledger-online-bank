<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * One row per message in a customer's support conversation with the
     * bank. `user_id` is always the CUSTOMER the conversation belongs to
     * — never an admin — so a whole thread is just
     * `where('user_id', $customer->id)` regardless of who actually sent
     * any individual message in it. `sender_id` is whoever actually typed
     * THIS one message: either that same customer, or whichever admin
     * replied. Comparing sender_id to user_id (see
     * SupportMessage::isFromAdmin()) is enough to tell which side of the
     * conversation a message is on, since a customer's thread only ever
     * has two possible senders: the customer themselves, or an admin.
     */
    public function up(): void
    {
        Schema::create('support_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->text('body');
            // Read by whichever side did NOT send this message — a
            // customer's message counts as read once an admin opens the
            // thread; an admin's reply counts as read once the customer
            // opens (or polls) it back.
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('support_messages');
    }
};
