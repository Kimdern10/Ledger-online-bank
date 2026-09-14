<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gates Send Money only — see web.php, where 'kyc-approved' is added
 * alongside 'feature:can_send' on the send routes. Every other page
 * (Receive, Withdraw, Top Up, Pay Bills, the dashboard itself) keeps
 * working the moment onboarding finishes, whether or not an admin has
 * reviewed this user's KYC submission yet — see KycController for how
 * kyc_status moves from 'not_submitted' to 'pending' to 'approved'/
 * 'rejected', and AdminKycController for who flips that last switch.
 * Skipped for admins and guests, same reasoning as EnsureFeatureEnabled.
 */
class EnsureKycApproved
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && ! $user->isAdmin() && $user->kyc_status !== 'approved') {
            return response()->view('kyc-required', [
                'status' => $user->kyc_status,
            ], 403);
        }

        return $next($request);
    }
}
