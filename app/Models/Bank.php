<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * One entry in the admin-managed bank directory (see that migration's own
 * doc comment for why this exists instead of a live third-party API call).
 * type=external is a domestic bank, offered from the "Another bank" tab on
 * Send Money; type=international is offered from the new "International
 * bank" tab. Both live in one table since they're really the same shape of
 * record — a name plus whichever identifying numbers apply — and admins
 * manage them from one "Banks" screen either way.
 */
class Bank extends Model
{
    protected $fillable = [
        'name',
        'type',
        'country',
        'routing_number',
        'swift_code',
        'currency',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function isInternational(): bool
    {
        return $this->type === 'international';
    }

    public function isExternal(): bool
    {
        return $this->type === 'external';
    }

    public function typeLabel(): string
    {
        return $this->isInternational() ? 'International' : 'Domestic';
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }
}
