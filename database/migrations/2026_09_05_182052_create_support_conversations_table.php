<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * One row per support "session" between a customer and the bank. A
     * customer only ever has ONE open conversation at a time, but can have
     * any number of closed ones — that's what makes "start a new
     * conversation" and "view my past conversations" both possible without
     * ever deleting anything.
     *
     * status is 'open' or 'closed'.
     *
     * closed_by records who/what ended it: 'admin' (the End conversation
     * button), 'customer' (the Start new conversation button, which closes
     * whatever was open first), or 'timeout' (nobody closed it — an admin
     * set timeout_at, the customer never replied, and the deadline passed).
     *
     * timeout_at is an optional "auto-close if the customer doesn't reply
     * by this time" deadline an admin can set instead of ending a
     * conversation outright. There's no background job checking it — see
     * SupportConversation::applyTimeoutIfExpired(), which every controller
     * method that loads a conversation calls first, closing it right then
     * if the deadline has already passed.
     */
    public function up(): void
    {
        Schema::create('support_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('status')->default('open');
            $table->string('closed_by')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamp('timeout_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('support_conversations');
    }
};
