<?php

return [
    // Page chrome (send-receipt.blade.php)
    'page_title' => 'Biên lai',
    'download_pdf' => 'Tải PDF',
    'share' => 'Chia sẻ',
    'done' => 'Xong',
    'send_another' => 'Gửi khoản khác',

    // "from"/"to"/"by" word shown before the other party's name, e.g.
    // "to Jane Doe" or "from Ledger" — controllers pass a plain identifier,
    // the blade maps it through these.
    'subverb_from' => 'từ',
    'subverb_to' => 'đến',
    'subverb_by' => 'bởi',

    // Row labels — shared across every receipt type (Send Money,
    // Another bank, International, Withdraw, Top up, admin adjustment)
    'row_from' => 'Từ',
    'row_bank' => 'Ngân hàng',
    'row_status' => 'Trạng thái',
    'row_category' => 'Danh mục',
    'row_note' => 'Ghi chú',
    'row_reference' => 'Mã tham chiếu',
    'row_date' => 'Ngày',
    'row_recipient' => 'Người nhận',
    'row_account_number' => 'Số tài khoản',
    'row_routing_number' => 'Số định tuyến',
    'row_scheduled_for' => 'Lên lịch vào',
    'row_country' => 'Quốc gia',
    'row_swift_bic' => 'SWIFT/BIC',
    'row_account_iban' => 'Tài khoản/IBAN',
    'row_currency' => 'Loại tiền tệ',
    'row_exchange_rate' => 'Tỷ giá hối đoái',
    'row_recipient_receives_approx' => 'Người nhận sẽ nhận được (ước tính)',
    'row_withdraw_to' => 'Rút về',
    'row_fee' => 'Phí',
    'row_youll_receive' => 'Bạn sẽ nhận được',
    'row_top_up_from' => 'Nạp tiền từ',
    'row_card_processing_fee' => 'Phí xử lý thẻ',
    'row_account_name' => 'Tên tài khoản',

    // Titles
    'title_payment_received' => 'Đã nhận thanh toán',
    'title_payment_sent' => 'Đã gửi thanh toán',
    'title_transfer_scheduled' => 'Đã lên lịch chuyển khoản',
    'title_transfer_cancelled' => 'Đã hủy chuyển khoản',
    'title_transfer_failed' => 'Chuyển khoản thất bại',
    'title_withdrawal' => 'Rút tiền',
    'title_topup' => 'Nạp tiền',
    'title_deposit_received' => 'Đã nhận tiền gửi',
    'title_balance_adjustment' => 'Điều chỉnh số dư',

    // Banners
    'banner_scheduled' => 'Khoản chuyển tiền này được lên lịch vào :date và vẫn chưa được thực hiện. Bạn có thể hủy bất cứ lúc nào trước thời điểm đó.',
    'banner_cancelled' => 'Khoản chuyển tiền này đã bị hủy và chưa từng được thực hiện.',
    'banner_failed' => 'Khoản chuyển tiền này không thành công — không đủ số dư vào ngày được lên lịch thực hiện.',
    'banner_transfer_sent' => 'Khoản chuyển tiền của bạn đã được gửi đi.',
    'banner_international_transfer_sent' => 'Khoản chuyển tiền quốc tế của bạn đã được gửi đi.',
    'banner_withdrawal_bank_days' => 'Sẽ về tài khoản ngân hàng của bạn trong 1–3 ngày làm việc.',
    'banner_topup_fee_note' => 'Phí xử lý được tính bởi thẻ của bạn, không bị trừ vào số tiền đã nạp vào Ledger.',
    'banner_adjustment_credit' => 'Khoản tiền gửi này được Ledger thực hiện vào tài khoản của bạn — liên hệ Hỗ trợ nếu bạn có thắc mắc.',
    'banner_adjustment_debit' => 'Khoản điều chỉnh này được Ledger thực hiện trên tài khoản của bạn — liên hệ Hỗ trợ nếu bạn có thắc mắc.',

    // Status labels (App\Support\TransactionStatus — shared by Transfer,
    // ExternalTransfer, InternationalTransfer, Withdrawal, TopUp)
    'status_completed' => 'Hoàn tất',
    'status_scheduled' => 'Đã lên lịch',
    'status_cancelled' => 'Đã hủy',
    'status_failed' => 'Thất bại',

    // JS strings for the Download PDF / Share buttons
    'share_text_prefix' => 'Biên lai thanh toán Ledger',
    'share_title' => 'Biên lai Ledger',
    'js_preparing_pdf' => 'Đang chuẩn bị PDF của bạn…',
    'js_pdf_error' => 'Không thể tạo PDF. Vui lòng thử lại.',
    'js_preparing_share' => 'Đang chuẩn bị biên lai của bạn để chia sẻ…',
    'js_share_error' => 'Không thể chia sẻ biên lai. Hãy thử Tải xuống thay thế.',
    'js_share_unsupported' => 'Trình duyệt này không hỗ trợ chia sẻ. Hãy sử dụng Tải xuống thay thế.',
];
