<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Sent once, right when an account is created — see CreateNewUser::create().
 * Not gated behind notify_transactions_email/notify_security_email like
 * TransactionMail below: this isn't a transaction or a security event, it's
 * the one-time "your account exists" confirmation every real bank sends
 * regardless of notification preferences, so it always goes out.
 */
class WelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to Ledger, '.$this->user->first_name.'!',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.welcome',
        );
    }
}
