<?php

return [
    // ---- Shared auth-layout decorative side panel (layouts/auth/simple.blade.php)
    // — shown on every auth page (login, register, forgot/reset password, 2FA,
    // confirm password, verify email). "Ledger" itself stays untranslated
    // everywhere, same convention as the rest of the app.
    'panel_eyebrow' => 'Banking, geordnet',
    'panel_headline_line1' => 'Jeder Euro,',
    'panel_headline_accent' => 'erfasst.',
    'panel_sub' => 'Eröffnen Sie ein Konto, bewegen Sie Geld und lassen Sie Ihr Guthaben wachsen – in einem ruhigen, ehrlichen Kontobuch.',
    'mock_balance_label' => 'Kontostand',
    'mock_account_label' => 'Konto · Girokonto',
    'entry_fdic' => 'FDIC-versichert bis zu 250.000 $',
    'entry_no_fees' => 'Keine versteckten Gebühren, niemals',
    'entry_instant' => 'Sofortige Überweisungen, rund um die Uhr',

    // ---- Shared field labels (reused across several forms below)
    'field_email' => 'E-Mail-Adresse',
    'field_email_short' => 'E-Mail',
    'field_password' => 'Passwort',
    'field_confirm_password' => 'Passwort bestätigen',

    // ---- Login (pages/auth/login.blade.php)
    'login_title' => 'Anmelden',
    'login_heading' => 'Melden Sie sich bei Ihrem Konto an',
    'login_description' => 'Geben Sie unten Ihre E-Mail-Adresse und Ihr Passwort ein, um sich anzumelden',
    'forgot_password_link' => 'Passwort vergessen?',
    'remember_me' => 'Angemeldet bleiben',
    'no_account' => 'Noch kein Konto?',
    'sign_up' => 'Registrieren',
    'passkey_signin' => 'Mit einem Passkey anmelden',
    'passkey_authenticating' => 'Authentifizierung läuft...',
    'passkey_or_email' => 'Oder weiter mit E-Mail',

    // ---- Register (pages/auth/register.blade.php)
    'register_title' => 'Konto erstellen',
    'register_subtitle' => 'Eröffnen Sie in wenigen Minuten ein Ledger-Konto.',
    'field_first_name' => 'Vorname',
    'field_last_name' => 'Nachname',
    'field_middle_name' => 'Zweiter Vorname (optional)',
    'field_phone' => 'Telefon (optional)',
    'register_button' => 'Konto erstellen',
    'already_have_account' => 'Sie haben bereits ein Konto?',

    // ---- Confirm password (pages/auth/confirm-password.blade.php)
    'confirm_password_title' => 'Passwort bestätigen',
    'confirm_password_description' => 'Dies ist ein geschützter Bereich der Anwendung. Bitte bestätigen Sie Ihr Passwort, bevor Sie fortfahren.',
    'confirm_with_passkey' => 'Mit Passkey bestätigen',
    'confirming' => 'Wird bestätigt...',
    'or_confirm_with_password' => 'Oder mit Passwort bestätigen',
    'confirm_button' => 'Bestätigen',

    // ---- Forgot / reset password — the live 6-digit-code flow
    // (livewire/auth/forgot-password-code.blade.php, both steps)
    'forgot_password_title' => 'Passwort vergessen',
    'forgot_password_description' => 'Geben Sie Ihre E-Mail-Adresse ein und wir senden Ihnen einen 6-stelligen Code zum Zurücksetzen.',
    'send_reset_code' => 'Reset-Code senden',
    'or_return_to' => 'Oder kehren Sie zurück zur',
    'log_in_lowercase' => 'Anmeldung',
    'enter_your_code' => 'Geben Sie Ihren Code ein',
    'code_sent_to_prefix' => 'Geben Sie den 6-stelligen Code ein, den wir gesendet haben an',
    'code_sent_to_suffix' => 'und wählen Sie ein neues Passwort.',
    'field_reset_code' => 'Reset-Code',
    'field_new_password' => 'Neues Passwort',
    'field_confirm_new_password' => 'Neues Passwort bestätigen',
    'use_different_email' => 'Andere E-Mail-Adresse verwenden',
    'resend_code' => 'Code erneut senden',
    'status_code_sent_known' => 'Falls für :email ein Konto existiert, haben wir dorthin einen 6-stelligen Code gesendet.',
    'status_code_resent' => 'Ein neuer Code wurde gesendet, sofern zu dieser E-Mail-Adresse ein Konto gehört.',
    'error_code_invalid' => 'Dieser Code ist ungültig oder abgelaufen. Fordern Sie unten einen neuen an.',
    'status_password_reset_done' => 'Ihr Passwort wurde zurückgesetzt. Melden Sie sich mit Ihrem neuen Passwort an.',
    'js_code_expires_in' => 'Code läuft ab in',
    'js_code_expired_resend' => 'Code abgelaufen. Verwenden Sie unten „Code erneut senden".',

    // ---- Reset password — the classic email-link flow (pages/auth/reset-password.blade.php)
    'reset_password_title' => 'Passwort zurücksetzen',
    'reset_password_description' => 'Bitte geben Sie unten Ihr neues Passwort ein',

    // ---- Two-factor challenge (pages/auth/two-factor-challenge.blade.php)
    'two_factor_title' => 'Zwei-Faktor-Authentifizierung',
    'auth_code_title' => 'Authentifizierungscode',
    'auth_code_description' => 'Geben Sie den Authentifizierungscode aus Ihrer Authenticator-App ein.',
    'field_otp_code' => 'OTP-Code',
    'recovery_code_title' => 'Wiederherstellungscode',
    'recovery_code_description' => 'Bitte bestätigen Sie den Zugriff auf Ihr Konto, indem Sie einen Ihrer Notfall-Wiederherstellungscodes eingeben.',
    'continue_button' => 'Weiter',
    'or_you_can' => 'oder Sie können',
    'login_using_recovery_code' => 'sich mit einem Wiederherstellungscode anmelden',
    'login_using_auth_code' => 'sich mit einem Authentifizierungscode anmelden',

    // ---- Verify email — the live 6-digit-code flow
    // (resources/views/auth/verify-email-page.blade.php + livewire/auth/verify-code.blade.php)
    'verify_email_title' => 'Bestätigen Sie Ihre E-Mail-Adresse',
    'verify_sent_prefix' => 'Wir haben einen 6-stelligen Code gesendet an',
    'verify_sent_suffix' => 'Geben Sie ihn unten ein, um fortzufahren.',
    'field_verification_code' => 'Bestätigungscode',
    'verify_button' => 'Bestätigen',
    'status_new_code_sent' => 'Ein neuer Code wurde an Ihre E-Mail-Adresse gesendet.',
    'status_account_verified' => 'Ihr Konto ist bestätigt. Melden Sie sich an, um loszulegen.',
    'js_code_expired_request' => 'Code abgelaufen. Fordern Sie unten einen neuen an.',
    'js_no_active_code' => 'Kein aktiver Code hinterlegt. Verwenden Sie unten „Code erneut senden", um einen zu erhalten.',

    // ---- App\Models\User::accountBlockedMessage() — shown as the login-form
    // error for a frozen/suspended/disabled account, and mid-session by
    // EnsureAccountIsActive middleware.
    'account_frozen' => 'Dieses Konto ist eingefroren. Wenden Sie sich für Hilfe an den Support.',
    'account_suspended' => 'Dieses Konto ist gesperrt. Wenden Sie sich für Hilfe an den Support.',
    'account_disabled' => 'Dieses Konto wurde deaktiviert.',
    'account_blocked_default' => 'Mit diesem Konto ist derzeit keine Anmeldung möglich.',
];
