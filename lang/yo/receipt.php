<?php

return [
    // Page chrome (send-receipt.blade.php)
    'page_title' => 'Risiiti',
    'download_pdf' => 'Gba PDF sílẹ̀',
    'share' => 'Pín',
    'done' => 'Parí',
    'send_another' => 'Fi òmíràn ránṣẹ́',

    // "from"/"to"/"by" word shown before the other party's name, e.g.
    // "to Jane Doe" or "from Ledger" — controllers pass a plain identifier,
    // the blade maps it through these.
    'subverb_from' => 'láti ọ̀dọ̀',
    'subverb_to' => 'sí',
    'subverb_by' => 'láti ọwọ́',

    // Row labels — shared across every receipt type (Send Money,
    // Another bank, International, Withdraw, Top up, admin adjustment)
    'row_from' => 'Láti Ọ̀dọ̀',
    'row_bank' => 'Báńkì',
    'row_status' => 'Ipò',
    'row_category' => 'Ẹ̀ka',
    'row_note' => 'Àkíyèsí',
    'row_reference' => 'Nọ́mbà Ìtọ́kasí',
    'row_date' => 'Ọjọ́',
    'row_recipient' => 'Olùgbà',
    'row_account_number' => 'Nọ́mbà Àkọọ́lẹ̀',
    'row_routing_number' => 'Nọ́mbà Routing',
    'row_scheduled_for' => 'Ti Ṣètò fún',
    'row_country' => 'Orílẹ̀-èdè',
    'row_swift_bic' => 'SWIFT/BIC',
    'row_account_iban' => 'Àkọọ́lẹ̀/IBAN',
    'row_currency' => 'Owó Orílẹ̀-èdè',
    'row_exchange_rate' => 'Oṣùwọ̀n Pàṣípààrọ̀',
    'row_recipient_receives_approx' => 'Olùgbà yóò gbà (ìdíwọ̀n)',
    'row_withdraw_to' => 'Yọ sí',
    'row_fee' => 'Ọ̀yà',
    'row_youll_receive' => 'Ìwọ yóò gbà',
    'row_top_up_from' => 'Kún sórí láti',
    'row_card_processing_fee' => 'Ọ̀yà Ìṣàkóso Káàdì',
    'row_account_name' => 'Orúkọ Àkọọ́lẹ̀',

    // Titles
    'title_payment_received' => 'A Ti Gba Ìsanwó',
    'title_payment_sent' => 'A Ti Fi Ìsanwó Ránṣẹ́',
    'title_transfer_scheduled' => 'A Ti Ṣètò Ìfiránṣẹ́',
    'title_transfer_cancelled' => 'A Ti Fagilé Ìfiránṣẹ́',
    'title_transfer_failed' => 'Ìfiránṣẹ́ Kùnà',
    'title_withdrawal' => 'Yíyọ Owó',
    'title_topup' => 'Kíkún Sórí',
    'title_deposit_received' => 'A Ti Gba Ìfisílẹ̀',
    'title_balance_adjustment' => 'Àtúnṣe Ìwọ̀ntúnwọ̀nsì',

    // Banners
    'banner_scheduled' => 'A ti ṣètò ìfiránṣẹ́ yìí fún :date, kò sì tíì jáde. O lè fagilé e nígbàkigbà kí ó tó di àkókò náà.',
    'banner_cancelled' => 'A ti fagilé ìfiránṣẹ́ yìí, kò sì jáde rí.',
    'banner_failed' => 'Ìfiránṣẹ́ yìí kò gòkè — owó kò tó ní ọjọ́ tí a ṣètò fún un láti jáde.',
    'banner_transfer_sent' => 'A ti fi ìfiránṣẹ́ rẹ ránṣẹ́.',
    'banner_international_transfer_sent' => 'A ti fi ìfiránṣẹ́ kárí-ayé rẹ ránṣẹ́.',
    'banner_withdrawal_bank_days' => 'Yóò dé báńkì rẹ láàrin ọjọ́ iṣẹ́ 1–3.',
    'banner_topup_fee_note' => 'Káàdì rẹ ni a gba ọ̀yà ìṣàkóso náà lọ́wọ́, a kò yọ ọ́ kúrò nínú iye tí a fi kún Ledger.',
    'banner_adjustment_credit' => 'Ledger ni ó ṣe ìfisílẹ̀ yìí sí àkọọ́lẹ̀ rẹ — kan sí Ìrànlọ́wọ́ bí o bá ní ìbéèrè nípa rẹ̀.',
    'banner_adjustment_debit' => 'Ledger ni ó ṣe àtúnṣe yìí sí àkọọ́lẹ̀ rẹ — kan sí Ìrànlọ́wọ́ bí o bá ní ìbéèrè nípa rẹ̀.',

    // Status labels (App\Support\TransactionStatus — shared by Transfer,
    // ExternalTransfer, InternationalTransfer, Withdrawal, TopUp)
    'status_completed' => 'Ti Parí',
    'status_scheduled' => 'Ti Ṣètò',
    'status_cancelled' => 'Ti Fagilé',
    'status_failed' => 'Kùnà',

    // JS strings for the Download PDF / Share buttons
    'share_text_prefix' => 'Risiiti ìsanwó Ledger',
    'share_title' => 'Risiiti Ledger',
    'js_preparing_pdf' => 'Ń pèsè PDF rẹ sílẹ̀…',
    'js_pdf_error' => 'A kò lè ṣẹ̀dá PDF. Tún gbìyànjú.',
    'js_preparing_share' => 'Ń pèsè risiiti rẹ sílẹ̀ láti pín…',
    'js_share_error' => 'A kò lè pín risiiti náà. Gbìyànjú Gbígba sílẹ̀ dípò.',
    'js_share_unsupported' => 'Pínpín kò ṣiṣẹ́ nínú aṣàwákiri yìí. Lo Gbígba sílẹ̀ dípò.',
];
