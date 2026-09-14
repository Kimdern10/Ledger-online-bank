<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * "International bank" transfer — see that table's own migration for why
 * this is a separate table from external_transfers rather than reusing it.
 */
class InternationalTransfer extends Model
{
    protected $fillable = [
        'reference',
        'sender_id',
        'amount',
        'status',
        'scheduled_for',
        'fee',
        'recipient_name',
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
        return \App\Support\TransactionStatus::label($this->status);
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
