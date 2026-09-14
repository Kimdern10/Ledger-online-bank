<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminBalanceAdjustment extends Model
{
    protected $fillable = [
        'user_id',
        'admin_id',
        'direction',
        'balance_pot',
        'amount',
        'sender_name',
        'sender_account_name',
        'sender_account_number',
        'sender_bank_name',
        'bank_address',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
