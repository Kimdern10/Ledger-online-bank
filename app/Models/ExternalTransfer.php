<?php

namespace App\Models;

use App\Models\Concerns\HasReferenceRouteKey;
use App\Support\TransactionStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExternalTransfer extends Model
{
    use HasReferenceRouteKey;

    protected $fillable = [
        'reference',
        'sender_id',
        'amount',
        'status',
        'scheduled_for',
        'fee',
        'recipient_name',
        'recipient_name_status',
        'recipient_name_provider',
        'bank_name',
        'account_number',
        'routing_number',
        'category',
        'note',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'fee' => 'decimal:2',
        'scheduled_for' => 'date',
    ];

    /**
     * Never claims more than the provider said — see
     * PayeeVerificationManager::outcomeFor(). Anything short of a
     * confirmation reads "not verified by the bank".
     */
    /**
     * The provider returned the name, or matched the one the sender typed.
     */
    public function hasVerifiedRecipientName(): bool
    {
        return in_array($this->recipient_name_status, ['verified', 'match'], true);
    }

    public function recipientNameStatusLabel(): string
    {
        return __('receipt.recipient_name_'.match ($this->recipient_name_status) {
            'verified', 'match', 'close_match', 'no_match', 'unavailable', 'failed' => $this->recipient_name_status,
            default => 'manual',
        });
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Same idea as Transfer::statusLabel() — see the migration that added
     * this column for why every real row is 'completed' today. Delegates to
     * App\Support\TransactionStatus, shared with the other four transaction
     * models.
     */
    public function statusLabel(): string
    {
        return TransactionStatus::label($this->status);
    }
}
