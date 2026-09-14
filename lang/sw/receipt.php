<?php

return [
    // Page chrome (send-receipt.blade.php)
    'page_title' => 'Risiti',
    'download_pdf' => 'Pakua PDF',
    'share' => 'Shiriki',
    'done' => 'Imekamilika',
    'send_another' => 'Tuma nyingine',

    // "from"/"to"/"by" word shown before the other party's name, e.g.
    // "to Jane Doe" or "from Ledger" — controllers pass a plain identifier,
    // the blade maps it through these.
    'subverb_from' => 'kutoka',
    'subverb_to' => 'kwa',
    'subverb_by' => 'na',

    // Row labels — shared across every receipt type (Send Money,
    // Another bank, International, Withdraw, Top up, admin adjustment)
    'row_from' => 'Kutoka',
    'row_bank' => 'Benki',
    'row_status' => 'Hali',
    'row_category' => 'Aina',
    'row_note' => 'Maelezo',
    'row_reference' => 'Rejea',
    'row_date' => 'Tarehe',
    'row_recipient' => 'Mpokeaji',
    'row_account_number' => 'Nambari ya Akaunti',
    'row_routing_number' => 'Nambari ya Kuelekeza',
    'row_scheduled_for' => 'Imepangwa kwa',
    'row_country' => 'Nchi',
    'row_swift_bic' => 'SWIFT/BIC',
    'row_account_iban' => 'Akaunti/IBAN',
    'row_currency' => 'Sarafu',
    'row_exchange_rate' => 'Kiwango cha ubadilishaji',
    'row_recipient_receives_approx' => 'Mpokeaji atapokea (takriban)',
    'row_withdraw_to' => 'Toa kwenda',
    'row_fee' => 'Ada',
    'row_youll_receive' => 'Utapokea',
    'row_top_up_from' => 'Ongeza kutoka',
    'row_card_processing_fee' => 'Ada ya uchakataji wa kadi',
    'row_account_name' => 'Jina la Akaunti',

    // Titles
    'title_payment_received' => 'Malipo yamepokelewa',
    'title_payment_sent' => 'Malipo yametumwa',
    'title_transfer_scheduled' => 'Uhamisho umepangwa',
    'title_transfer_cancelled' => 'Uhamisho umeghairiwa',
    'title_transfer_failed' => 'Uhamisho umeshindwa',
    'title_withdrawal' => 'Utoaji',
    'title_topup' => 'Uongezaji',
    'title_deposit_received' => 'Amana imepokelewa',
    'title_balance_adjustment' => 'Marekebisho ya salio',

    // Banners
    'banner_scheduled' => 'Uhamisho huu umepangwa kwa :date na bado haujatoka. Unaweza kuughairi wakati wowote kabla ya hapo.',
    'banner_cancelled' => 'Uhamisho huu ulighairiwa na haukuwahi kutoka.',
    'banner_failed' => 'Uhamisho huu haukufanikiwa — salio halikutosha siku uliyopangwa kutoka.',
    'banner_transfer_sent' => 'Uhamisho wako umetumwa.',
    'banner_international_transfer_sent' => 'Uhamisho wako wa kimataifa umetumwa.',
    'banner_withdrawal_bank_days' => 'Utafika kwenye benki yako ndani ya siku 1-3 za kazi.',
    'banner_topup_fee_note' => 'Ada ya uchakataji ilitozwa na kadi yako, haikukatwa kutoka kiasi kilichoongezwa kwenye Ledger.',
    'banner_adjustment_credit' => 'Amana hii iliwekwa kwenye akaunti yako na Ledger — wasiliana na Msaada ikiwa una maswali kuhusu hili.',
    'banner_adjustment_debit' => 'Marekebisho haya yalifanywa kwenye akaunti yako na Ledger — wasiliana na Msaada ikiwa una maswali kuhusu hili.',

    // Status labels (App\Support\TransactionStatus — shared by Transfer,
    // ExternalTransfer, InternationalTransfer, Withdrawal, TopUp)
    'status_completed' => 'Imekamilika',
    'status_scheduled' => 'Imepangwa',
    'status_cancelled' => 'Imeghairiwa',
    'status_failed' => 'Imeshindwa',

    // JS strings for the Download PDF / Share buttons
    'share_text_prefix' => 'Risiti ya malipo ya Ledger',
    'share_title' => 'Risiti ya Ledger',
    'js_preparing_pdf' => 'Inaandaa PDF yako…',
    'js_pdf_error' => 'Imeshindwa kutengeneza PDF. Jaribu tena.',
    'js_preparing_share' => 'Inaandaa risiti yako kwa ajili ya kushiriki…',
    'js_share_error' => 'Imeshindwa kushiriki risiti. Jaribu Kupakua badala yake.',
    'js_share_unsupported' => 'Kushiriki hakutumiki kwenye kivinjari hiki. Tumia Kupakua badala yake.',
];
