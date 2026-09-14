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
    'failed' => 'Thông tin đăng nhập này không khớp với dữ liệu của chúng tôi.',
    'password' => 'Mật khẩu bạn nhập không đúng.',
    'throttle' => 'Bạn đã thử đăng nhập quá nhiều lần. Vui lòng thử lại sau :seconds giây.',
];
