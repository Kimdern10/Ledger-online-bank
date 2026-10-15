<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Enforces "Freeze wallet" (see User::isWalletFrozen()) on every route that
 * actually moves money — Send, Withdraw, Top Up, Pay Bills, and Scan to Pay
 * (which only ever leads to Send) — on BOTH the page-open (GET) and submit
 * (POST) routes, so a frozen user is bounced to the dashboard (with a popup
 * pointing to Support) before the form ever loads, not just on submit.
 *
 * A frozen account can still sign in, see its balance, view History,
 * manage linked accounts/cards, and use Support. Skipped for admins. (The
 * old separate "Restrict transactions" toggle did the same job and was
 * folded into Freeze wallet; the route alias is still 'not-restricted'.)
 */
class EnsureAccountIsNotRestricted
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && ! $user->isAdmin() && $user->isWalletFrozen()) {
            return redirect()->route('dashboard')->with('restricted', __('authpage.wallet_frozen'));
        }

        return $next($request);
    }
}
