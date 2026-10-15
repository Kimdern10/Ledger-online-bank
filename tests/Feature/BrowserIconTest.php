<?php

use App\Models\Profile;
use App\Models\User;

function assertHasLedgerIcon($response): void
{
    $response->assertOk()
        ->assertSee('favicon.ico?v=2', false)
        ->assertSee('favicon.svg?v=2', false)
        ->assertSee('apple-touch-icon.png?v=2', false);
}

test('the marketing and sign-in pages show the Ledger browser icon', function (string $path) {
    assertHasLedgerIcon($this->get($path));
})->with(['/', '/login', '/register', '/forgot-password']);

test('the customer dashboard shows the Ledger browser icon', function () {
    $user = User::factory()->create()->refresh();
    Profile::create(['user_id' => $user->id, 'onboarding_completed' => true]);

    assertHasLedgerIcon($this->actingAs($user)->get(route('dashboard')));
});

test('the admin pages show the Ledger browser icon', function () {
    $admin = User::factory()->create()->refresh();
    $admin->forceFill(['is_admin' => true])->save();

    assertHasLedgerIcon($this->actingAs($admin)->get(route('admin.dashboard')));
});

test('the icon files exist', function (string $file) {
    expect(file_exists(public_path($file)))->toBeTrue()
        ->and(filesize(public_path($file)))->toBeGreaterThan(100);
})->with(['favicon.ico', 'favicon.svg', 'apple-touch-icon.png']);
