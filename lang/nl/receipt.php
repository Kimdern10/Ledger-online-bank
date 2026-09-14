<?php

return [
    // Page chrome (send-receipt.blade.php)
    'page_title' => 'Betalingsbewijs',
    'download_pdf' => 'PDF downloaden',
    'share' => 'Delen',
    'done' => 'Klaar',
    'send_another' => 'Nog een versturen',

    // "from"/"to"/"by" word shown before the other party's name, e.g.
    // "to Jane Doe" or "from Ledger" — controllers pass a plain identifier,
    // the blade maps it through these.
    'subverb_from' => 'van',
    'subverb_to' => 'naar',
    'subverb_by' => 'door',

    // Row labels — shared across every receipt type (Send Money,
    // Another bank, International, Withdraw, Top up, admin adjustment)
    'row_from' => 'Van',
    'row_bank' => 'Bank',
    'row_status' => 'Status',
    'row_category' => 'Categorie',
    'row_note' => 'Notitie',
    'row_reference' => 'Referentie',
    'row_date' => 'Datum',
    'row_recipient' => 'Ontvanger',
    'row_account_number' => 'Rekeningnummer',
    'row_routing_number' => 'Routingnummer',
    'row_scheduled_for' => 'Gepland voor',
    'row_country' => 'Land',
    'row_swift_bic' => 'SWIFT/BIC',
    'row_account_iban' => 'Rekening/IBAN',
    'row_currency' => 'Valuta',
    'row_exchange_rate' => 'Wisselkoers',
    'row_recipient_receives_approx' => 'Ontvanger ontvangt (ca.)',
    'row_withdraw_to' => 'Opnemen naar',
    'row_fee' => 'Kosten',
    'row_youll_receive' => 'U ontvangt',
    'row_top_up_from' => 'Opwaarderen vanaf',
    'row_card_processing_fee' => 'Verwerkingskosten kaart',
    'row_account_name' => 'Rekeningnaam',

    // Titles
    'title_payment_received' => 'Betaling ontvangen',
    'title_payment_sent' => 'Betaling verzonden',
    'title_transfer_scheduled' => 'Overschrijving gepland',
    'title_transfer_cancelled' => 'Overschrijving geannuleerd',
    'title_transfer_failed' => 'Overschrijving mislukt',
    'title_withdrawal' => 'Opname',
    'title_topup' => 'Opwaardering',
    'title_deposit_received' => 'Storting ontvangen',
    'title_balance_adjustment' => 'Saldocorrectie',

    // Banners
    'banner_scheduled' => 'Deze overschrijving is gepland voor :date en is nog niet verzonden. U kunt deze op elk moment daarvoor annuleren.',
    'banner_cancelled' => 'Deze overschrijving is geannuleerd en is nooit verzonden.',
    'banner_failed' => 'Deze overschrijving is niet doorgegaan — onvoldoende saldo op de dag waarop deze verzonden zou worden.',
    'banner_transfer_sent' => 'Uw overschrijving is verzonden.',
    'banner_international_transfer_sent' => 'Uw internationale overschrijving is verzonden.',
    'banner_withdrawal_bank_days' => 'Komt binnen 1-3 werkdagen aan op uw bankrekening.',
    'banner_topup_fee_note' => 'De verwerkingskosten zijn door uw kaart in rekening gebracht en niet afgetrokken van het bedrag dat aan Ledger is toegevoegd.',
    'banner_adjustment_credit' => 'Deze storting is door Ledger op uw rekening gedaan — neem contact op met Ondersteuning als u hier vragen over heeft.',
    'banner_adjustment_debit' => 'Deze correctie is door Ledger op uw rekening doorgevoerd — neem contact op met Ondersteuning als u hier vragen over heeft.',

    // Status labels (App\Support\TransactionStatus — shared by Transfer,
    // ExternalTransfer, InternationalTransfer, Withdrawal, TopUp)
    'status_completed' => 'Voltooid',
    'status_scheduled' => 'Gepland',
    'status_cancelled' => 'Geannuleerd',
    'status_failed' => 'Mislukt',

    // JS strings for the Download PDF / Share buttons
    'share_text_prefix' => 'Ledger betalingsbewijs',
    'share_title' => 'Ledger betalingsbewijs',
    'js_preparing_pdf' => 'Uw PDF wordt voorbereid…',
    'js_pdf_error' => 'De PDF kon niet worden gegenereerd. Probeer het opnieuw.',
    'js_preparing_share' => 'Uw betalingsbewijs wordt voorbereid om te delen…',
    'js_share_error' => 'Het betalingsbewijs kon niet worden gedeeld. Probeer in plaats daarvan PDF downloaden.',
    'js_share_unsupported' => 'Delen wordt niet ondersteund in deze browser. Gebruik in plaats daarvan PDF downloaden.',
];
