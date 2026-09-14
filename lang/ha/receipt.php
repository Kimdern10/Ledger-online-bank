<?php

return [
    // Page chrome (send-receipt.blade.php)
    'page_title' => 'Rasit',
    'download_pdf' => 'Sauke PDF',
    'share' => 'Raba',
    'done' => 'An gama',
    'send_another' => 'Aika wani',

    // "from"/"to"/"by" word shown before the other party's name, e.g.
    // "to Jane Doe" or "from Ledger" — controllers pass a plain identifier,
    // the blade maps it through these.
    'subverb_from' => 'daga',
    'subverb_to' => 'ga',
    'subverb_by' => 'ta hannun',

    // Row labels — shared across every receipt type (Send Money,
    // Another bank, International, Withdraw, Top up, admin adjustment)
    'row_from' => 'Daga',
    'row_bank' => 'Banki',
    'row_status' => 'Matsayi',
    'row_category' => 'Rukuni',
    'row_note' => 'Bayani',
    'row_reference' => 'Lambar Ganewa',
    'row_date' => 'Kwanan wata',
    'row_recipient' => 'Mai karɓa',
    'row_account_number' => 'Lambar asusu',
    'row_routing_number' => 'Lambar Routing',
    'row_scheduled_for' => 'An shirya don',
    'row_country' => 'Ƙasa',
    'row_swift_bic' => 'SWIFT/BIC',
    'row_account_iban' => 'Asusu/IBAN',
    'row_currency' => 'Kuɗin Ƙasa',
    'row_exchange_rate' => 'Farashin Canji',
    'row_recipient_receives_approx' => 'Mai karɓa zai samu (kimanin)',
    'row_withdraw_to' => 'Cire zuwa',
    'row_fee' => 'Caji',
    'row_youll_receive' => 'Za ka samu',
    'row_top_up_from' => 'Cika daga',
    'row_card_processing_fee' => 'Cajin Sarrafa Katin',
    'row_account_name' => 'Sunan Asusu',

    // Titles
    'title_payment_received' => 'An karɓi kuɗi',
    'title_payment_sent' => 'An aika kuɗi',
    'title_transfer_scheduled' => 'An shirya canja wuri',
    'title_transfer_cancelled' => 'An soke canja wuri',
    'title_transfer_failed' => 'Canja wuri ya kasa',
    'title_withdrawal' => 'Cirewa',
    'title_topup' => 'Cikawa',
    'title_deposit_received' => 'An karɓi ajiya',
    'title_balance_adjustment' => 'Gyaran ma\'auni',

    // Banners
    'banner_scheduled' => 'An shirya wannan canja wurin don :date kuma bai fita ba tukuna. Za ka iya soke shi a kowane lokaci kafin wannan.',
    'banner_cancelled' => 'An soke wannan canja wurin kuma bai taɓa fita ba.',
    'banner_failed' => 'Wannan canja wurin bai wuce ba — kuɗi bai isa ba a ranar da aka shirya ya fita.',
    'banner_transfer_sent' => 'An aika canja wurinka.',
    'banner_international_transfer_sent' => 'An aika canja wurin ƙasashen waje naka.',
    'banner_withdrawal_bank_days' => 'Zai isa bankinka cikin kwanaki 1-3 na kasuwanci.',
    'banner_topup_fee_note' => 'Katin ka ne ya karɓi cajin sarrafawa, ba a cire shi daga adadin da aka ƙara wa Ledger ba.',
    'banner_adjustment_credit' => 'Ledger ne ya sanya wannan ajiya a asusunka — tuntuɓi Tallafi idan kana da tambayoyi game da wannan.',
    'banner_adjustment_debit' => 'Ledger ne ya yi wannan gyaran a asusunka — tuntuɓi Tallafi idan kana da tambayoyi game da wannan.',

    // Status labels (App\Support\TransactionStatus — shared by Transfer,
    // ExternalTransfer, InternationalTransfer, Withdrawal, TopUp)
    'status_completed' => 'An kammala',
    'status_scheduled' => 'An shirya',
    'status_cancelled' => 'An soke',
    'status_failed' => 'Ya Kasa',

    // JS strings for the Download PDF / Share buttons
    'share_text_prefix' => 'Rasit na biyan kuɗi na Ledger',
    'share_title' => 'Rasit na Ledger',
    'js_preparing_pdf' => 'Ana shirya PDF ɗinka…',
    'js_pdf_error' => 'An kasa ƙirƙirar PDF. Sake gwadawa.',
    'js_preparing_share' => 'Ana shirya rasit ɗinka don rabawa…',
    'js_share_error' => 'An kasa raba rasit. Gwada Saukewa maimakon.',
    'js_share_unsupported' => 'Raba ba ya aiki a wannan burauza. Yi amfani da Saukewa maimakon.',
];
