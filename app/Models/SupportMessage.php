<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SupportMessage extends Model
{
    protected $fillable = [
        'user_id',
        'support_conversation_id',
        'sender_id',
        'is_bot',
        'body',
        'attachment_path',
        'attachment_name',
        'attachment_mime',
        'read_at',
    ];

    protected $casts = [
        'is_bot' => 'boolean',
        'read_at' => 'datetime',
    ];

    /**
     * The customer this CONVERSATION belongs to — not necessarily who sent
     * this particular message (see sender() below). Every message in a
     * thread shares the same user_id regardless of who wrote it.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The specific support "session" this message is part of. A customer
     * can have several conversations over time (see SupportConversation) —
     * this is what separates today's chat from one they ended last month.
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(SupportConversation::class, 'support_conversation_id');
    }

    /**
     * Whoever actually typed this one message — the customer themselves,
     * or whichever admin replied.
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * A customer's own thread only ever has two possible senders: the
     * customer, or an admin. So "the sender isn't the conversation owner"
     * is enough to mean "this came from an admin", with no need to load
     * the sender relation or check is_admin at all.
     */
    public function isFromAdmin(): bool
    {
        return $this->sender_id !== $this->user_id;
    }

    /**
     * True only for a message an actual human admin typed — false for
     * both the customer's own messages AND the bot's auto-replies. This
     * is what SupportController::maybeSendBotReply() checks for to decide
     * whether the bot should still be answering, or whether a real member
     * has already taken over this conversation.
     */
    public function isFromRealAdmin(): bool
    {
        return $this->isFromAdmin() && ! $this->is_bot;
    }

    /**
     * body stays a plain NOT NULL text column (never migrated to
     * nullable, which would need doctrine/dbal just to run ->change()) —
     * an attachment-only message just stores '' for body instead. This is
     * what lets preview() and the chat bubble fall back to describing the
     * attachment when there's no caption text to show.
     */
    public function preview(int $length = 60): string
    {
        if (trim((string) $this->body) === '' && $this->hasAttachment()) {
            return '📎 '.$this->attachment_name;
        }

        return Str::limit($this->body, $length);
    }

    public function hasAttachment(): bool
    {
        return ! empty($this->attachment_path);
    }

    public function isImageAttachment(): bool
    {
        return $this->hasAttachment() && str_starts_with((string) $this->attachment_mime, 'image/');
    }

    /**
     * The public URL for this message's attachment, or null if it doesn't
     * have one. Requires `php artisan storage:link` to have been run once
     * (standard Laravel setup step) so the "public" disk is actually
     * reachable over HTTP.
     */
    public function attachmentUrl(): ?string
    {
        return $this->hasAttachment() ? Storage::disk('public')->url($this->attachment_path) : null;
    }
}
