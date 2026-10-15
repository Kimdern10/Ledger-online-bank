<?php

use App\Models\ExternalTransfer;
use App\Models\InternationalTransfer;
use App\Models\User;
use App\Support\TransferCode;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Testing\TestResponse;

beforeEach(function () {
    config([
        'services.plaid.client_id' => 'test-plaid-id',
        'services.plaid.secret' => 'test-plaid-secret',
        'services.plaid.env' => 'sandbox',
        'services.cashfree.client_id' => 'test-cashfree-id',
        'services.cashfree.client_secret' => 'test-cashfree-secret',
        'services.cashfree.env' => 'sandbox',
    ]);

    // refresh(): the gates on Send read DB defaults (account_status, can_send).
    $this->user = User::factory()->create(['kyc_status' => 'approved'])->refresh();
    $this->user->forceFill(['transaction_pin' => Hash::make('4821')])->save();
});

const US_ACCOUNT = ['country' => 'US', 'account_number' => '20314839374', 'bank_code' => '021000021'];
const IN_ACCOUNT = ['country' => 'India', 'account_number' => '026291800001191', 'bank_code' => 'YESB0000262'];

function verifyRecipient(User $user, array $payload): TestResponse
{
    return test()->actingAs($user)->postJson(route('send.verify-recipient'), $payload);
}

function fakePlaid(?int $score, string $status = 'database_insights_pass', int $http = 200): void
{
    Http::fake(['sandbox.plaid.com/auth/verify' => Http::response([
        'verification_status' => $status,
        'verification_insights' => ['name_match_score' => $score],
    ], $http)]);
}

function fakeCashfree(array $body, int $http = 200): void
{
    Http::fake(['sandbox.cashfree.com/verification/bank-account/sync' => Http::response($body, $http)]);
}

/**
 * A scheduled transfer — same validation, PIN check and recipient-name
 * bookkeeping as "send now", without needing a balance.
 */
function scheduleTransfer(User $user, array $fields): TestResponse
{
    return test()->actingAs($user)->post(route('send.store'), array_merge([
        'when' => 'later',
        'scheduled_date' => now()->addDays(3)->toDateString(),
        'amount' => '25',
        'transaction_pin' => TransferCode::issue($user),
    ], $fields));
}

function chaseTransfer(array $overrides = []): array
{
    return array_merge([
        'mode' => 'bank',
        'bank_name' => 'Chase Bank',
        'bank_recipient_name' => 'Kathy Padgett',
        'bank_account_number' => US_ACCOUNT['account_number'],
        'bank_routing_number' => US_ACCOUNT['bank_code'],
    ], $overrides);
}

function indiaTransfer(array $overrides = []): array
{
    return array_merge([
        'mode' => 'international',
        'intl_bank_name' => 'Yes Bank',
        'intl_country' => 'India',
        'intl_recipient_name' => 'PRIYA SHARMA',
        'intl_account_number' => IN_ACCOUNT['account_number'],
        'intl_bank_code' => IN_ACCOUNT['bank_code'],
        'intl_swift_code' => 'YESBINBB',
        'intl_currency' => 'INR',
    ], $overrides);
}

// ---------------------------------------------------------------- India

test('an Indian account returns the real name from Cashfree, without the sender typing one', function () {
    fakeCashfree(['account_status' => 'VALID', 'name_at_bank' => 'PRIYA SHARMA']);

    verifyRecipient($this->user, IN_ACCOUNT)
        ->assertOk()
        ->assertJson(['status' => 'verified', 'verified_name' => 'PRIYA SHARMA', 'method' => 'Cashfree bank account verification']);

    Http::assertSent(fn ($request) => $request->hasHeader('x-client-id', 'test-cashfree-id')
        && $request['bank_account'] === IN_ACCOUNT['account_number']
        && $request['ifsc'] === 'YESB0000262'
        && ! isset($request['name']));
});

test('Cashfree saying the account is invalid returns no name', function () {
    fakeCashfree(['account_status' => 'INVALID', 'name_at_bank' => '']);

    verifyRecipient($this->user, IN_ACCOUNT)->assertJson(['status' => 'invalid', 'verified_name' => null]);
});

