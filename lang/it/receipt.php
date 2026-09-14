<?php

return [
    // Page chrome (send-receipt.blade.php)
    'page_title' => 'Ricevuta',
    'download_pdf' => 'Scarica PDF',
    'share' => 'Condividi',
    'done' => 'Fatto',
    'send_another' => 'Invia un altro',

    // "from"/"to"/"by" word shown before the other party's name, e.g.
    // "to Jane Doe" or "from Ledger" — controllers pass a plain identifier,
    // the blade maps it through these.
    'subverb_from' => 'da',
    'subverb_to' => 'a',
    'subverb_by' => 'da',

    // Row labels — shared across every receipt type (Send Money,
    // Another bank, International, Withdraw, Top up, admin adjustment)
    'row_from' => 'Da',
    'row_bank' => 'Banca',
    'row_status' => 'Stato',
    'row_category' => 'Categoria',
    'row_note' => 'Nota',
    'row_reference' => 'Riferimento',
    'row_date' => 'Data',
    'row_recipient' => 'Destinatario',
    'row_account_number' => 'Numero di conto',
    'row_routing_number' => 'Codice ABI/routing',
    'row_scheduled_for' => 'Programmato per',
    'row_country' => 'Paese',
    'row_swift_bic' => 'SWIFT/BIC',
    'row_account_iban' => 'Conto/IBAN',
    'row_currency' => 'Valuta',
    'row_exchange_rate' => 'Tasso di cambio',
    'row_recipient_receives_approx' => 'Il destinatario riceve (circa)',
    'row_withdraw_to' => 'Preleva su',
    'row_fee' => 'Commissione',
    'row_youll_receive' => 'Riceverai',
    'row_top_up_from' => 'Ricarica da',
    'row_card_processing_fee' => 'Commissione di elaborazione della carta',
    'row_account_name' => 'Nome del conto',

    // Titles
    'title_payment_received' => 'Pagamento ricevuto',
    'title_payment_sent' => 'Pagamento inviato',
    'title_transfer_scheduled' => 'Bonifico programmato',
    'title_transfer_cancelled' => 'Bonifico annullato',
    'title_transfer_failed' => 'Bonifico non riuscito',
    'title_withdrawal' => 'Prelievo',
    'title_topup' => 'Ricarica',
    'title_deposit_received' => 'Deposito ricevuto',
    'title_balance_adjustment' => 'Rettifica del saldo',

    // Banners
    'banner_scheduled' => 'Questo bonifico è programmato per il :date e non è ancora stato inviato. Puoi annullarlo in qualsiasi momento prima di allora.',
    'banner_cancelled' => 'Questo bonifico è stato annullato e non è mai stato inviato.',
    'banner_failed' => 'Questo bonifico non è andato a buon fine — fondi insufficienti il giorno in cui doveva essere inviato.',
    'banner_transfer_sent' => 'Il tuo bonifico è stato inviato.',
    'banner_international_transfer_sent' => 'Il tuo bonifico internazionale è stato inviato.',
    'banner_withdrawal_bank_days' => 'Arriva sul tuo conto bancario in 1-3 giorni lavorativi.',
    'banner_topup_fee_note' => 'La commissione di elaborazione è stata addebitata dalla tua carta, non dedotta dall\'importo aggiunto a Ledger.',
    'banner_adjustment_credit' => 'Questo deposito è stato effettuato sul tuo conto da Ledger — contatta l\'Assistenza se hai domande in merito.',
    'banner_adjustment_debit' => 'Questa rettifica è stata effettuata sul tuo conto da Ledger — contatta l\'Assistenza se hai domande in merito.',

    // Status labels (App\Support\TransactionStatus — shared by Transfer,
    // ExternalTransfer, InternationalTransfer, Withdrawal, TopUp)
    'status_completed' => 'Completato',
    'status_scheduled' => 'Programmato',
    'status_cancelled' => 'Annullato',
    'status_failed' => 'Non riuscito',

    // JS strings for the Download PDF / Share buttons
    'share_text_prefix' => 'Ricevuta di pagamento Ledger',
    'share_title' => 'Ricevuta Ledger',
    'js_preparing_pdf' => 'Preparazione del PDF in corso…',
    'js_pdf_error' => 'Impossibile generare il PDF. Riprova.',
    'js_preparing_share' => 'Preparazione della ricevuta da condividere…',
    'js_share_error' => 'Impossibile condividere la ricevuta. Prova invece a scaricarla.',
    'js_share_unsupported' => 'La condivisione non è supportata in questo browser. Usa invece Scarica.',
];
