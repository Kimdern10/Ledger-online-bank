<?php

return [
    // ---- Shared auth-layout decorative side panel (layouts/auth/simple.blade.php)
    // — shown on every auth page (login, register, forgot/reset password, 2FA,
    // confirm password, verify email). "Ledger" itself stays untranslated
    // everywhere, same convention as the rest of the app.
    'panel_eyebrow' => '질서 있게 관리되는 뱅킹',
    'panel_headline_line1' => '모든 금액이,',
    'panel_headline_accent' => '빠짐없이 기록됩니다.',
    'panel_sub' => '계좌를 개설하고, 자금을 이체하고, 신뢰할 수 있는 하나의 장부에서 잔액을 늘려보세요.',
    'mock_balance_label' => '잔액',
    'mock_account_label' => '계좌 · 입출금',
    'entry_fdic' => 'FDIC 보험 최대 $250,000까지 보장',
    'entry_no_fees' => '숨겨진 수수료 없음',
    'entry_instant' => '24시간 즉시 이체',

    // ---- Shared field labels (reused across several forms below)
    'field_email' => '이메일 주소',
    'field_email_short' => '이메일',
    'field_password' => '비밀번호',
    'field_confirm_password' => '비밀번호 확인',

    // ---- Login (pages/auth/login.blade.php)
    'login_title' => '로그인',
    'login_heading' => '계정에 로그인하세요',
    'login_description' => '로그인하려면 아래에 이메일과 비밀번호를 입력하세요',
    'forgot_password_link' => '비밀번호를 잊으셨나요?',
    'remember_me' => '로그인 상태 유지',
    'no_account' => '계정이 없으신가요?',
    'sign_up' => '회원가입',
    'passkey_signin' => '패스키로 로그인',
    'passkey_authenticating' => '인증 중...',
    'passkey_or_email' => '또는 이메일로 계속하기',

    // ---- Register (pages/auth/register.blade.php)
    'register_title' => '계정 만들기',
    'register_subtitle' => '몇 분 만에 Ledger 계좌를 개설하세요.',
    'field_first_name' => '이름',
    'field_last_name' => '성',
    'field_middle_name' => '중간 이름 (선택 사항)',
    'field_phone' => '전화번호 (선택 사항)',
    'register_button' => '계정 생성',
    'already_have_account' => '이미 계정이 있으신가요?',

    // ---- Confirm password (pages/auth/confirm-password.blade.php)
    'confirm_password_title' => '비밀번호 확인',
    'confirm_password_description' => '이곳은 애플리케이션의 보안 영역입니다. 계속하려면 비밀번호를 확인해 주세요.',
    'confirm_with_passkey' => '패스키로 확인',
    'confirming' => '확인 중...',
    'or_confirm_with_password' => '또는 비밀번호로 확인',
    'confirm_button' => '확인',

    // ---- Forgot / reset password — the live 6-digit-code flow
    // (livewire/auth/forgot-password-code.blade.php, both steps)
    'forgot_password_title' => '비밀번호 찾기',
    'forgot_password_description' => '이메일을 입력하시면 비밀번호 재설정을 위한 6자리 코드를 보내드립니다.',
    'send_reset_code' => '재설정 코드 보내기',
    'or_return_to' => '또는, 돌아가기',
    'log_in_lowercase' => '로그인',
    'enter_your_code' => '코드를 입력하세요',
    'code_sent_to_prefix' => '다음 주소로 보낸 6자리 코드를 입력하세요',
    'code_sent_to_suffix' => '그리고 새 비밀번호를 설정하세요.',
    'field_reset_code' => '재설정 코드',
    'field_new_password' => '새 비밀번호',
    'field_confirm_new_password' => '새 비밀번호 확인',
    'use_different_email' => '다른 이메일 사용',
    'resend_code' => '코드 재전송',
    'status_code_sent_known' => ':email 주소로 계정이 존재하는 경우, 해당 주소로 6자리 코드를 보내드렸습니다.',
    'status_code_resent' => '해당 이메일에 계정이 있는 경우 새 코드가 전송되었습니다.',
    'error_code_invalid' => '코드가 유효하지 않거나 만료되었습니다. 아래에서 새 코드를 요청하세요.',
    'status_password_reset_done' => '비밀번호가 재설정되었습니다. 새 비밀번호로 로그인하세요.',
    'js_code_expires_in' => '코드 만료까지 남은 시간',
    'js_code_expired_resend' => '코드가 만료되었습니다. 아래의 "코드 재전송"을 이용하세요.',

    // ---- Reset password — the classic email-link flow (pages/auth/reset-password.blade.php)
    'reset_password_title' => '비밀번호 재설정',
    'reset_password_description' => '아래에 새 비밀번호를 입력해 주세요',

    // ---- Two-factor challenge (pages/auth/two-factor-challenge.blade.php)
    'two_factor_title' => '2단계 인증',
    'auth_code_title' => '인증 코드',
    'auth_code_description' => '인증 앱에서 제공하는 인증 코드를 입력하세요.',
    'field_otp_code' => 'OTP 코드',
    'recovery_code_title' => '복구 코드',
    'recovery_code_description' => '비상 복구 코드 중 하나를 입력하여 계정 접근을 확인해 주세요.',
    'continue_button' => '계속',
    'or_you_can' => '또는 다음을 할 수 있습니다',
    'login_using_recovery_code' => '복구 코드로 로그인',
    'login_using_auth_code' => '인증 코드로 로그인',

    // ---- Verify email — the live 6-digit-code flow
    // (resources/views/auth/verify-email-page.blade.php + livewire/auth/verify-code.blade.php)
    'verify_email_title' => '이메일 인증',
    'verify_sent_prefix' => '다음 주소로 6자리 코드를 보냈습니다',
    'verify_sent_suffix' => '계속하려면 아래에 입력하세요.',
    'field_verification_code' => '인증 코드',
    'verify_button' => '인증',
    'status_new_code_sent' => '새 코드가 이메일로 전송되었습니다.',
    'status_account_verified' => '계정이 인증되었습니다. 로그인하여 시작하세요.',
    'js_code_expired_request' => '코드가 만료되었습니다. 아래에서 새 코드를 요청하세요.',
    'js_no_active_code' => '활성화된 코드가 없습니다. 아래의 "코드 재전송"을 이용해 코드를 받으세요.',

    // ---- App\Models\User::accountBlockedMessage() — shown as the login-form
    // error for a frozen/suspended/disabled account, and mid-session by
    // EnsureAccountIsActive middleware.
    'account_frozen' => '이 계정은 동결되었습니다. 도움이 필요하면 고객지원팀에 문의하세요.',
    'account_suspended' => '이 계정은 정지되었습니다. 도움이 필요하면 고객지원팀에 문의하세요.',
    'account_disabled' => '이 계정은 비활성화되었습니다.',
    'account_blocked_default' => '현재 이 계정으로 로그인할 수 없습니다.',
];
