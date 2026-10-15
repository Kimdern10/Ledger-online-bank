<?php

use App\Models\User;
use Livewire\Livewire;

test('account setup is served as a live Livewire page', function () {
    $this->actingAs(User::factory()->create()->refresh())
        ->get(route('onboarding'))
        ->assertOk()
        ->assertSee('wire:snapshot', false)
        ->assertSee('wire:submit', false)
        ->assertSee('livewire', false);
});

test('pressing Next on account setup runs on the server: it moves on, or holds back with errors', function () {
    $user = User::factory()->create(['first_name' => 'Ada', 'last_name' => 'Okafor'])->refresh();
    $this->actingAs($user);

    // Step 1 comes pre-filled from the account, so Next moves on.
    $wizard = Livewire::test('onboarding.account-wizard')
        ->assertSet('step', 1)
        ->assertSet('first_name', 'Ada')
        ->call('nextStep')
        ->assertHasNoErrors()
        ->assertSet('step', 2);

    // Step 2 (next of kin) is empty, so Next is held back with errors.
    $wizard->call('nextStep')
        ->assertHasErrors(['kin_full_name', 'kin_phone', 'kin_address'])
        ->assertSet('step', 2);
});

test('a new customer can only choose one account type', function () {
    $this->actingAs(User::factory()->create()->refresh());

    Livewire::test('onboarding.account-wizard')
        ->set('step', 5)
        ->set('account_type', 'both')
        ->set('agree_terms', true)
        ->call('submit')
        ->assertHasErrors(['account_type' => 'in'])
        ->assertSee('You can only choose one account type: Checking or Savings.');
});

test('every new account is opened in US dollars', function () {
    $user = User::factory()->create()->refresh();
    $this->actingAs($user);

    Livewire::test('onboarding.account-wizard')
        ->set('step', 5)
        ->set('account_type', 'checking')
        ->set('currency', 'EUR')
        ->set('agree_terms', true)
        ->call('submit')
        ->assertHasNoErrors();

    expect($user->fresh()->profile)
        ->account_type->toBe('checking')
        ->currency->toBe('USD');
});

test('the account step offers no Both option and no currency choice', function () {
    $this->actingAs(User::factory()->create()->refresh());

    Livewire::test('onboarding.account-wizard')
        ->set('step', 5)
        ->assertDontSee('value="both"', false)
        ->assertDontSee('wire:model="currency"', false)
        ->assertSee('USD US dollar');
});
