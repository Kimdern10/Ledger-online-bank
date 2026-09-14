<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A customer's request for a new card — nothing is issued until an admin
 * approves it (see AdminCardController::approve(), which is what actually
 * calls Card::issueFor()). One row per request, so a declined request
 * doesn't erase the fact that it happened, and a customer can always see
 * why.
 */
class CardRequest extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'status',
        'decline_reason',
        'reviewed_by',
        'reviewed_at',
        'card_id',
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

    public function card(): BelongsTo
    {
        return $this->belongsTo(Card::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isDeclined(): bool
    {
        return $this->status === 'declined';
    }

    public function isVirtual(): bool
    {
        return $this->type === 'virtual';
    }

    public function typeLabel(): string
    {
        return $this->isVirtual() ? 'Virtual card' : 'Physical card';
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'approved' => 'Approved',
            'declined' => 'Declined',
            default => 'Pending',
        };
    }
}
