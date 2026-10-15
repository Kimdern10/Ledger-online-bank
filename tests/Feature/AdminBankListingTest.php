<?php

use App\Models\Bank;
use App\Models\User;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    config([
        'services.plaid.client_id' => 'test-client',
        'services.plaid.secret' => 'test-secret',
        'services.plaid.env' => 'sandbox',
        'services.plaid.country_codes' => 'US',
    ]);

    $this->admin = User::factory()->create()->refresh();
    $this->admin->forceFill(['is_admin' => true])->save();
});

function plaidInstitutions(array $names, int $total): array
{
    return [
        'institutions' => array_map(fn (string $name) => ['name' => $name, 'country_codes' => ['US'], 'routing_numbers' => ['021000021']], $names),
        'total' => $total,
    ];
}

// ---------------------------------------------------------- your directory

test('the directory can be searched by name, country, routing number or SWIFT', function (string $search, string $expected) {
    Bank::create(['name' => 'JPMorgan Chase Bank', 'type' => 'external', 'routing_number' => '021000021', 'is_active' => true]);
    Bank::create(['name' => 'Barclays Bank UK PLC', 'type' => 'international', 'country' => 'United Kingdom', 'swift_code' => 'BARCGB22', 'is_active' => true]);

    $response = $this->actingAs($this->admin)->get(route('admin.banks', ['q' => $search]))->assertOk();

    $other = $expected === 'JPMorgan Chase Bank' ? 'Barclays Bank UK PLC' : 'JPMorgan Chase Bank';
    $response->assertSee($expected)->assertDontSee($other);
})->with([
    'name' => ['chase', 'JPMorgan Chase Bank'],
    'country' => ['united kingdom', 'Barclays Bank UK PLC'],
    'routing number' => ['021000021', 'JPMorgan Chase Bank'],
    'SWIFT' => ['BARCGB', 'Barclays Bank UK PLC'],
]);

test('the directory can be filtered by status and keeps the search across pages', function () {
    Bank::create(['name' => 'Active Bank', 'type' => 'external', 'is_active' => true]);
    Bank::create(['name' => 'Switched Off Bank', 'type' => 'external', 'is_active' => false]);
    foreach (range(1, 20) as $n) {
        Bank::create(['name' => "Search Bank $n", 'type' => 'external', 'is_active' => true]);
    }

    $this->actingAs($this->admin)->get(route('admin.banks', ['status' => 'inactive']))
        ->assertSee('Switched Off Bank')->assertDontSee('Active Bank');

    $this->actingAs($this->admin)->get(route('admin.banks', ['q' => 'Search Bank']))
        ->assertOk()
        ->assertSee('q=Search', false);
});

// ---------------------------------------------------------- provider banks

test('Plaid with nothing searched is paged through institutions/get', function () {
    Http::fake(['sandbox.plaid.com/institutions/get' => Http::response(plaidInstitutions(['Chase', 'Citibank'], 10157))]);

    $this->actingAs($this->admin)
        ->get(route('admin.banks.providers', ['source' => 'plaid', 'page' => 3]))
        ->assertOk()
        ->assertSee('10,157 banks')
        ->assertSee('Chase')
        ->assertSee('Add to my banks');

    Http::assertSent(fn ($request) => str_ends_with($request->url(), '/institutions/get')
        && $request['count'] === 25 && $request['offset'] === 50 && $request['country_codes'] === ['US']);
});

test('a Plaid search uses institutions/search', function () {
    Http::fake(['sandbox.plaid.com/institutions/search' => Http::response(['institutions' => [
        ['name' => 'Chase', 'country_codes' => ['US'], 'routing_numbers' => ['021000021']],
    ]])]);

    $this->actingAs($this->admin)
        ->get(route('admin.banks.providers', ['source' => 'plaid', 'q' => 'chase']))
        ->assertOk()
        ->assertSee('1 bank')
        ->assertSee('021000021');

    Http::assertSent(fn ($request) => $request['query'] === 'chase');
});

