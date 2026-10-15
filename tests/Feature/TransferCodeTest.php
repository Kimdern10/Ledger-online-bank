<?php

use App\Mail\TransferCodeMail;
use App\Models\ExternalTransfer;
use App\Models\User;
use App\Support\TransferCode;
use Illuminate\Support\Facades\Mail;
use Illuminate\Testing\TestResponse;

beforeEach(function () {
    $this->user = User::factory()->create(['kyc_status' => 'approved', 'email' => 'daniel.gay@example.com'])->refresh();
    $this->user->forceFill(['balance' => 1000, 'address_status' => 'approved'])->save();
});

function transferWithCode(User $user, string $code): TestResponse
{
    return test()->actingAs($user)->post(route('send.store'), [
        'mode' => 'bank',
        'when' => 'now',
        'amount' => '25',
        'bank_name' => 'Chase',
        'bank_recipient_name' => 'Kathy Padgett',
        'bank_account_number' => '20314839374',
        'bank_routing_number' => '021000021',
        'transaction_pin' => $code,
    ]);
}

test('asking for a code emails a fresh 4-digit code to the user', function () {
    Mail::fake();

    $this->actingAs($this->user)
        ->postJson(route('transfer-code.send'))
        ->assertOk()
        ->assertJson(['sent' => true])
        ->assertJsonFragment(['message' => __('transfer_code.sent_to', ['email' => 'd•••••y@example.com'])]);

    Mail::assertSent(TransferCodeMail::class, fn (TransferCodeMail $mail) => $mail->hasTo('daniel.gay@example.com')
        && preg_match('/^\d{4}$/', $mail->code) === 1);
});

test('the emailed code confirms the transfer', function () {
    Mail::fake();
    $this->actingAs($this->user)->postJson(route('transfer-code.send'));

    $code = null;
    Mail::assertSent(TransferCodeMail::class, function (TransferCodeMail $mail) use (&$code) {
        $code = $mail->code;

        return true;
    });

    transferWithCode($this->user, $code)->assertSessionHasNoErrors()->assertSessionMissing('sendError');

    expect(ExternalTransfer::count())->toBe(1);
});

test('a wrong code, or no code, stops the transfer', function (string $typed) {
    $real = TransferCode::issue($this->user);
    $wrong = $typed === 'wrong' ? str_pad((string) (((int) $real + 1) % 10000), 4, '0', STR_PAD_LEFT) : '';

    transferWithCode($this->user, $wrong)->assertSessionHas('transferCodeError', __('transfer_code.incorrect'));

    expect(ExternalTransfer::count())->toBe(0);
})->with(['wrong', 'empty']);

test('a code works only once', function () {
    $code = TransferCode::issue($this->user);

    transferWithCode($this->user, $code)->assertSessionMissing('sendError');
    transferWithCode($this->user, $code)->assertSessionHas('transferCodeError', __('transfer_code.incorrect'));

    expect(ExternalTransfer::count())->toBe(1);
});

test('a code expires after ten minutes', function () {
    $code = TransferCode::issue($this->user);

    $this->travel(11)->minutes();

    transferWithCode($this->user, $code)->assertSessionHas('transferCodeError');
    expect(ExternalTransfer::count())->toBe(0);
});

test('five wrong tries cancel the code', function () {
    $code = TransferCode::issue($this->user);
    $wrong = str_pad((string) (((int) $code + 1) % 10000), 4, '0', STR_PAD_LEFT);

    foreach (range(1, 5) as $ignored) {
        expect(TransferCode::verify($this->user, $wrong))->toBeFalse();
    }

    expect(TransferCode::verify($this->user, $code))->toBeFalse();
});

test('asking for a new code replaces the old one', function () {
    $first = TransferCode::issue($this->user);
    $second = TransferCode::issue($this->user);

    if ($first !== $second) {
        expect(TransferCode::verify($this->user, $first))->toBeFalse();
    }

    expect(TransferCode::verify($this->user, $second))->toBeTrue();
});

test('one user\'s code never works for another user', function () {
    $other = User::factory()->create()->refresh();
    $code = TransferCode::issue($this->user);

    expect(TransferCode::verify($other, $code))->toBeFalse()
        ->and(TransferCode::verify($this->user, $code))->toBeTrue();
});

test('users no longer need a saved PIN to send money', function () {
    expect($this->user->hasTransactionPin())->toBeFalse();

    transferWithCode($this->user, TransferCode::issue($this->user))
        ->assertSessionMissing('pinRequired')
        ->assertSessionMissing('sendError');

    expect(ExternalTransfer::count())->toBe(1);
});

test('going over the daily limit says so plainly', function () {
    $this->user->forceFill(['balance' => 1000000])->save();

    $this->actingAs($this->user)->post(route('send.store'), [
        'mode' => 'bank',
        'when' => 'now',
        'amount' => '250,000.01',
        'bank_name' => 'Chase',
        'bank_recipient_name' => 'Kathy Padgett',
        'bank_account_number' => '20314839374',
        'bank_routing_number' => '021000021',
        'transaction_pin' => TransferCode::issue($this->user),
    ])->assertSessionHas('sendError', 'You have reached your daily limit.');
});

test('the code request needs a signed-in user and is rate limited', function () {
    Mail::fake();

    $this->postJson(route('transfer-code.send'))->assertUnauthorized();

    foreach (range(1, 5) as $ignored) {
        $this->actingAs($this->user)->postJson(route('transfer-code.send'))->assertOk();
    }

    $this->actingAs($this->user)->postJson(route('transfer-code.send'))->assertStatus(429);
});

// ------------------------------------------------- checked before submitting

test('checking a wrong code says so and keeps the real code usable', function () {
    $code = TransferCode::issue($this->user);
    $wrong = str_pad((string) (((int) $code + 1) % 10000), 4, '0', STR_PAD_LEFT);

    $this->actingAs($this->user)
        ->postJson(route('transfer-code.check'), ['code' => $wrong])
        ->assertOk()
        ->assertExactJson(['valid' => false, 'message' => __('transfer_code.incorrect')]);

    transferWithCode($this->user, $code)->assertSessionMissing('transferCodeError');

    expect(ExternalTransfer::count())->toBe(1);
});

test('checking the right code does not use it up', function () {
    $code = TransferCode::issue($this->user);

    $this->actingAs($this->user)
        ->postJson(route('transfer-code.check'), ['code' => $code])
        ->assertExactJson(['valid' => true, 'message' => null]);

    transferWithCode($this->user, $code)->assertSessionMissing('transferCodeError');

    expect(ExternalTransfer::count())->toBe(1);
});

test('wrong checks count towards the five-try limit', function () {
    $code = TransferCode::issue($this->user);
    $wrong = str_pad((string) (((int) $code + 1) % 10000), 4, '0', STR_PAD_LEFT);

    foreach (range(1, 5) as $ignored) {
        $this->actingAs($this->user)->postJson(route('transfer-code.check'), ['code' => $wrong]);
    }

    $this->actingAs($this->user)
        ->postJson(route('transfer-code.check'), ['code' => $code])
        ->assertJson(['valid' => false]);
});

test('a wrong code that reaches the page comes back as a SweetAlert', function () {
    $this->actingAs($this->user)
        ->withSession(['transferCodeError' => __('transfer_code.incorrect')])
        ->get(route('send'))
        ->assertOk()
        ->assertSee('window.ledgerToast("That code is incorrect or has expired. Request a new one and try again.", \'error\')', false)
        ->assertDontSee('class="send-alert fade-in d1">That code', false);
});