test('a valid Indian account with no name on record is unavailable, never verified', function () {
    fakeCashfree(['account_status' => 'VALID', 'name_at_bank' => '']);

    verifyRecipient($this->user, IN_ACCOUNT)
        ->assertJson(['status' => 'unavailable', 'verified_name' => null, 'message' => null]);
});

test('an unexpected Cashfree response is a failure, not a success', function (array $body, int $http) {
    fakeCashfree($body, $http);

    verifyRecipient($this->user, IN_ACCOUNT)->assertJson(['status' => 'failed', 'verified_name' => null]);
})->with([
    'server error' => [['message' => 'internal'], 500],
    'ip not whitelisted' => [['message' => 'IP not whitelisted'], 403],
    'no account_status' => [['name_at_bank' => 'SOMEONE'], 200],
]);

test('a malformed IFSC is rejected before Cashfree is called', function () {
    Http::fake();

    verifyRecipient($this->user, array_merge(IN_ACCOUNT, ['bank_code' => 'NOTANIFSC']))->assertJson(['status' => 'invalid']);

    Http::assertNothingSent();
});

// ------------------------------------------------------------ United States

test('a Chase account only gets a match result on a typed name — no name is ever returned', function (int $score, string $expected) {
    fakePlaid($score);

    verifyRecipient($this->user, US_ACCOUNT + ['name' => 'Kathy Padgett'])
        ->assertOk()
        ->assertJson(['status' => $expected, 'verified_name' => null, 'method' => 'Plaid name match']);

    Http::assertSent(fn ($request) => $request['legal_name'] === 'Kathy Padgett'
        && $request['numbers']['ach'] === ['account' => US_ACCOUNT['account_number'], 'routing' => US_ACCOUNT['bank_code']]);
})->with([
    'match' => [91, 'match'],
    'threshold' => [70, 'match'],
    'close' => [60, 'close_match'],
    'mismatch' => [20, 'no_match'],
]);

test('with no name typed, Plaid is not called and the sender is asked for one', function () {
    Http::fake();

    verifyRecipient($this->user, US_ACCOUNT)->assertJson(['status' => 'needs_name', 'verified_name' => null]);

    Http::assertNothingSent();
});

test('Plaid outcomes other than a score are reported accurately', function (?int $score, string $plaidStatus, int $http, string $expected) {
    fakePlaid($score, $plaidStatus, $http);

    verifyRecipient($this->user, US_ACCOUNT + ['name' => 'Kathy Padgett'])->assertJson(['status' => $expected]);
})->with([
    'account numbers fail' => [null, 'database_insights_fail', 200, 'invalid'],
    'no score on record' => [null, 'database_insights_pass', 200, 'unavailable'],
    'provider error' => [null, 'database_insights_pass', 500, 'failed'],
]);

test('a malformed routing number is rejected before Plaid is called', function () {
    Http::fake();

    verifyRecipient($this->user, ['country' => 'US', 'account_number' => '20314839374', 'bank_code' => '1234', 'name' => 'Kathy Padgett'])
        ->assertJson(['status' => 'invalid']);

    Http::assertNothingSent();
});

// --------------------------------------------------------------- unsupported

test('countries and setups without a provider are unsupported and call nothing', function (Closure $setUp, array $payload) {
    $setUp();
    Http::fake();

    verifyRecipient($this->user, $payload)
        ->assertOk()
        ->assertJson(['status' => 'unsupported', 'verified_name' => null, 'message' => null]);

    Http::assertNothingSent();
})->with([
    'United Kingdom' => [fn () => null, ['country' => 'United Kingdom', 'account_number' => '12345678', 'bank_code' => '200000']],
    'Germany' => [fn () => null, ['country' => 'DE', 'account_number' => 'DE89370400440532013000', 'bank_code' => 'COBADEFF']],
    'Nigeria' => [fn () => null, ['country' => 'Nigeria', 'account_number' => '0123456789', 'bank_code' => '058']],
    'no bank code' => [fn () => null, ['country' => 'India', 'account_number' => '026291800001191', 'bank_code' => '']],
    'Plaid keys missing' => [fn () => config(['services.plaid.secret' => null]), US_ACCOUNT + ['name' => 'Kathy Padgett']],
]);

