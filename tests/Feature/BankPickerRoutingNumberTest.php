<?php

use App\Models\Bank;
use App\Models\User;
use Illuminate\Support\Facades\Http;

beforeEach(fn () => Http::fake());

test('the Another bank picker returns each directory bank with its routing number', function () {
    Bank::create(['name' => 'JPMorgan Chase Bank', 'type' => 'external', 'routing_number' => '021000021', 'is_active' => true]);

    $this->getJson(route('banks.search', ['q' => 'chase']))
        ->assertOk()
        ->assertJsonPath('banks.0.name', 'JPMorgan Chase Bank')
        ->assertJsonPath('banks.0.routing_number', '021000021');
});

test('an inactive bank is not offered by the picker', function () {
    Bank::create(['name' => 'Closed Street Bank', 'type' => 'external', 'routing_number' => '011000015', 'is_active' => false]);

    expect($this->getJson(route('banks.search', ['q' => 'closed']))->json('banks'))->toBe([]);
});

test('the routing number field on Another bank is limited to 9 digits', function () {
    $user = User::factory()->create(['kyc_status' => 'approved'])->refresh();

    $this->actingAs($user)
        ->get(route('send'))
        ->assertOk()
        ->assertSee('name="bank_routing_number"', false)
        ->assertSee('maxlength="9"', false);
});
