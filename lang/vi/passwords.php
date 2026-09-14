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
    'reset' => 'Mật khẩu của bạn đã được đặt lại.',
    'sent' => 'Chúng tôi đã gửi email chứa liên kết đặt lại mật khẩu cho bạn.',
    'throttled' => 'Vui lòng đợi trước khi thử lại.',
    'token' => 'Mã đặt lại mật khẩu này không hợp lệ.',
    'user' => 'Chúng tôi không thể tìm thấy người dùng với địa chỉ email đó.',
];
