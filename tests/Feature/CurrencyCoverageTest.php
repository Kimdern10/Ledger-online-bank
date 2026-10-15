<?php

use App\Http\Controllers\BankDirectoryController;
use App\Services\BankProviders\SaltEdgeBankProvider;
use App\Support\CurrencyRates;
use Illuminate\Support\Facades\Http;

test('every currency a bank can be given has an exchange rate', function (string $class) {
    $countryCurrencies = (new ReflectionClassConstant($class, 'COUNTRY_CURRENCY'))->getValue();

    $missing = collect($countryCurrencies)
        ->unique()
        ->reject(fn (string $currency) => CurrencyRates::rate($currency) !== null)
        ->values()
        ->all();

    expect($missing)->toBe([]);
})->with([
    'bank picker' => [BankDirectoryController::class],
    'Salt Edge' => [SaltEdgeBankProvider::class],
]);

test('a Ukrainian bank from Salt Edge is offered in hryvnia, and hryvnia converts', function () {
    config([
        'services.salt_edge.app_id' => 'test-app',
        'services.salt_edge.secret' => 'test-secret',
        'services.salt_edge.country_codes' => '',
    ]);
    Http::fake([
        'www.saltedge.com/api/v6/providers*' => Http::response(['data' => [
            ['name' => 'Ощадбанк', 'country_code' => 'UA'],
        ], 'meta' => ['next_id' => null]]),
        '*' => Http::response([], 500),
    ]);

    $this->getJson(route('banks.search.international', ['q' => 'Ощад']))
        ->assertOk()
        ->assertJsonFragment(['name' => 'Ощадбанк', 'country' => 'UA', 'currency' => 'UAH']);

    expect(CurrencyRates::convert(100, 'UAH'))->toBeGreaterThan(100.0);
});
