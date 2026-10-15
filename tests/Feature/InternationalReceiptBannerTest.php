<?php

use App\Models\ExternalTransfer;
use App\Models\InternationalTransfer;
use App\Models\User;
use App\Support\TransferCode;
use Illuminate\Support\Facades\Hash;

function internationalTransferFor(User $sender, array $attributes = []): InternationalTransfer
{
    return InternationalTransfer::create(array_merge([
        'reference' => 'LDG'.fake()->unique()->numerify('######'),
        'sender_id' => $sender->id,
        'amount' => 100,
        'status' => 'completed',
        'fee' => 0,
        'recipient_name' => 'Tom Reed',
        'bank_name' => 'Barclays',
        'country' => 'United Kingdom',
        'swift_code' => 'BARCGB22',
        'account_number' => 'GB33BUKB20201555555555',
        'currency' => 'GBP',
    ], $attributes));
}

function externalTransferFor(User $sender, array $attributes = []): ExternalTransfer
{
    return ExternalTransfer::create(array_merge([
        'reference' => 'LDG'.fake()->unique()->numerify('######'),
        'sender_id' => $sender->id,
        'amount' => 50,
        'status' => 'completed',
        'fee' => 0,
        'recipient_name' => 'Kathy Padgett',
        'bank_name' => 'Chase',
        'account_number' => '20314839374',
        'routing_number' => '021000021',
    ], $attributes));
}

beforeEach(function () {
    $this->sender = User::factory()->create(['kyc_status' => 'approved'])->refresh();
});

test('a sent transfer shows a self-closing toast instead of a banner', function (string $route, Closure $make) {
    $this->actingAs($this->sender)
        ->withSession(['transferSent' => true])
        ->get(route($route, $make($this->sender)))
        ->assertOk()
        ->assertDontSee('Your international transfer has been sent.')
        ->assertDontSee('class="receipt-banner', false)
        ->assertSee('window.ledgerToast("Your transfer has been sent.", \'success\')', false);
})->with([
    'international' => ['send.receipt', fn (User $sender) => internationalTransferFor($sender)],
    'another bank' => ['send.receipt', fn (User $sender) => externalTransferFor($sender)],
]);

test('no toast when a receipt is opened again later', function () {
    $this->actingAs($this->sender)
        ->get(route('send.receipt', internationalTransferFor($this->sender)).'?from=history')
        ->assertOk()
        ->assertDontSee('window.ledgerToast(', false);
});

test('a scheduled transfer keeps its banner and gets no toast', function () {
    $transfer = internationalTransferFor($this->sender, ['status' => 'scheduled', 'scheduled_for' => now()->addDays(3)->toDateString()]);

    $this->actingAs($this->sender)
        ->get(route('send.receipt', $transfer))
        ->assertOk()
        ->assertSee('class="receipt-banner', false)
        ->assertDontSee('window.ledgerToast(', false);
});

test('the toast helper counts down with a progress line', function () {
    $this->actingAs($this->sender)
        ->withSession(['transferSent' => true])
        ->get(route('send.receipt', internationalTransferFor($this->sender)))
        ->assertSee('window.ledgerToast = function', false)
        ->assertSee('timerProgressBar: true', false);
});

test('the toast shows once right after sending, and not when the receipt is reloaded', function () {
    $this->sender->forceFill(['balance' => 1000, 'transaction_pin' => Hash::make('4821')])->save();

    $send = $this->actingAs($this->sender)->post(route('send.store'), [
        'mode' => 'bank',
        'when' => 'now',
        'amount' => '25',
        'bank_name' => 'Chase',
        'bank_recipient_name' => 'Kathy Padgett',
        'bank_account_number' => '20314839374',
        'bank_routing_number' => '021000021',
        'transaction_pin' => TransferCode::issue($this->sender),
    ]);

    $receiptUrl = $send->assertRedirect()->headers->get('Location');

    // First visit — the one the send redirects to.
    $this->actingAs($this->sender)->get($receiptUrl)
        ->assertOk()
        ->assertSee('window.ledgerToast("Your transfer has been sent."', false);

    // Reload / come back later.
    $this->actingAs($this->sender)->get($receiptUrl)
        ->assertOk()
        ->assertDontSee('window.ledgerToast(', false);
});
