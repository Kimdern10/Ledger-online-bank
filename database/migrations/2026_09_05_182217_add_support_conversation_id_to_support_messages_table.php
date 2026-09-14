<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Every message now belongs to a specific conversation "session", not
     * just a customer — that's what lets a customer end one conversation
     * and start a fresh one without losing the old messages. user_id stays
     * on support_messages exactly as it was (several existing queries, like
     * the sidebar unread count in layouts/admin.blade.php, read it
     * directly), this just adds the new column alongside it.
     */
    public function up(): void
    {
        Schema::table('support_messages', function (Blueprint $table) {
            $table->foreignId('support_conversation_id')
                ->nullable()
                ->after('user_id')
                ->constrained('support_conversations')
                ->cascadeOnDelete();
        });

        // Backfill: anyone who already had support messages before this
        // migration ran gets a single conversation containing all of their
        // existing history, so nothing already in the database is left
        // pointing at no conversation at all.
        $userIds = DB::table('support_messages')
            ->whereNull('support_conversation_id')
            ->distinct()
            ->pluck('user_id');

        foreach ($userIds as $userId) {
            $conversationId = DB::table('support_conversations')->insertGetId([
                'user_id' => $userId,
                'status' => 'open',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('support_messages')
                ->where('user_id', $userId)
                ->update(['support_conversation_id' => $conversationId]);
        }
    }

    public function down(): void
    {
        Schema::table('support_messages', function (Blueprint $table) {
            $table->dropForeign(['support_conversation_id']);
            $table->dropColumn('support_conversation_id');
        });
    }
};
