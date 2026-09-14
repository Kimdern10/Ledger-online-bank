<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GuestSupportMessage extends Model
{
    protected $fillable = [
        'guest_support_conversation_id',
        'sender_id',
        'is_system',
        'body',
        'attachment_path',
        'attachment_name',
        'attachment_mime',
        'read_at',
    ];

    protected $casts = [
        'is_system' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(GuestSupportConversation::class, 'guest_support_conversation_id');
    }

    /**
     * Null for a guest's own message — see this table's migration for why
     * a guest has no User row to point sender_id at. Set for anything an
     * admin (or the automatic greeting) sent.
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * A guest conversation only ever has two kinds of message: the guest's
     * own (sender_id null), or something from the support side — a real
     * admin reply, or the automatic opening greeting (is_system). Both of
     * those render on the same "support" side of the widget/admin thread,
     * so this is enough to decide which side a bubble belongs on without
     * loading the sender relation.
     */
    public function isFromAdmin(): bool
    {
        return $this->sender_id !== null;
    }

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
     * Same "public" disk as support_messages' attachments — see this
     * table's own attachment migration for why a guest's photo doesn't
     * need the private-disk treatment KYC photos get. Requires
     * `php artisan storage:link` to have been run once, same as every
     * other attachment URL in this app.
     */
    public function attachmentUrl(): ?string
    {
        return $this->hasAttachment() ? Storage::disk('public')->url($this->attachment_path) : null;
    }
}
