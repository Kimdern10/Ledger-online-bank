<?php

/**
 * Laravel/Fortify's own 3-key auth-failure namespace — used directly by
 * FortifyServiceProvider::configureActions() (the ValidationException thrown
 * for a frozen/suspended account uses Fortify::username() as the field, not
 * this file, but Fortify's own login attempt failure and its login-throttle
 * lockout message both resolve through here). Never published in this app
 * before, so these always rendered in English — same root cause as
 * validation.php, see that file's comment.
 */
return [
    'failed' => '입력하신 정보가 저희 기록과 일치하지 않습니다.',
    'password' => '입력하신 비밀번호가 올바르지 않습니다.',
    'throttle' => '로그인 시도가 너무 많습니다. :seconds초 후에 다시 시도해 주세요.',
];
