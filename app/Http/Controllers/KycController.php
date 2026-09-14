<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class KycController extends Controller
{
    /**
     * Single source of truth for the ID types this page accepts — shared
     * with the "in:" validation rule below so the dropdown and the
     * validator can never quietly drift apart. A method rather than a class
     * constant since the labels resolve through __() and a PHP class
     * constant can't call a function.
     */
    private static function documentTypes(): array
    {
        return [
            'drivers_license' => __('verify.doc_drivers_license'),
            'state_id' => __('verify.doc_state_id'),
            'passport' => __('verify.doc_passport'),
            'other' => __('verify.doc_other_id'),
        ];
    }

    /**
     * The step every new account lands on right after the onboarding
     * wizard finishes (see account-wizard.blade.php's submit(), which now
     * redirects here instead of straight to the dashboard) — upload a
     * government ID photo plus a selfie, so an admin can manually compare
     * them (see AdminKycController). Also reachable any time afterward from
     * Settings, so someone whose submission was rejected can see why and
     * try again, or an approved user can see their own status.
     */
    public function create(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        // Can't verify an identity for an onboarding that isn't finished
        // yet — send them back to pick up where they left off instead.
        if (! $user->profile?->onboarding_completed) {
            return redirect()->route('onboarding');
        }

        return view('kyc-verify', [
            'documentTypes' => self::documentTypes(),
            'kyc' => $user->kycVerification,
        ]);
    }

    /**
     * Accepts a new submission (or a resubmission after a rejection) and
     * puts the account into "pending" review — see EnsureKycApproved for
     * what that actually gates (Send Money only; every other page keeps
     * working right away, including this one landing on the dashboard next).
     * One row per user: a resubmission overwrites the previous id/selfie
     * photos and clears any earlier decision, rather than piling up old
     * rejected attempts nobody will look at again.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        abort_if($user->kyc_status === 'approved', 403, 'Your identity is already verified.');

        $data = $request->validate([
            'id_document_type' => ['required', 'string', 'in:'.implode(',', array_keys(self::documentTypes()))],
            'id_document' => ['required', 'image', 'max:8192'],
            'selfie' => ['required', 'image', 'max:8192'],
        ]);

        $idPath = $request->file('id_document')->store('kyc/id-documents/'.$user->id, 'local');
        $selfiePath = $request->file('selfie')->store('kyc/selfies/'.$user->id, 'local');

        $existing = $user->kycVerification;

        if ($existing) {
            // Clean up the previous photos on disk so a resubmission
            // doesn't leave a declined attempt's files sitting around
            // forever with nothing left pointing at them.
            Storage::disk('local')->delete(array_filter([
                $existing->id_document_path,
                $existing->selfie_path,
            ]));
        }

        $user->kycVerification()->updateOrCreate([], [
            'id_document_type' => $data['id_document_type'],
            'id_document_path' => $idPath,
            'selfie_path' => $selfiePath,
            'status' => 'pending',
            'rejection_reason' => null,
            'reviewed_by' => null,
            'reviewed_at' => null,
        ]);

        $user->kyc_status = 'pending';
        $user->save();

        // Same "email verification is the last step" fallback the
        // onboarding wizard used to apply itself — see account-wizard.blade.php.
        // Whichever of those two comes next, KYC review itself never blocks
        // getting there: it just sits pending until an admin decides it.
        return redirect($user->hasVerifiedEmail() ? route('dashboard') : route('verification.notice'))
            ->with('status', __('verify.kyc_submitted_status'));
    }
}
