<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Dashboard, Onboarding, and Pay Bills are the consumer side of the app —
 * an admin account has no personal balance, bills, or onboarding profile
 * of its own to sit in there. Without this, an admin could still type
 * /dashboard into the address bar and land on a page that isn't really
 * "theirs" (or worse, get bounced into the 5-step onboarding wizard, which
 * an admin account was never meant to go through — see
 * EnsureOnboardingIsComplete).
 *
 * This runs before those checks and simply sends any is_admin account
 * straight to /admin/dashboard instead. Regular users pass through
 * untouched.
 */
class RedirectAdminFromUserArea
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        return $next($request);
    }
}
