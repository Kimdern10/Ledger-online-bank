<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transfer extends Model
{
    protected $fillable = [
        'reference',
        'sender_id',
        'recipient_id',
        'amount',
        'status',
        'scheduled_for',
        'category',
        'note',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'scheduled_for' => 'date',
    ];

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    /**
     * 'completed' / 'in_progress' / 'initiated' -> 'Completed' / 'In
     * progress' / 'Initiated' for the receipt page. Every real transfer
     * today is 'completed' the instant it's created (see the migration
     * that added this column for why) — this just formats whatever the
     * column actually holds, rather than hardcoding "Completed" in the view.
     * See App\Support\TransactionStatus — shared with the other four
     * transaction models so the translated labels live in one place.
     */
    public function statusLabel(): string
    {
        return \App\Support\TransactionStatus::label($this->status);
    }
}
