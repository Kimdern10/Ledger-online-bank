<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AddressVerificationController extends Controller
{
    /**
     * Single source of truth for the document types this page accepts —
     * shared with the "in:" validation rule below, same pattern
     * KycController uses for id_document_type. A method rather than a class
     * constant since the labels resolve through __() and a PHP class
     * constant can't call a function.
     */
    private static function documentTypes(): array
    {
        return [
            'utility_bill' => __('verify.doc_utility_bill'),
            'bank_statement' => __('verify.doc_bank_statement'),
            'tenancy_agreement' => __('verify.doc_tenancy_agreement'),
            'other' => __('verify.doc_other_address'),
        ];
    }

    /**
     * Tier 3's one step — reachable from Settings once Tier 2 (identity)
     * is approved. Can't be reached before that: address verification only
     * ever raises the limit Tier 2 already unlocked, so someone who hasn't
     * verified their identity yet is sent there first instead.
     */
    public function create(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if (! $user->profile?->onboarding_completed) {
            return redirect()->route('onboarding');
        }

        if ($user->kyc_status !== 'approved') {
            return redirect()->route('kyc.create')
                ->with('status', __('verify.address_verify_identity_first'));
        }

        return view('address-verify', [
            'documentTypes' => self::documentTypes(),
            'addressVerification' => $user->addressVerification,
        ]);
    }

    /**
     * Accepts a new submission (or a resubmission after a rejection) and
     * puts the account into "pending" review — same one-row-per-user,
     * updateOrCreate, old-file-cleanup pattern KycController::store() uses.
     * Unlike KYC's id/selfie photos, a proof-of-address document is very
     * often a PDF (a bank statement or utility bill downloaded from a
     * portal), so this accepts jpg/png/pdf rather than images only.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        abort_if($user->kyc_status !== 'approved', 403, 'Verify your identity first.');
        abort_if($user->address_status === 'approved', 403, 'Your address is already verified.');

        $data = $request->validate([
            'document_type' => ['required', 'string', 'in:'.implode(',', array_keys(self::documentTypes()))],
            'document' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:8192'],
        ]);

        $path = $request->file('document')->store('address/documents/'.$user->id, 'local');

        $existing = $user->addressVerification;

        if ($existing) {
            // Same "clean up the previous file on disk so a resubmission
            // doesn't leave a declined attempt sitting around forever"
            // reasoning KycController::store() uses.
            Storage::disk('local')->delete(array_filter([$existing->document_path]));
        }

        $user->addressVerification()->updateOrCreate([], [
            'document_type' => $data['document_type'],
            'document_path' => $path,
            'status' => 'pending',
            'rejection_reason' => null,
            'reviewed_by' => null,
            'reviewed_at' => null,
        ]);

        $user->address_status = 'pending';
        $user->save();

        return redirect()->route('setting')
            ->with('status', __('verify.address_submitted_status'));
    }
}
