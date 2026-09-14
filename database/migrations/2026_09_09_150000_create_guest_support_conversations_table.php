<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The guest-facing counterpart to support_conversations (see that
     * migration's own doc comment) — kept as an entirely separate table
     * rather than making support_conversations.user_id nullable, so nothing
     * about the existing, already-working logged-in support chat has to
     * change at all.
     *
     * There's no user_id here on purpose: a visitor on the public marketing
     * page (welcome.blade.php) hasn't signed up yet, so there's no User row
     * to attach this to. guest_name/guest_email are whatever they typed
     * into the floating widget's opening form — never verified, purely
     * informational for whichever admin picks this up. guest_token is a
     * random, unguessable id (see GuestSupportController::start()) stored
     * in the visitor's browser as a cookie, which is what lets them close
     * the widget and come back to the same conversation later without ever
     * logging in.
     *
     * status/closed_by/closed_at mean exactly what they mean on
     * support_conversations.
     */
    public function up(): void
    {
        Schema::create('guest_support_conversations', function (Blueprint $table) {
            $table->id();
            $table->string('guest_name');
            $table->string('guest_email');
            $table->string('guest_token')->unique();
            $table->string('status')->default('open');
            $table->string('closed_by')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guest_support_conversations');
    }
};
