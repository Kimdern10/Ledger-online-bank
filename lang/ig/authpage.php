<?php

return [
    // ---- Shared auth-layout decorative side panel (layouts/auth/simple.blade.php)
    // — shown on every auth page (login, register, forgot/reset password, 2FA,
    // confirm password, verify email). "Ledger" itself stays untranslated
    // everywhere, same convention as the rest of the app.
    'panel_eyebrow' => 'Ọrụ ụlọ akụ, edoziri nke ọma',
    'panel_headline_line1' => 'Dollar ọ bụla,',
    'panel_headline_accent' => 'a na-akọ ya nke ọma.',
    'panel_sub' => 'Mepee akaụntụ, zipụ ego, ma too nkwụnye gị n\'otu ndekọ dị jụụ, bụrụkwa eziokwu.',
    'mock_balance_label' => 'Ihe fọdụrụ',
    'mock_account_label' => 'Akaụntụ · Nlele',
    'entry_fdic' => 'FDIC kwadoro ya ruo $250,000',
    'entry_no_fees' => 'Enweghị ụgwọ zoro ezo, mgbe ọ bụla',
    'entry_instant' => 'Nnyefe ozugbo, awa 24 ụbọchị 7',

    // ---- Shared field labels (reused across several forms below)
    'field_email' => 'Adreesị ozi-e',
    'field_email_short' => 'Ozi-e',
    'field_password' => 'Okwuntughe',
    'field_confirm_password' => 'Kwenye okwuntughe',

    // ---- Login (pages/auth/login.blade.php)
    'login_title' => 'Banye',
    'login_heading' => 'Banye n\'akaụntụ gị',
    'login_description' => 'Tinye ozi-e na okwuntughe gị n\'okpuru iji banye',
    'forgot_password_link' => 'Ị chefuru okwuntughe gị?',
    'remember_me' => 'Cheta m',
    'no_account' => 'Ị nweghị akaụntụ?',
    'sign_up' => 'Debanye aha',
    'passkey_signin' => 'Jiri passkey banye',
    'passkey_authenticating' => 'Na-akwado nkwenye...',
    'passkey_or_email' => 'Ma ọ bụ gaa n\'ihu na ozi-e',

    // ---- Register (pages/auth/register.blade.php)
    'register_title' => 'Mepee akaụntụ gị',
    'register_subtitle' => 'Mepee akaụntụ Ledger n\'ime nkeji ole na ole.',
    'field_first_name' => 'Aha Mbụ',
    'field_last_name' => 'Aha Ikpeazụ',
    'field_middle_name' => 'Aha Etiti (nhọrọ)',
    'field_phone' => 'Nọmba Ekwentị (nhọrọ)',
    'register_button' => 'Mepee akaụntụ',
    'already_have_account' => 'Ị nweelarị akaụntụ?',

    // ---- Confirm password (pages/auth/confirm-password.blade.php)
    'confirm_password_title' => 'Kwenye okwuntughe',
    'confirm_password_description' => 'Nke a bụ ebe echekwabara nke ngwa a. Biko kwenye okwuntughe gị tupu ị gaa n\'ihu.',
    'confirm_with_passkey' => 'Jiri passkey kwenye',
    'confirming' => 'Na-akwenye...',
    'or_confirm_with_password' => 'Ma ọ bụ jiri okwuntughe kwenye',
    'confirm_button' => 'Kwenye',

    // ---- Forgot / reset password — the live 6-digit-code flow
    // (livewire/auth/forgot-password-code.blade.php, both steps)
    'forgot_password_title' => 'Chefuru okwuntughe',
    'forgot_password_description' => 'Tinye ozi-e gị anyị ga-ezitekwara gị koodu nwere ọnụọgụ isii iji hazigharịa ya.',
    'send_reset_code' => 'Zipu koodu mgbanwe',
    'or_return_to' => 'Ma ọ bụ, laghachi na',
    'log_in_lowercase' => 'banye',
    'enter_your_code' => 'Tinye koodu gị',
    'code_sent_to_prefix' => 'Tinye koodu nwere ọnụọgụ isii anyị zitere na',
    'code_sent_to_suffix' => 'ma họrọ okwuntughe ọhụrụ.',
    'field_reset_code' => 'Koodu mgbanwe',
    'field_new_password' => 'Okwuntughe ọhụrụ',
    'field_confirm_new_password' => 'Kwenye okwuntughe ọhụrụ',
    'use_different_email' => 'Jiri ozi-e ọzọ',
    'resend_code' => 'Zigharịa koodu',
    'status_code_sent_known' => "Ọ bụrụ na akaụntụ dị maka :email, anyị ezitela koodu nwere ọnụọgụ isii na ya.",
    'status_code_resent' => 'E zigharịala koodu ọhụrụ, ọ bụrụ na ozi-e ahụ nwere akaụntụ.',
    'error_code_invalid' => 'Koodu ahụ ezighị ezi ma ọ bụ agwụla. Rịọ koodu ọhụrụ n\'okpuru.',
    'status_password_reset_done' => 'A hazigharịla okwuntughe gị. Jiri okwuntughe ọhụrụ gị banye.',
    'js_code_expires_in' => 'Koodu ga-agwụ n\'ime',
    'js_code_expired_resend' => 'Koodu agwụla. Jiri "Zigharịa koodu" n\'okpuru.',

    // ---- Reset password — the classic email-link flow (pages/auth/reset-password.blade.php)
    'reset_password_title' => 'Hazigharịa okwuntughe',
    'reset_password_description' => 'Biko tinye okwuntughe ọhụrụ gị n\'okpuru',

    // ---- Two-factor challenge (pages/auth/two-factor-challenge.blade.php)
    'two_factor_title' => 'Nkwenye ụzọ abụọ',
    'auth_code_title' => 'Koodu nkwenye',
    'auth_code_description' => 'Tinye koodu nkwenye nke ngwa nkwenye gị nyere.',
    'field_otp_code' => 'Koodu OTP',
    'recovery_code_title' => 'Koodu mgbake',
    'recovery_code_description' => 'Biko kwenye ohere ịbanye n\'akaụntụ gị site na ịtinye otu n\'ime koodu mgbake mberede gị.',
    'continue_button' => 'Gaa n\'ihu',
    'or_you_can' => 'ma ọ bụ ị nwere ike',
    'login_using_recovery_code' => 'banye jiri koodu mgbake',
    'login_using_auth_code' => 'banye jiri koodu nkwenye',

    // ---- Verify email — the live 6-digit-code flow
    // (resources/views/auth/verify-email-page.blade.php + livewire/auth/verify-code.blade.php)
    'verify_email_title' => 'Kwenye ozi-e gị',
    'verify_sent_prefix' => 'Anyị zitere koodu nwere ọnụọgụ isii na',
    'verify_sent_suffix' => 'Tinye ya n\'okpuru iji gaa n\'ihu.',
    'field_verification_code' => 'Koodu nkwenye',
    'verify_button' => 'Kwenye',
    'status_new_code_sent' => 'E zitela koodu ọhụrụ n\'ozi-e gị.',
    'status_account_verified' => 'Akwadoola akaụntụ gị. Banye ka ị malite.',
    'js_code_expired_request' => 'Koodu agwụla. Rịọ koodu ọhụrụ n\'okpuru.',
    'js_no_active_code' => 'Enweghị koodu na-arụ ọrụ dị. Jiri "Zigharịa koodu" n\'okpuru nweta otu.',

    // ---- App\Models\User::accountBlockedMessage() — shown as the login-form
    // error for a frozen/suspended/disabled account, and mid-session by
    // EnsureAccountIsActive middleware.
    'account_frozen' => 'Akaụntụ a akpọchiri. Kpọtụrụ ndị nkwado maka enyemaka.',
    'account_suspended' => 'Akaụntụ a kwụsịtụrụ. Kpọtụrụ ndị nkwado maka enyemaka.',
    'account_disabled' => 'Akaụntụ a agbaghawo.',
    'account_blocked_default' => 'Akaụntụ a enweghị ike ịbanye ugbu a.',
];
