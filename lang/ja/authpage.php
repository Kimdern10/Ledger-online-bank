<?php

return [
    // ---- Shared auth-layout decorative side panel (layouts/auth/simple.blade.php)
    // — shown on every auth page (login, register, forgot/reset password, 2FA,
    // confirm password, verify email). "Ledger" itself stays untranslated
    // everywhere, same convention as the rest of the app.
    'panel_eyebrow' => '整然としたバンキング',
    'panel_headline_line1' => 'すべての1ドルを、',
    'panel_headline_accent' => '正確に記録。',
    'panel_sub' => '口座を開設し、送金し、残高を増やす。すべてを一つの落ち着いた誠実な帳簿で。',
    'mock_balance_label' => '残高',
    'mock_account_label' => '口座・普通預金',
    'entry_fdic' => 'FDICにより25万ドルまで保険適用',
    'entry_no_fees' => '隠れた手数料は一切なし',
    'entry_instant' => '24時間365日、即時送金',

    // ---- Shared field labels (reused across several forms below)
    'field_email' => 'メールアドレス',
    'field_email_short' => 'メール',
    'field_password' => 'パスワード',
    'field_confirm_password' => 'パスワードの確認',

    // ---- Login (pages/auth/login.blade.php)
    'login_title' => 'ログイン',
    'login_heading' => 'アカウントにログイン',
    'login_description' => 'ログインするには、以下にメールアドレスとパスワードを入力してください',
    'forgot_password_link' => 'パスワードをお忘れですか?',
    'remember_me' => 'ログイン状態を保存する',
    'no_account' => 'アカウントをお持ちでないですか?',
    'sign_up' => '新規登録',
    'passkey_signin' => 'パスキーでサインイン',
    'passkey_authenticating' => '認証しています...',
    'passkey_or_email' => 'またはメールで続ける',

    // ---- Register (pages/auth/register.blade.php)
    'register_title' => 'アカウントを作成',
    'register_subtitle' => '数分でLedgerアカウントを開設できます。',
    'field_first_name' => '名',
    'field_last_name' => '姓',
    'field_middle_name' => 'ミドルネーム(任意)',
    'field_phone' => '電話番号(任意)',
    'register_button' => 'アカウントを作成',
    'already_have_account' => 'すでにアカウントをお持ちですか?',

    // ---- Confirm password (pages/auth/confirm-password.blade.php)
    'confirm_password_title' => 'パスワードの確認',
    'confirm_password_description' => 'ここはアプリケーションの保護されたエリアです。続行する前にパスワードを確認してください。',
    'confirm_with_passkey' => 'パスキーで確認',
    'confirming' => '確認しています...',
    'or_confirm_with_password' => 'またはパスワードで確認',
    'confirm_button' => '確認',

    // ---- Forgot / reset password — the live 6-digit-code flow
    // (livewire/auth/forgot-password-code.blade.php, both steps)
    'forgot_password_title' => 'パスワードをお忘れの方',
    'forgot_password_description' => 'メールアドレスを入力すると、パスワードを再設定するための6桁のコードをお送りします。',
    'send_reset_code' => '再設定コードを送信',
    'or_return_to' => 'または、戻る:',
    'log_in_lowercase' => 'ログイン',
    'enter_your_code' => 'コードを入力してください',
    'code_sent_to_prefix' => '次の宛先に送信した6桁のコードを入力してください:',
    'code_sent_to_suffix' => 'そして新しいパスワードを設定してください。',
    'field_reset_code' => '再設定コード',
    'field_new_password' => '新しいパスワード',
    'field_confirm_new_password' => '新しいパスワードの確認',
    'use_different_email' => '別のメールアドレスを使用',
    'resend_code' => 'コードを再送信',
    'status_code_sent_known' => ':email のアカウントが存在する場合、そのアドレスに6桁のコードを送信しました。',
    'status_code_resent' => 'そのメールアドレスにアカウントがある場合、新しいコードが送信されました。',
    'error_code_invalid' => 'そのコードは無効か、有効期限が切れています。以下から新しいコードをリクエストしてください。',
    'status_password_reset_done' => 'パスワードが再設定されました。新しいパスワードでログインしてください。',
    'js_code_expires_in' => 'コードの有効期限まで残り',
    'js_code_expired_resend' => 'コードの有効期限が切れました。以下の「コードを再送信」を使用してください。',

    // ---- Reset password — the classic email-link flow (pages/auth/reset-password.blade.php)
    'reset_password_title' => 'パスワードの再設定',
    'reset_password_description' => '以下に新しいパスワードを入力してください',

    // ---- Two-factor challenge (pages/auth/two-factor-challenge.blade.php)
    'two_factor_title' => '二要素認証',
    'auth_code_title' => '認証コード',
    'auth_code_description' => '認証アプリに表示されている認証コードを入力してください。',
    'field_otp_code' => 'OTPコード',
    'recovery_code_title' => 'リカバリーコード',
    'recovery_code_description' => '緊急用リカバリーコードのいずれかを入力して、アカウントへのアクセスを確認してください。',
    'continue_button' => '続ける',
    'or_you_can' => 'または、次の方法も利用できます:',
    'login_using_recovery_code' => 'リカバリーコードを使用してログイン',
    'login_using_auth_code' => '認証コードを使用してログイン',

    // ---- Verify email — the live 6-digit-code flow
    // (resources/views/auth/verify-email-page.blade.php + livewire/auth/verify-code.blade.php)
    'verify_email_title' => 'メールアドレスの確認',
    'verify_sent_prefix' => '次の宛先に6桁のコードを送信しました:',
    'verify_sent_suffix' => '続行するには、以下にそのコードを入力してください。',
    'field_verification_code' => '確認コード',
    'verify_button' => '確認する',
    'status_new_code_sent' => '新しいコードをあなたのメールアドレスに送信しました。',
    'status_account_verified' => 'アカウントが確認されました。ログインして始めましょう。',
    'js_code_expired_request' => 'コードの有効期限が切れました。以下から新しいコードをリクエストしてください。',
    'js_no_active_code' => '有効なコードが登録されていません。以下の「コードを再送信」を使用して取得してください。',

    // ---- App\Models\User::accountBlockedMessage() — shown as the login-form
    // error for a frozen/suspended/disabled account, and mid-session by
    // EnsureAccountIsActive middleware.
    'account_frozen' => 'このアカウントは凍結されています。サポートまでお問い合わせください。',
    'account_suspended' => 'このアカウントは停止されています。サポートまでお問い合わせください。',
    'account_disabled' => 'このアカウントは無効化されています。',
    'account_blocked_default' => 'このアカウントは現在サインインできません。',
];
