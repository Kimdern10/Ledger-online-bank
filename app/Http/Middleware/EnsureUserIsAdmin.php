<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Blocks every /admin/* route to anyone whose account isn't flagged
     * is_admin — including a logged-in regular user who just types the URL.
     * Runs after 'auth', so $request->user() is always a real logged-in
     * user here; this only adds the admin check on top of that.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || ! $request->user()->isAdmin()) {
            abort(403, 'You do not have access to the admin area.');
        }

        return $next($request);
    }
}
