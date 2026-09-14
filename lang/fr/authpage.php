<?php

return [
    // ---- Shared auth-layout decorative side panel (layouts/auth/simple.blade.php)
    // — shown on every auth page (login, register, forgot/reset password, 2FA,
    // confirm password, verify email). "Ledger" itself stays untranslated
    // everywhere, same convention as the rest of the app.
    'panel_eyebrow' => 'La banque, en ordre',
    'panel_headline_line1' => 'Chaque euro,',
    'panel_headline_accent' => 'pris en compte.',
    'panel_sub' => "Ouvrez un compte, transférez de l'argent et faites fructifier votre solde dans un registre calme et honnête.",
    'mock_balance_label' => 'Solde',
    'mock_account_label' => 'Compte · Courant',
    'entry_fdic' => "Assuré par la FDIC jusqu'à 250 000 $",
    'entry_no_fees' => 'Aucun frais caché, jamais',
    'entry_instant' => 'Virements instantanés, 24h/24 et 7j/7',

    // ---- Shared field labels (reused across several forms below)
    'field_email' => 'Adresse e-mail',
    'field_email_short' => 'E-mail',
    'field_password' => 'Mot de passe',
    'field_confirm_password' => 'Confirmer le mot de passe',

    // ---- Login (pages/auth/login.blade.php)
    'login_title' => 'Connexion',
    'login_heading' => 'Connectez-vous à votre compte',
    'login_description' => 'Saisissez votre e-mail et votre mot de passe ci-dessous pour vous connecter',
    'forgot_password_link' => 'Mot de passe oublié ?',
    'remember_me' => 'Se souvenir de moi',
    'no_account' => "Vous n'avez pas de compte ?",
    'sign_up' => 'Créer un compte',
    'passkey_signin' => "Se connecter avec une clé d'accès",
    'passkey_authenticating' => 'Authentification en cours...',
    'passkey_or_email' => 'Ou continuez avec un e-mail',

    // ---- Register (pages/auth/register.blade.php)
    'register_title' => 'Créez votre compte',
    'register_subtitle' => 'Ouvrez un compte Ledger en quelques minutes.',
    'field_first_name' => 'Prénom',
    'field_last_name' => 'Nom',
    'field_middle_name' => 'Deuxième prénom (facultatif)',
    'field_phone' => 'Téléphone (facultatif)',
    'register_button' => 'Créer un compte',
    'already_have_account' => 'Vous avez déjà un compte ?',

    // ---- Confirm password (pages/auth/confirm-password.blade.php)
    'confirm_password_title' => 'Confirmer le mot de passe',
    'confirm_password_description' => "Ceci est une zone sécurisée de l'application. Veuillez confirmer votre mot de passe avant de continuer.",
    'confirm_with_passkey' => "Confirmer avec une clé d'accès",
    'confirming' => 'Confirmation en cours...',
    'or_confirm_with_password' => 'Ou confirmez avec votre mot de passe',
    'confirm_button' => 'Confirmer',

    // ---- Forgot / reset password — the live 6-digit-code flow
    // (livewire/auth/forgot-password-code.blade.php, both steps)
    'forgot_password_title' => 'Mot de passe oublié',
    'forgot_password_description' => 'Saisissez votre e-mail et nous vous enverrons un code à 6 chiffres pour le réinitialiser.',
    'send_reset_code' => 'Envoyer le code de réinitialisation',
    'or_return_to' => 'Ou bien, retournez à la',
    'log_in_lowercase' => 'connexion',
    'enter_your_code' => 'Saisissez votre code',
    'code_sent_to_prefix' => 'Saisissez le code à 6 chiffres que nous avons envoyé à',
    'code_sent_to_suffix' => 'et choisissez un nouveau mot de passe.',
    'field_reset_code' => 'Code de réinitialisation',
    'field_new_password' => 'Nouveau mot de passe',
    'field_confirm_new_password' => 'Confirmer le nouveau mot de passe',
    'use_different_email' => 'Utiliser une autre adresse e-mail',
    'resend_code' => 'Renvoyer le code',
    'status_code_sent_known' => 'Si un compte existe pour :email, nous lui avons envoyé un code à 6 chiffres.',
    'status_code_resent' => 'Un nouveau code a été envoyé, si cette adresse e-mail correspond à un compte.',
    'error_code_invalid' => 'Ce code est invalide ou a expiré. Demandez-en un nouveau ci-dessous.',
    'status_password_reset_done' => 'Votre mot de passe a été réinitialisé. Connectez-vous avec votre nouveau mot de passe.',
    'js_code_expires_in' => 'Le code expire dans',
    'js_code_expired_resend' => 'Le code a expiré. Utilisez « Renvoyer le code » ci-dessous.',

    // ---- Reset password — the classic email-link flow (pages/auth/reset-password.blade.php)
    'reset_password_title' => 'Réinitialiser le mot de passe',
    'reset_password_description' => 'Veuillez saisir votre nouveau mot de passe ci-dessous',

    // ---- Two-factor challenge (pages/auth/two-factor-challenge.blade.php)
    'two_factor_title' => 'Authentification à deux facteurs',
    'auth_code_title' => "Code d'authentification",
    'auth_code_description' => "Saisissez le code d'authentification fourni par votre application d'authentification.",
    'field_otp_code' => 'Code OTP',
    'recovery_code_title' => 'Code de récupération',
    'recovery_code_description' => "Veuillez confirmer l'accès à votre compte en saisissant l'un de vos codes de récupération d'urgence.",
    'continue_button' => 'Continuer',
    'or_you_can' => 'ou vous pouvez',
    'login_using_recovery_code' => 'vous connecter avec un code de récupération',
    'login_using_auth_code' => "vous connecter avec un code d'authentification",

    // ---- Verify email — the live 6-digit-code flow
    // (resources/views/auth/verify-email-page.blade.php + livewire/auth/verify-code.blade.php)
    'verify_email_title' => 'Vérifiez votre e-mail',
    'verify_sent_prefix' => 'Nous avons envoyé un code à 6 chiffres à',
    'verify_sent_suffix' => 'Saisissez-le ci-dessous pour continuer.',
    'field_verification_code' => 'Code de vérification',
    'verify_button' => 'Vérifier',
    'status_new_code_sent' => 'Un nouveau code a été envoyé à votre adresse e-mail.',
    'status_account_verified' => 'Votre compte est vérifié. Connectez-vous pour commencer.',
    'js_code_expired_request' => 'Le code a expiré. Demandez-en un nouveau ci-dessous.',
    'js_no_active_code' => 'Aucun code actif enregistré. Utilisez « Renvoyer le code » ci-dessous pour en obtenir un.',

    // ---- App\Models\User::accountBlockedMessage() — shown as the login-form
    // error for a frozen/suspended/disabled account, and mid-session by
    // EnsureAccountIsActive middleware.
    'account_frozen' => "Ce compte est gelé. Contactez le support pour obtenir de l'aide.",
    'account_suspended' => "Ce compte est suspendu. Contactez le support pour obtenir de l'aide.",
    'account_disabled' => 'Ce compte a été désactivé.',
    'account_blocked_default' => 'Ce compte ne peut pas se connecter pour le moment.',
];
