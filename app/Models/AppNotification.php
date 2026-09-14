<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A dashboard-bell notification — deliberately named AppNotification, not
 * Notification: Illuminate\Notifications\Notifiable (already used by User)
 * reserves that name and a "notifications" table for its own, differently-
 * shaped system, which this app has never actually used. See the
 * app_notifications migration's comment for the same reasoning.
 */
class AppNotification extends Model
{
    protected $table = 'app_notifications';

    protected $fillable = ['user_id', 'title', 'body', 'url', 'read_at'];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeUnread(Builder $query): Builder
    {
        return $query->whereNull('read_at');
    }

    public function markAsRead(): void
    {
        if (! $this->read_at) {
            $this->read_at = now();
            $this->save();
        }
    }

    /**
     * The one place every real event in the app that's worth telling
     * someone about creates a bell notification — see the notify_
     * transactions_email-gated Mail::send() calls in TransferController,
     * WithdrawalController, TopUpController, AdminController::
     * adjustBalance(), and ProcessScheduledTransfers, each of which now
     * has a matching call to this right alongside the email. Deliberately
     * NOT gated by notify_transactions_email — that's an email-only
     * preference (see NotificationSettingController); the bell is a
     * separate channel that always reflects real activity regardless of
     * whether emails are turned off. See NotificationController for how
     * these get back out to the dashboard bell.
     */
    public static function notify(User $user, string $title, ?string $body = null, ?string $url = null): self
    {
        return self::create([
            'user_id' => $user->id,
            'title' => $title,
            'body' => $body,
            'url' => $url,
        ]);
    }
}
