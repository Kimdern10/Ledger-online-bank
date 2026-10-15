<?php

use App\Models\User;
use Livewire\Livewire;

test('the reset code page gives its countdown the translated words', function () {
    Livewire::test('auth.forgot-password-code')
        ->assertSeeHtml('id="resetCodeCountdown"')
        ->assertSeeHtml('data-expires-in="'.e(__('authpage.js_code_expires_in')).'"')
        ->assertSeeHtml('data-expired="'.e(__('authpage.js_code_expired_resend')).'"');
});

test('sending a reset code starts the countdown at ten minutes', function () {
    $this->freezeTime();

    Livewire::test('auth.forgot-password-code')
        ->set('email', 'nobody@example.test')
        ->call('sendCode')
        ->assertSet('step', 2)
        ->assertDispatched('code-sent', expiresAt: now()->addMinutes(10)->toIso8601String());
});

test('the verify email page gives its countdown the deadline and words', function () {
    $user = User::factory()->unverified()->create();
    $user->forceFill(['verification_code' => '123456', 'verification_code_expires_at' => now()->addMinutes(10)])->save();

    Livewire::actingAs($user)->test('auth.verify-code')
        ->assertSeeHtml('data-expires-at="'.$user->fresh()->verification_code_expires_at->toIso8601String().'"')
        ->assertSeeHtml('data-expires-in="'.e(__('authpage.js_code_expires_in')).'"');
});

test('the countdown scripts contain no Blade, which Livewire would not render', function (string $component) {
    $source = file_get_contents(resource_path("views/livewire/auth/{$component}.blade.php"));
    $script = substr($source, strrpos($source, '<script>'));

    expect($script)->not->toContain('@json')
        ->not->toContain('@js(')
        ->not->toContain('{{');
})->with(['forgot-password-code', 'verify-code']);

test('refreshing the enter-your-code step stays on it with the same deadline', function () {
    $this->freezeTime();

    Livewire::test('auth.forgot-password-code')
        ->set('email', 'nobody@example.test')
        ->call('sendCode');

    // A fresh mount is what a page refresh does.
    Livewire::test('auth.forgot-password-code')
        ->assertSet('step', 2)
        ->assertSet('email', 'nobody@example.test')
        ->assertSet('codeExpiresAt', now()->addMinutes(10)->toIso8601String())
        ->assertSeeHtml('data-expires-at="'.now()->addMinutes(10)->toIso8601String().'"');
});

test('using a different email forgets the code step', function () {
    Livewire::test('auth.forgot-password-code')
        ->set('email', 'nobody@example.test')
        ->call('sendCode')
        ->call('backToEmail')
        ->assertSet('step', 1);

    Livewire::test('auth.forgot-password-code')
        ->assertSet('step', 1)
        ->assertSet('email', '');
});

test('resetting the password forgets the code step', function () {
    $user = User::factory()->create(['email' => 'reset@example.test']);

    Livewire::test('auth.forgot-password-code')
        ->set('email', 'reset@example.test')
        ->call('sendCode');

    Livewire::test('auth.forgot-password-code')
        ->set('code', $user->fresh()->password_reset_code)
        ->set('password', 'NewPassword123!')
        ->set('password_confirmation', 'NewPassword123!')
        ->call('resetPassword')
        ->assertHasNoErrors()
        ->assertRedirect(route('login'));

    expect(session()->has('password_reset_code_step'))->toBeFalse();
});

test('the countdown scripts sit after the component root so Livewire runs them once it has loaded', function (string $component) {
    $source = file_get_contents(resource_path("views/livewire/auth/{$component}.blade.php"));

    expect(strrpos($source, '<script>'))->toBeGreaterThan(strrpos($source, '</div>'));
})->with(['forgot-password-code', 'verify-code']);
