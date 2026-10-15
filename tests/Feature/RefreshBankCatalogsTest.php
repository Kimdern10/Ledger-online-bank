<?php

use App\Services\BankProviders\SaltEdgeBankProvider;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    config(['services.salt_edge.app_id' => 'test-app', 'services.salt_edge.secret' => 'test-secret', 'services.salt_edge.country_codes' => '']);
});

test('refreshing caches the Salt Edge list so searches do not download it again', function () {
    Http::fake(['www.saltedge.com/api/v6/providers*' => Http::response(['data' => [
        ['name' => 'Barclays Bank', 'country_code' => 'GB'],
        ['name' => 'Westpac', 'country_code' => 'AU'],
    ], 'meta' => ['next_id' => null]])]);

    $this->artisan('banks:refresh-catalogs')
        ->expectsOutputToContain('Salt Edge: 2 banks cached.')
        ->assertSuccessful();

    Http::assertSentCount(1);

    $results = app(SaltEdgeBankProvider::class)->search('barclays');

    expect($results)->toHaveCount(1)
        ->and($results[0]['name'])->toBe('Barclays Bank');
    Http::assertSentCount(1);
});

test('a failed refresh keeps the banks already cached', function () {
    Http::fakeSequence('www.saltedge.com/api/v6/providers*')
        ->push(['data' => [['name' => 'Barclays Bank', 'country_code' => 'GB']], 'meta' => ['next_id' => null]])
        ->push([], 500)
        ->push([], 500);

    $this->artisan('banks:refresh-catalogs')->assertSuccessful();
    $this->artisan('banks:refresh-catalogs')->assertFailed();

    expect(app(SaltEdgeBankProvider::class)->search('barclays'))->toHaveCount(1);
});

test('the refresh does nothing when Salt Edge is not set up', function () {
    config(['services.salt_edge.app_id' => null]);
    Http::fake();

    $this->artisan('banks:refresh-catalogs')
        ->expectsOutputToContain('not set up')
        ->assertSuccessful();

    Http::assertNothingSent();
});

test('the refresh is scheduled twice a day', function () {
    $event = collect(app(Schedule::class)->events())
        ->first(fn ($event) => str_contains($event->command ?? '', 'banks:refresh-catalogs'));

    expect($event)->not->toBeNull()
        ->and($event->expression)->toBe('0 1,13 * * *');
});
