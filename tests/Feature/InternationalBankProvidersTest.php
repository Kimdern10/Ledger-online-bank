<?php

use App\Services\BankProviders\SaltEdgeBankProvider;
use App\Services\BankProviders\TrueLayerBankProvider;
use Illuminate\Support\Facades\Http;

test('Salt Edge fetches each configured country with its own country_code filter', function () {
    config([
        'services.salt_edge.app_id' => 'test-app',
        'services.salt_edge.secret' => 'test-secret',
        'services.salt_edge.country_codes' => 'SG,HK',
    ]);

    Http::fake(function ($request) {
        parse_str((string) parse_url($request->url(), PHP_URL_QUERY), $query);

        return Http::response(match ($query['country_code'] ?? null) {
            'SG' => ['data' => [['name' => 'DBS', 'country_code' => 'SG', 'bic_codes' => ['DBSSSGSG']]], 'meta' => ['next_id' => null]],
            'HK' => ['data' => [['name' => 'Hang Seng Bank', 'country_code' => 'HK']], 'meta' => ['next_id' => null]],
            default => ['data' => [['name' => 'Should not be asked for', 'country_code' => 'DE']], 'meta' => ['next_id' => null]],
        });
    });

    $banks = collect(app(SaltEdgeBankProvider::class)->search(''));

    expect($banks->pluck('name')->all())->toBe(['DBS', 'Hang Seng Bank'])
        ->and($banks->firstWhere('name', 'DBS')['swift_code'])->toBe('DBSSSGSG');

    Http::assertSentCount(2);
    Http::assertSent(fn ($request) => str_contains($request->url(), 'country_code=SG') && str_contains($request->url(), 'exclude_inactive=true'));
});

test('Salt Edge follows next_id within a country', function () {
    config([
        'services.salt_edge.app_id' => 'test-app',
        'services.salt_edge.secret' => 'test-secret',
        'services.salt_edge.country_codes' => 'IN',
    ]);

    Http::fakeSequence('www.saltedge.com/api/v6/providers*')
        ->push(['data' => [['name' => 'HDFC', 'country_code' => 'IN']], 'meta' => ['next_id' => '42']])
        ->push(['data' => [['name' => 'ICICI', 'country_code' => 'IN']], 'meta' => ['next_id' => null]]);

    expect(collect(app(SaltEdgeBankProvider::class)->search(''))->pluck('name')->all())->toBe(['HDFC', 'ICICI']);

    Http::assertSent(fn ($request) => str_contains($request->url(), 'from_id=42'));
});

test('TrueLayer banks are read from the documented items list', function () {
    config([
        'services.truelayer.client_id' => 'test-client',
        'services.truelayer.client_secret' => 'test-secret',
        'services.truelayer.env' => 'sandbox',
    ]);

    Http::fake([
        'auth.truelayer-sandbox.com/connect/token' => Http::response(['access_token' => 'token-1', 'expires_in' => 3600]),
        'api.truelayer-sandbox.com/v3/payments-providers/search' => Http::response(['items' => [
            ['id' => 'ob-barclays', 'display_name' => 'Barclays', 'country_code' => 'GB', 'swift_code' => 'BARCGB22', 'capabilities' => []],
            ['id' => 'ob-ing-nl', 'display_name' => 'ING', 'country_code' => 'NL', 'capabilities' => []],
        ]]),
    ]);

    $banks = collect(app(TrueLayerBankProvider::class)->search(''));

    expect($banks->pluck('name')->all())->toBe(['Barclays', 'ING'])
        ->and($banks->firstWhere('name', 'Barclays')['country'])->toBe('GB')
        ->and($banks->firstWhere('name', 'Barclays')['swift_code'])->toBe('BARCGB22');
});

test('a TrueLayer server error gives no banks rather than breaking the picker', function () {
    config([
        'services.truelayer.client_id' => 'test-client',
        'services.truelayer.client_secret' => 'test-secret',
        'services.truelayer.env' => 'sandbox',
    ]);

    Http::fake([
        'auth.truelayer-sandbox.com/connect/token' => Http::response(['access_token' => 'token-1', 'expires_in' => 3600]),
        'api.truelayer-sandbox.com/*' => Http::response(['title' => 'Unknown Error', 'status' => 500], 500),
    ]);

    expect(app(TrueLayerBankProvider::class)->search(''))->toBe([]);
});

test('with no country list, Salt Edge fetches every country and drops its fake and country-less entries', function () {
    config([
        'services.salt_edge.app_id' => 'test-app',
        'services.salt_edge.secret' => 'test-secret',
        'services.salt_edge.country_codes' => '',
    ]);

    Http::fake(['www.saltedge.com/api/v6/providers*' => Http::response(['data' => [
        ['name' => 'Westpac Personal', 'country_code' => 'AU'],
        ['name' => 'Banco Galicia', 'country_code' => 'AR'],
        ['name' => 'Fake Bank Simple', 'country_code' => 'XF'],
        ['name' => 'PayPal', 'country_code' => 'XO'],
    ], 'meta' => ['next_id' => null]])]);

    expect(collect(app(SaltEdgeBankProvider::class)->search(''))->pluck('name')->all())->toBe(['Westpac Personal', 'Banco Galicia']);

    Http::assertSentCount(1);
    Http::assertSent(fn ($request) => str_contains($request->url(), 'per_page=1000') && ! str_contains($request->url(), 'country_code'));
});
