<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Backs the "Restrict transactions" toggle on the admin per-user page (see
 * AdminController::toggleRestriction()). Attached to every route that
 * actually moves money — Send, Withdraw, Top Up, Pay Bills, and Scan to
 * Pay (which only ever leads to Send) — on BOTH the page-open (GET) and
 * submit (POST) routes, so a restricted user is bounced before the form
 * ever loads, not just when they try to submit it.
 *
 * Deliberately does NOT touch account_status or log the user out —
 * unlike EnsureAccountIsActive, a restricted account can still sign in,
 * see its balance, view History, manage linked accounts/cards, and use
 * Support — it just can't send, withdraw, top up, or pay a bill. Skipped
 * for admins, same reasoning as every other middleware here: nothing
 * ever restricts an admin's own account.
 */
class EnsureAccountIsNotRestricted
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && ! $user->isAdmin() && $user->isRestricted()) {
            return redirect()->route('dashboard')->with(
                'restricted',
                'Your account was restricted. Contact our support to help you fix that.'
            );
        }

        return $next($request);
    }
}
