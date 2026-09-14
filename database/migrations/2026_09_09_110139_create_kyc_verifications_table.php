<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * One row per user — a government ID photo plus a selfie, submitted
     * right after the onboarding wizard (see KycController) and reviewed by
     * hand by an admin (see AdminKycController), the same manual-review
     * pattern CardRequest already uses for card requests. Both photos are
     * stored on the private "local" disk, not "public" — id_document_path
     * and selfie_path are just relative paths on that disk, never served
     * directly; AdminKycController::image() and AvatarController::show()
     * are the only routes that ever read them, and both check the viewer
     * is either this user or an admin first.
     */
    public function up(): void
    {
        Schema::create('kyc_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            // 'drivers_license', 'state_id', 'passport', or 'other'.
            $table->string('id_document_type', 20);
            $table->string('id_document_path');
            $table->string('selfie_path');
            // 'pending', 'approved', or 'rejected'.
            $table->string('status', 10)->default('pending');
            $table->text('rejection_reason')->nullable();
            // The admin who approved/rejected this — null while still pending.
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kyc_verifications');
    }
};
