<?php

namespace App\Support;

/**
 * The only languages this app will ever accept — kept here as the single
 * source of truth so the marketing header's selector, the dashboard's
 * floating switcher, LanguageSettingController's validation rule, SetLocale
 * middleware, and User::languageLabel() can't quietly drift out of sync
 * with each other.
 *
 * Igbo, Yorùbá, and Hausa were removed from this list (they used to sit
 * right after Português) at the user's request — not a translation-quality
 * decision, just narrowing the list. Their lang/ files (lang/ig, lang/yo,
 * lang/ha) were left in place rather than deleted in case they're wanted
 * back later; a value here is the only thing that makes a locale selectable
 * or valid input to LanguageSettingController::update()/updateGuest().
 * Anyone with 'ig'/'yo'/'ha' already saved as their account's `language`
 * falls back to the app's default locale (English) automatically the next
 * time SetLocale runs, since it only ever applies a value that's still a
 * key in this array.
 *
 * Moved here from LanguageSettingController (a controller class) so that
 * non-HTTP code — the User model, SetLocale middleware — never has to
 * import a controller just to read a shared constant off it. Every place
 * that used to reach into LanguageSettingController::LANGUAGES now reads
 * Locale::LANGUAGES instead; the controller keeps its own validation logic
 * but no longer owns this list.
 */
class Locale
{
    public const LANGUAGES = [
        'en' => 'English',
        'es' => 'Español',
        'fr' => 'Français',
        'pt' => 'Português',
        'de' => 'Deutsch',
        'it' => 'Italiano',
        'ar' => 'العربية',
        'zh' => '中文',
        'hi' => 'हिन्दी',
        'sw' => 'Kiswahili',
        'ru' => 'Русский',
        'tr' => 'Türkçe',
        'ja' => '日本語',
        'ko' => '한국어',
        'vi' => 'Tiếng Việt',
        'id' => 'Bahasa Indonesia',
        'pl' => 'Polski',
        'nl' => 'Nederlands',
    ];
}
