<?php

return [
    // ---- Shared auth-layout decorative side panel (layouts/auth/simple.blade.php)
    // — shown on every auth page (login, register, forgot/reset password, 2FA,
    // confirm password, verify email). "Ledger" itself stays untranslated
    // everywhere, same convention as the rest of the app.
    'panel_eyebrow' => 'Banki, cikin tsari',
    'panel_headline_line1' => 'Kowane dala,',
    'panel_headline_accent' => 'ana lissafta shi.',
    'panel_sub' => "Bude asusu, tura kudi, kuma ka bunkasa ma'aunin ka a cikin littafin lissafi mai natsuwa da gaskiya.",
    'mock_balance_label' => "Ma'auni",
    'mock_account_label' => 'Asusu · Na Yau da Kullum',
    'entry_fdic' => 'An inshora ta FDIC har zuwa $250,000',
    'entry_no_fees' => 'Babu kudaden ɓoye, koyaushe',
    'entry_instant' => 'Canja kudi nan take, awa 24 kowace rana',

    // ---- Shared field labels (reused across several forms below)
    'field_email' => 'Adireshin imel',
    'field_email_short' => 'Imel',
    'field_password' => 'Kalmar sirri',
    'field_confirm_password' => 'Tabbatar da kalmar sirri',

    // ---- Login (pages/auth/login.blade.php)
    'login_title' => 'Shiga',
    'login_heading' => 'Shiga cikin asusunka',
    'login_description' => 'Shigar da imel da kalmar sirrinka a ƙasa domin shiga',
    'forgot_password_link' => 'Ka manta da kalmar sirri?',
    'remember_me' => 'Ka tuna da ni',
    'no_account' => 'Ba ka da asusu?',
    'sign_up' => 'Yi rijista',
    'passkey_signin' => 'Shiga da maɓallin shiga (passkey)',
    'passkey_authenticating' => 'Ana tabbatarwa...',
    'passkey_or_email' => 'Ko ci gaba da imel',

    // ---- Register (pages/auth/register.blade.php)
    'register_title' => 'Ƙirƙiri asusunka',
    'register_subtitle' => 'Bude asusun Ledger cikin mintuna kaɗan.',
    'field_first_name' => 'Sunan Farko',
    'field_last_name' => 'Sunan Iyali',
    'field_middle_name' => 'Sunan Tsakiya (na zaɓi)',
    'field_phone' => 'Waya (na zaɓi)',
    'register_button' => 'Ƙirƙiri asusu',
    'already_have_account' => 'Kana da asusu tuni?',

    // ---- Confirm password (pages/auth/confirm-password.blade.php)
    'confirm_password_title' => 'Tabbatar da kalmar sirri',
    'confirm_password_description' => 'Wannan wuri ne mai tsaro na aikace-aikacen. Da fatan za a tabbatar da kalmar sirrinka kafin ci gaba.',
    'confirm_with_passkey' => 'Tabbatar da maɓallin shiga',
    'confirming' => 'Ana tabbatarwa...',
    'or_confirm_with_password' => 'Ko tabbatar da kalmar sirri',
    'confirm_button' => 'Tabbatar',

    // ---- Forgot / reset password — the live 6-digit-code flow
    // (livewire/auth/forgot-password-code.blade.php, both steps)
    'forgot_password_title' => 'Manta da kalmar sirri',
    'forgot_password_description' => "Shigar da imel ɗinka kuma za mu turo maka lambar sirri mai lamba 6 domin sake saita ta.",
    'send_reset_code' => 'Aika lambar sake saitawa',
    'or_return_to' => 'Ko, koma zuwa',
    'log_in_lowercase' => 'shiga',
    'enter_your_code' => 'Shigar da lambarka',
    'code_sent_to_prefix' => 'Shigar da lambar sirri mai lamba 6 da muka turo zuwa',
    'code_sent_to_suffix' => 'kuma zaɓi sabuwar kalmar sirri.',
    'field_reset_code' => 'Lambar sake saitawa',
    'field_new_password' => 'Sabuwar kalmar sirri',
    'field_confirm_new_password' => 'Tabbatar da sabuwar kalmar sirri',
    'use_different_email' => 'Yi amfani da wani imel',
    'resend_code' => 'Sake tura lambar',
    'status_code_sent_known' => 'Idan akwai asusu na :email, mun turo masa lambar sirri mai lamba 6.',
    'status_code_resent' => 'An turo sabuwar lambar, idan wannan imel yana da asusu.',
    'error_code_invalid' => 'Wannan lambar ba ta inganta ba ko kuwa ta ƙare. Nemi sabuwa a ƙasa.',
    'status_password_reset_done' => 'An sake saita kalmar sirrinka. Shiga da sabuwar kalmar sirrinka.',
    'js_code_expires_in' => 'Lambar za ta ƙare a cikin',
    'js_code_expired_resend' => 'Lambar ta ƙare. Yi amfani da "Sake tura lambar" a ƙasa.',

    // ---- Reset password — the classic email-link flow (pages/auth/reset-password.blade.php)
    'reset_password_title' => 'Sake saita kalmar sirri',
    'reset_password_description' => 'Da fatan za a shigar da sabuwar kalmar sirrinka a ƙasa',

    // ---- Two-factor challenge (pages/auth/two-factor-challenge.blade.php)
    'two_factor_title' => 'Tabbatarwa mai matakai biyu',
    'auth_code_title' => 'Lambar tabbatarwa',
    'auth_code_description' => 'Shigar da lambar tabbatarwa da aikace-aikacen tabbatarwarka ya bayar.',
    'field_otp_code' => 'Lambar OTP',
    'recovery_code_title' => 'Lambar dawowa',
    'recovery_code_description' => 'Da fatan za a tabbatar da samun dama zuwa asusunka ta hanyar shigar da ɗaya daga cikin lambobin dawowa na gaggawa.',
    'continue_button' => 'Ci gaba',
    'or_you_can' => 'ko kuma za ka iya',
    'login_using_recovery_code' => 'shiga ta amfani da lambar dawowa',
    'login_using_auth_code' => 'shiga ta amfani da lambar tabbatarwa',

    // ---- Verify email — the live 6-digit-code flow
    // (resources/views/auth/verify-email-page.blade.php + livewire/auth/verify-code.blade.php)
    'verify_email_title' => 'Tabbatar da imel ɗinka',
    'verify_sent_prefix' => 'Mun turo lambar sirri mai lamba 6 zuwa',
    'verify_sent_suffix' => 'Shigar da ita a ƙasa domin ci gaba.',
    'field_verification_code' => 'Lambar tabbatarwa',
    'verify_button' => 'Tabbatar',
    'status_new_code_sent' => "An turo sabuwar lambar zuwa imel ɗinka.",
    'status_account_verified' => 'An tabbatar da asusunka. Shiga domin fara amfani.',
    'js_code_expired_request' => 'Lambar ta ƙare. Nemi sabuwa a ƙasa.',
    'js_no_active_code' => 'Babu wata lambar da take aiki a halin yanzu. Yi amfani da "Sake tura lambar" a ƙasa domin samun ɗaya.',

    // ---- App\Models\User::accountBlockedMessage() — shown as the login-form
    // error for a frozen/suspended/disabled account, and mid-session by
    // EnsureAccountIsActive middleware.
    'account_frozen' => 'An daskarar da wannan asusu. Tuntuɓi tallafi domin taimako.',
    'account_suspended' => 'An dakatar da wannan asusu. Tuntuɓi tallafi domin taimako.',
    'account_disabled' => 'An kashe wannan asusu.',
    'account_blocked_default' => 'Wannan asusu ba zai iya shiga a yanzu ba.',
];
