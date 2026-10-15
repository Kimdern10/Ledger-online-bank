<?php

use App\Models\AddressVerification;
use App\Models\KycVerification;
use App\Models\Profile;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create(['kyc_status' => 'approved'])->refresh();
    Profile::create(['user_id' => $this->user->id, 'onboarding_completed' => true]);
});

test('the approved ID message does not show the approval date', function () {
    KycVerification::create([
        'user_id' => $this->user->id,
        'id_document_type' => 'passport',
        'id_document_path' => 'kyc/id.jpg',
        'selfie_path' => 'kyc/selfie.jpg',
        'status' => 'approved',
        'reviewed_at' => '2026-09-25 10:00:00',
    ]);

    $this->actingAs($this->user)
        ->get(route('kyc.create'))
        ->assertOk()
        ->assertSee(__('verify.kyc_approved_body'))
        ->assertDontSee('Sep 25, 2026')
        ->assertDontSee('approved on');
});

test('the approved address message does not show the approval date', function () {
    AddressVerification::create([
        'user_id' => $this->user->id,
        'document_type' => 'utility_bill',
        'document_path' => 'address/bill.jpg',
        'status' => 'approved',
        'reviewed_at' => '2026-09-25 10:00:00',
    ]);

    $this->actingAs($this->user)
        ->get(route('address-verification.create'))
        ->assertOk()
        ->assertSee('Your proof of address was reviewed and approved.')
        ->assertDontSee('Sep 25, 2026')
        ->assertDontSee('approved on');
});
