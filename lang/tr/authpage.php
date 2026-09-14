<?php

return [
    // ---- Shared auth-layout decorative side panel (layouts/auth/simple.blade.php)
    // — shown on every auth page (login, register, forgot/reset password, 2FA,
    // confirm password, verify email). "Ledger" itself stays untranslated
    // everywhere, same convention as the rest of the app.
    'panel_eyebrow' => 'Bankacılık, düzenli tutulur',
    'panel_headline_line1' => 'Her dolar,',
    'panel_headline_accent' => 'hesabı verilir.',
    'panel_sub' => 'Bir hesap açın, para transfer edin ve bakiyenizi tek, sakin ve dürüst bir hesap defterinde büyütün.',
    'mock_balance_label' => 'Bakiye',
    'mock_account_label' => 'Hesap · Vadesiz',
    'entry_fdic' => "250.000 $'a kadar FDIC güvencesi",
    'entry_no_fees' => 'Asla gizli ücret yok',
    'entry_instant' => 'Anında transferler, 7/24',

    // ---- Shared field labels (reused across several forms below)
    'field_email' => 'E-posta adresi',
    'field_email_short' => 'E-posta',
    'field_password' => 'Şifre',
    'field_confirm_password' => 'Şifreyi onayla',

    // ---- Login (pages/auth/login.blade.php)
    'login_title' => 'Giriş yap',
    'login_heading' => 'Hesabınıza giriş yapın',
    'login_description' => 'Giriş yapmak için e-postanızı ve şifrenizi aşağıya girin',
    'forgot_password_link' => 'Şifrenizi mi unuttunuz?',
    'remember_me' => 'Beni hatırla',
    'no_account' => 'Hesabınız yok mu?',
    'sign_up' => 'Kaydol',
    'passkey_signin' => 'Geçiş anahtarıyla giriş yap',
    'passkey_authenticating' => 'Doğrulanıyor...',
    'passkey_or_email' => 'Ya da e-posta ile devam et',

    // ---- Register (pages/auth/register.blade.php)
    'register_title' => 'Hesabınızı oluşturun',
    'register_subtitle' => 'Birkaç dakika içinde bir Ledger hesabı açın.',
    'field_first_name' => 'Ad',
    'field_last_name' => 'Soyad',
    'field_middle_name' => 'İkinci ad (isteğe bağlı)',
    'field_phone' => 'Telefon (isteğe bağlı)',
    'register_button' => 'Hesap oluştur',
    'already_have_account' => 'Zaten bir hesabınız var mı?',

    // ---- Confirm password (pages/auth/confirm-password.blade.php)
    'confirm_password_title' => 'Şifreyi onayla',
    'confirm_password_description' => 'Bu, uygulamanın güvenli bir bölümüdür. Devam etmeden önce lütfen şifrenizi onaylayın.',
    'confirm_with_passkey' => 'Geçiş anahtarıyla onayla',
    'confirming' => 'Onaylanıyor...',
    'or_confirm_with_password' => 'Ya da şifreyle onayla',
    'confirm_button' => 'Onayla',

    // ---- Forgot / reset password — the live 6-digit-code flow
    // (livewire/auth/forgot-password-code.blade.php, both steps)
    'forgot_password_title' => 'Şifremi unuttum',
    'forgot_password_description' => 'E-postanızı girin, sıfırlamanız için size 6 haneli bir kod gönderelim.',
    'send_reset_code' => 'Sıfırlama kodu gönder',
    'or_return_to' => 'Ya da şuraya dön:',
    'log_in_lowercase' => 'giriş yap',
    'enter_your_code' => 'Kodunuzu girin',
    'code_sent_to_prefix' => 'Şu adrese gönderdiğimiz 6 haneli kodu girin:',
    'code_sent_to_suffix' => 've yeni bir şifre belirleyin.',
    'field_reset_code' => 'Sıfırlama kodu',
    'field_new_password' => 'Yeni şifre',
    'field_confirm_new_password' => 'Yeni şifreyi onayla',
    'use_different_email' => 'Farklı bir e-posta kullan',
    'resend_code' => 'Kodu yeniden gönder',
    'status_code_sent_known' => ':email için bir hesap varsa, 6 haneli bir kod gönderdik.',
    'status_code_resent' => 'Bu e-postaya ait bir hesap varsa yeni bir kod gönderildi.',
    'error_code_invalid' => 'Bu kod geçersiz veya süresi dolmuş. Aşağıdan yeni bir tane isteyin.',
    'status_password_reset_done' => 'Şifreniz sıfırlandı. Yeni şifrenizle giriş yapın.',
    'js_code_expires_in' => 'Kodun süresi doluyor:',
    'js_code_expired_resend' => 'Kodun süresi doldu. Aşağıdan "Kodu yeniden gönder"i kullanın.',

    // ---- Reset password — the classic email-link flow (pages/auth/reset-password.blade.php)
    'reset_password_title' => 'Şifreyi sıfırla',
    'reset_password_description' => 'Lütfen yeni şifrenizi aşağıya girin',

    // ---- Two-factor challenge (pages/auth/two-factor-challenge.blade.php)
    'two_factor_title' => 'İki faktörlü kimlik doğrulama',
    'auth_code_title' => 'Doğrulama kodu',
    'auth_code_description' => 'Kimlik doğrulama uygulamanızın sağladığı doğrulama kodunu girin.',
    'field_otp_code' => 'OTP Kodu',
    'recovery_code_title' => 'Kurtarma kodu',
    'recovery_code_description' => 'Lütfen acil durum kurtarma kodlarınızdan birini girerek hesabınıza erişimi onaylayın.',
    'continue_button' => 'Devam et',
    'or_you_can' => 'veya şunu yapabilirsiniz:',
    'login_using_recovery_code' => 'kurtarma koduyla giriş yap',
    'login_using_auth_code' => 'doğrulama koduyla giriş yap',

    // ---- Verify email — the live 6-digit-code flow
    // (resources/views/auth/verify-email-page.blade.php + livewire/auth/verify-code.blade.php)
    'verify_email_title' => 'E-postanızı doğrulayın',
    'verify_sent_prefix' => 'Şu adrese 6 haneli bir kod gönderdik:',
    'verify_sent_suffix' => 'Devam etmek için aşağıya girin.',
    'field_verification_code' => 'Doğrulama kodu',
    'verify_button' => 'Doğrula',
    'status_new_code_sent' => 'E-postanıza yeni bir kod gönderildi.',
    'status_account_verified' => 'Hesabınız doğrulandı. Başlamak için giriş yapın.',
    'js_code_expired_request' => 'Kodun süresi doldu. Aşağıdan yeni bir tane isteyin.',
    'js_no_active_code' => 'Kayıtlı aktif bir kod yok. Bir tane almak için aşağıdan "Kodu yeniden gönder"i kullanın.',

    // ---- App\Models\User::accountBlockedMessage() — shown as the login-form
    // error for a frozen/suspended/disabled account, and mid-session by
    // EnsureAccountIsActive middleware.
    'account_frozen' => 'Bu hesap donduruldu. Yardım için destek ekibiyle iletişime geçin.',
    'account_suspended' => 'Bu hesap askıya alındı. Yardım için destek ekibiyle iletişime geçin.',
    'account_disabled' => 'Bu hesap devre dışı bırakıldı.',
    'account_blocked_default' => 'Bu hesap şu anda oturum açamaz.',
];
