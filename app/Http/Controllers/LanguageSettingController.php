<?php

namespace App\Http\Controllers;

use App\Support\Locale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LanguageSettingController extends Controller
{
    /**
     * Saves a signed-in user's choice from the dashboard's floating
     * language switcher (see layouts/app.blade.php's .dash-lang-float) —
     * the one place to change language now that there's no standalone
     * Settings > Language page. Also writes session('locale') so
     * SetLocale (see app/Http/Middleware) applies it starting with the
     * very next request, the same immediate-effect behavior updateGuest()
     * below has.
     *
     * Every language in App\Support\Locale::LANGUAGES has full translations for the
     * public Welcome page, Dashboard, Send, History, Receive, the Settings
     * hub and all of its sub-pages (Profile, Notifications, Password,
     * Budget, Transaction PIN, Delete account), identity/address
     * verification (kyc-required/kyc-verify/address-verify), App info, and
     * Support (see the lang/<code>/ files for each) — the pages a
     * signed-in user actually spends most of their time on. Everything
     * else in the app (Pay Bills, Cards, admin pages, and so on) is still
     * hardcoded English text regardless of language chosen, so picking a
     * language changes what you see on most pages but not all of them yet.
     */
    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'language' => ['required', Rule::in(array_keys(Locale::LANGUAGES))],
        ]);

        $user = $request->user();
        $user->language = $data['language'];
        $user->save();

        $request->session()->put('locale', $data['language']);

        return redirect()->back()->with('status', 'Language preference saved.');
    }

    /**
     * Handles the language selector in the public marketing header (see
     * layouts/partials/headers.blade.php), which is visible to signed-out
     * visitors and signed-in users alike — unlike update() above, there's
     * no guarantee a user is authenticated here.
     *
     * Always writes session('locale') — see SetLocale::handle(), which
     * checks that first before falling back to a signed-in user's saved
     * `language` column. That's what makes this take effect immediately,
     * on the very next request, for a guest and a signed-in visitor alike.
     *
     * Signed in: also saves straight to the account, exactly like
     * update() does, so picking a language from the marketing site and
     * picking one from the app's own switcher both end up as the same one
     * saved preference rather than two competing ones.
     *
     * Signed out: there's no account yet to save a preference to, so the
     * session write above is the only record of the choice — it applies
     * for the rest of this browsing session until they change it again or
     * create an account, at which point their saved account preference
     * takes over.
     */
    public function updateGuest(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'language' => ['required', Rule::in(array_keys(Locale::LANGUAGES))],
        ]);

        $user = $request->user();

        if ($user) {
            $user->language = $data['language'];
            $user->save();
        }

        $request->session()->put('locale', $data['language']);

        return redirect()->back();
    }
}
