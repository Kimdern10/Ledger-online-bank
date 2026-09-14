<?php

return [
    // Page chrome (send-receipt.blade.php)
    'page_title' => 'Beleg',
    'download_pdf' => 'PDF herunterladen',
    'share' => 'Teilen',
    'done' => 'Fertig',
    'send_another' => 'Weiteres senden',

    // "from"/"to"/"by" word shown before the other party's name, e.g.
    // "to Jane Doe" or "from Ledger" — controllers pass a plain identifier,
    // the blade maps it through these.
    'subverb_from' => 'von',
    'subverb_to' => 'an',
    'subverb_by' => 'durch',

    // Row labels — shared across every receipt type (Send Money,
    // Another bank, International, Withdraw, Top up, admin adjustment)
    'row_from' => 'Von',
    'row_bank' => 'Bank',
    'row_status' => 'Status',
    'row_category' => 'Kategorie',
    'row_note' => 'Notiz',
    'row_reference' => 'Referenz',
    'row_date' => 'Datum',
    'row_recipient' => 'Empfänger',
    'row_account_number' => 'Kontonummer',
    'row_routing_number' => 'Bankleitzahl',
    'row_scheduled_for' => 'Geplant für',
    'row_country' => 'Land',
    'row_swift_bic' => 'SWIFT/BIC',
    'row_account_iban' => 'Konto/IBAN',
    'row_currency' => 'Währung',
    'row_exchange_rate' => 'Wechselkurs',
    'row_recipient_receives_approx' => 'Empfänger erhält (ca.)',
    'row_withdraw_to' => 'Auszahlen auf',
    'row_fee' => 'Gebühr',
    'row_youll_receive' => 'Du erhältst',
    'row_top_up_from' => 'Aufladen von',
    'row_card_processing_fee' => 'Bearbeitungsgebühr der Karte',
    'row_account_name' => 'Kontoname',

    // Titles
    'title_payment_received' => 'Zahlung erhalten',
    'title_payment_sent' => 'Zahlung gesendet',
    'title_transfer_scheduled' => 'Überweisung geplant',
    'title_transfer_cancelled' => 'Überweisung storniert',
    'title_transfer_failed' => 'Überweisung fehlgeschlagen',
    'title_withdrawal' => 'Auszahlung',
    'title_topup' => 'Aufladung',
    'title_deposit_received' => 'Einzahlung erhalten',
    'title_balance_adjustment' => 'Saldoanpassung',

    // Banners
    'banner_scheduled' => 'Diese Überweisung ist für den :date geplant und wurde noch nicht ausgeführt. Du kannst sie jederzeit vorher stornieren.',
    'banner_cancelled' => 'Diese Überweisung wurde storniert und wurde nie ausgeführt.',
    'banner_failed' => 'Diese Überweisung wurde nicht ausgeführt — unzureichendes Guthaben am geplanten Ausführungstag.',
    'banner_transfer_sent' => 'Deine Überweisung wurde gesendet.',
    'banner_international_transfer_sent' => 'Deine internationale Überweisung wurde gesendet.',
    'banner_withdrawal_bank_days' => 'Erreicht deine Bank in 1–3 Werktagen.',
    'banner_topup_fee_note' => 'Die Bearbeitungsgebühr wurde von deiner Karte berechnet und nicht vom zu Ledger hinzugefügten Betrag abgezogen.',
    'banner_adjustment_credit' => 'Diese Einzahlung wurde von Ledger auf deinem Konto vorgenommen — wende dich bei Fragen dazu an den Support.',
    'banner_adjustment_debit' => 'Diese Anpassung wurde von Ledger auf deinem Konto vorgenommen — wende dich bei Fragen dazu an den Support.',

    // Status labels (App\Support\TransactionStatus — shared by Transfer,
    // ExternalTransfer, InternationalTransfer, Withdrawal, TopUp)
    'status_completed' => 'Abgeschlossen',
    'status_scheduled' => 'Geplant',
    'status_cancelled' => 'Storniert',
    'status_failed' => 'Fehlgeschlagen',

    // JS strings for the Download PDF / Share buttons
    'share_text_prefix' => 'Ledger-Zahlungsbeleg',
    'share_title' => 'Ledger-Beleg',
    'js_preparing_pdf' => 'Dein PDF wird vorbereitet…',
    'js_pdf_error' => 'PDF konnte nicht erstellt werden. Versuche es erneut.',
    'js_preparing_share' => 'Dein Beleg wird zum Teilen vorbereitet…',
    'js_share_error' => 'Beleg konnte nicht geteilt werden. Versuche stattdessen Herunterladen.',
    'js_share_unsupported' => 'Teilen wird in diesem Browser nicht unterstützt. Verwende stattdessen Herunterladen.',
];
