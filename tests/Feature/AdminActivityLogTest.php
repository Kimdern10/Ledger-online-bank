<?php

use App\Models\AdminActivity;
use App\Models\KycVerification;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create(['first_name' => 'Grace', 'last_name' => 'Admin'])->refresh();
    $this->admin->forceFill(['is_admin' => true])->save();
    $this->customer = User::factory()->create(['first_name' => 'Ada', 'last_name' => 'Okafor'])->refresh();
});

test('changing an account status is recorded with the admin, customer and message', function () {
    $this->actingAs($this->admin)
        ->from(route('admin.users.show', $this->customer))
        ->post(route('admin.users.status', $this->customer), ['account_status' => 'frozen'])
        ->assertRedirect();

    $activity = AdminActivity::sole();

    expect($activity->admin_id)->toBe($this->admin->id)
        ->and($activity->user_id)->toBe($this->customer->id)
        ->and($activity->action)->toBe('admin.users.status')
        ->and($activity->actionLabel())->toBe('Account status')
        ->and($activity->description)->toBe('Ada Okafor is now frozen.')
        ->and($activity->ip_address)->not->toBeNull();
});

test('approving an identity check records the customer it belonged to', function () {
    $kyc = KycVerification::forceCreate(['user_id' => $this->customer->id, 'id_document_type' => 'passport', 'id_document_path' => 'a', 'selfie_path' => 'b', 'status' => 'pending']);

    $this->actingAs($this->admin)->post(route('admin.kyc.approve', $kyc))->assertRedirect();

    expect(AdminActivity::sole())
        ->action->toBe('admin.kyc.approve')
        ->user_id->toBe($this->customer->id);
});

test('failed actions and page views are not recorded', function () {
    $this->actingAs($this->admin)->get(route('admin.users'))->assertOk();

    $this->actingAs($this->admin)
        ->post(route('admin.users.status', $this->customer), ['account_status' => 'not-a-status'])
        ->assertSessionHasErrors('account_status');

    $this->actingAs($this->admin)
        ->post(route('admin.trash.purge', $this->customer))
        ->assertSessionHas('adminError');

    expect(AdminActivity::count())->toBe(0);
});

test('the activity log lists, searches and filters what admins did', function () {
    AdminActivity::factory()->create(['admin_id' => $this->admin->id, 'user_id' => $this->customer->id, 'action' => 'admin.users.status', 'description' => 'Ada Okafor is now frozen.']);
    AdminActivity::factory()->create(['admin_id' => $this->admin->id, 'user_id' => null, 'action' => 'admin.banks.store', 'description' => 'Barclays was added.']);

    $this->actingAs($this->admin)->get(route('admin.activity'))
        ->assertOk()
        ->assertSee('Ada Okafor is now frozen.')
        ->assertSee('Barclays was added.')
        ->assertSee('Activity log');

    $this->actingAs($this->admin)->get(route('admin.activity', ['q' => 'okafor']))
        ->assertSee('Ada Okafor is now frozen.')
        ->assertDontSee('Barclays was added.');

    $this->actingAs($this->admin)->get(route('admin.activity', ['action' => 'admin.banks.store']))
        ->assertSee('Barclays was added.')
        ->assertDontSee('Ada Okafor is now frozen.');
});

test('customers cannot see the activity log', function () {
    $this->actingAs($this->customer)->get(route('admin.activity'))->assertForbidden();
});
