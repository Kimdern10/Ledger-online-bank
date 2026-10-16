<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use App\Console\Commands\PurgeDeletedAccounts;
use App\Console\Commands\RefreshBankCatalogs;
use App\Console\Commands\SendMonthlyStatements;
use Illuminate\Support\Facades\Schedule;

// Permanently scrubs accounts 30 days after they were deleted (the Trash
// page's "Purges in N days").
Schedule::command(PurgeDeletedAccounts::class)->daily()->withoutOverlapping();

// Emails every customer last month's statement (statements are email-only).
Schedule::command(SendMonthlyStatements::class)->monthlyOn(1, '06:00')->withoutOverlapping();

// Re-downloads Salt Edge's bank list (~12 s) before its 24-hour cache runs
// out, so customers' International bank searches never wait for it.
Schedule::command(RefreshBankCatalogs::class)->twiceDaily()->withoutOverlapping();
