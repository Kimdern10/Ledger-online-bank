<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * One optional attachment per message — a screenshot of a failed
     * payment, a photo of a card, etc. attachment_path is the path on the
     * "public" storage disk (see SupportMessage::attachmentUrl(), which
     * turns it into a real URL); attachment_name is the original filename,
     * kept only so the UI can show/download it under its real name instead
     * of the random generated storage name; attachment_mime decides
     * whether the message renders an inline image or a plain file link.
     */
    public function up(): void
    {
        Schema::table('support_messages', function (Blueprint $table) {
            $table->string('attachment_path')->nullable()->after('body');
            $table->string('attachment_name')->nullable()->after('attachment_path');
            $table->string('attachment_mime')->nullable()->after('attachment_name');
        });
    }

    public function down(): void
    {
        Schema::table('support_messages', function (Blueprint $table) {
            $table->dropColumn(['attachment_path', 'attachment_name', 'attachment_mime']);
        });
    }
};
