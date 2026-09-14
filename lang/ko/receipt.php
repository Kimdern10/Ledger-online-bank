<?php

return [
    // Page chrome (send-receipt.blade.php)
    'page_title' => '영수증',
    'download_pdf' => 'PDF 다운로드',
    'share' => '공유',
    'done' => '완료',
    'send_another' => '다시 송금하기',

    // "from"/"to"/"by" word shown before the other party's name, e.g.
    // "to Jane Doe" or "from Ledger" — controllers pass a plain identifier,
    // the blade maps it through these.
    'subverb_from' => '보낸 사람',
    'subverb_to' => '받는 사람',
    'subverb_by' => '처리자',

    // Row labels — shared across every receipt type (Send Money,
    // Another bank, International, Withdraw, Top up, admin adjustment)
    'row_from' => '보낸 사람',
    'row_bank' => '은행',
    'row_status' => '상태',
    'row_category' => '분류',
    'row_note' => '메모',
    'row_reference' => '참조 번호',
    'row_date' => '날짜',
    'row_recipient' => '받는 사람',
    'row_account_number' => '계좌번호',
    'row_routing_number' => '라우팅 번호',
    'row_scheduled_for' => '예정일',
    'row_country' => '국가',
    'row_swift_bic' => 'SWIFT/BIC',
    'row_account_iban' => '계좌번호/IBAN',
    'row_currency' => '통화',
    'row_exchange_rate' => '환율',
    'row_recipient_receives_approx' => '수취인 수령액 (약)',
    'row_withdraw_to' => '출금 계좌',
    'row_fee' => '수수료',
    'row_youll_receive' => '받으실 금액',
    'row_top_up_from' => '충전 출처',
    'row_card_processing_fee' => '카드 처리 수수료',
    'row_account_name' => '예금주명',

    // Titles
    'title_payment_received' => '결제 수신 완료',
    'title_payment_sent' => '결제 전송 완료',
    'title_transfer_scheduled' => '송금 예약됨',
    'title_transfer_cancelled' => '송금 취소됨',
    'title_transfer_failed' => '송금 실패',
    'title_withdrawal' => '출금',
    'title_topup' => '충전',
    'title_deposit_received' => '입금 수신 완료',
    'title_balance_adjustment' => '잔액 조정',

    // Banners
    'banner_scheduled' => '이 송금은 :date에 예정되어 있으며 아직 발송되지 않았습니다. 그 전까지 언제든지 취소할 수 있습니다.',
    'banner_cancelled' => '이 송금은 취소되어 발송되지 않았습니다.',
    'banner_failed' => '예정된 날짜에 잔액이 부족하여 이 송금이 처리되지 않았습니다.',
    'banner_transfer_sent' => '송금이 완료되었습니다.',
    'banner_international_transfer_sent' => '해외 송금이 완료되었습니다.',
    'banner_withdrawal_bank_days' => '영업일 기준 1~3일 내에 은행 계좌로 입금됩니다.',
    'banner_topup_fee_note' => '이 처리 수수료는 카드사에서 부과한 것으로, Ledger에 추가된 금액에서 차감되지 않았습니다.',
    'banner_adjustment_credit' => '이 입금은 Ledger에서 회원님의 계좌로 처리한 것입니다 — 문의사항이 있으시면 고객지원팀에 연락해 주세요.',
    'banner_adjustment_debit' => '이 조정은 Ledger에서 회원님의 계좌에 처리한 것입니다 — 문의사항이 있으시면 고객지원팀에 연락해 주세요.',

    // Status labels (App\Support\TransactionStatus — shared by Transfer,
    // ExternalTransfer, InternationalTransfer, Withdrawal, TopUp)
    'status_completed' => '완료됨',
    'status_scheduled' => '예약됨',
    'status_cancelled' => '취소됨',
    'status_failed' => '실패',

    // JS strings for the Download PDF / Share buttons
    'share_text_prefix' => 'Ledger 결제 영수증',
    'share_title' => 'Ledger 영수증',
    'js_preparing_pdf' => 'PDF를 준비하는 중…',
    'js_pdf_error' => 'PDF를 생성할 수 없습니다. 다시 시도해 주세요.',
    'js_preparing_share' => '공유할 영수증을 준비하는 중…',
    'js_share_error' => '영수증을 공유할 수 없습니다. 대신 다운로드를 이용해 주세요.',
    'js_share_unsupported' => '이 브라우저에서는 공유 기능이 지원되지 않습니다. 대신 다운로드를 이용해 주세요.',
];
