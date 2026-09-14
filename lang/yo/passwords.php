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
    'reset' => 'A ti ṣe àtúnṣe ọ̀rọ̀ ìpamọ́ rẹ.',
    'sent' => 'A ti fi ọ̀nà àtúnṣe ọ̀rọ̀ ìpamọ́ rẹ ránṣẹ́ sí ímeèlì rẹ.',
    'throttled' => 'Jọ̀wọ́ dúró díẹ̀ kí o tó tún gbìyànjú.',
    'token' => 'Àmì àtúnṣe ọ̀rọ̀ ìpamọ́ yìí kò tọ́.',
    'user' => 'A kò rí olùmúlò kankan pẹ̀lú àdírẹ́sì ímeèlì yẹn.',
];
