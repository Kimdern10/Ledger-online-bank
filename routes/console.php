<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use App\Console\Commands\ProcessScheduledTransfers;
use App\Console\Commands\RefreshBankCatalogs;
use Illuminate\Support\Facades\Schedule;

Schedule::command(ProcessScheduledTransfers::class)->daily();

// Re-downloads Salt Edge's bank list (~12 s) before its 24-hour cache runs
// out, so customers' International bank searches never wait for it.
Schedule::command(RefreshBankCatalogs::class)->twiceDaily()->withoutOverlapping();
