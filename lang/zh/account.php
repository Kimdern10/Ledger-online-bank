<?php

return [
    // Profile setting
    'first_name' => '名字',
    'last_name' => '姓氏',
    'middle_name' => '中间名',
    'optional' => '（可选）',
    'email' => '电子邮箱',
    'phone' => '电话',
    'save_changes' => '保存更改',

    // Notifications
    'notifications_title' => '通知',
    'notifications_intro' => "这些设置会保存到您的账户中，但 Ledger 目前尚未发送任何交易、安全或推广类邮件。这只是先记录您的偏好，以便日后启用该功能时使用。",
    'transaction_emails' => '交易邮件',
    'transaction_emails_desc' => '转账、收款、提现和充值通知。',
    'security_alerts' => '安全提醒',
    'security_alerts_desc' => '登录、密码更改以及账户上的管理操作。',
    'promotions' => '促销与优惠',
    'promotions_desc' => '来自 Ledger 的新闻、功能和优惠信息。',
    'save_preferences' => '保存偏好设置',

    // Password
    'password_intro' => "输入您当前的密码以确认是您本人操作，然后设置新密码。",
    'current_password' => '当前密码',
    'new_password' => '新密码',
    'confirm_new_password' => '确认新密码',
    'update_password' => '更新密码',

    // Budget
    'budget_intro' => '设置您计划每月支出的金额。仪表盘上的"本月支出"环形图会将您的实际支出与该数字进行比较。',
    'monthly_budget_field' => '每月预算（$）',
    'save_budget' => '保存预算',

    // Transaction PIN
    'pin_intro_has' => "您的交易密码会在“转账”“提现”或“充值”实际转移真实资金之前确认是您本人操作。请输入当前的交易密码，然后设置新的密码。",
    'pin_intro_new' => "创建一个 4 位数的交易密码。设置完成后，“转账”“提现”和“充值”在实际转移资金前都会要求输入该密码。这可以防止即使有人进入您的账户会话，也无法转移您的资金。",
    'current_pin' => '当前交易密码',
    'new_pin' => '新交易密码',
    'create_pin' => '创建交易密码',
    'confirm_new_pin' => '确认新交易密码',
    'confirm_pin' => '确认交易密码',
    'update_pin' => '更新交易密码',

    // Delete account
    'delete_warning_title' => "此操作一旦执行将无法在此撤销。",
    'delete_intro' => '删除账户将永久注销您的登录状态。您将无法重新登录。您的余额必须先为 $0，因此在继续操作之前，请使用“提现”或“转账”转移所有剩余资金。如果这是误操作，请日后联系 :support_link。系统不会立即抹除任何数据，因此我们这边可以撤销此操作。',
    'support_link_text' => '客服支持',
    'current_balance' => '当前余额',
    'confirm_password_field' => '确认您的密码',
    'delete_my_account' => '删除我的账户',
    'delete_confirm_dialog' => '您确定要永久删除您的账户吗？',
    'withdraw_first' => '请先提现或转出您的剩余余额。',
];
