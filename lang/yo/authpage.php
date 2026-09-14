<?php

return [
    // ---- Shared auth-layout decorative side panel (layouts/auth/simple.blade.php)
    // — shown on every auth page (login, register, forgot/reset password, 2FA,
    // confirm password, verify email). "Ledger" itself stays untranslated
    // everywhere, same convention as the rest of the app.
    'panel_eyebrow' => 'Ìṣúná owó, tí ó ṣètò dáadáa',
    'panel_headline_line1' => 'Dọ́là kọ̀ọ̀kan,',
    'panel_headline_accent' => 'ni a kọ sílẹ̀.',
    'panel_sub' => 'Ṣí àkọọ́lẹ̀ rẹ, fi owó ránṣẹ́, kí o sì mú owó rẹ pọ̀ sí i nínú ìwé àkọọ́lẹ̀ kan tí ó dájú, tí kò sì ní àṣírí kankan.',
    'mock_balance_label' => 'Owó tí ó kù',
    'mock_account_label' => 'Àkọọ́lẹ̀ · Tí a ń lò lójoojúmọ́',
    'entry_fdic' => 'FDIC ṣe ìdánilójú fún owó tí ó tó $250,000',
    'entry_no_fees' => "Kò sí owó ìsanwó tí a fi pamọ́, láéláé",
    'entry_instant' => 'Ìfiránṣẹ́ owó lẹ́sẹ̀kẹsẹ̀, ní gbogbo àkókò',

    // ---- Shared field labels (reused across several forms below)
    'field_email' => 'Àdírẹ́sì ímeèlì',
    'field_email_short' => 'Ímeèlì',
    'field_password' => 'Ọ̀rọ̀ ìpamọ́',
    'field_confirm_password' => 'Jẹ́rìí sí ọ̀rọ̀ ìpamọ́',

    // ---- Login (pages/auth/login.blade.php)
    'login_title' => 'Wọlé',
    'login_heading' => 'Wọlé sí àkọọ́lẹ̀ rẹ',
    'login_description' => 'Tẹ ímeèlì àti ọ̀rọ̀ ìpamọ́ rẹ sí ìsàlẹ̀ láti wọlé',
    'forgot_password_link' => 'Ṣé o gbàgbé ọ̀rọ̀ ìpamọ́ rẹ?',
    'remember_me' => 'Rántí mi',
    'no_account' => 'Ṣé o kò ì tí ì ní àkọọ́lẹ̀?',
    'sign_up' => 'Forúkọ sílẹ̀',
    'passkey_signin' => 'Wọlé pẹ̀lú passkey',
    'passkey_authenticating' => 'A ń jẹ́rìí sí i...',
    'passkey_or_email' => 'Tàbí tẹ̀síwájú pẹ̀lú ímeèlì',

    // ---- Register (pages/auth/register.blade.php)
    'register_title' => 'Ṣẹ̀dá àkọọ́lẹ̀ rẹ',
    'register_subtitle' => 'Ṣí àkọọ́lẹ̀ Ledger láàrín ìṣẹ́jú díẹ̀.',
    'field_first_name' => 'Orúkọ àkọ́kọ́',
    'field_last_name' => 'Orúkọ ìdílé',
    'field_middle_name' => 'Orúkọ àárín (kò pọn dandan)',
    'field_phone' => 'Nọ́mbà fóònù (kò pọn dandan)',
    'register_button' => 'Ṣẹ̀dá àkọọ́lẹ̀',
    'already_have_account' => 'Ṣé o ti ní àkọọ́lẹ̀ tẹ́lẹ̀?',

    // ---- Confirm password (pages/auth/confirm-password.blade.php)
    'confirm_password_title' => 'Jẹ́rìí sí ọ̀rọ̀ ìpamọ́',
    'confirm_password_description' => 'Ìhà ààbò ni èyí nínú ìṣẹ́ ohun èlò yìí. Jọ̀wọ́ jẹ́rìí sí ọ̀rọ̀ ìpamọ́ rẹ kí o tó tẹ̀síwájú.',
    'confirm_with_passkey' => 'Jẹ́rìí pẹ̀lú passkey',
    'confirming' => 'A ń jẹ́rìí sí i...',
    'or_confirm_with_password' => 'Tàbí jẹ́rìí pẹ̀lú ọ̀rọ̀ ìpamọ́',
    'confirm_button' => 'Jẹ́rìí',

    // ---- Forgot / reset password — the live 6-digit-code flow
    // (livewire/auth/forgot-password-code.blade.php, both steps)
    'forgot_password_title' => 'Mo gbàgbé ọ̀rọ̀ ìpamọ́',
    'forgot_password_description' => 'Tẹ ímeèlì rẹ sí ìsàlẹ̀, a óò sì fi nọ́mbà mẹ́fà ránṣẹ́ sí ọ láti fi ṣe àtúnṣe ọ̀rọ̀ ìpamọ́ rẹ.',
    'send_reset_code' => 'Fi nọ́mbà àtúnṣe ránṣẹ́',
    'or_return_to' => 'Tàbí padà sí',
    'log_in_lowercase' => 'wíwọlé',
    'enter_your_code' => 'Tẹ nọ́mbà rẹ',
    'code_sent_to_prefix' => 'Tẹ nọ́mbà mẹ́fà tí a fi ránṣẹ́ sí',
    'code_sent_to_suffix' => 'kí o sì yan ọ̀rọ̀ ìpamọ́ tuntun.',
    'field_reset_code' => 'Nọ́mbà àtúnṣe',
    'field_new_password' => 'Ọ̀rọ̀ ìpamọ́ tuntun',
    'field_confirm_new_password' => 'Jẹ́rìí sí ọ̀rọ̀ ìpamọ́ tuntun',
    'use_different_email' => 'Lo ímeèlì mìíràn',
    'resend_code' => 'Tún nọ́mbà ránṣẹ́',
    'status_code_sent_known' => 'Bí àkọọ́lẹ̀ bá wà fún :email, a ti fi nọ́mbà mẹ́fà ránṣẹ́ sí i.',
    'status_code_resent' => 'A ti fi nọ́mbà tuntun ránṣẹ́, bí ímeèlì náà bá ní àkọọ́lẹ̀.',
    'error_code_invalid' => 'Nọ́mbà yẹn kò tọ́ tàbí ó ti pé àkókò rẹ̀. Béèrè fún tuntun ní ìsàlẹ̀.',
    'status_password_reset_done' => 'A ti ṣe àtúnṣe ọ̀rọ̀ ìpamọ́ rẹ. Wọlé pẹ̀lú ọ̀rọ̀ ìpamọ́ tuntun rẹ.',
    'js_code_expires_in' => 'Àkókò nọ́mbà yìí máa parí ní',
    'js_code_expired_resend' => 'Àkókò nọ́mbà náà ti parí. Lo "Tún nọ́mbà ránṣẹ́" ní ìsàlẹ̀.',

    // ---- Reset password — the classic email-link flow (pages/auth/reset-password.blade.php)
    'reset_password_title' => 'Ṣe àtúnṣe ọ̀rọ̀ ìpamọ́',
    'reset_password_description' => 'Jọ̀wọ́ tẹ ọ̀rọ̀ ìpamọ́ tuntun rẹ sí ìsàlẹ̀',

    // ---- Two-factor challenge (pages/auth/two-factor-challenge.blade.php)
    'two_factor_title' => 'Ìjẹ́rìísí ìlọ́po méjì',
    'auth_code_title' => 'Nọ́mbà ìjẹ́rìísí',
    'auth_code_description' => 'Tẹ nọ́mbà ìjẹ́rìísí tí ohun èlò ìjẹ́rìísí rẹ fún ọ.',
    'field_otp_code' => 'Nọ́mbà OTP',
    'recovery_code_title' => 'Nọ́mbà ìgbàpadà',
    'recovery_code_description' => 'Jọ̀wọ́ jẹ́rìí sí àkọọ́lẹ̀ rẹ nípa lílo ọ̀kan lára àwọn nọ́mbà ìgbàpadà pàjáwìrì rẹ.',
    'continue_button' => 'Tẹ̀síwájú',
    'or_you_can' => 'tàbí o lè',
    'login_using_recovery_code' => 'wọlé pẹ̀lú nọ́mbà ìgbàpadà',
    'login_using_auth_code' => 'wọlé pẹ̀lú nọ́mbà ìjẹ́rìísí',

    // ---- Verify email — the live 6-digit-code flow
    // (resources/views/auth/verify-email-page.blade.php + livewire/auth/verify-code.blade.php)
    'verify_email_title' => 'Jẹ́rìí sí ímeèlì rẹ',
    'verify_sent_prefix' => 'A ti fi nọ́mbà mẹ́fà ránṣẹ́ sí',
    'verify_sent_suffix' => 'Tẹ ọ sí ìsàlẹ̀ láti tẹ̀síwájú.',
    'field_verification_code' => 'Nọ́mbà ìjẹ́rìísí',
    'verify_button' => 'Jẹ́rìí',
    'status_new_code_sent' => 'A ti fi nọ́mbà tuntun ránṣẹ́ sí ímeèlì rẹ.',
    'status_account_verified' => 'A ti jẹ́rìí sí àkọọ́lẹ̀ rẹ. Wọlé láti bẹ̀rẹ̀.',
    'js_code_expired_request' => 'Àkókò nọ́mbà náà ti parí. Béèrè fún tuntun ní ìsàlẹ̀.',
    'js_no_active_code' => 'Kò sí nọ́mbà tí ó ṣiṣẹ́ lọ́wọ́lọ́wọ́. Lo "Tún nọ́mbà ránṣẹ́" ní ìsàlẹ̀ láti rí ọ̀kan gbà.',

    // ---- App\Models\User::accountBlockedMessage() — shown as the login-form
    // error for a frozen/suspended/disabled account, and mid-session by
    // EnsureAccountIsActive middleware.
    'account_frozen' => 'A ti dí àkọọ́lẹ̀ yìí. Kàn sí àwọn olùrànlọ́wọ́ fún ìrànlọ́wọ́.',
    'account_suspended' => 'A ti dá àkọọ́lẹ̀ yìí dúró fún ìgbà díẹ̀. Kàn sí àwọn olùrànlọ́wọ́ fún ìrànlọ́wọ́.',
    'account_disabled' => 'A ti dá àkọọ́lẹ̀ yìí dúró pátápátá.',
    'account_blocked_default' => 'Àkọọ́lẹ̀ yìí kò lè wọlé báyìí.',
];
