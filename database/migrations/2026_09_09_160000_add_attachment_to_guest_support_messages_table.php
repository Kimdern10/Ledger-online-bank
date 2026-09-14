<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Lets a guest attach a photo (or PDF) from the floating widget, same
     * idea as support_messages' own attachment columns — see that
     * migration and SupportMessage::attachmentUrl() for why these live on
     * the "public" disk rather than the private one KYC photos use: a
     * support attachment isn't sensitive ID material, so there's no reason
     * it can't be served by a normal /storage/... URL.
     */
    public function up(): void
    {
        Schema::table('guest_support_messages', function (Blueprint $table) {
            $table->string('attachment_path')->nullable()->after('body');
            $table->string('attachment_name')->nullable()->after('attachment_path');
            $table->string('attachment_mime')->nullable()->after('attachment_name');
        });
    }

    public function down(): void
    {
        Schema::table('guest_support_messages', function (Blueprint $table) {
            $table->dropColumn(['attachment_path', 'attachment_name', 'attachment_mime']);
        });
    }
};
