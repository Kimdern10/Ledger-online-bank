<?php

namespace App\Console\Commands;

use App\Mail\StatementMail;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

/**
 * Emails every customer their statement for a month — last month by
 * default, or --month=2026-10. Scheduled for the 1st of each month (see
 * routes/console.php). Statements are email-only: customers can't view
 * them in the app.
 *
 * Goes to every customer who has a Ledger account number, a verified
 * email, an account that wasn't deleted, and who signed up before the
 * month ended — including months with no transactions, which say so.
 */
#[Signature('statements:send {--month= : The month to send, as YYYY-MM (default: last month)}')]
#[Description('Email every customer their monthly statement')]
class SendMonthlyStatements extends Command
{
    public function handle(): int
    {
        $option = (string) $this->option('month');

        if ($option !== '' && preg_match('/^\d{4}-\d{2}$/', $option) !== 1) {
            $this->components->error('Use --month=YYYY-MM, for example --month=2026-10.');

            return self::FAILURE;
        }

        $month = $option !== ''
            ? CarbonImmutable::createFromFormat('!Y-m', $option)->startOfMonth()
            : CarbonImmutable::now()->subMonthNoOverflow()->startOfMonth();

        $sent = 0;

        User::query()
            ->where('is_admin', false)
            ->whereNull('account_deleted_at')
            ->whereNull('permanently_deleted_at')
            ->whereNotNull('email_verified_at')
            ->where(fn ($query) => $query->whereNotNull('checking_account_number')->orWhereNotNull('savings_account_number'))
            ->where('created_at', '<=', $month->endOfMonth())
            ->each(function (User $user) use ($month, &$sent) {
                Mail::to($user->email)->send(new StatementMail($user, $month->format('Y-m-d'), $month->endOfMonth()->format('Y-m-d')));
                $sent++;
            });

        $this->components->info("Queued {$sent} statement email(s) for {$month->format('F Y')}.");

        return self::SUCCESS;
    }
}
