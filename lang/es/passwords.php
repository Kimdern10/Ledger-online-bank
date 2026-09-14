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
    'reset' => 'Su contraseña ha sido restablecida.',
    'sent' => 'Le hemos enviado por correo electrónico el enlace para restablecer su contraseña.',
    'throttled' => 'Espere antes de volver a intentarlo.',
    'token' => 'Este token de restablecimiento de contraseña no es válido.',
    'user' => 'No podemos encontrar un usuario con esa dirección de correo electrónico.',
];
