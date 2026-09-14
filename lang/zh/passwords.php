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
    'reset' => '您的密码已重置。',
    'sent' => '我们已将密码重置链接发送至您的邮箱。',
    'throttled' => '请稍候再试。',
    'token' => '此密码重置令牌无效。',
    'user' => '我们找不到使用该邮箱地址的用户。',
];
