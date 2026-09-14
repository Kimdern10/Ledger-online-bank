<?php

return [
    // Page chrome (send-receipt.blade.php)
    'page_title' => 'Recibo',
    'download_pdf' => 'Descargar PDF',
    'share' => 'Compartir',
    'done' => 'Listo',
    'send_another' => 'Enviar otro',

    // "from"/"to"/"by" word shown before the other party's name, e.g.
    // "to Jane Doe" or "from Ledger" — controllers pass a plain identifier,
    // the blade maps it through these.
    'subverb_from' => 'de',
    'subverb_to' => 'a',
    'subverb_by' => 'por',

    // Row labels — shared across every receipt type (Send Money,
    // Another bank, International, Withdraw, Top up, admin adjustment)
    'row_from' => 'De',
    'row_bank' => 'Banco',
    'row_status' => 'Estado',
    'row_category' => 'Categoría',
    'row_note' => 'Nota',
    'row_reference' => 'Referencia',
    'row_date' => 'Fecha',
    'row_recipient' => 'Destinatario',
    'row_account_number' => 'Número de cuenta',
    'row_routing_number' => 'Número de ruta',
    'row_scheduled_for' => 'Programado para',
    'row_country' => 'País',
    'row_swift_bic' => 'SWIFT/BIC',
    'row_account_iban' => 'Cuenta/IBAN',
    'row_currency' => 'Moneda',
    'row_exchange_rate' => 'Tipo de cambio',
    'row_recipient_receives_approx' => 'El destinatario recibe (aprox.)',
    'row_withdraw_to' => 'Retirar a',
    'row_fee' => 'Comisión',
    'row_youll_receive' => 'Recibirás',
    'row_top_up_from' => 'Recargar desde',
    'row_card_processing_fee' => 'Comisión de procesamiento de la tarjeta',
    'row_account_name' => 'Nombre de la cuenta',

    // Titles
    'title_payment_received' => 'Pago recibido',
    'title_payment_sent' => 'Pago enviado',
    'title_transfer_scheduled' => 'Transferencia programada',
    'title_transfer_cancelled' => 'Transferencia cancelada',
    'title_transfer_failed' => 'Transferencia fallida',
    'title_withdrawal' => 'Retiro',
    'title_topup' => 'Recarga',
    'title_deposit_received' => 'Depósito recibido',
    'title_balance_adjustment' => 'Ajuste de saldo',

    // Banners
    'banner_scheduled' => 'Esta transferencia está programada para el :date y aún no se ha enviado. Puedes cancelarla en cualquier momento antes de esa fecha.',
    'banner_cancelled' => 'Esta transferencia fue cancelada y nunca se envió.',
    'banner_failed' => 'Esta transferencia no se completó: fondos insuficientes el día en que estaba programada para salir.',
    'banner_transfer_sent' => 'Tu transferencia ha sido enviada.',
    'banner_international_transfer_sent' => 'Tu transferencia internacional ha sido enviada.',
    'banner_withdrawal_bank_days' => 'Llega a tu banco en 1–3 días hábiles.',
    'banner_topup_fee_note' => 'La comisión de procesamiento fue cobrada por tu tarjeta, no se dedujo del monto añadido a Ledger.',
    'banner_adjustment_credit' => 'Este depósito fue realizado en tu cuenta por Ledger — contacta a Soporte si tienes preguntas al respecto.',
    'banner_adjustment_debit' => 'Este ajuste fue realizado en tu cuenta por Ledger — contacta a Soporte si tienes preguntas al respecto.',

    // Status labels (App\Support\TransactionStatus — shared by Transfer,
    // ExternalTransfer, InternationalTransfer, Withdrawal, TopUp)
    'status_completed' => 'Completado',
    'status_scheduled' => 'Programado',
    'status_cancelled' => 'Cancelado',
    'status_failed' => 'Fallido',

    // JS strings for the Download PDF / Share buttons
    'share_text_prefix' => 'Recibo de pago de Ledger',
    'share_title' => 'Recibo de Ledger',
    'js_preparing_pdf' => 'Preparando tu PDF…',
    'js_pdf_error' => 'No se pudo generar el PDF. Inténtalo de nuevo.',
    'js_preparing_share' => 'Preparando tu recibo para compartir…',
    'js_share_error' => 'No se pudo compartir el recibo. Prueba a descargarlo.',
    'js_share_unsupported' => 'Compartir no es compatible con este navegador. Usa Descargar en su lugar.',
];
