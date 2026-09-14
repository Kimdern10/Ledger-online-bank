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
    'reset' => 'आपका पासवर्ड रीसेट कर दिया गया है।',
    'sent' => 'हमने आपका पासवर्ड रीसेट लिंक ईमेल कर दिया है।',
    'throttled' => 'कृपया पुनः प्रयास करने से पहले प्रतीक्षा करें।',
    'token' => 'यह पासवर्ड रीसेट टोकन अमान्य है।',
    'user' => 'हमें उस ईमेल पते वाला कोई उपयोगकर्ता नहीं मिला।',
];