test('a provider that fails or isn\'t configured says so', function () {
    Http::fake(['sandbox.plaid.com/*' => Http::response(['error_code' => 'INTERNAL_SERVER_ERROR'], 500)]);

    $this->actingAs($this->admin)->get(route('admin.banks.providers', ['source' => 'plaid']))
        ->assertOk()->assertSee("didn't answer", false);

    config(['services.plaid.secret' => null]);

    $this->actingAs($this->admin)->get(route('admin.banks.providers', ['source' => 'plaid']))
        ->assertOk()->assertSee("isn't set up", false);
});

test('adding a provider bank copies it into the directory once', function () {
    $bank = ['name' => 'Chase', 'type' => 'external', 'country' => 'US', 'routing_number' => '021000021'];

    $this->actingAs($this->admin)->post(route('admin.banks.import'), $bank)->assertSessionHas('status', 'Chase added to the directory.');
    $this->actingAs($this->admin)->post(route('admin.banks.import'), $bank)->assertSessionHas('status', 'Chase is already in the directory.');

    $saved = Bank::sole();

    expect($saved->routing_number)->toBe('021000021')
        ->and($saved->type)->toBe('external')
        ->and($saved->is_active)->toBeTrue();
});

test('a bank already in the directory shows as such in the provider list', function () {
    Bank::create(['name' => 'Chase', 'type' => 'external', 'is_active' => true]);
    Http::fake(['sandbox.plaid.com/institutions/get' => Http::response(plaidInstitutions(['Chase'], 1))]);

    $this->actingAs($this->admin)
        ->get(route('admin.banks.providers', ['source' => 'plaid']))
        ->assertSee('Added</span>', false)
        ->assertDontSee('Add to my banks');
});

test('customers cannot see provider banks or add banks', function () {
    $customer = User::factory()->create()->refresh();

    $this->actingAs($customer)->post(route('admin.banks.import'), ['name' => 'Sneaky Bank', 'type' => 'external']);
    $this->actingAs($customer)->get(route('admin.banks.providers'))->assertForbidden();

    expect(Bank::count())->toBe(0);
});

// ---------------------------------------------------------- country filter

test('filtering Plaid International by country asks Plaid for just that country', function () {
    config(['services.plaid.international_country_codes' => 'GB,FR,CA']);
    Http::fake(['sandbox.plaid.com/institutions/get' => Http::response([
        'institutions' => [['name' => 'Hello bank!', 'country_codes' => ['FR'], 'routing_numbers' => []]],
        'total' => 99,
    ])]);

    $this->actingAs($this->admin)
        ->get(route('admin.banks.providers', ['source' => 'plaid_international', 'country' => 'fr']))
        ->assertOk()
        ->assertSee('99 banks')
        ->assertSee('<option value="FR" selected>France</option>', false)
        ->assertSee('country=FR', false);

    Http::assertSent(fn ($request) => $request['country_codes'] === ['FR']);
});

test('other sources are filtered by country from their own list', function () {
    config(['services.salt_edge.app_id' => 'test-app', 'services.salt_edge.secret' => 'test-secret', 'services.salt_edge.country_codes' => '']);
    Http::fake(['www.saltedge.com/api/v6/providers*' => Http::response(['data' => [
        ['name' => 'Ощадбанк', 'country_code' => 'UA'],
        ['name' => 'Westpac Personal', 'country_code' => 'AU'],
    ], 'meta' => ['next_id' => null]])]);

    $this->actingAs($this->admin)
        ->get(route('admin.banks.providers', ['source' => 'salt_edge', 'country' => 'UA']))
        ->assertOk()
        ->assertSee('Ощадбанк')
        ->assertSee('Ukraine')
        ->assertDontSee('Westpac Personal');
});

test('a country the source has no banks in is ignored rather than breaking the list', function () {
    config(['services.plaid.international_country_codes' => 'GB']);
    Http::fake(['sandbox.plaid.com/institutions/get' => Http::response([
        'institutions' => [['name' => 'Barclays', 'country_codes' => ['GB'], 'routing_numbers' => []]],
        'total' => 1,
    ])]);

    $this->actingAs($this->admin)
        ->get(route('admin.banks.providers', ['source' => 'plaid_international', 'country' => 'ZZ']))
        ->assertOk()
        ->assertSee('Barclays');

    Http::assertSent(fn ($request) => $request['country_codes'] === ['GB']);
});
