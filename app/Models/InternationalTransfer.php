<?php

namespace App\Models;

use App\Models\Concerns\HasReferenceRouteKey;
use App\Support\TransactionStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * "International bank" transfer — see that table's own migration for why
 * this is a separate table from external_transfers rather than reusing it.
 */
/**
 * @property string $recipient_name_status
 * @property string|null $recipient_name_provider
 */
class InternationalTransfer extends Model
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
        'country',
        'swift_code',
        'account_number',
        'currency',
        'exchange_rate',
        'converted_amount',
        'category',
        'note',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'fee' => 'decimal:2',
        'exchange_rate' => 'decimal:6',
        'converted_amount' => 'decimal:2',
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

    /**
     * @return BelongsTo<User, $this>
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Delegates to App\Support\TransactionStatus, shared with the other four
     * transaction models — see Transfer::statusLabel() for the fuller note.
     */
    public function statusLabel(): string
    {
        return TransactionStatus::label($this->status);
    }

    /**
     * "£395.00 GBP" for the receipt/history — only shown when there's a
     * locked-in converted_amount (a USD transfer, or a currency
     * App\Support\CurrencyRates doesn't carry a rate for, has nothing to
     * convert to and this returns null).
     */
    public function convertedAmountDisplay(): ?string
    {
        if ($this->converted_amount === null) {
            return null;
        }

        return number_format((float) $this->converted_amount, 2).' '.$this->currency;
    }
}
