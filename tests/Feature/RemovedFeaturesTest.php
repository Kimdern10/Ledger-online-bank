<?php

use App\Models\User;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Route;

test('transfers can no longer be scheduled or cancelled', function () {
    expect(Route::has('send.scheduled.cancel'))->toBeFalse()
        ->and(Route::has('send.scheduled.cancel.external'))->toBeFalse()
        ->and(Route::has('send.scheduled.cancel.international'))->toBeFalse()
        ->and(class_exists('App\\Console\\Commands\\ProcessScheduledTransfers'))->toBeFalse();
});

test('deleted accounts are purged by the daily schedule', function () {
    $commands = collect(app(Schedule::class)->events())->map(fn ($event) => $event->command ?? '');

    expect($commands->contains(fn ($command) => str_contains($command, 'accounts:purge-deleted')))->toBeTrue()
        ->and($commands->contains(fn ($command) => str_contains($command, 'transfers:process-scheduled')))->toBeFalse();
});

test('the transaction PIN page is gone and Settings links to Security instead', function () {
    $user = User::factory()->create()->refresh();

    expect(Route::has('setting.transaction-pin'))->toBeFalse();

    $this->actingAs($user)->get(route('setting'))
        ->assertOk()
        ->assertSee(route('security.edit'), false)
        ->assertSee(__('setting.security'));
});

test('SweetAlert is served from this site, not a CDN', function () {
    expect(file_exists(public_path('assets/vendor/sweetalert2.all.min.js')))->toBeTrue();

    foreach (['partials/sweetalert.blade.php', 'paybills.blade.php'] as $view) {
        expect(file_get_contents(resource_path('views/'.$view)))
            ->not->toContain('cdn.jsdelivr.net/npm/sweetalert2')
            ->toContain('assets/vendor/sweetalert2.all.min.js');
    }
});
