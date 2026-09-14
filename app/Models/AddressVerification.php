<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Tier 3's one row per user — a proof-of-address document reviewed by hand
 * by an admin, same manual-review pattern KycVerification (Tier 2) already
 * uses — see that model's own docblock. Approving it sets
 * User::address_status to 'approved', which is the second (and last) half
 * of User::tier() — see that method for how Tier 2 + Tier 3 combine into
 * one tier() number, and App\Support\AccountTier for what each tier
 * actually unlocks.
 */
class AddressVerification extends Model
{
    protected $fillable = [
        'user_id',
        'document_type',
        'document_path',
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
        return match ($this->document_type) {
            'utility_bill' => __('verify.doc_utility_bill'),
            'bank_statement' => __('verify.doc_bank_statement'),
            'tenancy_agreement' => __('verify.doc_tenancy_agreement'),
            default => __('verify.doc_other_address'),
        };
    }
}
