<?php

return [
    // ---- Shared auth-layout decorative side panel (layouts/auth/simple.blade.php)
    // — shown on every auth page (login, register, forgot/reset password, 2FA,
    // confirm password, verify email). "Ledger" itself stays untranslated
    // everywhere, same convention as the rest of the app.
    'panel_eyebrow' => 'Serviços bancários em ordem',
    'panel_headline_line1' => 'Cada dólar,',
    'panel_headline_accent' => 'devidamente registrado.',
    'panel_sub' => 'Abra uma conta, movimente dinheiro e faça seu saldo crescer em um único livro-razão calmo e transparente.',
    'mock_balance_label' => 'Saldo',
    'mock_account_label' => 'Conta · Corrente',
    'entry_fdic' => 'Segurado pela FDIC até $250.000',
    'entry_no_fees' => 'Sem taxas ocultas, nunca',
    'entry_instant' => 'Transferências instantâneas, 24 horas por dia',

    // ---- Shared field labels (reused across several forms below)
    'field_email' => 'Endereço de email',
    'field_email_short' => 'Email',
    'field_password' => 'Senha',
    'field_confirm_password' => 'Confirmar senha',

    // ---- Login (pages/auth/login.blade.php)
    'login_title' => 'Entrar',
    'login_heading' => 'Entre na sua conta',
    'login_description' => 'Digite seu email e senha abaixo para entrar',
    'forgot_password_link' => 'Esqueceu sua senha?',
    'remember_me' => 'Lembrar-me',
    'no_account' => 'Não tem uma conta?',
    'sign_up' => 'Cadastre-se',
    'passkey_signin' => 'Entrar com uma chave de acesso',
    'passkey_authenticating' => 'Autenticando...',
    'passkey_or_email' => 'Ou continue com o email',

    // ---- Register (pages/auth/register.blade.php)
    'register_title' => 'Crie sua conta',
    'register_subtitle' => 'Abra uma conta Ledger em poucos minutos.',
    'field_first_name' => 'Nome',
    'field_last_name' => 'Sobrenome',
    'field_middle_name' => 'Nome do meio (opcional)',
    'field_phone' => 'Telefone (opcional)',
    'register_button' => 'Criar conta',
    'already_have_account' => 'Já tem uma conta?',

    // ---- Confirm password (pages/auth/confirm-password.blade.php)
    'confirm_password_title' => 'Confirmar senha',
    'confirm_password_description' => 'Esta é uma área segura da aplicação. Confirme sua senha antes de continuar.',
    'confirm_with_passkey' => 'Confirmar com chave de acesso',
    'confirming' => 'Confirmando...',
    'or_confirm_with_password' => 'Ou confirme com a senha',
    'confirm_button' => 'Confirmar',

    // ---- Forgot / reset password — the live 6-digit-code flow
    // (livewire/auth/forgot-password-code.blade.php, both steps)
    'forgot_password_title' => 'Esqueci minha senha',
    'forgot_password_description' => 'Digite seu email e enviaremos um código de 6 dígitos para redefini-la.',
    'send_reset_code' => 'Enviar código de redefinição',
    'or_return_to' => 'Ou volte para',
    'log_in_lowercase' => 'entrar',
    'enter_your_code' => 'Digite seu código',
    'code_sent_to_prefix' => 'Digite o código de 6 dígitos que enviamos para',
    'code_sent_to_suffix' => 'e escolha uma nova senha.',
    'field_reset_code' => 'Código de redefinição',
    'field_new_password' => 'Nova senha',
    'field_confirm_new_password' => 'Confirmar nova senha',
    'use_different_email' => 'Usar outro email',
    'resend_code' => 'Reenviar código',
    'status_code_sent_known' => "Se existir uma conta para :email, enviamos um código de 6 dígitos para ela.",
    'status_code_resent' => 'Um novo código foi enviado, caso esse email tenha uma conta.',
    'error_code_invalid' => 'Esse código é inválido ou expirou. Solicite um novo abaixo.',
    'status_password_reset_done' => 'Sua senha foi redefinida. Entre com sua nova senha.',
    'js_code_expires_in' => 'O código expira em',
    'js_code_expired_resend' => 'Código expirado. Use "Reenviar código" abaixo.',

    // ---- Reset password — the classic email-link flow (pages/auth/reset-password.blade.php)
    'reset_password_title' => 'Redefinir senha',
    'reset_password_description' => 'Digite sua nova senha abaixo',

    // ---- Two-factor challenge (pages/auth/two-factor-challenge.blade.php)
    'two_factor_title' => 'Autenticação de dois fatores',
    'auth_code_title' => 'Código de autenticação',
    'auth_code_description' => 'Digite o código de autenticação fornecido pelo seu aplicativo autenticador.',
    'field_otp_code' => 'Código OTP',
    'recovery_code_title' => 'Código de recuperação',
    'recovery_code_description' => 'Confirme o acesso à sua conta digitando um dos seus códigos de recuperação de emergência.',
    'continue_button' => 'Continuar',
    'or_you_can' => 'ou você pode',
    'login_using_recovery_code' => 'entrar usando um código de recuperação',
    'login_using_auth_code' => 'entrar usando um código de autenticação',

    // ---- Verify email — the live 6-digit-code flow
    // (resources/views/auth/verify-email-page.blade.php + livewire/auth/verify-code.blade.php)
    'verify_email_title' => 'Verifique seu email',
    'verify_sent_prefix' => 'Enviamos um código de 6 dígitos para',
    'verify_sent_suffix' => 'Digite-o abaixo para continuar.',
    'field_verification_code' => 'Código de verificação',
    'verify_button' => 'Verificar',
    'status_new_code_sent' => 'Um novo código foi enviado para seu email.',
    'status_account_verified' => 'Sua conta foi verificada. Entre para começar.',
    'js_code_expired_request' => 'Código expirado. Solicite um novo abaixo.',
    'js_no_active_code' => 'Nenhum código ativo registrado. Use "Reenviar código" abaixo para obter um.',

    // ---- App\Models\User::accountBlockedMessage() — shown as the login-form
    // error for a frozen/suspended/disabled account, and mid-session by
    // EnsureAccountIsActive middleware.
    'account_frozen' => 'Esta conta está congelada. Entre em contato com o suporte para obter ajuda.',
    'account_suspended' => 'Esta conta está suspensa. Entre em contato com o suporte para obter ajuda.',
    'account_disabled' => 'Esta conta foi desativada.',
    'account_blocked_default' => 'Esta conta não pode entrar no momento.',
];
