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
    'reset' => 'Ваш пароль был сброшен.',
    'sent' => 'Мы отправили вам ссылку для сброса пароля по электронной почте.',
    'throttled' => 'Пожалуйста, подождите, прежде чем повторить попытку.',
    'token' => 'Этот токен для сброса пароля недействителен.',
    'user' => 'Мы не можем найти пользователя с таким адресом электронной почты.',
];
