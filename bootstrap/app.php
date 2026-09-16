<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Every custom route-middleware slug used anywhere in routes/web.php
        // has to be registered here with an alias, or Laravel has no idea
        // what 'admin', 'not-admin', 'account.active', 'not-restricted',
        // 'onboarding.complete', or 'feature:can_x' actually mean — and a
        // request through an unregistered alias fails open (the check
        // silently never runs) rather than blocking anything, which is
        // exactly what "the user can still access the page even when it's
        // turned off" looks like.
        $middleware->alias([
            'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
            'not-admin' => \App\Http\Middleware\RedirectAdminFromUserArea::class,
            'account.active' => \App\Http\Middleware\EnsureAccountIsActive::class,
            'not-restricted' => \App\Http\Middleware\EnsureAccountIsNotRestricted::class,
            'onboarding.complete' => \App\Http\Middleware\EnsureOnboardingIsComplete::class,
            'feature' => \App\Http\Middleware\EnsureFeatureEnabled::class,
            'kyc-approved' => \App\Http\Middleware\EnsureKycApproved::class,
        ]);

        // Runs on every web request (not just an aliased route) so a signed
        // in user's saved language preference (Settings > Language) takes
        // effect everywhere at once, the moment it's set — see SetLocale.
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // A support-chat page's live "typing" indicator keeps a native
        // EventSource connection open (Accept: text/event-stream) that the
        // browser will not let us attach a custom header to. If a session
        // expires while that connection is still open, Laravel's default
        // unauthenticated() handling can't tell it apart from a normal page
        // view (only Accept: application/json gets that treatment) and does
        // what it does for any other guest hitting a protected page:
        // redirects to /login and remembers the stream URL as "where to send
        // them back after logging in" (session's url.intended). The next
        // successful login then honors that remembered URL — see
        // FortifyServiceProvider's redirect()->intended(...) — landing the
        // browser on a raw event stream instead of the dashboard. This
        // stops that at the source: an EventSource request that hits an
        // expired-session 401 just gets a plain 401 response and is never
        // recorded as an intended redirect target. Every other request
        // (including this app's regular polling fetch() calls, which all
        // already send Accept: application/json) is completely unaffected.
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, \Illuminate\Http\Request $request) {
            if ($request->headers->get('Accept') === 'text/event-stream') {
                return response('', 401);
            }
        });
    })->create();
