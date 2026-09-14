<?php

return [
    // ---- Shared auth-layout decorative side panel (layouts/auth/simple.blade.php)
    // — shown on every auth page (login, register, forgot/reset password, 2FA,
    // confirm password, verify email). "Ledger" itself stays untranslated
    // everywhere, same convention as the rest of the app.
    'panel_eyebrow' => 'Банкинг в полном порядке',
    'panel_headline_line1' => 'Каждый доллар',
    'panel_headline_accent' => 'учтён.',
    'panel_sub' => 'Открывайте счёт, переводите деньги и приумножайте баланс в одном спокойном и честном гроссбухе.',
    'mock_balance_label' => 'Баланс',
    'mock_account_label' => 'Счёт · Текущий',
    'entry_fdic' => 'Застраховано FDIC на сумму до $250,000',
    'entry_no_fees' => 'Никаких скрытых комиссий, никогда',
    'entry_instant' => 'Мгновенные переводы, 24/7',

    // ---- Shared field labels (reused across several forms below)
    'field_email' => 'Адрес электронной почты',
    'field_email_short' => 'Email',
    'field_password' => 'Пароль',
    'field_confirm_password' => 'Подтвердите пароль',

    // ---- Login (pages/auth/login.blade.php)
    'login_title' => 'Вход',
    'login_heading' => 'Войдите в свой аккаунт',
    'login_description' => 'Введите email и пароль ниже, чтобы войти',
    'forgot_password_link' => 'Забыли пароль?',
    'remember_me' => 'Запомнить меня',
    'no_account' => 'Ещё нет аккаунта?',
    'sign_up' => 'Зарегистрироваться',
    'passkey_signin' => 'Войти с помощью ключа доступа',
    'passkey_authenticating' => 'Проверка...',
    'passkey_or_email' => 'Или продолжить через email',

    // ---- Register (pages/auth/register.blade.php)
    'register_title' => 'Создайте аккаунт',
    'register_subtitle' => 'Откройте счёт Ledger за пару минут.',
    'field_first_name' => 'Имя',
    'field_last_name' => 'Фамилия',
    'field_middle_name' => 'Отчество (необязательно)',
    'field_phone' => 'Телефон (необязательно)',
    'register_button' => 'Создать аккаунт',
    'already_have_account' => 'Уже есть аккаунт?',

    // ---- Confirm password (pages/auth/confirm-password.blade.php)
    'confirm_password_title' => 'Подтверждение пароля',
    'confirm_password_description' => 'Это защищённый раздел приложения. Пожалуйста, подтвердите свой пароль, прежде чем продолжить.',
    'confirm_with_passkey' => 'Подтвердить ключом доступа',
    'confirming' => 'Подтверждение...',
    'or_confirm_with_password' => 'Или подтвердить паролем',
    'confirm_button' => 'Подтвердить',

    // ---- Forgot / reset password — the live 6-digit-code flow
    // (livewire/auth/forgot-password-code.blade.php, both steps)
    'forgot_password_title' => 'Забыли пароль',
    'forgot_password_description' => 'Введите свой email, и мы отправим вам 6-значный код для сброса пароля.',
    'send_reset_code' => 'Отправить код сброса',
    'or_return_to' => 'Или вернуться к',
    'log_in_lowercase' => 'входу',
    'enter_your_code' => 'Введите код',
    'code_sent_to_prefix' => 'Введите 6-значный код, отправленный на',
    'code_sent_to_suffix' => 'и придумайте новый пароль.',
    'field_reset_code' => 'Код сброса',
    'field_new_password' => 'Новый пароль',
    'field_confirm_new_password' => 'Подтвердите новый пароль',
    'use_different_email' => 'Использовать другой email',
    'resend_code' => 'Отправить код повторно',
    'status_code_sent_known' => 'Если для :email существует аккаунт, мы отправили на него 6-значный код.',
    'status_code_resent' => 'Новый код отправлен, если у этого email есть аккаунт.',
    'error_code_invalid' => 'Этот код неверен или его срок истёк. Запросите новый ниже.',
    'status_password_reset_done' => 'Ваш пароль был сброшен. Войдите с новым паролем.',
    'js_code_expires_in' => 'Код действителен ещё',
    'js_code_expired_resend' => 'Срок действия кода истёк. Нажмите «Отправить код повторно» ниже.',

    // ---- Reset password — the classic email-link flow (pages/auth/reset-password.blade.php)
    'reset_password_title' => 'Сброс пароля',
    'reset_password_description' => 'Пожалуйста, введите новый пароль ниже',

    // ---- Two-factor challenge (pages/auth/two-factor-challenge.blade.php)
    'two_factor_title' => 'Двухфакторная аутентификация',
    'auth_code_title' => 'Код аутентификации',
    'auth_code_description' => 'Введите код аутентификации, предоставленный вашим приложением-аутентификатором.',
    'field_otp_code' => 'Код OTP',
    'recovery_code_title' => 'Код восстановления',
    'recovery_code_description' => 'Пожалуйста, подтвердите доступ к своему аккаунту, введя один из резервных кодов восстановления.',
    'continue_button' => 'Продолжить',
    'or_you_can' => 'или вы можете',
    'login_using_recovery_code' => 'войти с помощью кода восстановления',
    'login_using_auth_code' => 'войти с помощью кода аутентификации',

    // ---- Verify email — the live 6-digit-code flow
    // (resources/views/auth/verify-email-page.blade.php + livewire/auth/verify-code.blade.php)
    'verify_email_title' => 'Подтвердите свой email',
    'verify_sent_prefix' => 'Мы отправили 6-значный код на',
    'verify_sent_suffix' => 'Введите его ниже, чтобы продолжить.',
    'field_verification_code' => 'Код подтверждения',
    'verify_button' => 'Подтвердить',
    'status_new_code_sent' => 'На ваш email отправлен новый код.',
    'status_account_verified' => 'Ваш аккаунт подтверждён. Войдите, чтобы начать.',
    'js_code_expired_request' => 'Срок действия кода истёк. Запросите новый ниже.',
    'js_no_active_code' => 'Активный код не найден. Нажмите «Отправить код повторно» ниже, чтобы получить его.',

    // ---- App\Models\User::accountBlockedMessage() — shown as the login-form
    // error for a frozen/suspended/disabled account, and mid-session by
    // EnsureAccountIsActive middleware.
    'account_frozen' => 'Этот аккаунт заморожен. Обратитесь в службу поддержки.',
    'account_suspended' => 'Этот аккаунт приостановлен. Обратитесь в службу поддержки.',
    'account_disabled' => 'Этот аккаунт отключён.',
    'account_blocked_default' => 'Этот аккаунт сейчас не может войти в систему.',
];
