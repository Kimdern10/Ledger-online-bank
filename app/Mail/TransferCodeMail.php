<?php

namespace App\Mail;

use App\Support\TransferCode;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * The one-time 4-digit code that confirms a single transfer — see
 * App\Support\TransferCode. Sent straight away (not queued): the user is
 * waiting on the confirmation screen for it.
 */
class TransferCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public string $code) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Ledger transfer code: '.$this->code,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.transfer-code',
            with: ['expiresMinutes' => TransferCode::EXPIRES_MINUTES],
        );
    }
}
