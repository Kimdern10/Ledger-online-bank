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
    'reset' => 'Votre mot de passe a été réinitialisé.',
    'sent' => 'Nous vous avons envoyé par e-mail le lien de réinitialisation de votre mot de passe.',
    'throttled' => 'Veuillez patienter avant de réessayer.',
    'token' => 'Ce jeton de réinitialisation de mot de passe n\'est pas valide.',
    'user' => 'Nous ne trouvons aucun utilisateur avec cette adresse e-mail.',
];
