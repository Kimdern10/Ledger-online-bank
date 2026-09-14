<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Same idea as verification_code / verification_code_expires_at
            // (email verification) — a short-lived 6-digit code stored
            // directly on the user row instead of Laravel's default
            // password_reset_tokens table + emailed link. Nullable, cleared
            // the moment it's used successfully or replaced by a fresh one.
            $table->string('password_reset_code', 6)->nullable()->after('verification_code_expires_at');
            $table->timestamp('password_reset_code_expires_at')->nullable()->after('password_reset_code');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['password_reset_code', 'password_reset_code_expires_at']);
        });
    }
};
