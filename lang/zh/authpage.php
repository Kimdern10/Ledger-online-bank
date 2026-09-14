<?php

return [
    // ---- Shared auth-layout decorative side panel (layouts/auth/simple.blade.php)
    // — shown on every auth page (login, register, forgot/reset password, 2FA,
    // confirm password, verify email). "Ledger" itself stays untranslated
    // everywhere, same convention as the rest of the app.
    'panel_eyebrow' => '井井有条的银行服务',
    'panel_headline_line1' => '每一分钱，',
    'panel_headline_accent' => '都清清楚楚。',
    'panel_sub' => '开设账户、转账，并在一本安心、透明的账本中让您的余额稳步增长。',
    'mock_balance_label' => '余额',
    'mock_account_label' => '账户 · 活期',
    'entry_fdic' => 'FDIC 承保，最高 250,000 美元',
    'entry_no_fees' => '绝无隐藏费用',
    'entry_instant' => '全天候即时转账',

    // ---- Shared field labels (reused across several forms below)
    'field_email' => '电子邮箱地址',
    'field_email_short' => '邮箱',
    'field_password' => '密码',
    'field_confirm_password' => '确认密码',

    // ---- Login (pages/auth/login.blade.php)
    'login_title' => '登录',
    'login_heading' => '登录您的账户',
    'login_description' => '请在下方输入您的邮箱和密码以登录',
    'forgot_password_link' => '忘记密码？',
    'remember_me' => '记住我',
    'no_account' => '还没有账户？',
    'sign_up' => '注册',
    'passkey_signin' => '使用通行密钥登录',
    'passkey_authenticating' => '正在验证...',
    'passkey_or_email' => '或使用邮箱继续',

    // ---- Register (pages/auth/register.blade.php)
    'register_title' => '创建您的账户',
    'register_subtitle' => '几分钟内即可开设 Ledger 账户。',
    'field_first_name' => '名',
    'field_last_name' => '姓',
    'field_middle_name' => '中间名（选填）',
    'field_phone' => '电话（选填）',
    'register_button' => '创建账户',
    'already_have_account' => '已有账户？',

    // ---- Confirm password (pages/auth/confirm-password.blade.php)
    'confirm_password_title' => '确认密码',
    'confirm_password_description' => '这是应用程序的安全区域，请在继续之前确认您的密码。',
    'confirm_with_passkey' => '使用通行密钥确认',
    'confirming' => '正在确认...',
    'or_confirm_with_password' => '或使用密码确认',
    'confirm_button' => '确认',

    // ---- Forgot / reset password — the live 6-digit-code flow
    // (livewire/auth/forgot-password-code.blade.php, both steps)
    'forgot_password_title' => '忘记密码',
    'forgot_password_description' => '请输入您的邮箱，我们将发送一个 6 位验证码以帮您重置密码。',
    'send_reset_code' => '发送重置验证码',
    'or_return_to' => '或返回',
    'log_in_lowercase' => '登录',
    'enter_your_code' => '输入您的验证码',
    'code_sent_to_prefix' => '请输入我们发送到以下邮箱的 6 位验证码：',
    'code_sent_to_suffix' => '并设置新密码。',
    'field_reset_code' => '重置验证码',
    'field_new_password' => '新密码',
    'field_confirm_new_password' => '确认新密码',
    'use_different_email' => '使用其他邮箱',
    'resend_code' => '重新发送验证码',
    'status_code_sent_known' => '如果 :email 存在对应账户，我们已向其发送了 6 位验证码。',
    'status_code_resent' => '如果该邮箱有对应账户，新验证码已发送。',
    'error_code_invalid' => '该验证码无效或已过期，请在下方重新获取。',
    'status_password_reset_done' => '您的密码已重置，请使用新密码登录。',
    'js_code_expires_in' => '验证码将在以下时间后过期：',
    'js_code_expired_resend' => '验证码已过期，请点击下方“重新发送验证码”。',

    // ---- Reset password — the classic email-link flow (pages/auth/reset-password.blade.php)
    'reset_password_title' => '重置密码',
    'reset_password_description' => '请在下方输入您的新密码',

    // ---- Two-factor challenge (pages/auth/two-factor-challenge.blade.php)
    'two_factor_title' => '双重验证',
    'auth_code_title' => '验证码',
    'auth_code_description' => '请输入您的身份验证器应用生成的验证码。',
    'field_otp_code' => '一次性验证码（OTP）',
    'recovery_code_title' => '恢复代码',
    'recovery_code_description' => '请输入您的其中一个紧急恢复代码，以确认您对该账户的访问权限。',
    'continue_button' => '继续',
    'or_you_can' => '或者您可以',
    'login_using_recovery_code' => '使用恢复代码登录',
    'login_using_auth_code' => '使用验证码登录',

    // ---- Verify email — the live 6-digit-code flow
    // (resources/views/auth/verify-email-page.blade.php + livewire/auth/verify-code.blade.php)
    'verify_email_title' => '验证您的邮箱',
    'verify_sent_prefix' => '我们已向以下邮箱发送 6 位验证码：',
    'verify_sent_suffix' => '请在下方输入以继续。',
    'field_verification_code' => '验证码',
    'verify_button' => '验证',
    'status_new_code_sent' => '新验证码已发送至您的邮箱。',
    'status_account_verified' => '您的账户已验证，请登录以开始使用。',
    'js_code_expired_request' => '验证码已过期，请在下方重新获取。',
    'js_no_active_code' => '当前没有有效的验证码，请点击下方“重新发送验证码”以获取一个。',

    // ---- App\Models\User::accountBlockedMessage() — shown as the login-form
    // error for a frozen/suspended/disabled account, and mid-session by
    // EnsureAccountIsActive middleware.
    'account_frozen' => '此账户已被冻结，请联系客服寻求帮助。',
    'account_suspended' => '此账户已被暂停，请联系客服寻求帮助。',
    'account_disabled' => '此账户已被禁用。',
    'account_blocked_default' => '此账户目前无法登录。',
];
