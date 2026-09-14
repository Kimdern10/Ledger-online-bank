<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * One support "session" between a customer and the bank. A customer only
 * ever has ONE open conversation at a time (every controller here relies
 * on that), but can have any number of closed ones behind it — that's what
 * makes "start a new conversation" and "view my past conversations" work
 * without ever losing history.
 *
 * status is 'open' or 'closed'. closed_by records who/what closed it —
 * 'admin' (the End conversation button), 'customer' (the Start new
 * conversation button, which closes whatever was open first), or 'timeout'
 * (nobody closed it; an admin set timeout_at and the customer never
 * replied before it passed — see applyTimeoutIfExpired()).
 */
class SupportConversation extends Model
{
    protected $fillable = [
        'user_id',
        'status',
        'category',
        'priority',
        'closed_by',
        'closed_at',
        'timeout_at',
    ];

    protected function casts(): array
    {
        return [
            'closed_at' => 'datetime',
            'timeout_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(SupportMessage::class);
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function statusLabel(): string
    {
        return $this->isOpen() ? 'Open' : 'Ended';
    }

    /**
     * Set automatically the first time a customer's message matches one of
     * the bot's canned-response keys — see
     * SupportController::maybeSendBotReply(). Never set by an admin, the
     * same way a real triage bot would tag a ticket rather than a human
     * re-labeling it by hand.
     */
    public function categoryLabel(): string
    {
        return match ($this->category) {
            'card issue' => 'Card issue',
            'fraud or dispute' => 'Fraud or dispute',
            'payment problem' => 'Payment problem',
            'account access' => 'Account access',
            'account status' => 'Account status',
            'balance or statement' => 'Balance or statement',
            'bill pay' => 'Bill pay',
            'something else' => 'Something else',
            default => 'General',
        };
    }

    public function isUrgent(): bool
    {
        return $this->priority === 'urgent';
    }

    public function priorityLabel(): string
    {
        return $this->isUrgent() ? 'Urgent' : 'Normal';
    }

    /**
     * Human-readable reason a closed conversation ended, for the banner
     * shown above a read-only thread.
     */
    public function closedReasonLabel(): string
    {
        return match ($this->closed_by) {
            'admin' => __('support.closed_admin'),
            'customer' => __('support.closed_customer'),
            'timeout' => __('support.closed_timeout'),
            default => __('support.closed_default'),
        };
    }

    /**
     * Ends this conversation right now, attributed to whoever/whatever
     * ended it. Timeouts normally go through applyTimeoutIfExpired()
     * below instead, so the closed_at timestamp reflects the deadline
     * rather than whenever this happened to get noticed.
     */
    public function close(string $by): void
    {
        $this->update([
            'status' => 'closed',
            'closed_by' => $by,
            'closed_at' => now(),
        ]);
    }

    /**
     * If this conversation is still open, has an auto-close deadline set,
     * and that deadline has already passed, closes it right now and
     * returns true. There's no background job in this app checking
     * deadlines on a schedule — every controller method that loads a
     * conversation calls this first instead, so an overdue conversation
     * gets closed the moment anyone (customer or admin) next looks at it.
     */
    public function applyTimeoutIfExpired(): bool
    {
        if (! $this->isOpen() || ! $this->timeout_at || $this->timeout_at->isFuture()) {
            return false;
        }

        $this->update([
            'status' => 'closed',
            'closed_by' => 'timeout',
            'closed_at' => $this->timeout_at,
        ]);

        return true;
    }

    /**
     * The customer's most recent conversation, whether still open or
     * already closed — or null if they've never messaged support at all.
     * Applies the lazy timeout check on the way out, so callers never see
     * a conversation that's overdue but not yet marked closed.
     */
    public static function latestFor(User $user): ?self
    {
        $conversation = static::where('user_id', $user->id)->latest()->first();
        $conversation?->applyTimeoutIfExpired();

        return $conversation;
    }
}
