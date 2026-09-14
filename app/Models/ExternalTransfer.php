<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExternalTransfer extends Model
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
        return \App\Support\TransactionStatus::label($this->status);
    }
}
