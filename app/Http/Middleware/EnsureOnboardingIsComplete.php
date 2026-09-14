<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOnboardingIsComplete
{
    /**
     * Sends anyone who hasn't finished the 5-step wizard back to it,
     * instead of letting them reach the dashboard. Runs after 'auth' and
     * 'verified', so $request->user() is always a real, verified user here.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && ! $user->profile?->onboarding_completed) {
            return redirect()->route('onboarding');
        }

        return $next($request);
    }
}
