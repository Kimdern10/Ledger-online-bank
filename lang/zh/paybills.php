<?php

return [
    'page_title' => '缴费',
    'from_label' => '来自',
    'account_fallback' => '账户',
    'account_type_checking' => '支票账户',
    'account_type_savings' => '储蓄账户',
    'available' => '可用余额',
    'upcoming_bills' => '即将到期的账单',
    'due_prefix' => '到期',
    'add_bill_link' => '+ 添加账单',
    'no_bills_yet' => '暂无账单',
    'no_bills_sub' => '添加一笔账单，开始追踪到期账单并直接在这里完成支付。',
    'pay_btn' => '支付',
    'remove_bill_aria' => '删除 :category',
    'checking_required_title' => '需要支票账户',
    'checking_required_sub' => '账单只能通过支票账户添加和支付。您的账户仅为储蓄账户，因此此页面对您仅供查看。',

    // Add-bill sheet
    'add_bill_heading' => '添加账单',
    'bill_type_label' => '账单类型',
    'bill_type_rent_mortgage' => '房租 / 房贷',
    'bill_type_credit_card' => '信用卡',
    'bill_type_medical_insurance' => '医疗保险',
    'bill_type_taxes' => '税费',
    'bill_type_student_loan' => '助学贷款',
    'bill_type_other' => '其他',
    'bill_name_label' => '账单名称',
    'bill_name_placeholder' => '例如：健身会员',
    'biller_label' => '收款方',
    'biller_placeholder' => '您要支付给谁',
    'amount_label' => '金额',
    'due_date_label' => '到期日',
    'add_bill_submit' => '添加账单',
    'adding' => '正在添加…',

    // Add-bill validation errors
    'error_enter_bill_name' => '请输入此账单的名称。',
    'error_choose_bill_type' => '请选择账单类型。',
    'error_enter_biller' => '请输入您要支付给谁。',
    'error_enter_valid_amount' => '请输入有效金额。',
    'error_pick_due_date' => '请选择到期日。',
    'error_add_bill_failed' => '无法添加该账单，请重试。',
    'error_add_bill_connection' => '无法添加该账单 — 请检查您的网络连接后重试。',

    // Pay-bill confirmation sheet
    'pay_bill_heading' => '支付账单',
    'pay_from' => '付款账户',
    'confirm_payment' => '确认付款',
    'payment_scheduled_suffix' => '的付款已安排',
    'payment_scheduled_toast' => '付款已安排',

    // Remove-bill confirmation dialog
    'remove_confirm_title' => '要删除 :name 吗？',
    'remove_confirm_text' => '您以后随时可以重新添加。',
    'remove_confirm_button' => '删除',
    'remove_cancel_button' => '取消',
    'this_bill_fallback' => '此账单',
    'bill_removed_toast' => ':name 已删除',
    'bill_added_toast' => '已添加 :category',
    'error_remove_bill_failed' => '无法删除该账单，请重试',
    'error_remove_bill_connection' => '无法删除该账单 — 请检查您的网络连接',
];
