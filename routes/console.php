<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


use App\Console\Commands\ProcessScheduledTransfers;
use Illuminate\Support\Facades\Schedule;

Schedule::command(ProcessScheduledTransfers::class)->daily();
