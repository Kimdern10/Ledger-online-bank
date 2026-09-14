<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * kyc_status defaults to 'not_submitted' so every existing account (and
     * every brand-new one, right up until they finish the KYC step) reads
     * as not-yet-verified rather than silently approved. profile_picture_path
     * stays null until AdminKycController::approve() copies an approved
     * selfie's path onto it — see User::avatar() for how that turns into
     * the little round photo shown around the app instead of initials.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('kyc_status', 15)->default('not_submitted')->after('monthly_budget');
            $table->string('profile_picture_path')->nullable()->after('kyc_status');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['kyc_status', 'profile_picture_path']);
        });
    }
};
