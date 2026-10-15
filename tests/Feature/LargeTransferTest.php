<?php

use App\Models\ExternalTransfer;
use App\Models\User;
use App\Support\TransferCode;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\TestResponse;

function largeTransferSender(string $kyc, string $address, float $balance): User
{
    $user = User::factory()->create()->refresh();
    $user->forceFill([
        'kyc_status' => $kyc,
        'address_status' => $address,
        'balance' => $balance,
        'transaction_pin' => Hash::make('4821'),
    ])->save();

    return $user;
}

function sendNow(User $sender, string $amount): TestResponse
{
    return test()->actingAs($sender)->post(route('send.store'), [
        'mode' => 'bank',
        'when' => 'now',
        'amount' => $amount,
        'bank_name' => 'Chase',
        'bank_recipient_name' => 'Kathy Padgett',
        'bank_account_number' => '20314839374',
        'bank_routing_number' => '021000021',
        'transaction_pin' => TransferCode::issue($sender),
    ]);
}

test('users keep the original daily limits per tier', function (string $kyc, string $address, float $limit) {
    expect(largeTransferSender($kyc, $address, 0)->tierDailyLimit())->toBe($limit);
})->with([
    'tier 1' => ['none', 'none', 1000.0],
    'tier 2' => ['approved', 'none', 10000.0],
    'tier 3' => ['approved', 'approved', 250000.0],
]);

test('a fully verified user can send up to their daily limit', function () {
    $sender = largeTransferSender('approved', 'approved', 1000000);

    sendNow($sender, '250,000')->assertSessionHasNoErrors()->assertSessionMissing('sendError');

    expect((float) ExternalTransfer::sole()->amount)->toBe(250000.0);
});

test('a transfer above the daily limit is refused', function () {
    $sender = largeTransferSender('approved', 'approved', 1000000);

    sendNow($sender, '250,000.01')->assertSessionHas('sendError');

    expect(ExternalTransfer::count())->toBe(0);
});

test('a single transfer above $999,999.99 is refused', function () {
    $sender = largeTransferSender('approved', 'approved', 200000000);

    sendNow($sender, '1,000,000')->assertSessionHasErrors('amount');

    expect(ExternalTransfer::count())->toBe(0);
});
