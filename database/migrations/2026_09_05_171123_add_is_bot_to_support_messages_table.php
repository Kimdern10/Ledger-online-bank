<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Marks a support message as auto-generated rather than actually
     * typed by a human admin. sender_id still points at a real admin
     * account either way (so the foreign key stays simple and nothing
     * about the schema needs a nullable/system-account workaround) — this
     * column is what tells SupportController::maybeSendBotReply() whether
     * a REAL admin has ever replied in a given conversation, which is the
     * entire "hand off to a real member" mechanism: once any admin sends
     * one genuine reply (is_bot = false) from /admin/support, the bot
     * checks for that and simply stops replying to that customer forever
     * — nothing else has to explicitly "transfer" the conversation.
     */
    public function up(): void
    {
        Schema::table('support_messages', function (Blueprint $table) {
            $table->boolean('is_bot')->default(false)->after('sender_id');
        });
    }

    public function down(): void
    {
        Schema::table('support_messages', function (Blueprint $table) {
            $table->dropColumn('is_bot');
        });
    }
};
