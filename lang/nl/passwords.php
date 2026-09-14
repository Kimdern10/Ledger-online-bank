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
    'reset' => 'Je wachtwoord is opnieuw ingesteld.',
    'sent' => 'We hebben je een e-mail gestuurd met de link om je wachtwoord opnieuw in te stellen.',
    'throttled' => 'Wacht even voordat je het opnieuw probeert.',
    'token' => 'Dit token voor het opnieuw instellen van het wachtwoord is ongeldig.',
    'user' => 'We kunnen geen gebruiker vinden met dat e-mailadres.',
];
