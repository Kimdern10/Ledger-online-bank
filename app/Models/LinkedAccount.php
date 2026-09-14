<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A bank account or card a user has linked from the Link Account page (see
 * LinkedAccountController). Feeds three places: the "Already linked" list on
 * that same page, the destination/source chips on Withdraw and Top Up, and
 * the read-only "Linked accounts" card on the admin per-user page.
 */
class LinkedAccount extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'bank_name',
        'account_holder_name',
        'account_number',
        'routing_number',
        'card_number',
        'card_name',
        'card_expiry',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isBank(): bool
    {
        return $this->type === 'bank';
    }

    public function isCard(): bool
    {
        return $this->type === 'card';
    }

    /**
     * The last 4 digits shown everywhere this account appears — never the
     * full account/card number, even though it's stored in full (same
     * trade-off the rest of the app already makes for external transfer
     * details).
     */
    public function last4(): string
    {
        $number = $this->isCard() ? $this->card_number : $this->account_number;

        return substr((string) $number, -4);
    }

    /**
     * A short guess at the card network from the leading digit(s), purely
     * cosmetic (matches the little colored "mark" bubbles used everywhere
     * else in the app for banks/cards). Never validated against a real BIN
     * table — this is a demo app with no real card processor behind it.
     */
    public function brandLabel(): string
    {
        if (! $this->isCard()) {
            return 'Card';
        }

        $number = (string) $this->card_number;

        return match (true) {
            str_starts_with($number, '4') => 'Visa',
            preg_match('/^5[1-5]/', $number) === 1 => 'Mastercard',
            preg_match('/^3[47]/', $number) === 1 => 'Amex',
            str_starts_with($number, '6') => 'Discover',
            default => 'Card',
        };
    }

    /**
     * The main line shown for this row — "Citibank" for a bank account,
     * "Visa" (or whichever brand guess) for a card.
     */
    public function displayLabel(): string
    {
        return $this->isBank() ? ($this->bank_name ?: 'Bank account') : $this->brandLabel();
    }

    /**
     * The smaller sub-label under the main line — "Checking •••• 8842" for a
     * bank account, just "•••• 4242" for a card.
     */
    public function detailLabel(): string
    {
        return $this->isBank()
            ? 'Account •••• '.$this->last4()
            : '•••• '.$this->last4();
    }

    public function typeLabel(): string
    {
        return $this->isBank() ? 'Bank account' : 'Card';
    }
}
