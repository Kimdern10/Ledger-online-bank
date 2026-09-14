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
    'failed' => 'ये क्रेडेंशियल हमारे रिकॉर्ड से मेल नहीं खाते।',
    'password' => 'दिया गया पासवर्ड गलत है।',
    'throttle' => 'बहुत अधिक लॉगिन प्रयास। कृपया :seconds सेकंड में पुनः प्रयास करें।',
];
