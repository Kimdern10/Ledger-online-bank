<?php

use App\Models\User;
use Laravel\Fortify\Features;

test('login screen can be rendered', function () {
    $response = $this->get(route('login'));

    $response->assertOk();
});

test('users can authenticate using the login screen', function () {
    $user = User::factory()->create();

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $response->assertSessionHasErrorsIn('email');

    $this->assertGuest();
});

test('users with two factor enabled are redirected to two factor challenge', function () {
    $this->skipUnlessFortifyHas(Features::twoFactorAuthentication());

    Features::twoFactorAuthentication([
        'confirm' => true,
        'confirmPassword' => true,
    ]);

    $user = User::factory()->withTwoFactor()->create();

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('two-factor.login'));
    $this->assertGuest();
});

test('users can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('logout'));

    $response->assertRedirect(route('home'));

    $this->assertGuest();
});

test('remember me keeps users signed in after the session ends', function () {
    $user = User::factory()->create();

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
        'remember' => 'on',
    ]);

    $response->assertCookie(Auth::guard()->getRecallerName());
    expect($user->fresh()->remember_token)->not->toBeNull();
});

test('without remember me no long-lived sign-in cookie is set', function () {
    $user = User::factory()->create();

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertCookieMissing(Auth::guard()->getRecallerName());
});

test('the remember me cookie signs users back in once their session has ended', function () {
    $user = User::factory()->create();

    $login = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
        'remember' => 'on',
    ]);
    $recaller = $login->getCookie(Auth::guard()->getRecallerName(), false);

    Auth::forgetGuards();
    $this->flushSession();

    $this->withUnencryptedCookie($recaller->getName(), $recaller->getValue())
        ->get(route('dashboard'));

    $this->assertAuthenticatedAs($user);
});

test('remember me fills in the email address on the next visit', function () {
    $user = User::factory()->create();

    $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
        'remember' => 'on',
    ])->assertCookie('remembered_email', $user->email);

    $this->post(route('logout'));

    $this->withCookie('remembered_email', $user->email)
        ->get(route('login'))
        ->assertOk()
        ->assertSee('value="'.$user->email.'"', false);
});

test('signing in without remember me forgets the email address', function () {
    $user = User::factory()->create();

    $this->withCookie('remembered_email', $user->email)
        ->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password',
        ])
        ->assertCookieExpired('remembered_email');
});
