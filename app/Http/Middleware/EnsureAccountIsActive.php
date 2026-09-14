<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Fortify's authenticateUsing() (see FortifyServiceProvider) only checks
 * account_status at the moment someone logs in — it has no way to reach a
 * session that's already open. This middleware is what closes that gap: it
 * runs on every request to the routes it's attached to and re-checks
 * account_status every single time, not just at login. So if an admin
 * freezes/suspends/disables someone who is already logged in, this is what
 * actually signs them out right away — without it, that person would stay
 * fully logged in, with a working session, until they happened to log out
 * and try to log back in on their own.
 *
 * Skipped entirely for admins (isAdmin()) — nothing in this app currently
 * changes an admin's own account_status, but this keeps that from ever
 * being able to lock an admin out of their own dashboard by accident.
 */
class EnsureAccountIsActive
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && ! $user->isAdmin() && ! $user->canSignIn()) {
            $message = $user->accountBlockedMessage();

            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors(['email' => $message]);
        }

        return $next($request);
    }
}
