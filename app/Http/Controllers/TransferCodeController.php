<?php

namespace App\Http\Controllers;

use App\Support\TransferCode;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Emails the signed-in user a fresh one-time code for the transfer,
 * withdrawal or top-up they're about to confirm — called by
 * openPinModal() in dashboard.js the moment the confirmation pop-up opens,
 * and again by its "Resend code" link. Throttled on the route.
 */
class TransferCodeController extends Controller
{
    public function send(Request $request): JsonResponse
    {
        $user = $request->user();

        try {
            TransferCode::send($user);
        } catch (\Throwable $e) {
            Log::error('Failed to send transfer code email: '.$e->getMessage());

            return response()->json(['sent' => false, 'message' => __('transfer_code.send_failed')], 503);
        }

        return response()->json([
            'sent' => true,
            'message' => __('transfer_code.sent_to', ['email' => TransferCode::maskedEmail($user->email)]),
        ]);
    }

    /**
     * "Is this code right?" — asked by confirmPin() in dashboard.js before
     * it submits, so a wrong code keeps the user in the code pop-up. Does
     * not use the code up; the real submit does (RequiresTransactionPin).
     */
    public function check(Request $request): JsonResponse
    {
        $valid = TransferCode::check($request->user(), trim((string) $request->input('code', '')));

        return response()->json([
            'valid' => $valid,
            'message' => $valid ? null : __('transfer_code.incorrect'),
        ]);
    }
}
