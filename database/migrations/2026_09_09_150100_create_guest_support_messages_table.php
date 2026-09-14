<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * One row per message in a guest_support_conversations thread. There's
     * no user_id/sender_id-pair the way support_messages has, because a
     * guest isn't a User at all — instead:
     *
     *   sender_id null   => the guest wrote it (see
     *                        GuestSupportMessage::isFromAdmin(), which is
     *                        just "sender_id is not null").
     *   sender_id set    => whichever admin (a real User) wrote it,
     *                        including the automatic "we'll be right with
     *                        you" greeting — see is_system below for how
     *                        that one is told apart from an actual human
     *                        reply.
     *
     * is_system marks the one auto-generated greeting created the moment a
     * guest starts a chat (GuestSupportController::start()), attributed to
     * whichever admin account exists — same idea as SupportMessage's
     * is_bot, just without the keyword-matched canned replies: a guest
     * conversation always starts fresh with a real admin reading it, so
     * there's nothing to keep auto-replying past that first line.
     */
    public function up(): void
    {
        Schema::create('guest_support_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guest_support_conversation_id')
                ->constrained('guest_support_conversations')
                ->cascadeOnDelete();
            $table->foreignId('sender_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_system')->default(false);
            $table->text('body');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guest_support_messages');
    }
};
