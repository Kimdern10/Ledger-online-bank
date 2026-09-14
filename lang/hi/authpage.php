<?php

return [
    // ---- Shared auth-layout decorative side panel (layouts/auth/simple.blade.php)
    // — shown on every auth page (login, register, forgot/reset password, 2FA,
    // confirm password, verify email). "Ledger" itself stays untranslated
    // everywhere, same convention as the rest of the app.
    'panel_eyebrow' => 'बैंकिंग, व्यवस्थित रूप में',
    'panel_headline_line1' => 'हर डॉलर,',
    'panel_headline_accent' => 'हिसाब में।',
    'panel_sub' => 'खाता खोलें, पैसे भेजें, और एक शांत, ईमानदार लेजर में अपना बैलेंस बढ़ाएँ।',
    'mock_balance_label' => 'बैलेंस',
    'mock_account_label' => 'खाता · चेकिंग',
    'entry_fdic' => '$250,000 तक FDIC-बीमित',
    'entry_no_fees' => 'कभी कोई छिपा हुआ शुल्क नहीं',
    'entry_instant' => 'तुरंत ट्रांसफर, 24/7',

    // ---- Shared field labels (reused across several forms below)
    'field_email' => 'ईमेल पता',
    'field_email_short' => 'ईमेल',
    'field_password' => 'पासवर्ड',
    'field_confirm_password' => 'पासवर्ड की पुष्टि करें',

    // ---- Login (pages/auth/login.blade.php)
    'login_title' => 'लॉग इन करें',
    'login_heading' => 'अपने खाते में लॉग इन करें',
    'login_description' => 'लॉग इन करने के लिए नीचे अपना ईमेल और पासवर्ड दर्ज करें',
    'forgot_password_link' => 'अपना पासवर्ड भूल गए?',
    'remember_me' => 'मुझे याद रखें',
    'no_account' => 'खाता नहीं है?',
    'sign_up' => 'साइन अप करें',
    'passkey_signin' => 'पासकी से साइन इन करें',
    'passkey_authenticating' => 'प्रमाणीकरण हो रहा है...',
    'passkey_or_email' => 'या ईमेल से जारी रखें',

    // ---- Register (pages/auth/register.blade.php)
    'register_title' => 'अपना खाता बनाएँ',
    'register_subtitle' => 'कुछ ही मिनटों में एक Ledger खाता खोलें।',
    'field_first_name' => 'पहला नाम',
    'field_last_name' => 'अंतिम नाम',
    'field_middle_name' => 'मध्य नाम (वैकल्पिक)',
    'field_phone' => 'फ़ोन (वैकल्पिक)',
    'register_button' => 'खाता बनाएँ',
    'already_have_account' => 'पहले से खाता है?',

    // ---- Confirm password (pages/auth/confirm-password.blade.php)
    'confirm_password_title' => 'पासवर्ड की पुष्टि करें',
    'confirm_password_description' => 'यह एप्लिकेशन का एक सुरक्षित क्षेत्र है। जारी रखने से पहले कृपया अपने पासवर्ड की पुष्टि करें।',
    'confirm_with_passkey' => 'पासकी से पुष्टि करें',
    'confirming' => 'पुष्टि हो रही है...',
    'or_confirm_with_password' => 'या पासवर्ड से पुष्टि करें',
    'confirm_button' => 'पुष्टि करें',

    // ---- Forgot / reset password — the live 6-digit-code flow
    // (livewire/auth/forgot-password-code.blade.php, both steps)
    'forgot_password_title' => 'पासवर्ड भूल गए',
    'forgot_password_description' => 'अपना ईमेल दर्ज करें और हम आपको इसे रीसेट करने के लिए एक 6-अंकों का कोड भेजेंगे।',
    'send_reset_code' => 'रीसेट कोड भेजें',
    'or_return_to' => 'या, वापस जाएँ',
    'log_in_lowercase' => 'लॉग इन',
    'enter_your_code' => 'अपना कोड दर्ज करें',
    'code_sent_to_prefix' => 'हमने जो 6-अंकों का कोड भेजा है उसे दर्ज करें',
    'code_sent_to_suffix' => 'और एक नया पासवर्ड चुनें।',
    'field_reset_code' => 'रीसेट कोड',
    'field_new_password' => 'नया पासवर्ड',
    'field_confirm_new_password' => 'नए पासवर्ड की पुष्टि करें',
    'use_different_email' => 'एक अलग ईमेल का उपयोग करें',
    'resend_code' => 'कोड फिर से भेजें',
    'status_code_sent_known' => 'यदि :email के लिए कोई खाता मौजूद है, तो हमने उस पर एक 6-अंकों का कोड भेज दिया है।',
    'status_code_resent' => 'यदि उस ईमेल का कोई खाता है, तो एक नया कोड भेज दिया गया है।',
    'error_code_invalid' => 'वह कोड अमान्य है या समय समाप्त हो चुका है। नीचे एक नया अनुरोध करें।',
    'status_password_reset_done' => 'आपका पासवर्ड रीसेट हो गया है। अपने नए पासवर्ड से लॉग इन करें।',
    'js_code_expires_in' => 'कोड समाप्त होगा',
    'js_code_expired_resend' => 'कोड समाप्त हो गया। नीचे "कोड फिर से भेजें" का उपयोग करें।',

    // ---- Reset password — the classic email-link flow (pages/auth/reset-password.blade.php)
    'reset_password_title' => 'पासवर्ड रीसेट करें',
    'reset_password_description' => 'कृपया नीचे अपना नया पासवर्ड दर्ज करें',

    // ---- Two-factor challenge (pages/auth/two-factor-challenge.blade.php)
    'two_factor_title' => 'दो-चरणीय प्रमाणीकरण',
    'auth_code_title' => 'प्रमाणीकरण कोड',
    'auth_code_description' => 'अपने ऑथेंटिकेटर ऐप द्वारा दिए गए प्रमाणीकरण कोड को दर्ज करें।',
    'field_otp_code' => 'OTP कोड',
    'recovery_code_title' => 'रिकवरी कोड',
    'recovery_code_description' => 'कृपया अपने आपातकालीन रिकवरी कोड में से एक दर्ज करके अपने खाते तक पहुँच की पुष्टि करें।',
    'continue_button' => 'जारी रखें',
    'or_you_can' => 'या आप कर सकते हैं',
    'login_using_recovery_code' => 'रिकवरी कोड का उपयोग करके लॉग इन करें',
    'login_using_auth_code' => 'प्रमाणीकरण कोड का उपयोग करके लॉग इन करें',

    // ---- Verify email — the live 6-digit-code flow
    // (resources/views/auth/verify-email-page.blade.php + livewire/auth/verify-code.blade.php)
    'verify_email_title' => 'अपने ईमेल की पुष्टि करें',
    'verify_sent_prefix' => 'हमने एक 6-अंकों का कोड भेजा है',
    'verify_sent_suffix' => 'जारी रखने के लिए इसे नीचे दर्ज करें।',
    'field_verification_code' => 'सत्यापन कोड',
    'verify_button' => 'सत्यापित करें',
    'status_new_code_sent' => 'आपके ईमेल पर एक नया कोड भेज दिया गया है।',
    'status_account_verified' => 'आपका खाता सत्यापित हो गया है। शुरू करने के लिए लॉग इन करें।',
    'js_code_expired_request' => 'कोड समाप्त हो गया। नीचे एक नया अनुरोध करें।',
    'js_no_active_code' => 'फ़ाइल में कोई सक्रिय कोड नहीं है। एक पाने के लिए नीचे "कोड फिर से भेजें" का उपयोग करें।',

    // ---- App\Models\User::accountBlockedMessage() — shown as the login-form
    // error for a frozen/suspended/disabled account, and mid-session by
    // EnsureAccountIsActive middleware.
    'account_frozen' => 'यह खाता फ्रीज़ कर दिया गया है। सहायता के लिए सपोर्ट से संपर्क करें।',
    'account_suspended' => 'यह खाता निलंबित कर दिया गया है। सहायता के लिए सपोर्ट से संपर्क करें।',
    'account_disabled' => 'यह खाता निष्क्रिय कर दिया गया है।',
    'account_blocked_default' => 'यह खाता अभी साइन इन नहीं कर सकता।',
];
