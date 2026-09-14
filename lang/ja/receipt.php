<?php

return [
    // Page chrome (send-receipt.blade.php)
    'page_title' => '領収書',
    'download_pdf' => 'PDFをダウンロード',
    'share' => '共有',
    'done' => '完了',
    'send_another' => 'もう一度送金する',

    // "from"/"to"/"by" word shown before the other party's name, e.g.
    // "to Jane Doe" or "from Ledger" — controllers pass a plain identifier,
    // the blade maps it through these.
    'subverb_from' => 'から',
    'subverb_to' => 'へ',
    'subverb_by' => 'による',

    // Row labels — shared across every receipt type (Send Money,
    // Another bank, International, Withdraw, Top up, admin adjustment)
    'row_from' => '送金元',
    'row_bank' => '銀行',
    'row_status' => 'ステータス',
    'row_category' => 'カテゴリー',
    'row_note' => 'メモ',
    'row_reference' => '参照番号',
    'row_date' => '日付',
    'row_recipient' => '受取人',
    'row_account_number' => '口座番号',
    'row_routing_number' => 'ルーティング番号',
    'row_scheduled_for' => '予定日',
    'row_country' => '国',
    'row_swift_bic' => 'SWIFT/BIC',
    'row_account_iban' => '口座番号/IBAN',
    'row_currency' => '通貨',
    'row_exchange_rate' => '為替レート',
    'row_recipient_receives_approx' => '受取人の受取額（概算）',
    'row_withdraw_to' => '出金先',
    'row_fee' => '手数料',
    'row_youll_receive' => '受取額',
    'row_top_up_from' => 'チャージ元',
    'row_card_processing_fee' => 'カード処理手数料',
    'row_account_name' => '口座名義',

    // Titles
    'title_payment_received' => '支払いを受領しました',
    'title_payment_sent' => '支払いを送信しました',
    'title_transfer_scheduled' => '送金予約済み',
    'title_transfer_cancelled' => '送金がキャンセルされました',
    'title_transfer_failed' => '送金に失敗しました',
    'title_withdrawal' => '出金',
    'title_topup' => 'チャージ',
    'title_deposit_received' => '入金を受領しました',
    'title_balance_adjustment' => '残高調整',

    // Banners
    'banner_scheduled' => "この送金は:date に予定されており、まだ送信されていません。それまではいつでもキャンセルできます。",
    'banner_cancelled' => 'この送金はキャンセルされ、送信されませんでした。',
    'banner_failed' => "この送金は実行されませんでした — 予定日に残高が不足していました。",
    'banner_transfer_sent' => '送金が完了しました。',
    'banner_international_transfer_sent' => '国際送金が完了しました。',
    'banner_withdrawal_bank_days' => '1〜3営業日以内に銀行口座に着金します。',
    'banner_topup_fee_note' => 'この処理手数料はお使いのカード会社によって請求されたもので、Ledgerへの入金額から差し引かれたものではありません。',
    'banner_adjustment_credit' => 'この入金はLedgerによってお客様の口座に行われました — ご不明な点はサポートまでお問い合わせください。',
    'banner_adjustment_debit' => 'この調整はLedgerによってお客様の口座に行われました — ご不明な点はサポートまでお問い合わせください。',

    // Status labels (App\Support\TransactionStatus — shared by Transfer,
    // ExternalTransfer, InternationalTransfer, Withdrawal, TopUp)
    'status_completed' => '完了',
    'status_scheduled' => '予約済み',
    'status_cancelled' => 'キャンセル済み',
    'status_failed' => '失敗',

    // JS strings for the Download PDF / Share buttons
    'share_text_prefix' => 'Ledger 支払い領収書',
    'share_title' => 'Ledger 領収書',
    'js_preparing_pdf' => 'PDFを準備しています…',
    'js_pdf_error' => "PDFを生成できませんでした。もう一度お試しください。",
    'js_preparing_share' => '共有用の領収書を準備しています…',
    'js_share_error' => "領収書を共有できませんでした。代わりにダウンロードをお試しください。",
    'js_share_unsupported' => 'このブラウザでは共有機能がサポートされていません。代わりにダウンロードをご利用ください。',
];
