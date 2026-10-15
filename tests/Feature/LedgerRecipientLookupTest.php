<?php

use App\Models\Transfer;
use App\Models\User;
use App\Support\TransferCode;
use Illuminate\Support\Facades\Hash;

/**
 * A user who passes every gate on the Send Money routes. Refreshed so the
 * DB defaults those gates read (account_status, can_send) are loaded.
 */
function ledgerUser(array $attributes = []): User
{
    return User::factory()->create(array_merge(['kyc_status' => 'approved'], $attributes))->refresh();
}

test('looking up a Ledger account number returns the account holder name', function (string $column) {
    $recipient = ledgerUser(['first_name' => 'Kathy', 'last_name' => 'Padgett', $column => '1000000001']);

    $this->actingAs(ledgerUser())
        ->getJson(route('send.lookup', ['account_number' => '1000000001']))
        ->assertOk()
        ->assertExactJson(['found' => true, 'name' => $recipient->name]);
})->with(['checking_account_number', 'savings_account_number']);

test('an unknown account number, or the sender\'s own, is simply not found', function () {
    $sender = ledgerUser(['checking_account_number' => '2000000002']);

    $this->actingAs($sender)->getJson(route('send.lookup', ['account_number' => '9999999999']))
        ->assertExactJson(['found' => false]);

    $this->actingAs($sender)->getJson(route('send.lookup', ['account_number' => '2000000002']))
        ->assertExactJson(['found' => false]);
});

test('an admin account is never returned by the lookup', function () {
    $admin = ledgerUser(['checking_account_number' => '3000000003']);
    $admin->forceFill(['is_admin' => true])->save();

    $this->actingAs(ledgerUser())
        ->getJson(route('send.lookup', ['account_number' => '3000000003']))
        ->assertExactJson(['found' => false]);
});

test('the lookup gives nothing beyond the name', function () {
    ledgerUser(['first_name' => 'Kathy', 'last_name' => 'Padgett', 'checking_account_number' => '1000000001']);

    $json = $this->actingAs(ledgerUser())
        ->getJson(route('send.lookup', ['account_number' => '1000000001']))
        ->json();

    expect(array_keys($json))->toBe(['found', 'name']);
});

test('the lookup needs a signed-in user', function () {
    $this->getJson(route('send.lookup', ['account_number' => '1000000001']))->assertUnauthorized();
});

test('the Ledger tab has the name note and the strings it shows', function () {
    $this->actingAs(ledgerUser())
        ->get(route('send'))
        ->assertOk()
        ->assertSee('id="ledgerVerifyNote"', false)
        ->assertDontSee(__('send.ledger_account_found'))
        ->assertSee(__('send.ledger_account_not_found'));
});

test('the transfer goes to the account holder found by number, whatever name was submitted', function () {
    $recipient = ledgerUser(['first_name' => 'Kathy', 'last_name' => 'Padgett', 'checking_account_number' => '1000000001']);
    $sender = ledgerUser();
    $sender->forceFill(['transaction_pin' => Hash::make('4821')])->save();

    $this->actingAs($sender)->post(route('send.store'), [
        'mode' => 'ledger',
        'when' => 'later',
        'scheduled_date' => now()->addDays(2)->toDateString(),
        'amount' => '25',
        'recipient_account_number' => '1000000001',
        'recipient_name' => 'Somebody Else',
        'transaction_pin' => TransferCode::issue($sender),
    ])->assertSessionHasNoErrors();

    expect(Transfer::sole()->recipient_id)->toBe($recipient->id);
});
