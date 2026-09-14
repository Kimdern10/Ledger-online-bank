<?php

return [
    // Page chrome (send-receipt.blade.php)
    'page_title' => 'रसीद',
    'download_pdf' => 'PDF डाउनलोड करें',
    'share' => 'शेयर करें',
    'done' => 'हो गया',
    'send_another' => 'एक और भेजें',

    // "from"/"to"/"by" word shown before the other party's name, e.g.
    // "to Jane Doe" or "from Ledger" — controllers pass a plain identifier,
    // the blade maps it through these.
    'subverb_from' => 'से',
    'subverb_to' => 'को',
    'subverb_by' => 'द्वारा',

    // Row labels — shared across every receipt type (Send Money,
    // Another bank, International, Withdraw, Top up, admin adjustment)
    'row_from' => 'भेजने वाला',
    'row_bank' => 'बैंक',
    'row_status' => 'स्थिति',
    'row_category' => 'श्रेणी',
    'row_note' => 'नोट',
    'row_reference' => 'संदर्भ संख्या',
    'row_date' => 'तारीख',
    'row_recipient' => 'प्राप्तकर्ता',
    'row_account_number' => 'खाता संख्या',
    'row_routing_number' => 'रूटिंग नंबर',
    'row_scheduled_for' => 'निर्धारित तिथि',
    'row_country' => 'देश',
    'row_swift_bic' => 'SWIFT/BIC',
    'row_account_iban' => 'खाता/IBAN',
    'row_currency' => 'मुद्रा',
    'row_exchange_rate' => 'विनिमय दर',
    'row_recipient_receives_approx' => 'प्राप्तकर्ता को मिलेगा (लगभग)',
    'row_withdraw_to' => 'निकासी गंतव्य',
    'row_fee' => 'शुल्क',
    'row_youll_receive' => 'आपको मिलेगा',
    'row_top_up_from' => 'टॉप अप स्रोत',
    'row_card_processing_fee' => 'कार्ड प्रोसेसिंग शुल्क',
    'row_account_name' => 'खाताधारक का नाम',

    // Titles
    'title_payment_received' => 'भुगतान प्राप्त हुआ',
    'title_payment_sent' => 'भुगतान भेजा गया',
    'title_transfer_scheduled' => 'ट्रांसफर निर्धारित',
    'title_transfer_cancelled' => 'ट्रांसफर रद्द किया गया',
    'title_transfer_failed' => 'ट्रांसफर विफल',
    'title_withdrawal' => 'निकासी',
    'title_topup' => 'टॉप-अप',
    'title_deposit_received' => 'जमा प्राप्त हुआ',
    'title_balance_adjustment' => 'बैलेंस समायोजन',

    // Banners
    'banner_scheduled' => "यह ट्रांसफर :date के लिए निर्धारित है और अभी तक भेजा नहीं गया है। आप उससे पहले किसी भी समय इसे रद्द कर सकते हैं।",
    'banner_cancelled' => 'यह ट्रांसफर रद्द कर दिया गया था और कभी नहीं भेजा गया।',
    'banner_failed' => "यह ट्रांसफर पूरा नहीं हो सका — निर्धारित तिथि पर पर्याप्त बैलेंस नहीं था।",
    'banner_transfer_sent' => 'आपका ट्रांसफर भेज दिया गया है।',
    'banner_international_transfer_sent' => 'आपका अंतरराष्ट्रीय ट्रांसफर भेज दिया गया है।',
    'banner_withdrawal_bank_days' => '1–3 कार्य दिवसों में आपके बैंक में पहुंच जाएगा।',
    'banner_topup_fee_note' => 'प्रोसेसिंग शुल्क आपके कार्ड द्वारा लिया गया था, न कि Ledger में जोड़ी गई राशि से काटा गया।',
    'banner_adjustment_credit' => 'यह जमा Ledger द्वारा आपके खाते में किया गया है — इस बारे में कोई सवाल हो तो सपोर्ट से संपर्क करें।',
    'banner_adjustment_debit' => 'यह समायोजन Ledger द्वारा आपके खाते में किया गया है — इस बारे में कोई सवाल हो तो सपोर्ट से संपर्क करें।',

    // Status labels (App\Support\TransactionStatus — shared by Transfer,
    // ExternalTransfer, InternationalTransfer, Withdrawal, TopUp)
    'status_completed' => 'पूर्ण',
    'status_scheduled' => 'निर्धारित',
    'status_cancelled' => 'रद्द',
    'status_failed' => 'विफल',

    // JS strings for the Download PDF / Share buttons
    'share_text_prefix' => 'Ledger भुगतान रसीद',
    'share_title' => 'Ledger रसीद',
    'js_preparing_pdf' => 'आपकी PDF तैयार की जा रही है…',
    'js_pdf_error' => "PDF जनरेट नहीं हो सकी। फिर से कोशिश करें।",
    'js_preparing_share' => 'शेयर करने के लिए आपकी रसीद तैयार की जा रही है…',
    'js_share_error' => "रसीद शेयर नहीं हो सकी। इसके बजाय डाउनलोड करें।",
    'js_share_unsupported' => 'इस ब्राउज़र में शेयर करना समर्थित नहीं है। इसके बजाय डाउनलोड का उपयोग करें।',
];
