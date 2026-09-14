<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * One shared Mailable for every kind of real, balance-affecting event —
 * Send Money (sent + received), an external ("Another bank") transfer,
 * Withdraw, Top Up, and an admin balance adjustment — instead of five
 * near-identical mail classes. Same idea as send-receipt.blade.php being
 * reused across all of those on the receipt side: one normalized shape
 * ($title/$amountSign/$rows), fed different data per transaction type. See
 * each controller's use of this class for what $rows looks like there.
 *
 * Every call site checks $user->notify_transactions_email before even
 * constructing this — see NotificationSettingController — so this class
 * itself doesn't need to know about that preference.
 */
class TransactionMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  array<int, array{label: string, value: string}>  $rows
     */
    public function __construct(
        public User $user,
        public string $title,
        public string $amountSign,
        public float $amount,
        public string $reference,
        public array $rows,
        public float $newBalance,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->title.' — $'.number_format($this->amount, 2).' · Ledger',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.transaction',
        );
    }
}
