<?php

return [
    // ---- Shared auth-layout decorative side panel (layouts/auth/simple.blade.php)
    // — shown on every auth page (login, register, forgot/reset password, 2FA,
    // confirm password, verify email). "Ledger" itself stays untranslated
    // everywhere, same convention as the rest of the app.
    'panel_eyebrow' => 'Ngân hàng, luôn có trật tự',
    'panel_headline_line1' => 'Mọi đồng đô la,',
    'panel_headline_accent' => 'đều được ghi nhận.',
    'panel_sub' => 'Mở tài khoản, chuyển tiền và gia tăng số dư của bạn trong một cuốn sổ cái yên tâm, minh bạch.',
    'mock_balance_label' => 'Số dư',
    'mock_account_label' => 'Tài khoản · Thanh toán',
    'entry_fdic' => 'Được FDIC bảo hiểm lên đến 250.000 đô la',
    'entry_no_fees' => 'Không phí ẩn, không bao giờ',
    'entry_instant' => 'Chuyển tiền tức thì, 24/7',

    // ---- Shared field labels (reused across several forms below)
    'field_email' => 'Địa chỉ email',
    'field_email_short' => 'Email',
    'field_password' => 'Mật khẩu',
    'field_confirm_password' => 'Xác nhận mật khẩu',

    // ---- Login (pages/auth/login.blade.php)
    'login_title' => 'Đăng nhập',
    'login_heading' => 'Đăng nhập vào tài khoản của bạn',
    'login_description' => 'Nhập email và mật khẩu bên dưới để đăng nhập',
    'forgot_password_link' => 'Quên mật khẩu?',
    'remember_me' => 'Ghi nhớ đăng nhập',
    'no_account' => 'Chưa có tài khoản?',
    'sign_up' => 'Đăng ký',
    'passkey_signin' => 'Đăng nhập bằng khóa truy cập',
    'passkey_authenticating' => 'Đang xác thực...',
    'passkey_or_email' => 'Hoặc tiếp tục bằng email',

    // ---- Register (pages/auth/register.blade.php)
    'register_title' => 'Tạo tài khoản của bạn',
    'register_subtitle' => 'Mở tài khoản Ledger chỉ trong vài phút.',
    'field_first_name' => 'Tên',
    'field_last_name' => 'Họ',
    'field_middle_name' => 'Tên đệm (không bắt buộc)',
    'field_phone' => 'Số điện thoại (không bắt buộc)',
    'register_button' => 'Tạo tài khoản',
    'already_have_account' => 'Đã có tài khoản?',

    // ---- Confirm password (pages/auth/confirm-password.blade.php)
    'confirm_password_title' => 'Xác nhận mật khẩu',
    'confirm_password_description' => 'Đây là khu vực bảo mật của ứng dụng. Vui lòng xác nhận mật khẩu của bạn trước khi tiếp tục.',
    'confirm_with_passkey' => 'Xác nhận bằng khóa truy cập',
    'confirming' => 'Đang xác nhận...',
    'or_confirm_with_password' => 'Hoặc xác nhận bằng mật khẩu',
    'confirm_button' => 'Xác nhận',

    // ---- Forgot / reset password — the live 6-digit-code flow
    // (livewire/auth/forgot-password-code.blade.php, both steps)
    'forgot_password_title' => 'Quên mật khẩu',
    'forgot_password_description' => 'Nhập email của bạn và chúng tôi sẽ gửi mã 6 chữ số để đặt lại mật khẩu.',
    'send_reset_code' => 'Gửi mã đặt lại',
    'or_return_to' => 'Hoặc quay lại',
    'log_in_lowercase' => 'đăng nhập',
    'enter_your_code' => 'Nhập mã của bạn',
    'code_sent_to_prefix' => 'Nhập mã 6 chữ số chúng tôi đã gửi đến',
    'code_sent_to_suffix' => 'và chọn mật khẩu mới.',
    'field_reset_code' => 'Mã đặt lại',
    'field_new_password' => 'Mật khẩu mới',
    'field_confirm_new_password' => 'Xác nhận mật khẩu mới',
    'use_different_email' => 'Dùng email khác',
    'resend_code' => 'Gửi lại mã',
    'status_code_sent_known' => 'Nếu có tài khoản gắn với :email, chúng tôi đã gửi một mã 6 chữ số đến đó.',
    'status_code_resent' => 'Một mã mới đã được gửi, nếu email đó có tài khoản.',
    'error_code_invalid' => 'Mã đó không hợp lệ hoặc đã hết hạn. Hãy yêu cầu mã mới bên dưới.',
    'status_password_reset_done' => 'Mật khẩu của bạn đã được đặt lại. Hãy đăng nhập bằng mật khẩu mới.',
    'js_code_expires_in' => 'Mã hết hạn sau',
    'js_code_expired_resend' => 'Mã đã hết hạn. Hãy dùng "Gửi lại mã" bên dưới.',

    // ---- Reset password — the classic email-link flow (pages/auth/reset-password.blade.php)
    'reset_password_title' => 'Đặt lại mật khẩu',
    'reset_password_description' => 'Vui lòng nhập mật khẩu mới của bạn bên dưới',

    // ---- Two-factor challenge (pages/auth/two-factor-challenge.blade.php)
    'two_factor_title' => 'Xác thực hai yếu tố',
    'auth_code_title' => 'Mã xác thực',
    'auth_code_description' => 'Nhập mã xác thực do ứng dụng xác thực của bạn cung cấp.',
    'field_otp_code' => 'Mã OTP',
    'recovery_code_title' => 'Mã khôi phục',
    'recovery_code_description' => 'Vui lòng xác nhận quyền truy cập vào tài khoản của bạn bằng cách nhập một trong các mã khôi phục khẩn cấp.',
    'continue_button' => 'Tiếp tục',
    'or_you_can' => 'hoặc bạn có thể',
    'login_using_recovery_code' => 'đăng nhập bằng mã khôi phục',
    'login_using_auth_code' => 'đăng nhập bằng mã xác thực',

    // ---- Verify email — the live 6-digit-code flow
    // (resources/views/auth/verify-email-page.blade.php + livewire/auth/verify-code.blade.php)
    'verify_email_title' => 'Xác minh email của bạn',
    'verify_sent_prefix' => 'Chúng tôi đã gửi mã 6 chữ số đến',
    'verify_sent_suffix' => 'Nhập mã đó bên dưới để tiếp tục.',
    'field_verification_code' => 'Mã xác minh',
    'verify_button' => 'Xác minh',
    'status_new_code_sent' => 'Một mã mới đã được gửi đến email của bạn.',
    'status_account_verified' => 'Tài khoản của bạn đã được xác minh. Hãy đăng nhập để bắt đầu.',
    'js_code_expired_request' => 'Mã đã hết hạn. Hãy yêu cầu mã mới bên dưới.',
    'js_no_active_code' => 'Không có mã nào đang hoạt động. Hãy dùng "Gửi lại mã" bên dưới để nhận một mã.',

    // ---- App\Models\User::accountBlockedMessage() — shown as the login-form
    // error for a frozen/suspended/disabled account, and mid-session by
    // EnsureAccountIsActive middleware.
    'account_frozen' => 'Tài khoản này đã bị đóng băng. Vui lòng liên hệ bộ phận hỗ trợ.',
    'account_suspended' => 'Tài khoản này đã bị tạm ngưng. Vui lòng liên hệ bộ phận hỗ trợ.',
    'account_disabled' => 'Tài khoản này đã bị vô hiệu hóa.',
    'account_blocked_default' => 'Tài khoản này hiện không thể đăng nhập.',
];
