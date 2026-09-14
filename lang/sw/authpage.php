<?php

return [
    // ---- Shared auth-layout decorative side panel (layouts/auth/simple.blade.php)
    // — shown on every auth page (login, register, forgot/reset password, 2FA,
    // confirm password, verify email). "Ledger" itself stays untranslated
    // everywhere, same convention as the rest of the app.
    'panel_eyebrow' => 'Benki, ikiwa katika mpangilio',
    'panel_headline_line1' => 'Kila dola,',
    'panel_headline_accent' => 'inayohesabiwa.',
    'panel_sub' => 'Fungua akaunti, hamisha fedha, na kuza salio lako katika daftari moja tulivu na la kuaminika.',
    'mock_balance_label' => 'Salio',
    'mock_account_label' => 'Akaunti · Matumizi',
    'entry_fdic' => 'Imekingwa na FDIC hadi $250,000',
    'entry_no_fees' => 'Hakuna ada zilizofichwa, kamwe',
    'entry_instant' => 'Uhamisho wa papo hapo, saa 24/7',

    // ---- Shared field labels (reused across several forms below)
    'field_email' => 'Barua pepe',
    'field_email_short' => 'Barua pepe',
    'field_password' => 'Nenosiri',
    'field_confirm_password' => 'Thibitisha nenosiri',

    // ---- Login (pages/auth/login.blade.php)
    'login_title' => 'Ingia',
    'login_heading' => 'Ingia kwenye akaunti yako',
    'login_description' => 'Weka barua pepe na nenosiri lako hapa chini ili kuingia',
    'forgot_password_link' => 'Umesahau nenosiri lako?',
    'remember_me' => 'Nikumbuke',
    'no_account' => 'Huna akaunti?',
    'sign_up' => 'Jisajili',
    'passkey_signin' => 'Ingia kwa kutumia passkey',
    'passkey_authenticating' => 'Inathibitisha...',
    'passkey_or_email' => 'Au endelea kwa barua pepe',

    // ---- Register (pages/auth/register.blade.php)
    'register_title' => 'Fungua akaunti yako',
    'register_subtitle' => 'Fungua akaunti ya Ledger kwa dakika chache.',
    'field_first_name' => 'Jina la Kwanza',
    'field_last_name' => 'Jina la Ukoo',
    'field_middle_name' => 'Jina la Kati (hiari)',
    'field_phone' => 'Simu (hiari)',
    'register_button' => 'Fungua akaunti',
    'already_have_account' => 'Tayari una akaunti?',

    // ---- Confirm password (pages/auth/confirm-password.blade.php)
    'confirm_password_title' => 'Thibitisha nenosiri',
    'confirm_password_description' => 'Hii ni sehemu salama ya programu. Tafadhali thibitisha nenosiri lako kabla ya kuendelea.',
    'confirm_with_passkey' => 'Thibitisha kwa passkey',
    'confirming' => 'Inathibitisha...',
    'or_confirm_with_password' => 'Au thibitisha kwa nenosiri',
    'confirm_button' => 'Thibitisha',

    // ---- Forgot / reset password — the live 6-digit-code flow
    // (livewire/auth/forgot-password-code.blade.php, both steps)
    'forgot_password_title' => 'Umesahau nenosiri',
    'forgot_password_description' => "Weka barua pepe yako nasi tutakutumia msimbo wa tarakimu 6 ili kulirejesha.",
    'send_reset_code' => 'Tuma msimbo wa kurejesha',
    'or_return_to' => 'Au, rudi',
    'log_in_lowercase' => 'ingia',
    'enter_your_code' => 'Weka msimbo wako',
    'code_sent_to_prefix' => 'Weka msimbo wa tarakimu 6 tuliokutumia kwa',
    'code_sent_to_suffix' => 'kisha uchague nenosiri jipya.',
    'field_reset_code' => 'Msimbo wa kurejesha',
    'field_new_password' => 'Nenosiri jipya',
    'field_confirm_new_password' => 'Thibitisha nenosiri jipya',
    'use_different_email' => 'Tumia barua pepe nyingine',
    'resend_code' => 'Tuma tena msimbo',
    'status_code_sent_known' => "Ikiwa kuna akaunti ya :email, tumeshatuma msimbo wa tarakimu 6 kwenye barua pepe hiyo.",
    'status_code_resent' => 'Msimbo mpya umetumwa, ikiwa barua pepe hiyo ina akaunti.',
    'error_code_invalid' => 'Msimbo huo si sahihi au umeisha muda wake. Omba mwingine hapa chini.',
    'status_password_reset_done' => 'Nenosiri lako limerejeshwa. Ingia kwa nenosiri lako jipya.',
    'js_code_expires_in' => 'Msimbo unaisha muda baada ya',
    'js_code_expired_resend' => 'Muda wa msimbo umeisha. Tumia "Tuma tena msimbo" hapa chini.',

    // ---- Reset password — the classic email-link flow (pages/auth/reset-password.blade.php)
    'reset_password_title' => 'Rejesha nenosiri',
    'reset_password_description' => 'Tafadhali weka nenosiri lako jipya hapa chini',

    // ---- Two-factor challenge (pages/auth/two-factor-challenge.blade.php)
    'two_factor_title' => 'Uthibitishaji wa hatua mbili',
    'auth_code_title' => 'Msimbo wa uthibitishaji',
    'auth_code_description' => 'Weka msimbo wa uthibitishaji unaotolewa na programu yako ya uthibitishaji.',
    'field_otp_code' => 'Msimbo wa OTP',
    'recovery_code_title' => 'Msimbo wa dharura',
    'recovery_code_description' => 'Tafadhali thibitisha ufikiaji wa akaunti yako kwa kuweka mmoja wa misimbo yako ya dharura.',
    'continue_button' => 'Endelea',
    'or_you_can' => 'au unaweza',
    'login_using_recovery_code' => 'ingia ukitumia msimbo wa dharura',
    'login_using_auth_code' => 'ingia ukitumia msimbo wa uthibitishaji',

    // ---- Verify email — the live 6-digit-code flow
    // (resources/views/auth/verify-email-page.blade.php + livewire/auth/verify-code.blade.php)
    'verify_email_title' => 'Thibitisha barua pepe yako',
    'verify_sent_prefix' => 'Tumetuma msimbo wa tarakimu 6 kwa',
    'verify_sent_suffix' => 'Uweke hapa chini ili kuendelea.',
    'field_verification_code' => 'Msimbo wa uthibitishaji',
    'verify_button' => 'Thibitisha',
    'status_new_code_sent' => 'Msimbo mpya umetumwa kwenye barua pepe yako.',
    'status_account_verified' => 'Akaunti yako imethibitishwa. Ingia ili kuanza.',
    'js_code_expired_request' => 'Muda wa msimbo umeisha. Omba mwingine hapa chini.',
    'js_no_active_code' => 'Hakuna msimbo unaotumika kwa sasa. Tumia "Tuma tena msimbo" hapa chini kupata mmoja.',

    // ---- App\Models\User::accountBlockedMessage() — shown as the login-form
    // error for a frozen/suspended/disabled account, and mid-session by
    // EnsureAccountIsActive middleware.
    'account_frozen' => 'Akaunti hii imegandishwa. Wasiliana na msaada kwa usaidizi.',
    'account_suspended' => 'Akaunti hii imesimamishwa. Wasiliana na msaada kwa usaidizi.',
    'account_disabled' => 'Akaunti hii imezimwa.',
    'account_blocked_default' => 'Akaunti hii haiwezi kuingia kwa sasa.',
];
