<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tier 3's one step — one row per user, a proof-of-address document
     * (utility bill, bank statement, or tenancy agreement) reviewed by hand
     * by an admin, same manual-review pattern kyc_verifications already
     * uses for Tier 2 (see that table's migration). document_path is a
     * relative path on the private "local" disk, never served directly —
     * AdminAddressController::image() is the only route that ever reads
     * it, and it checks the viewer is either this user or an admin first.
     */
    public function up(): void
    {
        Schema::create('address_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            // 'utility_bill', 'bank_statement', 'tenancy_agreement', or 'other'.
            $table->string('document_type', 20);
            $table->string('document_path');
            // 'pending', 'approved', or 'rejected'.
            $table->string('status', 10)->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('address_verifications');
    }
};
