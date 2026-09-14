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
    'reset' => '비밀번호가 재설정되었습니다.',
    'sent' => '비밀번호 재설정 링크를 이메일로 보내드렸습니다.',
    'throttled' => '다시 시도하기 전에 잠시 기다려 주세요.',
    'token' => '비밀번호 재설정 토큰이 유효하지 않습니다.',
    'user' => '해당 이메일 주소를 가진 사용자를 찾을 수 없습니다.',
];
