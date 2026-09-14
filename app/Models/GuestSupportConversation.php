<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * One support "session" started from the floating widget on the public
 * marketing page (welcome.blade.php), by someone who isn't signed in — see
 * this table's own migration for why it's a separate table from
 * support_conversations rather than a nullable user_id on that one.
 */
class GuestSupportConversation extends Model
{
    protected $fillable = [
        'guest_name',
        'guest_email',
        'guest_token',
        'status',
        'closed_by',
        'closed_at',
    ];

    protected function casts(): array
    {
        return [
            'closed_at' => 'datetime',
        ];
    }

    public function messages(): HasMany
    {
        return $this->hasMany(GuestSupportMessage::class);
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function statusLabel(): string
    {
        return $this->isOpen() ? 'Open' : 'Ended';
    }

    public function close(string $by): void
    {
        $this->update([
            'status' => 'closed',
            'closed_by' => $by,
            'closed_at' => now(),
        ]);
    }

    /**
     * The two initials shown in the admin conversation list/thread header
     * in place of a real avatar — a guest never has a profile picture.
     * Mirrors User::initials() closely enough to look consistent next to
     * it, without pulling in the full name-splitting logic that method has
     * for first/last name columns this table doesn't have.
     */
    public function initials(): string
    {
        $initials = Str::initials(trim($this->guest_name), true);

        return Str::length($initials) > 1
            ? Str::substr($initials, 0, 1).Str::substr($initials, -1)
            : $initials;
    }

    /**
     * Generates a random, unguessable token and makes sure it isn't already
     * in use — same collision-avoidance pattern User uses for its fake
     * routing/SWIFT numbers. This is the value stored in the visitor's
     * guest_support_token cookie (see GuestSupportController), so it needs
     * to be long enough that nobody could ever guess someone else's and
     * read their conversation.
     */
    public static function generateToken(): string
    {
        do {
            $token = Str::random(64);
        } while (static::where('guest_token', $token)->exists());

        return $token;
    }

    /**
     * Looks up a conversation by the token from the visitor's cookie. Null
     * token (never started a chat) or no match (cookie is stale/invalid)
     * both just mean "no conversation yet" to every caller of this.
     */
    public static function findByToken(?string $token): ?self
    {
        if (! $token) {
            return null;
        }

        return static::where('guest_token', $token)->first();
    }
}
