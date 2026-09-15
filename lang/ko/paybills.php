<?php

return [
    'page_title' => '공과금 납부',
    'from_label' => '결제 계좌',
    'account_fallback' => '계좌',
    'account_type_checking' => '입출금',
    'account_type_savings' => '저축',
    'available' => '사용 가능 금액',
    'upcoming_bills' => '예정된 공과금',
    'due_prefix' => '납부기한',
    'add_bill_link' => '+ 공과금 추가',
    'no_bills_yet' => '등록된 공과금이 없습니다',
    'no_bills_sub' => '공과금을 추가하면 납부기한을 관리하고 여기서 바로 납부할 수 있습니다.',
    'pay_btn' => '납부',
    'remove_bill_aria' => ':category 삭제',
    'checking_required_title' => '입출금 계좌가 필요합니다',
    'checking_required_sub' => '공과금은 입출금 계좌에서만 추가하고 납부할 수 있습니다. 고객님의 계좌는 저축 전용이므로 이 페이지는 읽기 전용입니다.',

    // Add-bill sheet
    'add_bill_heading' => '공과금 추가',
    'bill_type_label' => '공과금 종류',
    'bill_type_rent_mortgage' => '임대료 / 주택담보대출',
    'bill_type_credit_card' => '신용카드',
    'bill_type_medical_insurance' => '의료보험',
    'bill_type_taxes' => '세금',
    'bill_type_student_loan' => '학자금 대출',
    'bill_type_other' => '기타',
    'bill_name_label' => '공과금 이름',
    'bill_name_placeholder' => '예: 헬스장 회원권',
    'biller_label' => '청구 업체',
    'biller_placeholder' => '납부 대상',
    'amount_label' => '금액',
    'due_date_label' => '납부기한',
    'add_bill_submit' => '공과금 추가',
    'adding' => '추가하는 중…',

    // Add-bill validation errors
    'error_enter_bill_name' => '이 공과금의 이름을 입력하세요.',
    'error_choose_bill_type' => '공과금 종류를 선택하세요.',
    'error_enter_biller' => '납부 대상을 입력하세요.',
    'error_enter_valid_amount' => '유효한 금액을 입력하세요.',
    'error_pick_due_date' => '납부기한을 선택하세요.',
    'error_add_bill_failed' => '공과금을 추가할 수 없습니다. 다시 시도해 주세요.',
    'error_add_bill_connection' => '공과금을 추가할 수 없습니다. 연결 상태를 확인하고 다시 시도해 주세요.',

    // Pay-bill confirmation sheet
    'pay_bill_heading' => '공과금 납부',
    'pay_from' => '납부 계좌',
    'confirm_payment' => '결제 확인',
    'payment_scheduled_suffix' => ' 납부가 예약되었습니다',
    'payment_scheduled_toast' => '납부가 예약되었습니다',

    // Remove-bill confirmation dialog
    'remove_confirm_title' => ':name을(를) 삭제하시겠습니까?',
    'remove_confirm_text' => '나중에 언제든지 다시 추가할 수 있습니다.',
    'remove_confirm_button' => '삭제',
    'remove_cancel_button' => '취소',
    'this_bill_fallback' => '이 공과금',
    'bill_removed_toast' => ':name 삭제됨',
    'bill_added_toast' => ':category 추가됨',
    'error_remove_bill_failed' => '공과금을 삭제할 수 없습니다. 다시 시도해 주세요',
    'error_remove_bill_connection' => '공과금을 삭제할 수 없습니다. 연결 상태를 확인해 주세요',
];
