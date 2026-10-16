<?php

namespace App\Mail;

use App\Models\User;
use App\Support\MonthlyStatement;
use Carbon\CarbonImmutable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * A customer's statement for a period — the only way statements reach
 * anyone (there's no statement page in the app). Like a bank's, the email is
 * a short covering note and the statement itself is the attached PDF
 * (resources/views/pdf/statement.blade.php). Sent for last month on the 1st
 * of each month by `php artisan statements:send`, or for the From/To dates
 * a customer picks on History (HistoryController::emailStatement()) —
 * $requested says which, for the note's wording.
 *
 * Holds just the customer and the dates; the statement itself is built
 * when the email is sent (see statement()).
 */
class StatementMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    private ?MonthlyStatement $statement = null;

    /**
     * @param  string  $startDate  first day, e.g. "2026-10-03"
     * @param  string  $endDate  last day, e.g. "2026-10-17"
     * @param  bool  $requested  asked for on History (vs. the monthly one)
     */
    public function __construct(public User $user, public string $startDate, public string $endDate, public bool $requested = false) {}

    public function statement(): MonthlyStatement
    {
        return $this->statement ??= MonthlyStatement::between(
            $this->user,
            CarbonImmutable::createFromFormat('!Y-m-d', $this->startDate),
            CarbonImmutable::createFromFormat('!Y-m-d', $this->endDate),
        );
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('statement.email_subject', ['period' => $this->statement()->periodLabel()]),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.statement',
            with: ['statement' => $this->statement(), 'requested' => $this->requested],
        );
    }

    /**
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        $statement = $this->statement();

        return [
            Attachment::fromData(fn () => $statement->pdf(), $statement->filename())->withMime('application/pdf'),
        ];
    }
}
