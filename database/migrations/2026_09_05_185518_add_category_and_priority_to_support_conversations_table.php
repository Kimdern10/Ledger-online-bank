<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * category is set automatically the first time a customer's message
     * matches one of the bot's canned-response keys ("card issue",
     * "payment problem", "account access", "something else") — see
     * SupportController::maybeSendBotReply(). It's read-only from the
     * admin side, the same way a real triage bot would tag a ticket.
     *
     * priority is 'normal' or 'urgent', and IS admin-settable — a toggle
     * button on the thread page (AdminSupportController::togglePriority()),
     * used to flag something that needs attention before the queue order
     * would otherwise get to it.
     */
    public function up(): void
    {
        Schema::table('support_conversations', function (Blueprint $table) {
            $table->string('category')->nullable()->after('status');
            $table->string('priority')->default('normal')->after('category');
        });
    }

    public function down(): void
    {
        Schema::table('support_conversations', function (Blueprint $table) {
            $table->dropColumn(['category', 'priority']);
        });
    }
};
