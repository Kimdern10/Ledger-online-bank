<?php

use App\Models\Bill;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

test('the marketing stylesheet is versioned so phones load the latest mobile fixes', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee('assets/css/welcome.css?v='.filemtime(public_path('assets/css/welcome.css')), false);
});

test('the admin stylesheet is versioned', function () {
    $admin = User::factory()->create()->refresh();
    $admin->forceFill(['is_admin' => true])->save();

    $this->actingAs($admin)->get(route('admin.banks'))
        ->assertOk()
        ->assertSee('admin_asset/css/admin.css?v='.filemtime(public_path('admin_asset/css/admin.css')), false);
});

test('admin filter rows stack full width on phones', function (string $routeName) {
    $admin = User::factory()->create()->refresh();
    $admin->forceFill(['is_admin' => true])->save();

    $this->actingAs($admin)->get(route($routeName))
        ->assertOk()
        ->assertSee('class="admin-filter-row"', false);
})->with(['admin.banks', 'admin.support']);

test('stylesheets keep pages from scrolling sideways and fit panels to the visible screen', function () {
    foreach (['assets/css/welcome.css', 'assets/css/dashboard.css', 'admin_asset/css/admin.css'] as $stylesheet) {
        expect(file_get_contents(public_path($stylesheet)))
            ->toContain('html, body{ overflow-x: clip; }')
            ->toContain('dvh');
    }
});

test('admin lists show the page bar even when everything fits on one page', function () {
    $admin = User::factory()->create()->refresh();
    $admin->forceFill(['is_admin' => true])->save();
    User::factory()->count(2)->create();

    $this->actingAs($admin)->get(route('admin.users'))
        ->assertOk()
        ->assertSee('admin-pagination', false)
        ->assertSee('Showing 1&ndash;2 of 2', false)
        ->assertSee('class="admin-page-link disabled" aria-label="Previous page"', false);
});

test('a customer support history is paginated for admins', function () {
    $admin = User::factory()->create()->refresh();
    $admin->forceFill(['is_admin' => true])->save();
    $customer = User::factory()->create();

    $response = $this->actingAs($admin)->get(route('admin.support.history', $customer))->assertOk();

    expect($response->viewData('conversations'))->toBeInstanceOf(LengthAwarePaginator::class);
});

test('the pay bills page lists ten bills per page', function () {
    $user = User::factory()->create(['kyc_status' => 'approved'])->refresh();
    $user->forceFill(['checking_account_number' => '200012345678'])->save();
    foreach (range(1, 11) as $day) {
        Bill::create(['user_id' => $user->id, 'category' => 'Internet', 'biller' => 'Biller '.$day, 'amount' => 10, 'due_date' => now()->addDays($day)]);
    }

    $this->actingAs($user)->get(route('pay-bills'))
        ->assertOk()
        ->assertSee('Biller 10')
        ->assertDontSee('Biller 11')
        ->assertSee('Showing 1&ndash;10 of 11', false);
});

test('the admin sidebar links to guest messages', function () {
    $admin = User::factory()->create()->refresh();
    $admin->forceFill(['is_admin' => true])->save();

    $this->actingAs($admin)->get(route('admin.users'))
        ->assertOk()
        ->assertSee('href="'.route('admin.support.guests').'"', false)
        ->assertSee('Guest messages');
});

test('the phone sidebar fills only the visible screen and lists leave room for the page bar', function () {
    expect(file_get_contents(public_path('admin_asset/css/admin.css')))
        ->toContain('position:fixed; top:0; bottom:0; left:0; height:auto;')
        ->toContain('.admin-main{ padding-bottom:calc(96px + env(safe-area-inset-bottom)); }');
});

test('admins log out from the sidebar only, not the phone top bar', function () {
    $admin = User::factory()->create()->refresh();
    $admin->forceFill(['is_admin' => true])->save();

    $html = $this->actingAs($admin)->get(route('admin.users'))->assertOk()->getContent();

    expect($html)->not->toContain('admin-mobile-logout')
        ->and(substr_count($html, 'action="'.route('logout').'"'))->toBe(1);
});

test('the guest inbox has the same heading and search row as the support inbox', function () {
    $admin = User::factory()->create()->refresh();
    $admin->forceFill(['is_admin' => true])->save();

    $this->actingAs($admin)->get(route('admin.support.guests'))
        ->assertOk()
        ->assertSee('<p class="eyebrow">Customer support</p>', false)
        ->assertDontSee('← Support inbox')
        ->assertSee('class="admin-filter-row"', false);
});

test('the admin sidebar scrolls on its own with a visible scrollbar', function () {
    expect(file_get_contents(public_path('admin_asset/css/admin.css')))
        ->toContain('overflow-y:auto; overscroll-behavior:contain;')
        ->toContain('.admin-sidebar::-webkit-scrollbar-thumb');
});

test('the phone sidebar scrolls as one list like the laptop sidebar', function () {
    expect(file_get_contents(public_path('admin_asset/css/admin.css')))
        ->not->toContain('position:sticky; bottom:0; z-index:1;')
        ->not->toContain('.admin-sidebar .admin-nav-link{ padding:7px 10px;');
});
