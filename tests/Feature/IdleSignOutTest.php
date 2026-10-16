<?php

use App\Models\Profile;
use App\Models\User;

function onboardedCustomer(): User
{
    $user = User::factory()->create()->refresh();
    Profile::create(['user_id' => $user->id, 'onboarding_completed' => true]);

    return $user;
}

test('customer pages sign out after the configured idle time', function () {
    config(['session.idle_timeout' => 15]);

    $this->actingAs(onboardedCustomer())->get(route('dashboard'))
        ->assertOk()
        ->assertSee('var IDLE_MS = 15 * 60 * 1000;', false)
        ->assertSee(json_encode(route('login', ['timeout' => 1])), false);
});

test('admin pages sign out after the configured idle time too', function () {
    $admin = User::factory()->create()->refresh();
    $admin->forceFill(['is_admin' => true])->save();

    $this->actingAs($admin)->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('ledgerLastActivity', false);
});

test('idle sign-out can be turned off', function () {
    config(['session.idle_timeout' => 0]);

    $this->actingAs(onboardedCustomer())->get(route('dashboard'))
        ->assertOk()
        ->assertDontSee('ledgerLastActivity', false);
});

test('the login page explains an idle sign-out', function () {
    $this->get(route('login', ['timeout' => 1]))
        ->assertOk()
        ->assertSee(__('authpage.idle_signed_out'));

    $this->get(route('login'))
        ->assertOk()
        ->assertDontSee(__('authpage.idle_signed_out'));
});
