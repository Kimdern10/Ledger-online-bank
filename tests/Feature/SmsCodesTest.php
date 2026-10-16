<?php

use App\Mail\TransferCodeMail;
use App\Models\User;
use App\Support\PhoneNumber;
use App\Support\Sms;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

beforeEach(function () {
    config(['services.twilio' => ['sid' => 'AC123', 'token' => 'secret', 'from' => '+15550001111']]);
    Http::fake(['api.twilio.com/*' => Http::response(['sid' => 'SM1'], 201)]);
    Mail::fake();
});

/**
 * The code in the last text message sent.
 */
function lastTextedCode(): string
{
    $body = '';
    Http::assertSent(function (Request $request) use (&$body) {
        $body = (string) ($request['Body'] ?? '');

        return str_contains($request->url(), 'api.twilio.com');
    });
    preg_match('/\b(\d{4,6})\b/', $body, $match);

    return $match[1] ?? '';
}

test('phone numbers are only textable in international form', function (?string $typed, ?string $expected) {
    expect(PhoneNumber::forSms($typed))->toBe($expected);
})->with([
    'with spaces' => ['+234 801 234 5678', '+2348012345678'],
    'with 00' => ['00 44 20 7946 0958', '+442079460958'],
    'US with dashes' => ['+1 (555) 010-4477', '+15550104477'],
    'no country code' => ['0801 234 5678', null],
    'empty' => [null, null],
    'letters' => ['call me', null],
]);

test('a text goes to Twilio from the configured number', function () {
    expect(Sms::send('+2348012345678', 'Hello'))->toBeTrue();

    Http::assertSent(fn (Request $request) => $request->url() === 'https://api.twilio.com/2010-04-01/Accounts/AC123/Messages.json'
        && $request['To'] === '+2348012345678'
        && $request['From'] === '+15550001111'
        && $request['Body'] === 'Hello'
        && $request->hasHeader('Authorization'));
});

test('nothing is sent when Twilio is not set up', function () {
    config(['services.twilio.sid' => null]);

    expect(Sms::send('+2348012345678', 'Hello'))->toBeFalse();
    Http::assertNothingSent();
});

test('the transfer code is only ever emailed, never texted', function () {
    $user = User::factory()->create(['phone' => '+2348012345678'])->refresh();

    $this->actingAs($user)->postJson(route('transfer-code.send'), ['channel' => 'sms'])
        ->assertOk()
        ->assertJson(['sent' => true]);

    Mail::assertSent(TransferCodeMail::class);
    Http::assertNothingSent();
});

test('each number gets at most three texts every ten minutes', function () {
    foreach (range(1, Sms::MAX_PER_WINDOW) as $ignored) {
        expect(Sms::allowed('+2348012345678'))->toBeTrue();
    }

    expect(Sms::allowed('+2348012345678'))->toBeFalse()
        ->and(Sms::allowed('+2348099999999'))->toBeTrue();
});

test('the sign-up code can be texted instead, and verifies the account', function () {
    $user = User::factory()->unverified()->create(['phone' => '+2348012345678'])->refresh();

    Livewire::actingAs($user)->test('auth.verify-code')
        ->assertSee(__('authpage.text_code_instead', ['phone' => '•••• 5678']))
        ->call('textCode')
        ->assertSet('status', __('authpage.status_code_texted', ['phone' => '•••• 5678']))
        ->set('code', lastTextedCode())
        ->call('verify')
        ->assertHasNoErrors();

    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
});

test('the text option is hidden without a textable phone', function () {
    $user = User::factory()->unverified()->create(['phone' => null])->refresh();

    Livewire::actingAs($user)->test('auth.verify-code')
        ->assertDontSee('Text the code to');
});

test('a password reset code can be texted to the phone on the account', function () {
    $user = User::factory()->create(['email' => 'ada@example.test', 'phone' => '+2348012345678']);

    Livewire::test('auth.forgot-password-code')
        ->set('email', 'ada@example.test')
        ->set('channel', 'sms')
        ->call('sendCode')
        ->assertSet('step', 2)
        ->assertSet('status', __('authpage.status_code_texted_known', ['email' => 'ada@example.test']));

    Mail::assertNothingSent();
    expect(lastTextedCode())->toBe($user->fresh()->password_reset_code);
});

test('texting a reset code reveals nothing about accounts without a phone', function () {
    User::factory()->create(['email' => 'nophone@example.test', 'phone' => null]);

    Livewire::test('auth.forgot-password-code')
        ->set('email', 'nophone@example.test')
        ->set('channel', 'sms')
        ->call('sendCode')
        ->assertSet('status', __('authpage.status_code_texted_known', ['email' => 'nophone@example.test']));

    Http::assertNothingSent();
    Mail::assertNothingSent();
});

test('profile settings explain the phone format for text codes', function () {
    $user = User::factory()->create()->refresh();

    $this->actingAs($user)->get(route('setting.profile'))
        ->assertOk()
        ->assertSee(__('account.phone_hint'));
});
