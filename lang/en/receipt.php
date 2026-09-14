<?php

return [
    // Page chrome (send-receipt.blade.php)
    'page_title' => 'Receipt',
    'download_pdf' => 'Download PDF',
    'share' => 'Share',
    'done' => 'Done',
    'send_another' => 'Send another',

    // "from"/"to"/"by" word shown before the other party's name, e.g.
    // "to Jane Doe" or "from Ledger" — controllers pass a plain identifier,
    // the blade maps it through these.
    'subverb_from' => 'from',
    'subverb_to' => 'to',
    'subverb_by' => 'by',

    // Row labels — shared across every receipt type (Send Money,
    // Another bank, International, Withdraw, Top up, admin adjustment)
    'row_from' => 'From',
    'row_bank' => 'Bank',
    'row_status' => 'Status',
    'row_category' => 'Category',
    'row_note' => 'Note',
    'row_reference' => 'Reference',
    'row_date' => 'Date',
    'row_recipient' => 'Recipient',
    'row_account_number' => 'Account number',
    'row_routing_number' => 'Routing number',
    'row_scheduled_for' => 'Scheduled for',
    'row_country' => 'Country',
    'row_swift_bic' => 'SWIFT/BIC',
    'row_account_iban' => 'Account/IBAN',
    'row_currency' => 'Currency',
    'row_exchange_rate' => 'Exchange rate',
    'row_recipient_receives_approx' => 'Recipient receives (approx.)',
    'row_withdraw_to' => 'Withdraw to',
    'row_fee' => 'Fee',
    'row_youll_receive' => "You'll receive",
    'row_top_up_from' => 'Top up from',
    'row_card_processing_fee' => 'Card processing fee',
    'row_account_name' => 'Account name',

    // Titles
    'title_payment_received' => 'Payment received',
    'title_payment_sent' => 'Payment sent',
    'title_transfer_scheduled' => 'Transfer scheduled',
    'title_transfer_cancelled' => 'Transfer cancelled',
    'title_transfer_failed' => 'Transfer failed',
    'title_withdrawal' => 'Withdrawal',
    'title_topup' => 'Top-up',
    'title_deposit_received' => 'Deposit received',
    'title_balance_adjustment' => 'Balance adjustment',

    // Banners
    'banner_scheduled' => "This transfer is scheduled for :date and hasn't gone out yet. You can cancel it any time before then.",
    'banner_cancelled' => 'This transfer was cancelled and never went out.',
    'banner_failed' => "This transfer didn't go through — insufficient funds on the day it was scheduled to go out.",
    'banner_transfer_sent' => 'Your transfer has been sent.',
    'banner_international_transfer_sent' => 'Your international transfer has been sent.',
    'banner_withdrawal_bank_days' => 'Arrives in your bank in 1–3 business days.',
    'banner_topup_fee_note' => 'The processing fee was charged by your card, not deducted from the amount added to Ledger.',
    'banner_adjustment_credit' => 'This deposit was made to your account by Ledger — contact Support if you have questions about it.',
    'banner_adjustment_debit' => 'This adjustment was made to your account by Ledger — contact Support if you have questions about it.',

    // Status labels (App\Support\TransactionStatus — shared by Transfer,
    // ExternalTransfer, InternationalTransfer, Withdrawal, TopUp)
    'status_completed' => 'Completed',
    'status_scheduled' => 'Scheduled',
    'status_cancelled' => 'Cancelled',
    'status_failed' => 'Failed',

    // JS strings for the Download PDF / Share buttons
    'share_text_prefix' => 'Ledger payment receipt',
    'share_title' => 'Ledger receipt',
    'js_preparing_pdf' => 'Preparing your PDF…',
    'js_pdf_error' => "Couldn't generate the PDF. Try again.",
    'js_preparing_share' => 'Preparing your receipt to share…',
    'js_share_error' => "Couldn't share the receipt. Try Download instead.",
    'js_share_unsupported' => 'Sharing is not supported in this browser. Use Download instead.',
];
