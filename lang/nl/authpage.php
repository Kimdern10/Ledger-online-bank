<?php

return [
    // ---- Shared auth-layout decorative side panel (layouts/auth/simple.blade.php)
    // — shown on every auth page (login, register, forgot/reset password, 2FA,
    // confirm password, verify email). "Ledger" itself stays untranslated
    // everywhere, same convention as the rest of the app.
    'panel_eyebrow' => 'Bankieren, netjes op orde',
    'panel_headline_line1' => 'Elke dollar,',
    'panel_headline_accent' => 'wordt verantwoord.',
    'panel_sub' => 'Open een rekening, verplaats geld en laat je saldo groeien in één rustig, eerlijk grootboek.',
    'mock_balance_label' => 'Saldo',
    'mock_account_label' => 'Rekening · Betaalrekening',
    'entry_fdic' => 'FDIC-verzekerd tot $250.000',
    'entry_no_fees' => 'Nooit verborgen kosten',
    'entry_instant' => 'Directe overboekingen, 24/7',

    // ---- Shared field labels (reused across several forms below)
    'field_email' => 'E-mailadres',
    'field_email_short' => 'E-mail',
    'field_password' => 'Wachtwoord',
    'field_confirm_password' => 'Bevestig wachtwoord',

    // ---- Login (pages/auth/login.blade.php)
    'login_title' => 'Inloggen',
    'login_heading' => 'Log in op je account',
    'login_description' => 'Voer hieronder je e-mailadres en wachtwoord in om in te loggen',
    'forgot_password_link' => 'Wachtwoord vergeten?',
    'remember_me' => 'Onthoud mij',
    'no_account' => 'Nog geen account?',
    'sign_up' => 'Registreren',
    'passkey_signin' => 'Inloggen met een passkey',
    'passkey_authenticating' => 'Bezig met verifiëren...',
    'passkey_or_email' => 'Of ga verder met e-mail',

    // ---- Register (pages/auth/register.blade.php)
    'register_title' => 'Maak je account aan',
    'register_subtitle' => 'Open in een paar minuten een Ledger-account.',
    'field_first_name' => 'Voornaam',
    'field_last_name' => 'Achternaam',
    'field_middle_name' => 'Tussenvoegsel (optioneel)',
    'field_phone' => 'Telefoon (optioneel)',
    'register_button' => 'Account aanmaken',
    'already_have_account' => 'Heb je al een account?',

    // ---- Confirm password (pages/auth/confirm-password.blade.php)
    'confirm_password_title' => 'Bevestig wachtwoord',
    'confirm_password_description' => 'Dit is een beveiligd gedeelte van de applicatie. Bevestig je wachtwoord voordat je verdergaat.',
    'confirm_with_passkey' => 'Bevestigen met passkey',
    'confirming' => 'Bezig met bevestigen...',
    'or_confirm_with_password' => 'Of bevestig met wachtwoord',
    'confirm_button' => 'Bevestigen',

    // ---- Forgot / reset password — the live 6-digit-code flow
    // (livewire/auth/forgot-password-code.blade.php, both steps)
    'forgot_password_title' => 'Wachtwoord vergeten',
    'forgot_password_description' => 'Voer je e-mailadres in en we sturen je een 6-cijferige code om het opnieuw in te stellen.',
    'send_reset_code' => 'Resetcode versturen',
    'or_return_to' => 'Of ga terug naar',
    'log_in_lowercase' => 'inloggen',
    'enter_your_code' => 'Voer je code in',
    'code_sent_to_prefix' => 'Voer de 6-cijferige code in die we hebben gestuurd naar',
    'code_sent_to_suffix' => 'en kies een nieuw wachtwoord.',
    'field_reset_code' => 'Resetcode',
    'field_new_password' => 'Nieuw wachtwoord',
    'field_confirm_new_password' => 'Bevestig nieuw wachtwoord',
    'use_different_email' => 'Gebruik een ander e-mailadres',
    'resend_code' => 'Code opnieuw versturen',
    'status_code_sent_known' => 'Als er een account bestaat voor :email, hebben we er een 6-cijferige code naartoe gestuurd.',
    'status_code_resent' => 'Er is een nieuwe code verstuurd, als dat e-mailadres een account heeft.',
    'error_code_invalid' => 'Die code is ongeldig of verlopen. Vraag hieronder een nieuwe aan.',
    'status_password_reset_done' => 'Je wachtwoord is opnieuw ingesteld. Log in met je nieuwe wachtwoord.',
    'js_code_expires_in' => 'Code verloopt over',
    'js_code_expired_resend' => 'Code verlopen. Gebruik hieronder "Code opnieuw versturen".',

    // ---- Reset password — the classic email-link flow (pages/auth/reset-password.blade.php)
    'reset_password_title' => 'Wachtwoord opnieuw instellen',
    'reset_password_description' => 'Voer hieronder je nieuwe wachtwoord in',

    // ---- Two-factor challenge (pages/auth/two-factor-challenge.blade.php)
    'two_factor_title' => 'Tweefactorauthenticatie',
    'auth_code_title' => 'Authenticatiecode',
    'auth_code_description' => 'Voer de authenticatiecode in die je authenticator-app heeft gegenereerd.',
    'field_otp_code' => 'OTP-code',
    'recovery_code_title' => 'Herstelcode',
    'recovery_code_description' => 'Bevestig toegang tot je account door een van je noodherstelcodes in te voeren.',
    'continue_button' => 'Doorgaan',
    'or_you_can' => 'of je kunt',
    'login_using_recovery_code' => 'inloggen met een herstelcode',
    'login_using_auth_code' => 'inloggen met een authenticatiecode',

    // ---- Verify email — the live 6-digit-code flow
    // (resources/views/auth/verify-email-page.blade.php + livewire/auth/verify-code.blade.php)
    'verify_email_title' => 'Verifieer je e-mailadres',
    'verify_sent_prefix' => 'We hebben een 6-cijferige code gestuurd naar',
    'verify_sent_suffix' => 'Voer deze hieronder in om door te gaan.',
    'field_verification_code' => 'Verificatiecode',
    'verify_button' => 'Verifiëren',
    'status_new_code_sent' => 'Er is een nieuwe code naar je e-mailadres verstuurd.',
    'status_account_verified' => 'Je account is geverifieerd. Log in om te beginnen.',
    'js_code_expired_request' => 'Code verlopen. Vraag hieronder een nieuwe aan.',
    'js_no_active_code' => 'Er is geen actieve code bekend. Gebruik hieronder "Code opnieuw versturen" om er een te krijgen.',

    // ---- App\Models\User::accountBlockedMessage() — shown as the login-form
    // error for a frozen/suspended/disabled account, and mid-session by
    // EnsureAccountIsActive middleware.
    'account_frozen' => 'Dit account is bevroren. Neem contact op met de klantenservice voor hulp.',
    'account_suspended' => 'Dit account is geschorst. Neem contact op met de klantenservice voor hulp.',
    'account_disabled' => 'Dit account is uitgeschakeld.',
    'account_blocked_default' => 'Dit account kan op dit moment niet inloggen.',
    'idle_warning_title' => 'Ben je er nog?',
    'idle_warning_body' => 'Voor je veiligheid word je binnenkort afgemeld, omdat er een tijdje niets is gebeurd. Afmelden over',
    'idle_stay_signed_in' => 'Aangemeld blijven',
    'idle_signed_out' => 'Je bent afgemeld omdat er een tijdje niets is gebeurd. Log opnieuw in.',
    'wallet_frozen' => 'Je wallet is bevroren. Je kunt nog steeds inloggen en je rekening bekijken, maar er kan geen geld worden verstuurd of ontvangen. Neem contact op met support om hem te ontdooien.',
    'sms_verification_code' => 'Ledger: je verificatiecode is :code. Deze verloopt over 10 minuten. Deel hem met niemand.',
    'sms_reset_code' => 'Ledger: je code om je wachtwoord te herstellen is :code. Deze verloopt over 10 minuten. Heb je hier niet om gevraagd, negeer hem dan.',
    'text_code_instead' => 'Stuur de code in plaats daarvan per sms naar :phone',
    'status_code_texted' => 'We hebben een nieuwe code per sms naar :phone gestuurd.',
    'sms_add_phone' => 'Om codes per sms te ontvangen, voeg je je telefoonnummer met landcode (bijvoorbeeld +234 801 234 5678) toe in de profielinstellingen.',
    'sms_failed' => 'We konden nu geen sms versturen. Gebruik de code die we je hebben gemaild of probeer het later opnieuw.',
    'sms_too_many' => 'Er zijn te veel sms’jes naar dit nummer gestuurd. Wacht 10 minuten en probeer het opnieuw.',
    'reset_send_by' => 'Code versturen via',
    'reset_by_email' => 'E-mail',
    'reset_by_text' => 'Sms',
    'reset_by_text_note' => 'We sturen hem per sms naar het telefoonnummer van je account.',
    'code_texted_for_prefix' => 'Voer de 6-cijferige code in die we per sms hebben gestuurd naar de telefoon van het account van',
    'code_texted_for_suffix' => 'en kies daarna een nieuw wachtwoord.',
    'status_code_texted_known' => 'Als :email een account met een telefoonnummer heeft, hebben we er per sms een 6-cijferige code naartoe gestuurd.',
];
