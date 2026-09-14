<?php

return [
    // Tiers (App\Support\AccountTier)
    'tier_1' => "Niveau 1",
    'tier_2' => "Niveau 2",
    'tier_3' => "Niveau 3",
    'tier_1_desc' => "Débutant — vérifiez votre identité pour augmenter votre limite",
    'tier_2_desc' => "Identité vérifiée — vérifiez votre adresse pour l'augmenter davantage",
    'tier_3_desc' => "Entièrement vérifié",

    // Shared status labels (KycVerification / AddressVerification)
    'status_approved' => "Approuvé",
    'status_rejected' => "Rejeté",
    'status_pending_review' => "Examen en cours",

    // Settings hub rows
    'identity_verification' => "Vérification d'identité",
    'address_verification' => "Vérification d'adresse",
    'badge_verified' => "Vérifié",
    'badge_pending' => "En attente",
    'badge_needs_resubmit' => "Nouvel envoi requis",
    'badge_not_started' => "Non commencé",

    // Shared buttons
    'back_to_dashboard' => "Retour au tableau de bord",
    'back_to_settings' => "Retour aux paramètres",
    'message_support' => "Contacter l'assistance",
    'submit_for_review' => "Soumettre pour examen",
    'verify_identity' => "Vérifiez votre identité",
    'verify_address' => "Vérifiez votre adresse",
    'last_submission_rejected' => "Votre dernier envoi n'a pas été approuvé.",

    // Send Money gate (kyc-required.blade.php)
    'kyc_gate_pending_title' => "Envoyer de l'argent se débloque une fois votre pièce d'identité examinée",
    'kyc_gate_pending_body' => "Nous avons bien reçu votre pièce d'identité officielle et votre photo. Notre équipe les examine actuellement, généralement en une journée. Le reste de votre compte fonctionne normalement pendant ce temps.",
    'kyc_gate_rejected_title' => "Envoyer de l'argent nécessite une nouvelle vérification d'identité",
    'kyc_gate_rejected_body' => "Votre dernier envoi n'a pas été approuvé. Regardez-le à nouveau et renvoyez-le. Cela ne prend qu'une minute.",
    'kyc_gate_not_started_title' => "Vérifiez votre identité pour envoyer de l'argent",
    'kyc_gate_not_started_body' => "Téléversez une pièce d'identité officielle et un selfie rapide. C'est la dernière étape pour débloquer entièrement votre compte. Tout le reste fonctionne déjà.",

    // Identity verification page (kyc-verify.blade.php)
    'kyc_approved_title' => "Vous êtes vérifié",
    'kyc_approved_body' => "Votre pièce d'identité officielle et votre photo ont été examinées et approuvées. Envoyer de l'argent est entièrement débloqué.",
    'kyc_approved_body_dated' => "Votre pièce d'identité officielle et votre photo ont été examinées et approuvées le :date. Envoyer de l'argent est entièrement débloqué.",
    'kyc_pending_title' => "Votre pièce d'identité est en cours d'examen",
    'kyc_pending_body' => "Vous avez soumis un(e) :type et une photo :time. Notre équipe examine cela manuellement, généralement en une journée. Nous vous informerons dès qu'une décision sera prise. Le reste de votre compte fonctionne normalement pendant ce temps ; seul Envoyer de l'argent reste verrouillé jusque-là.",
    'kyc_resubmit_notice' => "Veuillez réessayer ci-dessous avec une photo claire et non modifiée de votre pièce d'identité ainsi qu'un selfie bien éclairé.",
    'kyc_form_title' => "Une dernière étape",
    'kyc_form_body' => "Téléversez une photo d'une pièce d'identité officielle valide et un selfie de vous-même. C'est ainsi que nous confirmons que c'est bien vous avant de débloquer entièrement votre compte. Aucun tiers ne les voit jamais, seule notre propre équipe les examine manuellement. Vous pouvez continuer à utiliser votre compte normalement pendant l'examen ; seul Envoyer de l'argent attend son approbation.",
    'id_type_label' => "Type de pièce d'identité",
    'select_id_type' => "Sélectionnez le type de pièce d'identité",
    'id_photo_label' => "Photo de votre pièce d'identité (recto)",
    'selfie_label' => "Un selfie de vous",
    'selfie_hint' => "(bien éclairé, visage clairement visible)",

    // Address verification page (address-verify.blade.php)
    'address_approved_title' => "Vous êtes entièrement vérifié",
    'address_approved_body' => "Votre justificatif de domicile a été examiné et approuvé. Vous êtes maintenant au Niveau 3 — votre limite quotidienne est de :limit.",
    'address_approved_body_dated' => "Votre justificatif de domicile a été examiné et approuvé le :date. Vous êtes maintenant au Niveau 3 — votre limite quotidienne est de :limit.",
    'address_pending_title' => "Votre document est en cours d'examen",
    'address_pending_body' => "Vous avez soumis un(e) :type :time. Notre équipe examine cela manuellement, généralement en une journée. Votre limite quotidienne actuelle reste à :limit jusque-là.",
    'address_resubmit_notice' => "Veuillez réessayer ci-dessous avec un document clair et récent.",
    'address_form_title' => "Augmentez votre limite quotidienne",
    'address_form_body' => "Téléversez un document récent indiquant votre nom et votre adresse personnelle — une facture de services publics, un relevé bancaire ou un contrat de location conviennent tous. C'est la dernière étape de vérification : elle augmente votre limite quotidienne d'Envoyer de l'argent et de Retirer de :from à :to. Seule notre propre équipe l'examine, généralement en une journée.",
    'document_type_label' => "Type de document",
    'select_document_type' => "Sélectionnez le type de document",
    'document_label' => "Document",
    'document_hint' => "(JPG, PNG ou PDF, daté des 3 derniers mois)",

    // Document type option labels (KycController/AddressVerificationController DOCUMENT_TYPES, and the models' documentTypeLabel())
    'doc_drivers_license' => "Permis de conduire",
    'doc_state_id' => "Carte d'identité délivrée par l'État",
    'doc_passport' => "Passeport américain",
    'doc_other_id' => "Autre pièce d'identité officielle",
    'doc_utility_bill' => "Facture de services publics",
    'doc_bank_statement' => "Relevé bancaire",
    'doc_tenancy_agreement' => "Contrat de location",
    'doc_other_address' => "Autre justificatif de domicile",

    // Controller flash messages
    'kyc_submitted_status' => "Merci — nous avons bien reçu votre pièce d'identité. Votre compte est prêt à être utilisé pendant que notre équipe l'examine, généralement en une journée. Nous vous informerons dès qu'une décision sera prise.",
    'address_submitted_status' => "Merci — nous avons bien reçu votre document. Nous vous informerons dès qu'une décision sera prise, généralement en une journée.",
    'address_verify_identity_first' => "Vérifiez d'abord votre identité — la vérification d'adresse est l'étape suivante.",
];