test('the verify endpoint needs a signed-in user and rejects odd input', function () {
    $this->postJson(route('send.verify-recipient'), IN_ACCOUNT)->assertUnauthorized();

    verifyRecipient($this->user, ['country' => 'India', 'account_number' => '0262 9180-0001', 'bank_code' => 'YESB0000262'])
        ->assertUnprocessable();
});

test('provider secrets never reach the browser', function () {
    fakeCashfree(['account_status' => 'VALID', 'name_at_bank' => 'PRIYA SHARMA']);

    $json = verifyRecipient($this->user, IN_ACCOUNT)->getContent();

    expect($json)->not->toContain('test-cashfree-secret')->not->toContain('test-cashfree-id');

    $this->actingAs($this->user)->get(route('send'))
        ->assertOk()
        ->assertDontSee('test-cashfree-secret')
        ->assertDontSee('test-plaid-secret');
});

// ------------------------------------------------------- saved on transfers

test('a transfer with the Cashfree-returned name is saved as verified', function () {
    fakeCashfree(['account_status' => 'VALID', 'name_at_bank' => 'PRIYA SHARMA']);
    verifyRecipient($this->user, IN_ACCOUNT);

    scheduleTransfer($this->user, indiaTransfer())->assertSessionHasNoErrors();

    $transfer = InternationalTransfer::sole();

    expect($transfer->recipient_name_status)->toBe('verified')
        ->and($transfer->recipient_name_provider)->toBe('cashfree')
        ->and($transfer->recipientNameStatusLabel())->toBe(__('receipt.recipient_name_verified'));
});

test('changing the name after Cashfree returned one makes it manual', function () {
    fakeCashfree(['account_status' => 'VALID', 'name_at_bank' => 'PRIYA SHARMA']);
    verifyRecipient($this->user, IN_ACCOUNT);

    scheduleTransfer($this->user, indiaTransfer(['intl_recipient_name' => 'Someone Else']));

    expect(InternationalTransfer::sole()->recipient_name_status)->toBe('manual');
});

test('a Chase transfer stores the match result for exactly the name that was checked', function (array $changes, string $expected) {
    fakePlaid(91);
    verifyRecipient($this->user, US_ACCOUNT + ['name' => 'Kathy Padgett']);

    scheduleTransfer($this->user, chaseTransfer($changes + [
        // A forged claim from the browser changes nothing.
        'recipient_name_status' => 'verified',
    ]))->assertSessionHasNoErrors();

    $transfer = ExternalTransfer::sole();

    expect($transfer->recipient_name_status)->toBe($expected);
})->with([
    'same name' => [[], 'match'],
    'same name, different spacing and case' => [['bank_recipient_name' => '  kathy   padgett '], 'match'],
    'different name' => [['bank_recipient_name' => 'Someone Else'], 'manual'],
    'different account' => [['bank_account_number' => '99999999999'], 'manual'],
]);

test('one sender\'s check never verifies another sender\'s transfer', function () {
    fakePlaid(91);
    verifyRecipient($this->user, US_ACCOUNT + ['name' => 'Kathy Padgett']);

    $otherSender = User::factory()->create(['kyc_status' => 'approved'])->refresh();
    $otherSender->forceFill(['transaction_pin' => Hash::make('4821')])->save();

    scheduleTransfer($otherSender, chaseTransfer());

    expect(ExternalTransfer::sole()->recipient_name_status)->toBe('manual');
});

test('mismatches, close matches and failures are stored as such and labelled not verified', function (?int $score, int $http, string $expected) {
    fakePlaid($score, 'database_insights_pass', $http);
    verifyRecipient($this->user, US_ACCOUNT + ['name' => 'Kathy Padgett']);

    scheduleTransfer($this->user, chaseTransfer());

    $transfer = ExternalTransfer::sole();

    expect($transfer->recipient_name_status)->toBe($expected)
        ->and(mb_strtolower($transfer->recipientNameStatusLabel()))->toContain('not verified by the bank');
})->with([
    'no match' => [20, 200, 'no_match'],
    'close match' => [60, 200, 'close_match'],
    'unavailable' => [null, 200, 'unavailable'],
    'failed' => [null, 500, 'failed'],
]);

