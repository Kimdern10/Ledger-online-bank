<?php

return [
    // Page chrome (send-receipt.blade.php)
    'page_title' => 'Potwierdzenie',
    'download_pdf' => 'Pobierz PDF',
    'share' => 'Udostępnij',
    'done' => 'Gotowe',
    'send_another' => 'Wyślij kolejny',

    // "from"/"to"/"by" word shown before the other party's name, e.g.
    // "to Jane Doe" or "from Ledger" — controllers pass a plain identifier,
    // the blade maps it through these.
    'subverb_from' => 'od',
    'subverb_to' => 'do',
    'subverb_by' => 'przez',

    // Row labels — shared across every receipt type (Send Money,
    // Another bank, International, Withdraw, Top up, admin adjustment)
    'row_from' => 'Od',
    'row_bank' => 'Bank',
    'row_status' => 'Status',
    'row_category' => 'Kategoria',
    'row_note' => 'Notatka',
    'row_reference' => 'Numer referencyjny',
    'row_date' => 'Data',
    'row_recipient' => 'Odbiorca',
    'row_account_number' => 'Numer konta',
    'row_routing_number' => 'Numer rozliczeniowy',
    'row_scheduled_for' => 'Zaplanowano na',
    'row_country' => 'Kraj',
    'row_swift_bic' => 'SWIFT/BIC',
    'row_account_iban' => 'Konto/IBAN',
    'row_currency' => 'Waluta',
    'row_exchange_rate' => 'Kurs wymiany',
    'row_recipient_receives_approx' => 'Odbiorca otrzyma (w przybliżeniu)',
    'row_withdraw_to' => 'Wypłata na',
    'row_fee' => 'Opłata',
    'row_youll_receive' => 'Otrzymasz',
    'row_top_up_from' => 'Doładowanie z',
    'row_card_processing_fee' => 'Opłata za przetwarzanie karty',
    'row_account_name' => 'Nazwa konta',

    // Titles
    'title_payment_received' => 'Płatność odebrana',
    'title_payment_sent' => 'Płatność wysłana',
    'title_transfer_scheduled' => 'Przelew zaplanowany',
    'title_transfer_cancelled' => 'Przelew anulowany',
    'title_transfer_failed' => 'Przelew nieudany',
    'title_withdrawal' => 'Wypłata',
    'title_topup' => 'Doładowanie',
    'title_deposit_received' => 'Wpłata odebrana',
    'title_balance_adjustment' => 'Korekta salda',

    // Banners
    'banner_scheduled' => 'Ten przelew jest zaplanowany na :date i jeszcze nie został wysłany. Możesz go anulować w dowolnym momencie przed tą datą.',
    'banner_cancelled' => 'Ten przelew został anulowany i nigdy nie został wysłany.',
    'banner_failed' => 'Ten przelew nie powiódł się — niewystarczające środki w dniu, na który był zaplanowany.',
    'banner_transfer_sent' => 'Twój przelew został wysłany.',
    'banner_international_transfer_sent' => 'Twój przelew międzynarodowy został wysłany.',
    'banner_withdrawal_bank_days' => 'Środki wpłyną na Twoje konto bankowe w ciągu 1–3 dni roboczych.',
    'banner_topup_fee_note' => 'Opłata za przetwarzanie została pobrana przez Twoją kartę, a nie odjęta od kwoty dodanej do Ledger.',
    'banner_adjustment_credit' => 'Ta wpłata została dokonana na Twoje konto przez Ledger — skontaktuj się z Pomocą techniczną w razie pytań.',
    'banner_adjustment_debit' => 'Ta korekta została wprowadzona na Twoim koncie przez Ledger — skontaktuj się z Pomocą techniczną w razie pytań.',

    // Status labels (App\Support\TransactionStatus — shared by Transfer,
    // ExternalTransfer, InternationalTransfer, Withdrawal, TopUp)
    'status_completed' => 'Zakończono',
    'status_scheduled' => 'Zaplanowano',
    'status_cancelled' => 'Anulowano',
    'status_failed' => 'Nieudane',

    // JS strings for the Download PDF / Share buttons
    'share_text_prefix' => 'Potwierdzenie płatności Ledger',
    'share_title' => 'Potwierdzenie Ledger',
    'js_preparing_pdf' => 'Przygotowywanie PDF…',
    'js_pdf_error' => 'Nie udało się wygenerować pliku PDF. Spróbuj ponownie.',
    'js_preparing_share' => 'Przygotowywanie potwierdzenia do udostępnienia…',
    'js_share_error' => 'Nie udało się udostępnić potwierdzenia. Spróbuj zamiast tego pobrać.',
    'js_share_unsupported' => 'Udostępnianie nie jest obsługiwane w tej przeglądarce. Użyj zamiast tego opcji Pobierz.',
];
