<?php

namespace App\Http\Controllers;

use App\Services\PayeeVerification\PayeeDetails;
use App\Services\PayeeVerification\PayeeResult;
use App\Services\PayeeVerification\PayeeVerificationManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;

/**
 * Backs the live recipient check on the "Another bank" and "International
 * bank" tabs of send.blade.php (see runPayeeVerify() in dashboard.js).
 *
 * What comes back is shown to the sender, and also remembered server-side
 * by PayeeVerificationManager — that memory is the only thing
 * TransferController trusts when it saves the recipient name's status.
 * A bank-directory match is never treated as evidence here: only a
 * provider's answer about this exact account counts.
 */
class RecipientVerificationController extends Controller
{
    public function __invoke(Request $request, PayeeVerificationManager $verification): JsonResponse
    {
        $data = $request->validate([
            'country' => ['required', 'string', 'max:100'],
            'account_number' => ['required', 'string', 'max:40', 'regex:/^[A-Za-z0-9]+$/'],
            'bank_code' => ['nullable', 'string', 'max:20', 'regex:/^[A-Za-z0-9]*$/'],
            'name' => ['nullable', 'string', 'max:150'],
        ]);

        $countryCode = $verification->countryCode($data['country']);

        $result = $countryCode === null || blank($data['bank_code'] ?? null)
            ? new PayeeResult(PayeeResult::UNSUPPORTED)
            : $verification->verify($request->user(), new PayeeDetails(
                countryCode: $countryCode,
                accountNumber: $data['account_number'],
                bankCode: $data['bank_code'],
                name: $data['name'] ?? null,
            ));

        $provider = $verification->providers()[$result->provider ?? ''] ?? null;

        return response()->json([
            'status' => $result->status,
            'verified_name' => $result->verifiedName,
            // Only messages that exist in the language file are sent — one
            // that's been removed there shows nothing, never its raw key.
            'message' => Lang::has('send.payee_'.$result->status) ? __('send.payee_'.$result->status) : null,
            // How the answer was reached — "Plaid name match" is a score on
            // a typed name; Cashfree retrieves the name.
            'method' => $provider?->label(),
        ]);
    }
}