test('a transfer still goes through when the provider said the account is invalid', function () {
    fakePlaid(null, 'database_insights_fail');

    verifyRecipient($this->user, US_ACCOUNT + ['name' => 'Kathy Padgett'])
        ->assertJson(['status' => 'invalid', 'message' => null])
        ->assertDontSee('send.payee_invalid');

    scheduleTransfer($this->user, chaseTransfer())
        ->assertSessionHasNoErrors()
        ->assertSessionMissing('sendError');

    expect(ExternalTransfer::count())->toBe(1);
});
test('a transfer nobody checked is saved as manual', function () {
    scheduleTransfer($this->user, chaseTransfer())->assertSessionHasNoErrors();

    $transfer = ExternalTransfer::sole();

    expect($transfer->recipient_name_status)->toBe('manual')
        ->and($transfer->recipient_name_provider)->toBeNull()
        ->and($transfer->recipientNameStatusLabel())->toBe(__('receipt.recipient_name_manual'));
});

test('the receipt shows the recipient name status', function () {
    fakeCashfree(['account_status' => 'VALID', 'name_at_bank' => 'PRIYA SHARMA']);
    verifyRecipient($this->user, IN_ACCOUNT);
    scheduleTransfer($this->user, indiaTransfer());

    $this->actingAs($this->user)
        ->get(route('send.receipt', InternationalTransfer::sole()))
        ->assertOk()
        ->assertSee(__('receipt.recipient_name_verified'));
});

test('the send page offers lookups only for configured countries', function () {
    config(['services.cashfree.client_secret' => null]);

    $this->actingAs($this->user)
        ->get(route('send'))
        ->assertOk()
        ->assertSee('"US":{', false)
        ->assertDontSee('"IN":{', false)
        ->assertSee('id="intlBankCodeGroup"', false)
        ->assertDontSee(__('send.payee_not_verified_tag'))
        ->assertDontSee(__('send.payee_needs_name'));
});

test('the send page never shows the verification-unavailable message', function (Closure $setUp) {
    $setUp();

    $this->actingAs($this->user)
        ->get(route('send'))
        ->assertOk()
        ->assertDontSee(__('send.payee_unsupported'))
        ->assertDontSee(__('send.payee_unavailable'))
        ->assertDontSee(__('send.payee_not_verified_tag'))
        ->assertDontSee(__('send.payee_needs_name'));
})->with([
    'providers configured' => [fn () => null],
    'no providers configured' => [fn () => config(['services.plaid.secret' => null, 'services.cashfree.client_secret' => null])],
]);

test('a receipt with a manually entered name shows no verification line', function () {
    scheduleTransfer($this->user, chaseTransfer())->assertSessionHasNoErrors();

    $this->actingAs($this->user)
        ->get(route('send.receipt', ExternalTransfer::sole()))
        ->assertOk()
        ->assertSee('Kathy Padgett')
        ->assertDontSee('Entered manually')
        ->assertDontSee('Not verified by the bank')
        ->assertDontSee(__('receipt.row_recipient_name_check'));
});

test('a receipt with a provider-matched name still shows it', function () {
    fakePlaid(91);
    verifyRecipient($this->user, US_ACCOUNT + ['name' => 'Kathy Padgett']);
    scheduleTransfer($this->user, chaseTransfer());

    $this->actingAs($this->user)
        ->get(route('send.receipt', ExternalTransfer::sole()))
        ->assertOk()
        ->assertSee(__('receipt.recipient_name_match'));
});

test('the send page no longer carries the checking, failure or try-again texts', function () {
    $this->actingAs($this->user)
        ->get(route('send'))
        ->assertOk()
        ->assertDontSee(__('send.payee_failed'))
        ->assertDontSee('payeeRetry', false)
        ->assertDontSee('payeeFailed', false);
});
