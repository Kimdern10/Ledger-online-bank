<?php

return [
    // Page chrome (send-receipt.blade.php)
    'page_title' => 'Risiti',
    'download_pdf' => 'Budata PDF',
    'share' => 'Kesaa',
    'done' => 'Emechaala',
    'send_another' => 'Ziga ọzọ',

    // "from"/"to"/"by" word shown before the other party's name, e.g.
    // "to Jane Doe" or "from Ledger" — controllers pass a plain identifier,
    // the blade maps it through these.
    'subverb_from' => 'site na',
    'subverb_to' => 'nye',
    'subverb_by' => "site n'aka",

    // Row labels — shared across every receipt type (Send Money,
    // Another bank, International, Withdraw, Top up, admin adjustment)
    'row_from' => 'Site na',
    'row_bank' => 'Ụlọ akụ',
    'row_status' => 'Ọnọdụ',
    'row_category' => 'Ụdị',
    'row_note' => 'Ndetu',
    'row_reference' => 'Nọmba Njirimara',
    'row_date' => 'Ụbọchị',
    'row_recipient' => 'Onye Nnata',
    'row_account_number' => 'Nọmba akaụntụ',
    'row_routing_number' => 'Nọmba Routing',
    'row_scheduled_for' => 'Edobere maka',
    'row_country' => 'Obodo',
    'row_swift_bic' => 'SWIFT/BIC',
    'row_account_iban' => 'Akaụntụ/IBAN',
    'row_currency' => 'Ụdị Ego',
    'row_exchange_rate' => 'Ọnụahịa Mgbanwe Ego',
    'row_recipient_receives_approx' => 'Onye nnata ga-enweta (ihe dịka)',
    'row_withdraw_to' => 'Wepụta gaa',
    'row_fee' => 'Ụgwọ',
    'row_youll_receive' => 'Ị ga-enweta',
    'row_top_up_from' => 'Gbakwunye site na',
    'row_card_processing_fee' => 'Ụgwọ nhazi kaadị',
    'row_account_name' => 'Aha Akaụntụ',

    // Titles
    'title_payment_received' => 'A Natala Ụgwọ',
    'title_payment_sent' => 'E Zigala Ụgwọ',
    'title_transfer_scheduled' => 'Edobere Mbufe',
    'title_transfer_cancelled' => 'A Kagburu Mbufe',
    'title_transfer_failed' => 'Mbufe Adaghị',
    'title_withdrawal' => 'Iwepụta Ego',
    'title_topup' => 'Mgbakwunye Ego',
    'title_deposit_received' => 'A Natala Ntinye Ego',
    'title_balance_adjustment' => 'Mgbanwe Ego Fọdụrụ',

    // Banners
    'banner_scheduled' => 'Edobere mbufe a maka :date, ọ kabeghị apụ. Ị nwere ike ịkagbu ya mgbe ọ bụla tupu mgbe ahụ.',
    'banner_cancelled' => 'A kagburu mbufe a, ọ pụtaghịkwa.',
    'banner_failed' => 'Mbufe a agaghị nke ọma — ego ezughị n\'ụbọchị e depụtara ka ọ pụọ.',
    'banner_transfer_sent' => 'E zigala mbufe gị.',
    'banner_international_transfer_sent' => 'E zigala mbufe mba ụwa gị.',
    'banner_withdrawal_bank_days' => 'Ọ ga-eru n\'ụlọ akụ gị n\'ime ụbọchị ọrụ 1–3.',
    'banner_topup_fee_note' => "Kaadị gị ka e ji gbaa ụgwọ nhazi ahụ, e wepụghị ya n'ego etinyere na Ledger.",
    'banner_adjustment_credit' => 'Ledger tinyere ntinye ego a n\'akaụntụ gị — kpọtụrụ Enyemaka ma ọ bụrụ na ị nwere ajụjụ banyere ya.',
    'banner_adjustment_debit' => 'Ledger mere mgbanwe a n\'akaụntụ gị — kpọtụrụ Enyemaka ma ọ bụrụ na ị nwere ajụjụ banyere ya.',

    // Status labels (App\Support\TransactionStatus — shared by Transfer,
    // ExternalTransfer, InternationalTransfer, Withdrawal, TopUp)
    'status_completed' => 'Emechaala',
    'status_scheduled' => 'Edobere',
    'status_cancelled' => 'Akagbuola',
    'status_failed' => 'Adaghị',

    // JS strings for the Download PDF / Share buttons
    'share_text_prefix' => 'Risiti ịkwụ ụgwọ Ledger',
    'share_title' => 'Risiti Ledger',
    'js_preparing_pdf' => 'Na-akwadebe PDF gị…',
    'js_pdf_error' => 'Enweghị ike ịmepụta PDF. Gbalịa ọzọ.',
    'js_preparing_share' => 'Na-akwadebe risiti gị iji kesaa…',
    'js_share_error' => 'Enweghị ike ikesa risiti. Gbalịa Budata kama.',
    'js_share_unsupported' => 'Ikesa anaghị arụ ọrụ na ihe nchọgharị a. Jiri Budata kama.',
];
