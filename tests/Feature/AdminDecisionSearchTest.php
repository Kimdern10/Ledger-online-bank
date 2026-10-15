<?php

use App\Models\AddressVerification;
use App\Models\Card;
use App\Models\CardRequest;
use App\Models\KycVerification;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create()->refresh();
    $this->admin->forceFill(['is_admin' => true])->save();

    $this->ada = User::factory()->create(['first_name' => 'Ada', 'last_name' => 'Okafor', 'email' => 'ada@example.com']);
    $this->ben = User::factory()->create(['first_name' => 'Ben', 'last_name' => 'Carter', 'email' => 'ben@example.com']);
});

test('admins can search decided card requests by customer and decision', function () {
    CardRequest::forceCreate(['user_id' => $this->ada->id, 'type' => 'virtual', 'status' => 'approved', 'reviewed_at' => now()]);
    CardRequest::forceCreate(['user_id' => $this->ben->id, 'type' => 'physical', 'status' => 'declined', 'reviewed_at' => now()]);

    $this->actingAs($this->admin)->get(route('admin.card-requests', ['q' => 'ada okafor']))
        ->assertOk()->assertSee('Ada Okafor')->assertDontSee('Ben Carter');

    $this->actingAs($this->admin)->get(route('admin.card-requests', ['decision' => 'declined']))
        ->assertOk()->assertSee('Ben Carter')->assertDontSee('Ada Okafor');
});

test('admins can search decided identity verifications', function () {
    KycVerification::forceCreate(['user_id' => $this->ada->id, 'id_document_type' => 'passport', 'id_document_path' => 'a', 'selfie_path' => 'b', 'status' => 'approved', 'reviewed_at' => now()]);
    KycVerification::forceCreate(['user_id' => $this->ben->id, 'id_document_type' => 'passport', 'id_document_path' => 'a', 'selfie_path' => 'b', 'status' => 'rejected', 'reviewed_at' => now()]);

    $this->actingAs($this->admin)->get(route('admin.kyc', ['q' => 'ben@example']))
        ->assertOk()->assertSee('Ben Carter')->assertDontSee('Ada Okafor');

    $this->actingAs($this->admin)->get(route('admin.kyc', ['decision' => 'approved']))
        ->assertOk()->assertSee('Ada Okafor')->assertDontSee('Ben Carter');
});

test('admins can search decided address verifications', function () {
    AddressVerification::forceCreate(['user_id' => $this->ada->id, 'document_type' => 'utility_bill', 'document_path' => 'a', 'status' => 'approved', 'reviewed_at' => now()]);
    AddressVerification::forceCreate(['user_id' => $this->ben->id, 'document_type' => 'utility_bill', 'document_path' => 'a', 'status' => 'rejected', 'reviewed_at' => now()]);

    $this->actingAs($this->admin)->get(route('admin.address', ['decision' => 'rejected']))
        ->assertOk()->assertSee('Ben Carter')->assertDontSee('Ada Okafor');

    $this->actingAs($this->admin)->get(route('admin.address', ['q' => 'nobody']))
        ->assertOk()->assertSee('No decisions match your search.');
});

test('admins can search reported cards by customer, last four digits and status', function () {
    $reported = ['reported_lost_stolen' => true, 'reported_at' => now(), 'expiry_month' => 1, 'expiry_year' => 2030, 'cvv' => '123'];
    Card::forceCreate($reported + ['user_id' => $this->ada->id, 'card_number' => '4471000000001111', 'cardholder_name' => 'Ada Okafor', 'type' => 'physical', 'status' => 'frozen']);
    Card::forceCreate($reported + ['user_id' => $this->ben->id, 'card_number' => '5310000000002222', 'cardholder_name' => 'Ben Carter', 'type' => 'virtual', 'status' => 'closed']);

    $this->actingAs($this->admin)->get(route('admin.cards.reported', ['q' => '2222']))
        ->assertOk()->assertSee('Ben Carter')->assertDontSee('Ada Okafor');

    $this->actingAs($this->admin)->get(route('admin.cards.reported', ['q' => 'okafor']))
        ->assertOk()->assertSee('Ada Okafor')->assertDontSee('Ben Carter');

    $this->actingAs($this->admin)->get(route('admin.cards.reported', ['status' => 'closed']))
        ->assertOk()->assertSee('Ben Carter')->assertDontSee('Ada Okafor');
});
