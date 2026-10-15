<?php

use App\Models\User;

test('the Added by you page renders with no banks', function () {
    $admin = User::factory()->create()->refresh();
    $admin->forceFill(['is_admin' => true])->save();

    $this->actingAs($admin)->get(route('admin.banks'))
        ->assertOk()
        ->assertSee('You have not added any banks yet.')
        ->assertSee('API banks')
        ->assertSee('Added by you');
});

test('the admin Banks link opens the API banks list', function () {
    $admin = User::factory()->create()->refresh();
    $admin->forceFill(['is_admin' => true])->save();

    $this->actingAs($admin)->get(route('admin.banks'))
        ->assertSee('href="'.route('admin.banks.providers').'" class="admin-nav-link', false);
});
