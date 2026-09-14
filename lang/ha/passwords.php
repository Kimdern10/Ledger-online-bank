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
    'reset' => 'An sake saita kalmar sirrinka.',
    'sent' => 'Mun aiko maka hanyar sake saita kalmar sirri ta imel.',
    'throttled' => 'Da fatan za a jira kafin sake gwadawa.',
    'token' => 'Wannan alamar sake saita kalmar sirri ba ingantacciya ba ce.',
    'user' => 'Ba mu iya samun mai amfani da wannan adireshin imel ba.',
];
