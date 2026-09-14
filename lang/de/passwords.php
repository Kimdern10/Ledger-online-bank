<?php

/**
 * Laravel's own password-broker namespace (used by the classic email-link
 * password reset flow — see pages/auth/reset-password.blade.php and
 * Fortify::requestPasswordResetLinkView). Never published in this app
 * before, so these always rendered in English — same root cause as
 * validation.php, see that file's comment. The app's primary reset flow is
 * actually the 6-digit-code one (see authpage.php's status_/error_ keys in
 * this same directory), so these mainly cover the token-link route if it's
 * ever used.
 */
return [
    'reset' => 'Ihr Passwort wurde zurückgesetzt.',
    'sent' => 'Wir haben Ihnen den Link zum Zurücksetzen des Passworts per E-Mail gesendet.',
    'throttled' => 'Bitte warten Sie, bevor Sie es erneut versuchen.',
    'token' => 'Dieser Token zum Zurücksetzen des Passworts ist ungültig.',
    'user' => 'Wir können keinen Benutzer mit dieser E-Mail-Adresse finden.',
];
