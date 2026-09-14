<?php

namespace App\Http\Middleware;

use App\Http\Controllers\LanguageSettingController;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Applies whichever language was chosen most recently on THIS browser to
 * every request — either from the marketing site's language selector (a
 * signed-out visitor, or a signed-in one who hasn't bothered with Settings)
 * or from Settings > Language for a signed-in user (see
 * LanguageSettingController).
 *
 * Checked in this order:
 *   1. session('locale') — set by both LanguageSettingController::update()
 *      and ::updateGuest(), so it's always the most recent explicit choice
 *      regardless of which picker made it.
 *   2. The signed-in user's saved `language` column — the durable
 *      preference from Settings, for a request where nothing has been
 *      picked yet this session (a fresh browser, or session data cleared).
 *   3. The app's default locale (config('app.locale'), 'en') — a guest who
 *      has never touched either picker.
 *
 * Only ever applies a value from LanguageSettingController::LANGUAGES, not
 * just "is this non-empty" — a corrupted or stale value in the session or
 * the language column should fall back to the default locale rather than
 * crash app()->setLocale() or silently render nothing.
 */
class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->session()->get('locale');

        if (! is_string($locale) || ! array_key_exists($locale, LanguageSettingController::LANGUAGES)) {
            $user = $request->user();
            $locale = ($user && array_key_exists($user->language, LanguageSettingController::LANGUAGES))
                ? $user->language
                : null;
        }

        if ($locale) {
            app()->setLocale($locale);

            // Without this, "2 hours ago" on a notification (Carbon's
            // diffForHumans(), used by dashboard.blade.php) would stay in
            // English even after every other string on the page switched —
            // Carbon's locale doesn't follow app()->setLocale() on its own.
            // Carbon ships its own translations for some of these locales;
            // any locale it doesn't recognize just falls back to Carbon's
            // default (English) for this one piece of text.
            Carbon::setLocale($locale);
        }

        return $next($request);
    }
}
