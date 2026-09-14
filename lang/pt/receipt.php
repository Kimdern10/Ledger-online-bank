<?php

return [
    // Page chrome (send-receipt.blade.php)
    'page_title' => 'Recibo',
    'download_pdf' => 'Baixar PDF',
    'share' => 'Compartilhar',
    'done' => 'Concluído',
    'send_another' => 'Enviar outro',

    // "from"/"to"/"by" word shown before the other party's name, e.g.
    // "to Jane Doe" or "from Ledger" — controllers pass a plain identifier,
    // the blade maps it through these.
    'subverb_from' => 'de',
    'subverb_to' => 'para',
    'subverb_by' => 'por',

    // Row labels — shared across every receipt type (Send Money,
    // Another bank, International, Withdraw, Top up, admin adjustment)
    'row_from' => 'De',
    'row_bank' => 'Banco',
    'row_status' => 'Status',
    'row_category' => 'Categoria',
    'row_note' => 'Nota',
    'row_reference' => 'Referência',
    'row_date' => 'Data',
    'row_recipient' => 'Destinatário',
    'row_account_number' => 'Número da conta',
    'row_routing_number' => 'Número de roteamento',
    'row_scheduled_for' => 'Agendado para',
    'row_country' => 'País',
    'row_swift_bic' => 'SWIFT/BIC',
    'row_account_iban' => 'Conta/IBAN',
    'row_currency' => 'Moeda',
    'row_exchange_rate' => 'Taxa de câmbio',
    'row_recipient_receives_approx' => 'O destinatário recebe (aprox.)',
    'row_withdraw_to' => 'Sacar para',
    'row_fee' => 'Taxa',
    'row_youll_receive' => 'Você receberá',
    'row_top_up_from' => 'Recarregar de',
    'row_card_processing_fee' => 'Taxa de processamento do cartão',
    'row_account_name' => 'Nome da conta',

    // Titles
    'title_payment_received' => 'Pagamento recebido',
    'title_payment_sent' => 'Pagamento enviado',
    'title_transfer_scheduled' => 'Transferência agendada',
    'title_transfer_cancelled' => 'Transferência cancelada',
    'title_transfer_failed' => 'Transferência falhou',
    'title_withdrawal' => 'Saque',
    'title_topup' => 'Recarga',
    'title_deposit_received' => 'Depósito recebido',
    'title_balance_adjustment' => 'Ajuste de saldo',

    // Banners
    'banner_scheduled' => 'Esta transferência está agendada para :date e ainda não foi enviada. Você pode cancelá-la a qualquer momento antes dessa data.',
    'banner_cancelled' => 'Esta transferência foi cancelada e nunca foi enviada.',
    'banner_failed' => 'Esta transferência não foi concluída — saldo insuficiente no dia em que estava agendada para sair.',
    'banner_transfer_sent' => 'Sua transferência foi enviada.',
    'banner_international_transfer_sent' => 'Sua transferência internacional foi enviada.',
    'banner_withdrawal_bank_days' => 'Chega ao seu banco em 1 a 3 dias úteis.',
    'banner_topup_fee_note' => 'A taxa de processamento foi cobrada pelo seu cartão, não deduzida do valor adicionado ao Ledger.',
    'banner_adjustment_credit' => 'Este depósito foi feito na sua conta pelo Ledger — entre em contato com o Suporte se tiver dúvidas sobre isso.',
    'banner_adjustment_debit' => 'Este ajuste foi feito na sua conta pelo Ledger — entre em contato com o Suporte se tiver dúvidas sobre isso.',

    // Status labels (App\Support\TransactionStatus — shared by Transfer,
    // ExternalTransfer, InternationalTransfer, Withdrawal, TopUp)
    'status_completed' => 'Concluído',
    'status_scheduled' => 'Agendado',
    'status_cancelled' => 'Cancelado',
    'status_failed' => 'Falhou',

    // JS strings for the Download PDF / Share buttons
    'share_text_prefix' => 'Recibo de pagamento Ledger',
    'share_title' => 'Recibo Ledger',
    'js_preparing_pdf' => 'Preparando seu PDF…',
    'js_pdf_error' => 'Não foi possível gerar o PDF. Tente novamente.',
    'js_preparing_share' => 'Preparando seu recibo para compartilhar…',
    'js_share_error' => 'Não foi possível compartilhar o recibo. Tente Baixar em vez disso.',
    'js_share_unsupported' => 'O compartilhamento não é compatível com este navegador. Use Baixar em vez disso.',
];
