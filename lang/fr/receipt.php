<?php

return [
    // Page chrome (send-receipt.blade.php)
    'page_title' => 'Reçu',
    'download_pdf' => 'Télécharger le PDF',
    'share' => 'Partager',
    'done' => 'Terminé',
    'send_another' => 'Envoyer un autre',

    // "from"/"to"/"by" word shown before the other party's name, e.g.
    // "to Jane Doe" or "from Ledger" — controllers pass a plain identifier,
    // the blade maps it through these.
    'subverb_from' => 'de',
    'subverb_to' => 'à',
    'subverb_by' => 'par',

    // Row labels — shared across every receipt type (Send Money,
    // Another bank, International, Withdraw, Top up, admin adjustment)
    'row_from' => 'De',
    'row_bank' => 'Banque',
    'row_status' => 'Statut',
    'row_category' => 'Catégorie',
    'row_note' => 'Note',
    'row_reference' => 'Référence',
    'row_date' => 'Date',
    'row_recipient' => 'Destinataire',
    'row_account_number' => 'Numéro de compte',
    'row_routing_number' => "Numéro d'acheminement",
    'row_scheduled_for' => 'Programmé pour',
    'row_country' => 'Pays',
    'row_swift_bic' => 'SWIFT/BIC',
    'row_account_iban' => 'Compte/IBAN',
    'row_currency' => 'Devise',
    'row_exchange_rate' => 'Taux de change',
    'row_recipient_receives_approx' => 'Le destinataire reçoit (env.)',
    'row_withdraw_to' => 'Retirer vers',
    'row_fee' => 'Frais',
    'row_youll_receive' => 'Vous recevrez',
    'row_top_up_from' => 'Recharger depuis',
    'row_card_processing_fee' => 'Frais de traitement de la carte',
    'row_account_name' => 'Nom du compte',

    // Titles
    'title_payment_received' => 'Paiement reçu',
    'title_payment_sent' => 'Paiement envoyé',
    'title_transfer_scheduled' => 'Virement programmé',
    'title_transfer_cancelled' => 'Virement annulé',
    'title_transfer_failed' => 'Virement échoué',
    'title_withdrawal' => 'Retrait',
    'title_topup' => 'Recharge',
    'title_deposit_received' => 'Dépôt reçu',
    'title_balance_adjustment' => 'Ajustement de solde',

    // Banners
    'banner_scheduled' => "Ce virement est programmé pour le :date et n'a pas encore été envoyé. Vous pouvez l'annuler à tout moment avant cette date.",
    'banner_cancelled' => "Ce virement a été annulé et n'a jamais été envoyé.",
    'banner_failed' => "Ce virement n'a pas abouti — fonds insuffisants le jour où il devait être envoyé.",
    'banner_transfer_sent' => 'Votre virement a été envoyé.',
    'banner_international_transfer_sent' => 'Votre virement international a été envoyé.',
    'banner_withdrawal_bank_days' => 'Arrive dans votre banque sous 1 à 3 jours ouvrés.',
    'banner_topup_fee_note' => "Les frais de traitement ont été facturés par votre carte, et non déduits du montant ajouté à Ledger.",
    'banner_adjustment_credit' => 'Ce dépôt a été effectué sur votre compte par Ledger — contactez le Support si vous avez des questions à ce sujet.',
    'banner_adjustment_debit' => 'Cet ajustement a été effectué sur votre compte par Ledger — contactez le Support si vous avez des questions à ce sujet.',

    // Status labels (App\Support\TransactionStatus — shared by Transfer,
    // ExternalTransfer, InternationalTransfer, Withdrawal, TopUp)
    'status_completed' => 'Terminé',
    'status_scheduled' => 'Programmé',
    'status_cancelled' => 'Annulé',
    'status_failed' => 'Échoué',

    // JS strings for the Download PDF / Share buttons
    'share_text_prefix' => 'Reçu de paiement Ledger',
    'share_title' => 'Reçu Ledger',
    'js_preparing_pdf' => 'Préparation de votre PDF…',
    'js_pdf_error' => "Impossible de générer le PDF. Réessayez.",
    'js_preparing_share' => 'Préparation de votre reçu à partager…',
    'js_share_error' => "Impossible de partager le reçu. Essayez plutôt de le télécharger.",
    'js_share_unsupported' => "Le partage n'est pas pris en charge par ce navigateur. Utilisez Télécharger à la place.",
];
