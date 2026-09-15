<?php

return [
    'page_title' => '請求書の支払い',
    'from_label' => '支払い元',
    'account_fallback' => '口座',
    'account_type_checking' => '当座預金',
    'account_type_savings' => '普通預金',
    'available' => '利用可能残高',
    'upcoming_bills' => '今後の請求書',
    'due_prefix' => '期日',
    'add_bill_link' => '+ 請求書を追加',
    'no_bills_yet' => '請求書はまだありません',
    'no_bills_sub' => '請求書を追加すると、支払期日を管理してここから直接支払うことができます。',
    'pay_btn' => '支払う',
    'remove_bill_aria' => ':categoryを削除',
    'checking_required_title' => '当座預金口座が必要です',
    'checking_required_sub' => '請求書の追加と支払いは当座預金口座からのみ行えます。お客様の口座は普通預金のみのため、このページは閲覧のみとなります。',

    // Add-bill sheet
    'add_bill_heading' => '請求書を追加',
    'bill_type_label' => '請求書の種類',
    'bill_type_rent_mortgage' => '家賃・住宅ローン',
    'bill_type_credit_card' => 'クレジットカード',
    'bill_type_medical_insurance' => '医療保険',
    'bill_type_taxes' => '税金',
    'bill_type_student_loan' => '学生ローン',
    'bill_type_other' => 'その他',
    'bill_name_label' => '請求書名',
    'bill_name_placeholder' => '例：ジムの会員費',
    'biller_label' => '請求先',
    'biller_placeholder' => '支払い先',
    'amount_label' => '金額',
    'due_date_label' => '支払期日',
    'add_bill_submit' => '請求書を追加',
    'adding' => '追加中…',

    // Add-bill validation errors
    'error_enter_bill_name' => 'この請求書の名前を入力してください。',
    'error_choose_bill_type' => '請求書の種類を選択してください。',
    'error_enter_biller' => '支払い先を入力してください。',
    'error_enter_valid_amount' => '有効な金額を入力してください。',
    'error_pick_due_date' => '支払期日を選択してください。',
    'error_add_bill_failed' => '請求書を追加できませんでした。もう一度お試しください。',
    'error_add_bill_connection' => '請求書を追加できませんでした。接続を確認してもう一度お試しください。',

    // Pay-bill confirmation sheet
    'pay_bill_heading' => '請求書を支払う',
    'pay_from' => '支払い元',
    'confirm_payment' => '支払いを確認',
    'payment_scheduled_suffix' => 'の支払いを予約しました',
    'payment_scheduled_toast' => '支払いを予約しました',

    // Remove-bill confirmation dialog
    'remove_confirm_title' => ':nameを削除しますか？',
    'remove_confirm_text' => '後でいつでも追加し直せます。',
    'remove_confirm_button' => '削除',
    'remove_cancel_button' => 'キャンセル',
    'this_bill_fallback' => 'この請求書',
    'bill_removed_toast' => ':nameを削除しました',
    'bill_added_toast' => ':categoryを追加しました',
    'error_remove_bill_failed' => '請求書を削除できませんでした。もう一度お試しください',
    'error_remove_bill_connection' => '請求書を削除できませんでした。接続を確認してください',
];
