<?php

return [
    // Profile setting
    'first_name' => '名',
    'last_name' => '姓',
    'middle_name' => 'ミドルネーム',
    'optional' => '（任意）',
    'email' => 'メールアドレス',
    'phone' => '電話番号',
    'save_changes' => '変更を保存',

    // Notifications
    'notifications_title' => '通知',
    'notifications_intro' => "これらの設定はアカウントに保存されますが、Ledgerは現時点では取引、セキュリティ、プロモーションに関するメールを送信していません。この機能が実装された際にすぐ反映できるよう、今のうちにご希望を記録しておくためのものです。",
    'transaction_emails' => '取引メール',
    'transaction_emails_desc' => '送金、受取、出金、チャージに関する通知。',
    'security_alerts' => 'セキュリティアラート',
    'security_alerts_desc' => 'ログイン、パスワード変更、アカウントに対する管理操作。',
    'promotions' => 'プロモーションと特典',
    'promotions_desc' => 'Ledgerからのニュース、新機能、キャンペーン情報。',
    'save_preferences' => '設定を保存',

    // Password
    'password_intro' => "本人確認のため現在のパスワードを入力し、新しいパスワードを設定してください。",
    'current_password' => '現在のパスワード',
    'new_password' => '新しいパスワード',
    'confirm_new_password' => '新しいパスワード（確認）',
    'update_password' => 'パスワードを更新',

    // Budget
    'budget_intro' => '毎月の支出予定額を設定してください。ダッシュボードの「今月の支出」リングは、実際の支出をこの金額と比較して表示されます。',
    'monthly_budget_field' => '月間予算（$）',
    'save_budget' => '予算を保存',

    // Transaction PIN
    'pin_intro_has' => "取引用PINは、送金・出金・チャージによって実際にお金が動く前に本人確認を行うためのものです。現在のPINを入力し、新しいPINを設定してください。",
    'pin_intro_new' => "4桁のPINを作成してください。設定後は、送金・出金・チャージの際に必ずこのPINの入力が求められ、実際にお金が動く前に確認されます。これにより、万が一セッションに他人がアクセスしても、あなたの資金が勝手に移動されるのを防ぎます。",
    'current_pin' => '現在のPIN',
    'new_pin' => '新しいPIN',
    'create_pin' => 'PINを作成',
    'confirm_new_pin' => '新しいPIN（確認）',
    'confirm_pin' => 'PIN（確認）',
    'update_pin' => 'PINを更新',

    // Delete account
    'delete_warning_title' => "この操作はここから元に戻すことはできません。",
    'delete_intro' => 'アカウントを削除すると、完全にサインアウトされます。再度サインインすることはできません。まず残高を$0にする必要があるため、続行する前に「出金」または「送金」で残りの資金を移動してください。後になってこれが誤りだったと気づいた場合は、:support_linkまでご連絡ください。データはすぐには消去されないため、当社側で元に戻すことが可能です。',
    'support_link_text' => 'サポート',
    'current_balance' => '現在の残高',
    'confirm_password_field' => 'パスワードを確認してください',
    'delete_my_account' => 'アカウントを削除する',
    'delete_confirm_dialog' => 'アカウントを完全に削除してもよろしいですか？',
    'withdraw_first' => '先に残高を出金または送金してください。',
];
