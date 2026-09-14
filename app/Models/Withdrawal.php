<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Withdrawal extends Model
{
    protected $fillable = [
        'user_id',
        'linked_account_id',
        'reference',
        'destination_type',
        'destination_label',
        'destination_last4',
        'amount',
        'fee',
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'fee' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function linkedAccount(): BelongsTo
    {
        return $this->belongsTo(LinkedAccount::class);
    }

    /**
     * Delegates to App\Support\TransactionStatus, shared with the other four
     * transaction models — see Transfer::statusLabel() for the fuller note.
     */
    public function statusLabel(): string
    {
        return \App\Support\TransactionStatus::label($this->status);
    }
}
