<?php

use App\Models\ExternalTransfer;
use App\Models\InternationalTransfer;
use App\Models\TopUp;
use App\Models\Transfer;
use App\Models\User;
use App\Models\Withdrawal;

beforeEach(function () {
    $this->sender = User::factory()->create(['kyc_status' => 'approved'])->refresh();
    $this->recipient = User::factory()->create()->refresh();
});

function anExternalTransfer(User $sender): ExternalTransfer
{
    return ExternalTransfer::create([
        'reference' => 'LDG261014EXT001', 'sender_id' => $sender->id, 'amount' => 50, 'status' => 'completed', 'fee' => 0,
        'recipient_name' => 'Kathy Padgett', 'bank_name' => 'Chase', 'account_number' => '20314839374', 'routing_number' => '021000021',
    ]);
}

function anInternationalTransfer(User $sender): InternationalTransfer
{
    return InternationalTransfer::create([
        'reference' => 'LDG261014INT001', 'sender_id' => $sender->id, 'amount' => 100, 'status' => 'completed', 'fee' => 0,
        'recipient_name' => 'Tom Reed', 'bank_name' => 'Barclays', 'country' => 'United Kingdom',
        'swift_code' => 'BARCGB22', 'account_number' => 'GB33BUKB20201555555555', 'currency' => 'GBP',
    ]);
}

function aLedgerTransfer(User $sender, User $recipient): Transfer
{
    return Transfer::create([
        'reference' => 'LDG261014LDG001', 'sender_id' => $sender->id, 'recipient_id' => $recipient->id,
        'amount' => 25, 'status' => 'completed',
    ]);
}

test('every kind of receipt has one link built from its reference', function (Closure $make, string $reference, string $shows) {
    $transfer = $make($this->sender, $this->recipient);
    $url = route('send.receipt', $transfer);

    expect($url)->toEndWith('/send/receipt/'.$reference)
        ->not->toContain('external')
        ->not->toContain('international');

    $this->actingAs($this->sender)->get($url)->assertOk()->assertSee($shows);
})->with([
    'another bank' => [fn (User $s) => anExternalTransfer($s), 'LDG261014EXT001', 'Kathy Padgett'],
    'international' => [fn (User $s) => anInternationalTransfer($s), 'LDG261014INT001', 'Tom Reed'],
    'ledger' => [fn (User $s, User $r) => aLedgerTransfer($s, $r), 'LDG261014LDG001', 'LDG261014LDG001'],
]);

test('old numbered links forward to the reference link, keeping ?from=history', function () {
    $external = anExternalTransfer($this->sender);
    $international = anInternationalTransfer($this->sender);
    $ledger = aLedgerTransfer($this->sender, $this->recipient);

    $this->actingAs($this->sender)->get('/send/receipt/external/'.$external->id.'?from=history')
        ->assertRedirect(route('send.receipt', ['reference' => 'LDG261014EXT001', 'from' => 'history']));

    $this->actingAs($this->sender)->get('/send/receipt/international/'.$international->id)
        ->assertRedirect(route('send.receipt', ['reference' => 'LDG261014INT001']));

    $this->actingAs($this->sender)->get('/send/receipt/'.$ledger->id)
        ->assertRedirect(route('send.receipt', ['reference' => 'LDG261014LDG001']));
});

test('someone else cannot open a receipt or use an old link to find its reference', function () {
    $stranger = User::factory()->create()->refresh();
    $external = anExternalTransfer($this->sender);

    $this->actingAs($stranger)->get(route('send.receipt', $external))->assertNotFound();
    $this->actingAs($stranger)->get('/send/receipt/external/'.$external->id)->assertNotFound();
});

test('an unknown reference is not found', function () {
    $this->actingAs($this->sender)->get('/send/receipt/LDG000000NOPE00')->assertNotFound();
});

test('withdrawal and top-up receipt links use their reference too', function () {
    expect((new Withdrawal)->getRouteKeyName())->toBe('reference')
        ->and((new TopUp)->getRouteKeyName())->toBe('reference');
});

// ------------------------------------------------------------ no /ledger prefix

test('pages are at their plain addresses, without /ledger', function () {
    expect(route('dashboard'))->toEndWith('/dashboard')->not->toContain('/ledger/')
        ->and(route('login'))->toEndWith('/login')->not->toContain('/ledger/')
        ->and(route('admin.users'))->toEndWith('/admin/users')->not->toContain('/ledger/')
        ->and(route('send.receipt', ['reference' => 'LDG261014EXT001']))->toEndWith('/send/receipt/LDG261014EXT001');
});

test('the only /ledger address left just forwards back to the plain page', function () {
    $ledgerRoutes = collect(app('router')->getRoutes()->getRoutes())
        ->map(fn ($route) => $route->uri())
        ->filter(fn (string $uri) => $uri === 'ledger' || str_starts_with($uri, 'ledger/'))
        ->values()
        ->all();

    expect($ledgerRoutes)->toBe(['ledger/{path?}']);

    $this->get('/ledger/login')->assertStatus(302)->assertRedirect('/login?r=1')->assertHeader('Clear-Site-Data', '"cache"');
    $this->get('/ledger/send/receipt/LDG261014EXT001?from=history')->assertRedirect('/send/receipt/LDG261014EXT001?from=history&r=1');
    $this->get('/ledger')->assertRedirect('/?r=1');
});
