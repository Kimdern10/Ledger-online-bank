<?php

use App\Models\Profile;
use App\Models\Transfer;
use App\Models\User;
use App\Support\TransferCode;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

function userWithStatus(string $status, array $extra = []): User
{
    $user = User::factory()->create(['kyc_status' => 'approved'])->refresh();
    $user->forceFill(array_merge(['account_status' => $status, 'balance' => 1000], $extra))->save();

    return $user;
}

// ---------------------------------------------------------------- signing in

test('which statuses can sign in', function (string $status, bool $canSignIn) {
    $user = userWithStatus($status);

    $response = $this->post(route('login.store'), ['email' => $user->email, 'password' => 'password']);

    if ($canSignIn) {
        $response->assertSessionHasNoErrors();
        $this->assertAuthenticatedAs($user);
    } else {
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }
})->with([
    'active' => ['active', true],
    'frozen wallet' => ['frozen', true],
    'suspended' => ['suspended', false],
    'disabled' => ['disabled', false],
]);

test('suspended and disabled users are signed out with their own message', function (string $status, string $message) {
    $user = userWithStatus($status);

    $this->actingAs($user)->get(route('dashboard'))
        ->assertRedirect(route('login'))
        ->assertSessionHasErrors(['email' => __($message)]);
})->with([
    'suspended' => ['suspended', 'authpage.account_suspended'],
    'disabled' => ['disabled', 'authpage.account_disabled'],
]);

// ------------------------------------------------------------- frozen wallet

test('a frozen wallet can still open the dashboard', function () {
    $user = userWithStatus('frozen');
    Profile::create(['user_id' => $user->id, 'onboarding_completed' => true]);

    $this->actingAs($user)->get(route('dashboard'))->assertOk();
});

test('a frozen wallet cannot open or use any money page', function (string $method, string $route) {
    $user = userWithStatus('frozen');

    $this->actingAs($user)->call($method, route($route), ['transaction_pin' => TransferCode::issue($user)])
        ->assertRedirect(route('dashboard'))
        ->assertSessionHas('restricted', __('authpage.wallet_frozen'));
})->with([
    'open send' => ['GET', 'send'],
    'send money' => ['POST', 'send.store'],
    'open withdraw' => ['GET', 'withdraw'],
    'withdraw' => ['POST', 'withdraw.store'],
    'open top up' => ['GET', 'top-up'],
    'top up' => ['POST', 'top-up.store'],
    'pay a bill' => ['POST', 'bills.store'],
]);

test('nobody can send money into a frozen wallet', function () {
    $sender = userWithStatus('active', ['address_status' => 'approved']);
    $frozen = userWithStatus('frozen', ['checking_account_number' => '1000000077']);

    $this->actingAs($sender)->post(route('send.store'), [
        'mode' => 'ledger',
        'when' => 'now',
        'amount' => '25',
        'recipient_account_number' => '1000000077',
        'recipient_name' => $frozen->name,
        'transaction_pin' => TransferCode::issue($sender),
    ])->assertSessionHas('sendError', "This account can't receive money right now.");

    expect(Transfer::count())->toBe(0)
        ->and((float) $frozen->refresh()->balance)->toBe(1000.0);
});

// ------------------------------------------------- Restrict folded into Freeze

test('there is no separate Restrict control any more', function () {
    $admin = User::factory()->create()->refresh();
    $admin->forceFill(['is_admin' => true])->save();
    $user = userWithStatus('active');

    expect(Route::has('admin.users.restriction.toggle'))->toBeFalse()
        ->and(Schema::hasColumn('users', 'is_restricted'))->toBeFalse();

    $this->actingAs($admin)->get(route('admin.users.show', $user))
        ->assertOk()
        ->assertDontSee('Restrict transactions')
        ->assertSee('Freeze wallet');
});
