<?php

use App\Services\BankProviders\PlaidInternationalBankProvider;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    config([
        'services.plaid.client_id' => 'test-client',
        'services.plaid.secret' => 'test-secret',
        'services.plaid.env' => 'sandbox',
        'services.plaid.international_country_codes' => 'GB,ES,FR,IT,CA,US',
    ]);
});

test('the international Plaid search covers the configured non-US countries', function () {
    Http::fake(['sandbox.plaid.com/institutions/search' => Http::response(['institutions' => [
        ['name' => 'Santander (UK) - Personal', 'country_codes' => ['GB'], 'routing_numbers' => []],
        ['name' => 'RBC Royal Bank', 'country_codes' => ['CA'], 'routing_numbers' => []],
    ]])]);

    $banks = collect(app(PlaidInternationalBankProvider::class)->search('bank'));

    expect($banks->pluck('country')->all())->toBe(['GB', 'CA'])
        ->and($banks->first()['meta'])->toBe('GB');

    // US is never searched from the International tab.
    Http::assertSent(fn ($request) => $request['country_codes'] === ['GB', 'ES', 'FR', 'IT', 'CA']);
});

test('Plaid sandbox test institutions are left out of the picker', function () {
    Http::fake(['sandbox.plaid.com/institutions/search' => Http::response(['institutions' => [
        ['name' => 'Unhealthy Platypus Bank - Degraded', 'country_codes' => ['FR']],
        ['name' => 'Bank of Plaid - sandbox', 'country_codes' => ['GB']],
        ['name' => 'Hello bank!', 'country_codes' => ['FR']],
    ]])]);

    expect(collect(app(PlaidInternationalBankProvider::class)->search('bank'))->pluck('name')->all())->toBe(['Hello bank!']);
});

test('the International picker includes Plaid banks with a currency from their country', function () {
    Http::fake([
        'sandbox.plaid.com/institutions/search' => Http::response(['institutions' => [
            ['name' => 'Intesa Sanpaolo', 'country_codes' => ['IT'], 'routing_numbers' => []],
        ]]),
        '*' => Http::response([], 500),
    ]);

    $this->getJson(route('banks.search.international', ['q' => 'Intesa']))
        ->assertOk()
        ->assertJsonFragment(['name' => 'Intesa Sanpaolo', 'country' => 'IT', 'currency' => 'EUR']);
});

test('a Plaid failure leaves the International picker working', function () {
    Http::fake(['*' => Http::response(['error_code' => 'INTERNAL_SERVER_ERROR'], 500)]);

    $this->getJson(route('banks.search.international', ['q' => 'anything']))->assertOk();
});
