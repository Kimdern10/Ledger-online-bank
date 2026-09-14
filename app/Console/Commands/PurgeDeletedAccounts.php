<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * The other half of Delete Account (see AccountDeletionController and the
 * Trash page in the admin dashboard). Deleting your own account only ever
 * sets account_deleted_at — nothing is actually erased at that point, which
 * is exactly what gives an admin (or the user, via Support) a 30-day window
 * to reverse it from the Trash page. This command is what makes "30 days"
 * real: run it daily (see routes/console.php) and it finds every account
 * whose 30 days are up and scrubs it for good via
 * User::anonymizeForPermanentDeletion() — never a real row delete, see that
 * method's own comment for why.
 *
 * You can also always run `php artisan accounts:purge-deleted` by hand any
 * time, e.g. to test this without waiting for the scheduler — same caveat
 * as ProcessScheduledTransfers about the scheduler needing to actually be
 * running locally for the daily version to fire on its own.
 */
class PurgeDeletedAccounts extends Command
{
    protected $signature = 'accounts:purge-deleted';

    protected $description = 'Permanently scrubs every account whose 30-day Delete Account window has passed';

    public function handle(): int
    {
        $due = User::whereNotNull('account_deleted_at')
            ->whereNull('permanently_deleted_at')
            ->where('account_deleted_at', '<=', now()->subDays(30))
            ->get();

        foreach ($due as $user) {
            try {
                $email = $user->email;
                $id = $user->id;
                $user->anonymizeForPermanentDeletion();
                $this->info("Purged account #{$id} ({$email}).");
            } catch (\Throwable $e) {
                Log::error('Failed to purge account #'.$user->id.': '.$e->getMessage());
            }
        }

        if ($due->isEmpty()) {
            $this->info('No accounts were due for purging.');
        }

        return self::SUCCESS;
    }
}
