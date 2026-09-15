<?php

return [
    'page_title' => 'Payer des factures',
    'from_label' => 'Depuis',
    'account_fallback' => 'Compte',
    'account_type_checking' => 'Compte courant',
    'account_type_savings' => 'Épargne',
    'available' => 'Disponible',
    'upcoming_bills' => 'Factures à venir',
    'due_prefix' => 'Échéance',
    'add_bill_link' => '+ Ajouter une facture',
    'no_bills_yet' => 'Aucune facture pour le moment',
    'no_bills_sub' => 'Ajoutez une facture pour suivre ce qui est dû et la payer directement depuis ici.',
    'pay_btn' => 'Payer',
    'remove_bill_aria' => 'Supprimer :category',
    'checking_required_title' => 'Compte courant requis',
    'checking_required_sub' => "Les factures ne peuvent être ajoutées et payées qu'à partir d'un compte courant. Votre compte est uniquement un compte d'épargne, cette page est donc en lecture seule pour vous.",

    // Add-bill sheet
    'add_bill_heading' => 'Ajouter une facture',
    'bill_type_label' => 'Type de facture',
    'bill_type_rent_mortgage' => 'Loyer / Prêt immobilier',
    'bill_type_credit_card' => 'Carte de crédit',
    'bill_type_medical_insurance' => 'Assurance médicale',
    'bill_type_taxes' => 'Impôts',
    'bill_type_student_loan' => 'Prêt étudiant',
    'bill_type_other' => 'Autre',
    'bill_name_label' => 'Nom de la facture',
    'bill_name_placeholder' => 'ex. Abonnement salle de sport',
    'biller_label' => 'Facturier',
    'biller_placeholder' => 'À qui vous payez',
    'amount_label' => 'Montant',
    'due_date_label' => "Date d'échéance",
    'add_bill_submit' => 'Ajouter la facture',
    'adding' => 'Ajout en cours…',

    // Add-bill validation errors
    'error_enter_bill_name' => 'Entrez un nom pour cette facture.',
    'error_choose_bill_type' => 'Choisissez un type de facture.',
    'error_enter_biller' => 'Indiquez à qui vous payez.',
    'error_enter_valid_amount' => 'Entrez un montant valide.',
    'error_pick_due_date' => "Choisissez une date d'échéance.",
    'error_add_bill_failed' => "Impossible d'ajouter cette facture — réessayez.",
    'error_add_bill_connection' => "Impossible d'ajouter cette facture — vérifiez votre connexion et réessayez.",

    // Pay-bill confirmation sheet
    'pay_bill_heading' => 'Payer la facture',
    'pay_from' => 'Payer depuis',
    'confirm_payment' => 'Confirmer le paiement',
    'payment_scheduled_suffix' => 'paiement programmé',
    'payment_scheduled_toast' => 'Paiement programmé',

    // Remove-bill confirmation dialog
    'remove_confirm_title' => 'Supprimer :name ?',
    'remove_confirm_text' => 'Vous pourrez toujours la rajouter plus tard.',
    'remove_confirm_button' => 'Supprimer',
    'remove_cancel_button' => 'Annuler',
    'this_bill_fallback' => 'cette facture',
    'bill_removed_toast' => ':name supprimée',
    'bill_added_toast' => ':category ajoutée',
    'error_remove_bill_failed' => 'Impossible de supprimer cette facture — réessayez',
    'error_remove_bill_connection' => 'Impossible de supprimer cette facture — vérifiez votre connexion',
];
