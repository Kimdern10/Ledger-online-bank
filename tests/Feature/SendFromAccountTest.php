<?php

use App\Models\User;

test('the send page shows the sender\'s own account type and last four digits', function (array $accounts, string $expected) {
    $user = User::factory()->create(['kyc_status' => 'approved'])->refresh();
    $user->forceFill($accounts)->save();

    $this->actingAs($user)
        ->get(route('send'))
        ->assertOk()
        ->assertSee($expected)
        ->assertDontSee('•••• 4471');
})->with([
    'checking account' => [['checking_account_number' => '1000005823', 'savings_account_number' => null], 'Checking •••• 5823'],
    'savings account only' => [['checking_account_number' => null, 'savings_account_number' => '2000009914'], 'Savings •••• 9914'],
    'both — checking first' => [['checking_account_number' => '1000005823', 'savings_account_number' => '2000009914'], 'Checking •••• 5823'],
    'no account yet' => [['checking_account_number' => null, 'savings_account_number' => null], 'Account •••• ----'],
]);
