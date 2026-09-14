<?php

return [
    // ---- Shared auth-layout decorative side panel (layouts/auth/simple.blade.php)
    // — shown on every auth page (login, register, forgot/reset password, 2FA,
    // confirm password, verify email). "Ledger" itself stays untranslated
    // everywhere, same convention as the rest of the app.
    'panel_eyebrow' => 'Banking, kept in order',
    'panel_headline_line1' => 'Every dollar,',
    'panel_headline_accent' => 'accounted for.',
    'panel_sub' => 'Open an account, move money, and grow your balance in one calm, honest ledger.',
    'mock_balance_label' => 'Balance',
    'mock_account_label' => 'Account · Checking',
    'entry_fdic' => 'FDIC-insured up to $250,000',
    'entry_no_fees' => 'No hidden fees, ever',
    'entry_instant' => 'Instant transfers, 24/7',

    // ---- Shared field labels (reused across several forms below)
    'field_email' => 'Email address',
    'field_email_short' => 'Email',
    'field_password' => 'Password',
    'field_confirm_password' => 'Confirm password',

    // ---- Login (pages/auth/login.blade.php)
    'login_title' => 'Log in',
    'login_heading' => 'Log in to your account',
    'login_description' => 'Enter your email and password below to log in',
    'forgot_password_link' => 'Forgot your password?',
    'remember_me' => 'Remember me',
    'no_account' => "Don't have an account?",
    'sign_up' => 'Sign up',
    'passkey_signin' => 'Sign in with a passkey',
    'passkey_authenticating' => 'Authenticating...',
    'passkey_or_email' => 'Or continue with email',

    // ---- Register (pages/auth/register.blade.php)
    'register_title' => 'Create your account',
    'register_subtitle' => 'Open a Ledger account in a couple of minutes.',
    'field_first_name' => 'First Name',
    'field_last_name' => 'Last Name',
    'field_middle_name' => 'Middle Name (optional)',
    'field_phone' => 'Phone (optional)',
    'register_button' => 'Create account',
    'already_have_account' => 'Already have an account?',

    // ---- Confirm password (pages/auth/confirm-password.blade.php)
    'confirm_password_title' => 'Confirm password',
    'confirm_password_description' => 'This is a secure area of the application. Please confirm your password before continuing.',
    'confirm_with_passkey' => 'Confirm with passkey',
    'confirming' => 'Confirming...',
    'or_confirm_with_password' => 'Or confirm with password',
    'confirm_button' => 'Confirm',

    // ---- Forgot / reset password — the live 6-digit-code flow
    // (livewire/auth/forgot-password-code.blade.php, both steps)
    'forgot_password_title' => 'Forgot password',
    'forgot_password_description' => "Enter your email and we'll send you a 6-digit code to reset it.",
    'send_reset_code' => 'Send reset code',
    'or_return_to' => 'Or, return to',
    'log_in_lowercase' => 'log in',
    'enter_your_code' => 'Enter your code',
    'code_sent_to_prefix' => 'Enter the 6-digit code we sent to',
    'code_sent_to_suffix' => 'and choose a new password.',
    'field_reset_code' => 'Reset code',
    'field_new_password' => 'New password',
    'field_confirm_new_password' => 'Confirm new password',
    'use_different_email' => 'Use a different email',
    'resend_code' => 'Resend code',
    'status_code_sent_known' => "If an account exists for :email, we've sent a 6-digit code to it.",
    'status_code_resent' => 'A new code has been sent, if that email has an account.',
    'error_code_invalid' => 'That code is invalid or has expired. Request a new one below.',
    'status_password_reset_done' => 'Your password has been reset. Log in with your new password.',
    'js_code_expires_in' => 'Code expires in',
    'js_code_expired_resend' => 'Code expired. Use "Resend code" below.',

    // ---- Reset password — the classic email-link flow (pages/auth/reset-password.blade.php)
    'reset_password_title' => 'Reset password',
    'reset_password_description' => 'Please enter your new password below',

    // ---- Two-factor challenge (pages/auth/two-factor-challenge.blade.php)
    'two_factor_title' => 'Two-factor authentication',
    'auth_code_title' => 'Authentication code',
    'auth_code_description' => 'Enter the authentication code provided by your authenticator application.',
    'field_otp_code' => 'OTP Code',
    'recovery_code_title' => 'Recovery code',
    'recovery_code_description' => 'Please confirm access to your account by entering one of your emergency recovery codes.',
    'continue_button' => 'Continue',
    'or_you_can' => 'or you can',
    'login_using_recovery_code' => 'login using a recovery code',
    'login_using_auth_code' => 'login using an authentication code',

    // ---- Verify email — the live 6-digit-code flow
    // (resources/views/auth/verify-email-page.blade.php + livewire/auth/verify-code.blade.php)
    'verify_email_title' => 'Verify your email',
    'verify_sent_prefix' => 'We sent a 6-digit code to',
    'verify_sent_suffix' => 'Enter it below to continue.',
    'field_verification_code' => 'Verification code',
    'verify_button' => 'Verify',
    'status_new_code_sent' => 'A new code has been sent to your email.',
    'status_account_verified' => 'Your account is verified. Log in to get started.',
    'js_code_expired_request' => 'Code expired. Request a new one below.',
    'js_no_active_code' => 'No active code on file. Use "Resend code" below to get one.',

    // ---- App\Models\User::accountBlockedMessage() — shown as the login-form
    // error for a frozen/suspended/disabled account, and mid-session by
    // EnsureAccountIsActive middleware.
    'account_frozen' => 'This account is frozen. Contact support for help.',
    'account_suspended' => 'This account is suspended. Contact support for help.',
    'account_disabled' => 'This account has been disabled.',
    'account_blocked_default' => 'This account cannot sign in right now.',
];
