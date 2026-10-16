<?php

return [
    // ---- Shared auth-layout decorative side panel (layouts/auth/simple.blade.php)
    // — shown on every auth page (login, register, forgot/reset password, 2FA,
    // confirm password, verify email). "Ledger" itself stays untranslated
    // everywhere, same convention as the rest of the app.
    'panel_eyebrow' => 'Banca en orden',
    'panel_headline_line1' => 'Cada dólar,',
    'panel_headline_accent' => 'contabilizado.',
    'panel_sub' => 'Abre una cuenta, mueve tu dinero y haz crecer tu saldo en un libro contable tranquilo y honesto.',
    'mock_balance_label' => 'Saldo',
    'mock_account_label' => 'Cuenta · Corriente',
    'entry_fdic' => 'Asegurado por la FDIC hasta $250,000',
    'entry_no_fees' => 'Sin comisiones ocultas, nunca',
    'entry_instant' => 'Transferencias instantáneas, 24/7',

    // ---- Shared field labels (reused across several forms below)
    'field_email' => 'Correo electrónico',
    'field_email_short' => 'Correo',
    'field_password' => 'Contraseña',
    'field_confirm_password' => 'Confirmar contraseña',

    // ---- Login (pages/auth/login.blade.php)
    'login_title' => 'Iniciar sesión',
    'login_heading' => 'Inicia sesión en tu cuenta',
    'login_description' => 'Introduce tu correo electrónico y contraseña para iniciar sesión',
    'forgot_password_link' => '¿Olvidaste tu contraseña?',
    'remember_me' => 'Recuérdame',
    'no_account' => '¿No tienes una cuenta?',
    'sign_up' => 'Regístrate',
    'passkey_signin' => 'Iniciar sesión con una clave de acceso',
    'passkey_authenticating' => 'Autenticando...',
    'passkey_or_email' => 'O continúa con correo electrónico',

    // ---- Register (pages/auth/register.blade.php)
    'register_title' => 'Crea tu cuenta',
    'register_subtitle' => 'Abre una cuenta Ledger en un par de minutos.',
    'field_first_name' => 'Nombre',
    'field_last_name' => 'Apellido',
    'field_middle_name' => 'Segundo nombre (opcional)',
    'field_phone' => 'Teléfono (opcional)',
    'register_button' => 'Crear cuenta',
    'already_have_account' => '¿Ya tienes una cuenta?',

    // ---- Confirm password (pages/auth/confirm-password.blade.php)
    'confirm_password_title' => 'Confirmar contraseña',
    'confirm_password_description' => 'Esta es un área segura de la aplicación. Confirma tu contraseña antes de continuar.',
    'confirm_with_passkey' => 'Confirmar con clave de acceso',
    'confirming' => 'Confirmando...',
    'or_confirm_with_password' => 'O confirma con tu contraseña',
    'confirm_button' => 'Confirmar',

    // ---- Forgot / reset password — the live 6-digit-code flow
    // (livewire/auth/forgot-password-code.blade.php, both steps)
    'forgot_password_title' => 'Olvidé mi contraseña',
    'forgot_password_description' => 'Introduce tu correo electrónico y te enviaremos un código de 6 dígitos para restablecerla.',
    'send_reset_code' => 'Enviar código de restablecimiento',
    'or_return_to' => 'O bien, vuelve a',
    'log_in_lowercase' => 'iniciar sesión',
    'enter_your_code' => 'Introduce tu código',
    'code_sent_to_prefix' => 'Introduce el código de 6 dígitos que enviamos a',
    'code_sent_to_suffix' => 'y elige una nueva contraseña.',
    'field_reset_code' => 'Código de restablecimiento',
    'field_new_password' => 'Nueva contraseña',
    'field_confirm_new_password' => 'Confirmar nueva contraseña',
    'use_different_email' => 'Usar otro correo electrónico',
    'resend_code' => 'Reenviar código',
    'status_code_sent_known' => 'Si existe una cuenta para :email, te hemos enviado un código de 6 dígitos.',
    'status_code_resent' => 'Se ha enviado un nuevo código, si ese correo tiene una cuenta asociada.',
    'error_code_invalid' => 'Ese código no es válido o ha caducado. Solicita uno nuevo a continuación.',
    'status_password_reset_done' => 'Tu contraseña se ha restablecido. Inicia sesión con tu nueva contraseña.',
    'js_code_expires_in' => 'El código caduca en',
    'js_code_expired_resend' => 'El código ha caducado. Usa "Reenviar código" abajo.',

    // ---- Reset password — the classic email-link flow (pages/auth/reset-password.blade.php)
    'reset_password_title' => 'Restablecer contraseña',
    'reset_password_description' => 'Introduce tu nueva contraseña a continuación',

    // ---- Two-factor challenge (pages/auth/two-factor-challenge.blade.php)
    'two_factor_title' => 'Autenticación de dos factores',
    'auth_code_title' => 'Código de autenticación',
    'auth_code_description' => 'Introduce el código de autenticación proporcionado por tu aplicación autenticadora.',
    'field_otp_code' => 'Código OTP',
    'recovery_code_title' => 'Código de recuperación',
    'recovery_code_description' => 'Confirma el acceso a tu cuenta introduciendo uno de tus códigos de recuperación de emergencia.',
    'continue_button' => 'Continuar',
    'or_you_can' => 'o puedes',
    'login_using_recovery_code' => 'iniciar sesión con un código de recuperación',
    'login_using_auth_code' => 'iniciar sesión con un código de autenticación',

    // ---- Verify email — the live 6-digit-code flow
    // (resources/views/auth/verify-email-page.blade.php + livewire/auth/verify-code.blade.php)
    'verify_email_title' => 'Verifica tu correo electrónico',
    'verify_sent_prefix' => 'Enviamos un código de 6 dígitos a',
    'verify_sent_suffix' => 'Introdúcelo abajo para continuar.',
    'field_verification_code' => 'Código de verificación',
    'verify_button' => 'Verificar',
    'status_new_code_sent' => 'Se ha enviado un nuevo código a tu correo electrónico.',
    'status_account_verified' => 'Tu cuenta está verificada. Inicia sesión para comenzar.',
    'js_code_expired_request' => 'El código ha caducado. Solicita uno nuevo abajo.',
    'js_no_active_code' => 'No hay ningún código activo registrado. Usa "Reenviar código" abajo para obtener uno.',

    // ---- App\Models\User::accountBlockedMessage() — shown as the login-form
    // error for a frozen/suspended/disabled account, and mid-session by
    // EnsureAccountIsActive middleware.
    'account_frozen' => 'Esta cuenta está congelada. Ponte en contacto con soporte para obtener ayuda.',
    'account_suspended' => 'Esta cuenta está suspendida. Ponte en contacto con soporte para obtener ayuda.',
    'account_disabled' => 'Esta cuenta ha sido deshabilitada.',
    'account_blocked_default' => 'Esta cuenta no puede iniciar sesión en este momento.',
    'idle_warning_title' => '¿Sigues ahí?',
    'idle_warning_body' => 'Por tu seguridad, pronto se cerrará tu sesión porque no ha habido actividad durante un rato. Se cerrará la sesión en',
    'idle_stay_signed_in' => 'Mantener la sesión',
    'idle_signed_out' => 'Se cerró tu sesión porque no hubo actividad durante un rato. Vuelve a iniciar sesión.',
    'wallet_frozen' => 'Tu billetera está congelada. Aún puedes iniciar sesión y ver tu cuenta, pero no se puede enviar ni recibir dinero. Contacta con soporte para descongelarla.',
    'sms_verification_code' => 'Ledger: tu código de verificación es :code. Caduca en 10 minutos. No lo compartas con nadie.',
    'sms_reset_code' => 'Ledger: tu código para restablecer la contraseña es :code. Caduca en 10 minutos. Si no lo solicitaste, ignóralo.',
    'text_code_instead' => 'Enviar el código por SMS a :phone',
    'status_code_texted' => 'Enviamos un nuevo código por SMS a :phone.',
    'sms_add_phone' => 'Para recibir códigos por SMS, añade tu número de teléfono con el código de país (por ejemplo +234 801 234 5678) en la configuración del perfil.',
    'sms_failed' => 'No pudimos enviar un SMS en este momento. Usa el código que te enviamos por correo o inténtalo más tarde.',
    'sms_too_many' => 'Se enviaron demasiados SMS a este número. Espera 10 minutos e inténtalo de nuevo.',
    'reset_send_by' => 'Enviar el código por',
    'reset_by_email' => 'Correo electrónico',
    'reset_by_text' => 'SMS',
    'reset_by_text_note' => 'Lo enviaremos por SMS al número de teléfono de tu cuenta.',
    'code_texted_for_prefix' => 'Introduce el código de 6 dígitos que enviamos por SMS al teléfono de la cuenta de',
    'code_texted_for_suffix' => 'y elige una nueva contraseña.',
    'status_code_texted_known' => 'Si :email tiene una cuenta con número de teléfono, le enviamos un código de 6 dígitos por SMS.',
];
