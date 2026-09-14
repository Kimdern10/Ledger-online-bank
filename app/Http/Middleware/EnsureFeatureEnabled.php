<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Backs the "Feature access" toggles on the admin per-user page (Send,
 * Pay Bills, Link Account, Withdraw, Top Up, Cards). Each of those routes
 * is tagged ->middleware('feature:can_x'), where can_x is the exact
 * boolean column on users this checks. The moment an admin switches one
 * off, that page stops opening for this user right away.
 *
 * A brand-new account now starts with only Send money turned on (see
 * CreateNewUser::create()) — everything else stays off until an admin
 * deliberately enables it. Since that's the normal, expected state for
 * most new users rather than a rare edge case, blocking here now renders
 * a proper branded page (feature-unavailable.blade.php) instead of
 * Laravel's bare default error page — a blank/generic error box on a
 * brand-new account is exactly what would make this look like a broken
 * or fraudulent site rather than an account still being set up.
 *
 * Skipped for admins (isAdmin()) and for guests ($user is null on a route
 * that isn't behind 'auth') — this only ever blocks a logged-in, regular
 * user from a feature that's specifically off for them.
 */
class EnsureFeatureEnabled
{
    // Maps each feature flag to the translation key (in lang/*/feature.php)
    // that holds its display label. Translated at request time via __()
    // below, rather than stored as literal English here, so the
    // feature-unavailable page shows in the viewer's own language.
    private const FEATURE_LABELS = [
        'can_send' => 'feature.send_money',
        'can_pay_bills' => 'feature.pay_bills',
        'can_link_account' => 'feature.link_account',
        'can_withdraw' => 'feature.withdraw',
        'can_top_up' => 'feature.top_up',
        'can_manage_cards' => 'feature.manage_cards',
        'can_receive' => 'feature.add_money',
        'can_scan' => 'feature.scan_to_pay',
    ];

    public function handle(Request $request, Closure $next, string $flag): Response
    {
        $user = $request->user();

        if ($user && ! $user->isAdmin() && ! (bool) $user->{$flag}) {
            return response()->view('feature-unavailable', [
                'feature' => isset(self::FEATURE_LABELS[$flag]) ? __(self::FEATURE_LABELS[$flag]) : __('feature.this_feature'),
            ], 403);
        }

        return $next($request);
    }
}
