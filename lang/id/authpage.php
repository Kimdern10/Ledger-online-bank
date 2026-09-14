<?php

return [
    // ---- Shared auth-layout decorative side panel (layouts/auth/simple.blade.php)
    // — shown on every auth page (login, register, forgot/reset password, 2FA,
    // confirm password, verify email). "Ledger" itself stays untranslated
    // everywhere, same convention as the rest of the app.
    'panel_eyebrow' => 'Perbankan yang tertata rapi',
    'panel_headline_line1' => 'Setiap dolar,',
    'panel_headline_accent' => 'tercatat dengan jelas.',
    'panel_sub' => 'Buka rekening, pindahkan uang, dan kembangkan saldo Anda dalam satu pembukuan yang tenang dan jujur.',
    'mock_balance_label' => 'Saldo',
    'mock_account_label' => 'Rekening · Giro',
    'entry_fdic' => 'Diasuransikan FDIC hingga $250.000',
    'entry_no_fees' => 'Tanpa biaya tersembunyi, selamanya',
    'entry_instant' => 'Transfer instan, 24/7',

    // ---- Shared field labels (reused across several forms below)
    'field_email' => 'Alamat email',
    'field_email_short' => 'Email',
    'field_password' => 'Kata sandi',
    'field_confirm_password' => 'Konfirmasi kata sandi',

    // ---- Login (pages/auth/login.blade.php)
    'login_title' => 'Masuk',
    'login_heading' => 'Masuk ke akun Anda',
    'login_description' => 'Masukkan email dan kata sandi Anda di bawah ini untuk masuk',
    'forgot_password_link' => 'Lupa kata sandi Anda?',
    'remember_me' => 'Ingat saya',
    'no_account' => 'Belum punya akun?',
    'sign_up' => 'Daftar',
    'passkey_signin' => 'Masuk dengan kunci sandi',
    'passkey_authenticating' => 'Mengautentikasi...',
    'passkey_or_email' => 'Atau lanjutkan dengan email',

    // ---- Register (pages/auth/register.blade.php)
    'register_title' => 'Buat akun Anda',
    'register_subtitle' => 'Buka akun Ledger hanya dalam beberapa menit.',
    'field_first_name' => 'Nama Depan',
    'field_last_name' => 'Nama Belakang',
    'field_middle_name' => 'Nama Tengah (opsional)',
    'field_phone' => 'Telepon (opsional)',
    'register_button' => 'Buat akun',
    'already_have_account' => 'Sudah punya akun?',

    // ---- Confirm password (pages/auth/confirm-password.blade.php)
    'confirm_password_title' => 'Konfirmasi kata sandi',
    'confirm_password_description' => 'Ini adalah area aman pada aplikasi. Harap konfirmasi kata sandi Anda sebelum melanjutkan.',
    'confirm_with_passkey' => 'Konfirmasi dengan kunci sandi',
    'confirming' => 'Mengonfirmasi...',
    'or_confirm_with_password' => 'Atau konfirmasi dengan kata sandi',
    'confirm_button' => 'Konfirmasi',

    // ---- Forgot / reset password — the live 6-digit-code flow
    // (livewire/auth/forgot-password-code.blade.php, both steps)
    'forgot_password_title' => 'Lupa kata sandi',
    'forgot_password_description' => 'Masukkan email Anda dan kami akan mengirimkan kode 6 digit untuk mengaturnya ulang.',
    'send_reset_code' => 'Kirim kode reset',
    'or_return_to' => 'Atau, kembali ke',
    'log_in_lowercase' => 'masuk',
    'enter_your_code' => 'Masukkan kode Anda',
    'code_sent_to_prefix' => 'Masukkan kode 6 digit yang kami kirim ke',
    'code_sent_to_suffix' => 'dan pilih kata sandi baru.',
    'field_reset_code' => 'Kode reset',
    'field_new_password' => 'Kata sandi baru',
    'field_confirm_new_password' => 'Konfirmasi kata sandi baru',
    'use_different_email' => 'Gunakan email lain',
    'resend_code' => 'Kirim ulang kode',
    'status_code_sent_known' => "Jika akun untuk :email ada, kami telah mengirimkan kode 6 digit ke sana.",
    'status_code_resent' => 'Kode baru telah dikirim, jika email tersebut memiliki akun.',
    'error_code_invalid' => 'Kode tersebut tidak valid atau telah kedaluwarsa. Minta kode baru di bawah ini.',
    'status_password_reset_done' => 'Kata sandi Anda telah direset. Masuk dengan kata sandi baru Anda.',
    'js_code_expires_in' => 'Kode kedaluwarsa dalam',
    'js_code_expired_resend' => 'Kode telah kedaluwarsa. Gunakan "Kirim ulang kode" di bawah ini.',

    // ---- Reset password — the classic email-link flow (pages/auth/reset-password.blade.php)
    'reset_password_title' => 'Reset kata sandi',
    'reset_password_description' => 'Silakan masukkan kata sandi baru Anda di bawah ini',

    // ---- Two-factor challenge (pages/auth/two-factor-challenge.blade.php)
    'two_factor_title' => 'Autentikasi dua faktor',
    'auth_code_title' => 'Kode autentikasi',
    'auth_code_description' => 'Masukkan kode autentikasi yang diberikan oleh aplikasi autentikator Anda.',
    'field_otp_code' => 'Kode OTP',
    'recovery_code_title' => 'Kode pemulihan',
    'recovery_code_description' => 'Silakan konfirmasi akses ke akun Anda dengan memasukkan salah satu kode pemulihan darurat Anda.',
    'continue_button' => 'Lanjutkan',
    'or_you_can' => 'atau Anda dapat',
    'login_using_recovery_code' => 'masuk menggunakan kode pemulihan',
    'login_using_auth_code' => 'masuk menggunakan kode autentikasi',

    // ---- Verify email — the live 6-digit-code flow
    // (resources/views/auth/verify-email-page.blade.php + livewire/auth/verify-code.blade.php)
    'verify_email_title' => 'Verifikasi email Anda',
    'verify_sent_prefix' => 'Kami mengirim kode 6 digit ke',
    'verify_sent_suffix' => 'Masukkan di bawah ini untuk melanjutkan.',
    'field_verification_code' => 'Kode verifikasi',
    'verify_button' => 'Verifikasi',
    'status_new_code_sent' => 'Kode baru telah dikirim ke email Anda.',
    'status_account_verified' => 'Akun Anda telah terverifikasi. Masuk untuk memulai.',
    'js_code_expired_request' => 'Kode telah kedaluwarsa. Minta kode baru di bawah ini.',
    'js_no_active_code' => 'Tidak ada kode aktif yang tersimpan. Gunakan "Kirim ulang kode" di bawah ini untuk mendapatkannya.',

    // ---- App\Models\User::accountBlockedMessage() — shown as the login-form
    // error for a frozen/suspended/disabled account, and mid-session by
    // EnsureAccountIsActive middleware.
    'account_frozen' => 'Akun ini dibekukan. Hubungi dukungan untuk bantuan.',
    'account_suspended' => 'Akun ini ditangguhkan. Hubungi dukungan untuk bantuan.',
    'account_disabled' => 'Akun ini telah dinonaktifkan.',
    'account_blocked_default' => 'Akun ini tidak dapat masuk saat ini.',
];
