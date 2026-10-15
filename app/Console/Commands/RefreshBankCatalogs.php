<?php

namespace App\Console\Commands;

use App\Services\BankProviders\SaltEdgeBankProvider;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

/**
 * Keeps Salt Edge's bank list warm in the cache. Downloading it takes about
 * 12 seconds, so without this the first International bank search after the
 * cache ran out made a customer wait that long. Scheduled twice a day (see
 * routes/console.php) — well inside the catalogue's 24-hour cache — and
 * worth running once after deploying or clearing the cache.
 */
#[Signature('banks:refresh-catalogs')]
#[Description('Download the bank lists that are slow to fetch (Salt Edge) into the cache ahead of time')]
class RefreshBankCatalogs extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(SaltEdgeBankProvider $saltEdge): int
    {
        if (! $saltEdge->isConfigured()) {
            $this->components->info('Salt Edge is not set up — nothing to refresh.');

            return self::SUCCESS;
        }

        $banks = count($saltEdge->refreshCatalog());

        if ($banks === 0) {
            $this->components->warn('Salt Edge returned no banks — the previous list (if any) was kept. See storage/logs/laravel.log.');

            return self::FAILURE;
        }

        $this->components->info("Salt Edge: {$banks} banks cached.");

        return self::SUCCESS;
    }
}
