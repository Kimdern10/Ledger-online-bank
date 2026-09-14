<?php

return [
    // Page chrome (send-receipt.blade.php)
    'page_title' => '收据',
    'download_pdf' => '下载 PDF',
    'share' => '分享',
    'done' => '完成',
    'send_another' => '再转一笔',

    // "from"/"to"/"by" word shown before the other party's name, e.g.
    // "to Jane Doe" or "from Ledger" — controllers pass a plain identifier,
    // the blade maps it through these.
    'subverb_from' => '来自',
    'subverb_to' => '至',
    'subverb_by' => '由',

    // Row labels — shared across every receipt type (Send Money,
    // Another bank, International, Withdraw, Top up, admin adjustment)
    'row_from' => '来自',
    'row_bank' => '银行',
    'row_status' => '状态',
    'row_category' => '分类',
    'row_note' => '备注',
    'row_reference' => '参考编号',
    'row_date' => '日期',
    'row_recipient' => '收款人',
    'row_account_number' => '账号',
    'row_routing_number' => '路由号码',
    'row_scheduled_for' => '预定于',
    'row_country' => '国家',
    'row_swift_bic' => 'SWIFT/BIC',
    'row_account_iban' => '账号/IBAN',
    'row_currency' => '货币',
    'row_exchange_rate' => '汇率',
    'row_recipient_receives_approx' => '收款人到账金额（约）',
    'row_withdraw_to' => '提现至',
    'row_fee' => '手续费',
    'row_youll_receive' => '您将收到',
    'row_top_up_from' => '充值来源',
    'row_card_processing_fee' => '银行卡处理费',
    'row_account_name' => '账户名称',

    // Titles
    'title_payment_received' => '已收到付款',
    'title_payment_sent' => '已发送付款',
    'title_transfer_scheduled' => '转账已预定',
    'title_transfer_cancelled' => '转账已取消',
    'title_transfer_failed' => '转账失败',
    'title_withdrawal' => '提现',
    'title_topup' => '充值',
    'title_deposit_received' => '已收到存款',
    'title_balance_adjustment' => '余额调整',

    // Banners
    'banner_scheduled' => '此转账预定于 :date 发出，目前尚未发出。您可以在此之前随时取消。',
    'banner_cancelled' => '此转账已取消，从未发出。',
    'banner_failed' => '此转账未能完成——在预定发出当天余额不足。',
    'banner_transfer_sent' => '您的转账已发送。',
    'banner_international_transfer_sent' => '您的国际转账已发送。',
    'banner_withdrawal_bank_days' => '将在 1 至 3 个工作日内到达您的银行账户。',
    'banner_topup_fee_note' => '此处理费由您的银行卡收取，并未从添加到 Ledger 的金额中扣除。',
    'banner_adjustment_credit' => '此笔存款由 Ledger 存入您的账户——如有疑问，请联系客服。',
    'banner_adjustment_debit' => '此笔调整由 Ledger 对您的账户进行——如有疑问，请联系客服。',

    // Status labels (App\Support\TransactionStatus — shared by Transfer,
    // ExternalTransfer, InternationalTransfer, Withdrawal, TopUp)
    'status_completed' => '已完成',
    'status_scheduled' => '已预定',
    'status_cancelled' => '已取消',
    'status_failed' => '失败',

    // JS strings for the Download PDF / Share buttons
    'share_text_prefix' => 'Ledger 付款收据',
    'share_title' => 'Ledger 收据',
    'js_preparing_pdf' => '正在准备您的 PDF…',
    'js_pdf_error' => '无法生成 PDF。请重试。',
    'js_preparing_share' => '正在准备要分享的收据…',
    'js_share_error' => '无法分享收据。请改用下载。',
    'js_share_unsupported' => '此浏览器不支持分享功能。请改用下载。',
];
