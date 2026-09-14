<?php

return [
    // ---- Shared auth-layout decorative side panel (layouts/auth/simple.blade.php)
    // — shown on every auth page (login, register, forgot/reset password, 2FA,
    // confirm password, verify email). "Ledger" itself stays untranslated
    // everywhere, same convention as the rest of the app.
    'panel_eyebrow' => 'Bankowość utrzymana w porządku',
    'panel_headline_line1' => 'Każdy dolar,',
    'panel_headline_accent' => 'rozliczony.',
    'panel_sub' => 'Otwórz konto, przesyłaj pieniądze i zwiększaj swoje saldo w jednym spokojnym, rzetelnym rejestrze.',
    'mock_balance_label' => 'Saldo',
    'mock_account_label' => 'Konto · Rachunek bieżący',
    'entry_fdic' => 'Ubezpieczenie FDIC do 250 000 USD',
    'entry_no_fees' => 'Bez ukrytych opłat, nigdy',
    'entry_instant' => 'Natychmiastowe przelewy, 24/7',

    // ---- Shared field labels (reused across several forms below)
    'field_email' => 'Adres e-mail',
    'field_email_short' => 'E-mail',
    'field_password' => 'Hasło',
    'field_confirm_password' => 'Potwierdź hasło',

    // ---- Login (pages/auth/login.blade.php)
    'login_title' => 'Zaloguj się',
    'login_heading' => 'Zaloguj się do swojego konta',
    'login_description' => 'Wprowadź poniżej swój adres e-mail i hasło, aby się zalogować',
    'forgot_password_link' => 'Nie pamiętasz hasła?',
    'remember_me' => 'Zapamiętaj mnie',
    'no_account' => 'Nie masz konta?',
    'sign_up' => 'Zarejestruj się',
    'passkey_signin' => 'Zaloguj się za pomocą klucza dostępu',
    'passkey_authenticating' => 'Uwierzytelnianie...',
    'passkey_or_email' => 'Lub kontynuuj za pomocą e-maila',

    // ---- Register (pages/auth/register.blade.php)
    'register_title' => 'Utwórz swoje konto',
    'register_subtitle' => 'Otwórz konto Ledger w kilka minut.',
    'field_first_name' => 'Imię',
    'field_last_name' => 'Nazwisko',
    'field_middle_name' => 'Drugie imię (opcjonalnie)',
    'field_phone' => 'Telefon (opcjonalnie)',
    'register_button' => 'Utwórz konto',
    'already_have_account' => 'Masz już konto?',

    // ---- Confirm password (pages/auth/confirm-password.blade.php)
    'confirm_password_title' => 'Potwierdź hasło',
    'confirm_password_description' => 'To jest bezpieczny obszar aplikacji. Potwierdź swoje hasło, aby kontynuować.',
    'confirm_with_passkey' => 'Potwierdź za pomocą klucza dostępu',
    'confirming' => 'Potwierdzanie...',
    'or_confirm_with_password' => 'Lub potwierdź hasłem',
    'confirm_button' => 'Potwierdź',

    // ---- Forgot / reset password — the live 6-digit-code flow
    // (livewire/auth/forgot-password-code.blade.php, both steps)
    'forgot_password_title' => 'Nie pamiętam hasła',
    'forgot_password_description' => 'Podaj swój adres e-mail, a wyślemy Ci 6-cyfrowy kod, aby je zresetować.',
    'send_reset_code' => 'Wyślij kod resetujący',
    'or_return_to' => 'Lub wróć do',
    'log_in_lowercase' => 'logowania',
    'enter_your_code' => 'Wprowadź swój kod',
    'code_sent_to_prefix' => 'Wprowadź 6-cyfrowy kod, który wysłaliśmy na adres',
    'code_sent_to_suffix' => 'i ustaw nowe hasło.',
    'field_reset_code' => 'Kod resetujący',
    'field_new_password' => 'Nowe hasło',
    'field_confirm_new_password' => 'Potwierdź nowe hasło',
    'use_different_email' => 'Użyj innego adresu e-mail',
    'resend_code' => 'Wyślij kod ponownie',
    'status_code_sent_known' => 'Jeśli konto istnieje dla adresu :email, wysłaliśmy na nie 6-cyfrowy kod.',
    'status_code_resent' => 'Nowy kod został wysłany, jeśli ten adres e-mail ma konto.',
    'error_code_invalid' => 'Ten kod jest nieprawidłowy lub wygasł. Poproś o nowy poniżej.',
    'status_password_reset_done' => 'Twoje hasło zostało zresetowane. Zaloguj się przy użyciu nowego hasła.',
    'js_code_expires_in' => 'Kod wygasa za',
    'js_code_expired_resend' => 'Kod wygasł. Skorzystaj z opcji "Wyślij kod ponownie" poniżej.',

    // ---- Reset password — the classic email-link flow (pages/auth/reset-password.blade.php)
    'reset_password_title' => 'Zresetuj hasło',
    'reset_password_description' => 'Wprowadź poniżej swoje nowe hasło',

    // ---- Two-factor challenge (pages/auth/two-factor-challenge.blade.php)
    'two_factor_title' => 'Uwierzytelnianie dwuskładnikowe',
    'auth_code_title' => 'Kod uwierzytelniający',
    'auth_code_description' => 'Wprowadź kod uwierzytelniający wygenerowany przez aplikację uwierzytelniającą.',
    'field_otp_code' => 'Kod OTP',
    'recovery_code_title' => 'Kod odzyskiwania',
    'recovery_code_description' => 'Potwierdź dostęp do swojego konta, wprowadzając jeden z awaryjnych kodów odzyskiwania.',
    'continue_button' => 'Kontynuuj',
    'or_you_can' => 'lub możesz',
    'login_using_recovery_code' => 'zalogować się za pomocą kodu odzyskiwania',
    'login_using_auth_code' => 'zalogować się za pomocą kodu uwierzytelniającego',

    // ---- Verify email — the live 6-digit-code flow
    // (resources/views/auth/verify-email-page.blade.php + livewire/auth/verify-code.blade.php)
    'verify_email_title' => 'Zweryfikuj swój adres e-mail',
    'verify_sent_prefix' => 'Wysłaliśmy 6-cyfrowy kod na adres',
    'verify_sent_suffix' => 'Wprowadź go poniżej, aby kontynuować.',
    'field_verification_code' => 'Kod weryfikacyjny',
    'verify_button' => 'Zweryfikuj',
    'status_new_code_sent' => 'Nowy kod został wysłany na Twój adres e-mail.',
    'status_account_verified' => 'Twoje konto zostało zweryfikowane. Zaloguj się, aby zacząć.',
    'js_code_expired_request' => 'Kod wygasł. Poproś o nowy poniżej.',
    'js_no_active_code' => 'Brak aktywnego kodu. Skorzystaj z opcji "Wyślij kod ponownie" poniżej, aby go otrzymać.',

    // ---- App\Models\User::accountBlockedMessage() — shown as the login-form
    // error for a frozen/suspended/disabled account, and mid-session by
    // EnsureAccountIsActive middleware.
    'account_frozen' => 'To konto zostało zamrożone. Skontaktuj się z pomocą techniczną, aby uzyskać wsparcie.',
    'account_suspended' => 'To konto zostało zawieszone. Skontaktuj się z pomocą techniczną, aby uzyskać wsparcie.',
    'account_disabled' => 'To konto zostało wyłączone.',
    'account_blocked_default' => 'To konto nie może obecnie się zalogować.',
];
