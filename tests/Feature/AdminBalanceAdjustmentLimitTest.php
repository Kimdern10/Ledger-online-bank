<?php

use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create()->refresh();
    $this->admin->forceFill(['is_admin' => true])->save();
    $this->customer = User::factory()->create()->refresh();
});

test('an admin can credit more than one hundred million dollars, typed with or without commas', function (string $typed, float $expected) {
    $this->actingAs($this->admin)
        ->post(route('admin.users.balance', $this->customer), ['direction' => 'credit', 'amount' => $typed])
        ->assertSessionHasNoErrors();

    expect((float) $this->customer->refresh()->balance)->toBe($expected);
})->with([
    'one hundred and fifty million' => ['150,000,000', 150000000.0],
    'one billion, the maximum' => ['$1,000,000,000.00', 1000000000.0],
    'no commas' => ['250000000', 250000000.0],
]);

test('more than one billion dollars in one adjustment is refused', function () {
    $this->actingAs($this->admin)
        ->post(route('admin.users.balance', $this->customer), ['direction' => 'credit', 'amount' => '1000000000.01'])
        ->assertSessionHasErrors('amount');

    expect((float) $this->customer->refresh()->balance)->toBe(0.0);
});
