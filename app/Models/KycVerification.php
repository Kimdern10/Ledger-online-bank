<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One row per user — a government ID photo plus a selfie, submitted right
 * after onboarding (see KycController) and reviewed by hand by an admin
 * (see AdminKycController) rather than through any third-party ID-matching
 * API. Approving it sets User::kyc_status to 'approved' (the only thing
 * EnsureKycApproved checks before letting someone use Send Money) and
 * copies the selfie's path onto User::profile_picture_path, so the same
 * photo becomes the account's profile picture — see
 * AdminKycController::approve() for both of those.
 */
class KycVerification extends Model
{
    protected $fillable = [
        'user_id',
        'id_document_type',
        'id_document_path',
        'selfie_path',
        'status',
        'rejection_reason',
        'reviewed_by',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'reviewed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'approved' => __('verify.status_approved'),
            'rejected' => __('verify.status_rejected'),
            default => __('verify.status_pending_review'),
        };
    }

    public function documentTypeLabel(): string
    {
        return match ($this->id_document_type) {
            'drivers_license' => __('verify.doc_drivers_license'),
            'state_id' => __('verify.doc_state_id'),
            'passport' => __('verify.doc_passport'),
            default => __('verify.doc_other_id'),
        };
    }
}
