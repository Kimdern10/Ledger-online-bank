<?php

return [
    // ---- Shared auth-layout decorative side panel (layouts/auth/simple.blade.php)
    // — shown on every auth page (login, register, forgot/reset password, 2FA,
    // confirm password, verify email). "Ledger" itself stays untranslated
    // everywhere, same convention as the rest of the app.
    'panel_eyebrow' => 'Il tuo conto, sempre in ordine',
    'panel_headline_line1' => 'Ogni dollaro,',
    'panel_headline_accent' => 'sempre tracciato.',
    'panel_sub' => 'Apri un conto, sposta denaro e fai crescere il tuo saldo in un unico registro chiaro e affidabile.',
    'mock_balance_label' => 'Saldo',
    'mock_account_label' => 'Conto · Corrente',
    'entry_fdic' => 'Assicurato FDIC fino a $250.000',
    'entry_no_fees' => 'Nessuna commissione nascosta, mai',
    'entry_instant' => 'Bonifici istantanei, 24/7',

    // ---- Shared field labels (reused across several forms below)
    'field_email' => 'Indirizzo email',
    'field_email_short' => 'Email',
    'field_password' => 'Password',
    'field_confirm_password' => 'Conferma password',

    // ---- Login (pages/auth/login.blade.php)
    'login_title' => 'Accedi',
    'login_heading' => 'Accedi al tuo account',
    'login_description' => 'Inserisci email e password qui sotto per accedere',
    'forgot_password_link' => 'Hai dimenticato la password?',
    'remember_me' => 'Ricordami',
    'no_account' => 'Non hai un account?',
    'sign_up' => 'Registrati',
    'passkey_signin' => 'Accedi con una passkey',
    'passkey_authenticating' => 'Autenticazione in corso...',
    'passkey_or_email' => 'Oppure continua con l\'email',

    // ---- Register (pages/auth/register.blade.php)
    'register_title' => 'Crea il tuo account',
    'register_subtitle' => 'Apri un account Ledger in un paio di minuti.',
    'field_first_name' => 'Nome',
    'field_last_name' => 'Cognome',
    'field_middle_name' => 'Secondo nome (facoltativo)',
    'field_phone' => 'Telefono (facoltativo)',
    'register_button' => 'Crea account',
    'already_have_account' => 'Hai già un account?',

    // ---- Confirm password (pages/auth/confirm-password.blade.php)
    'confirm_password_title' => 'Conferma password',
    'confirm_password_description' => 'Questa è un\'area protetta dell\'applicazione. Conferma la tua password prima di continuare.',
    'confirm_with_passkey' => 'Conferma con passkey',
    'confirming' => 'Conferma in corso...',
    'or_confirm_with_password' => 'Oppure conferma con la password',
    'confirm_button' => 'Conferma',

    // ---- Forgot / reset password — the live 6-digit-code flow
    // (livewire/auth/forgot-password-code.blade.php, both steps)
    'forgot_password_title' => 'Password dimenticata',
    'forgot_password_description' => 'Inserisci la tua email e ti invieremo un codice a 6 cifre per reimpostarla.',
    'send_reset_code' => 'Invia codice di reimpostazione',
    'or_return_to' => 'Oppure, torna a',
    'log_in_lowercase' => 'accedi',
    'enter_your_code' => 'Inserisci il tuo codice',
    'code_sent_to_prefix' => 'Inserisci il codice a 6 cifre che abbiamo inviato a',
    'code_sent_to_suffix' => 'e scegli una nuova password.',
    'field_reset_code' => 'Codice di reimpostazione',
    'field_new_password' => 'Nuova password',
    'field_confirm_new_password' => 'Conferma nuova password',
    'use_different_email' => 'Usa un\'altra email',
    'resend_code' => 'Invia di nuovo il codice',
    'status_code_sent_known' => "Se esiste un account per :email, vi abbiamo inviato un codice a 6 cifre.",
    'status_code_resent' => 'Un nuovo codice è stato inviato, se quell\'email è associata a un account.',
    'error_code_invalid' => 'Il codice non è valido o è scaduto. Richiedine uno nuovo qui sotto.',
    'status_password_reset_done' => 'La tua password è stata reimpostata. Accedi con la tua nuova password.',
    'js_code_expires_in' => 'Il codice scade tra',
    'js_code_expired_resend' => 'Codice scaduto. Usa "Invia di nuovo il codice" qui sotto.',

    // ---- Reset password — the classic email-link flow (pages/auth/reset-password.blade.php)
    'reset_password_title' => 'Reimposta password',
    'reset_password_description' => 'Inserisci qui sotto la tua nuova password',

    // ---- Two-factor challenge (pages/auth/two-factor-challenge.blade.php)
    'two_factor_title' => 'Autenticazione a due fattori',
    'auth_code_title' => 'Codice di autenticazione',
    'auth_code_description' => 'Inserisci il codice di autenticazione fornito dalla tua app di autenticazione.',
    'field_otp_code' => 'Codice OTP',
    'recovery_code_title' => 'Codice di recupero',
    'recovery_code_description' => 'Conferma l\'accesso al tuo account inserendo uno dei tuoi codici di recupero di emergenza.',
    'continue_button' => 'Continua',
    'or_you_can' => 'oppure puoi',
    'login_using_recovery_code' => 'accedere usando un codice di recupero',
    'login_using_auth_code' => 'accedere usando un codice di autenticazione',

    // ---- Verify email — the live 6-digit-code flow
    // (resources/views/auth/verify-email-page.blade.php + livewire/auth/verify-code.blade.php)
    'verify_email_title' => 'Verifica la tua email',
    'verify_sent_prefix' => 'Abbiamo inviato un codice a 6 cifre a',
    'verify_sent_suffix' => 'Inseriscilo qui sotto per continuare.',
    'field_verification_code' => 'Codice di verifica',
    'verify_button' => 'Verifica',
    'status_new_code_sent' => 'Un nuovo codice è stato inviato alla tua email.',
    'status_account_verified' => 'Il tuo account è verificato. Accedi per iniziare.',
    'js_code_expired_request' => 'Codice scaduto. Richiedine uno nuovo qui sotto.',
    'js_no_active_code' => 'Nessun codice attivo registrato. Usa "Invia di nuovo il codice" qui sotto per ottenerne uno.',

    // ---- App\Models\User::accountBlockedMessage() — shown as the login-form
    // error for a frozen/suspended/disabled account, and mid-session by
    // EnsureAccountIsActive middleware.
    'account_frozen' => 'Questo account è congelato. Contatta l\'assistenza per aiuto.',
    'account_suspended' => 'Questo account è sospeso. Contatta l\'assistenza per aiuto.',
    'account_disabled' => 'Questo account è stato disabilitato.',
    'account_blocked_default' => 'Questo account non può accedere in questo momento.',
];
