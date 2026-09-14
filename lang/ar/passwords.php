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
    'reset' => 'تمت إعادة تعيين كلمة المرور الخاصة بك.',
    'sent' => 'لقد أرسلنا لك رابط إعادة تعيين كلمة المرور عبر البريد الإلكتروني.',
    'throttled' => 'يُرجى الانتظار قبل إعادة المحاولة.',
    'token' => 'رمز إعادة تعيين كلمة المرور هذا غير صالح.',
    'user' => 'تعذر العثور على مستخدم بهذا البريد الإلكتروني.',
];
