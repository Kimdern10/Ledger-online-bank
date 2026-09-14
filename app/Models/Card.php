<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * One debit/virtual card belonging to a customer. The number, CVV, and PIN
 * here are demo data only (see the cards migration's comment on the pin
 * column) — this mirrors how the card page already reveals the full
 * number and CVV to the browser, just now backed by a real database
 * instead of a hardcoded JS object.
 */
class Card extends Model
{
    protected $fillable = [
        'user_id',
        'card_number',
        'cardholder_name',
        'type',
        'status',
        'expiry_month',
        'expiry_year',
        'cvv',
        'pin',
        'spending_limit',
        'contactless_enabled',
        'online_payments_enabled',
        'reported_lost_stolen',
        'reported_at',
        'closed_at',
    ];

    protected function casts(): array
    {
        return [
            'spending_limit' => 'decimal:2',
            'contactless_enabled' => 'boolean',
            'online_payments_enabled' => 'boolean',
            'reported_lost_stolen' => 'boolean',
            'reported_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    /**
     * Actually issues a card — new number, CVV, PIN, the lot. The only two
     * callers are CardController::replace() (a customer replacing their
     * own card) and AdminCardController::approve() (an admin approving a
     * request); a customer can never reach this directly, since there's no
     * "add a card" button that skips the request/approval flow any more.
     */
    public static function issueFor(User $user, string $type, array $overrides = []): self
    {
        $prefix = $type === 'virtual' ? '5310' : '4471';

        return static::create(array_merge([
            'user_id' => $user->id,
            'card_number' => static::generateNumber($prefix),
            'cardholder_name' => strtoupper($user->name),
            'type' => $type,
            'status' => 'active',
            'expiry_month' => (int) now()->format('m'),
            'expiry_year' => (int) now()->addYears($type === 'virtual' ? 2 : 3)->format('Y'),
            'cvv' => str_pad((string) random_int(0, 999), 3, '0', STR_PAD_LEFT),
            'pin' => str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT),
            'contactless_enabled' => true,
            'online_payments_enabled' => true,
        ], $overrides));
    }

    private static function generateNumber(string $prefix): string
    {
        do {
            $number = $prefix.str_pad((string) random_int(0, 999999999999), 12, '0', STR_PAD_LEFT);
        } while (static::where('card_number', $number)->exists());

        return $number;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(CardTransaction::class)->orderByDesc('occurred_at');
    }

    public function isVirtual(): bool
    {
        return $this->type === 'virtual';
    }

    public function isFrozen(): bool
    {
        return $this->status === 'frozen';
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    public function last4(): string
    {
        return substr($this->card_number, -4);
    }

    public function maskedNumber(): string
    {
        return '•••• •••• •••• '.$this->last4();
    }

    public function formattedNumber(): string
    {
        return trim(chunk_split($this->card_number, 4, ' '));
    }

    public function expiryLabel(): string
    {
        return sprintf('%02d/%s', $this->expiry_month, substr((string) $this->expiry_year, -2));
    }

    /**
     * Badge text for the card carousel. A closed card never reaches this —
     * CardController::index() only ever loads non-closed cards.
     */
    public function badgeLabel(): string
    {
        if ($this->isFrozen()) {
            return 'Frozen';
        }

        return $this->isVirtual() ? 'Virtual' : 'Active';
    }

    public function limitLabel(): string
    {
        return $this->spending_limit === null
            ? 'No limit set'
            : '$'.number_format((float) $this->spending_limit, 2).' limit';
    }
}
