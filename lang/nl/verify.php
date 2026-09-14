<?php

return [
    // Tiers (App\Support\AccountTier)
    'tier_1' => 'Niveau 1',
    'tier_2' => 'Niveau 2',
    'tier_3' => 'Niveau 3',
    'tier_1_desc' => 'Starter — verifieer uw identiteit om uw limiet te verhogen',
    'tier_2_desc' => 'Identiteit geverifieerd — verifieer uw adres om deze verder te verhogen',
    'tier_3_desc' => 'Volledig geverifieerd',

    // Shared status labels (KycVerification / AddressVerification)
    'status_approved' => 'Goedgekeurd',
    'status_rejected' => 'Afgewezen',
    'status_pending_review' => 'In afwachting van beoordeling',

    // Settings hub rows
    'identity_verification' => 'Identiteitsverificatie',
    'address_verification' => 'Adresverificatie',
    'badge_verified' => 'Geverifieerd',
    'badge_pending' => 'In behandeling',
    'badge_needs_resubmit' => 'Opnieuw indienen vereist',
    'badge_not_started' => 'Niet gestart',

    // Shared buttons
    'back_to_dashboard' => 'Terug naar dashboard',
    'back_to_settings' => 'Terug naar Instellingen',
    'message_support' => 'Bericht naar ondersteuning',
    'submit_for_review' => 'Indienen ter beoordeling',
    'verify_identity' => 'Verifieer uw identiteit',
    'verify_address' => 'Verifieer uw adres',
    'last_submission_rejected' => "Uw laatste indiening is niet goedgekeurd.",

    // Send Money gate (kyc-required.blade.php)
    'kyc_gate_pending_title' => 'Geld versturen wordt ontgrendeld zodra uw ID is beoordeeld',
    'kyc_gate_pending_body' => "We hebben uw legitimatiebewijs en foto ontvangen. Ons team beoordeelt deze nu, meestal binnen een dag. Ondertussen werkt al het andere op uw account gewoon normaal.",
    'kyc_gate_rejected_title' => 'Geld versturen vereist een nieuwe identiteitsverificatie',
    'kyc_gate_rejected_body' => "Uw laatste indiening is niet goedgekeurd. Bekijk het nog eens en dien het opnieuw in. Het kost maar een minuutje.",
    'kyc_gate_not_started_title' => 'Verifieer uw identiteit om geld te versturen',
    'kyc_gate_not_started_body' => "Upload een legitimatiebewijs en een korte selfie. Dit is de laatste stap om uw account volledig te ontgrendelen. Al het andere werkt al.",

    // Identity verification page (kyc-verify.blade.php)
    'kyc_approved_title' => "U bent geverifieerd",
    'kyc_approved_body' => 'Uw legitimatiebewijs en foto zijn beoordeeld en goedgekeurd. Geld versturen is volledig ontgrendeld.',
    'kyc_approved_body_dated' => 'Uw legitimatiebewijs en foto zijn op :date beoordeeld en goedgekeurd. Geld versturen is volledig ontgrendeld.',
    'kyc_pending_title' => 'Uw ID wordt beoordeeld',
    'kyc_pending_body' => "U heeft een :type en een foto ingediend :time. Ons team beoordeelt deze handmatig, meestal binnen een dag. We laten het u weten zodra hierover is beslist. Ondertussen werkt al het andere op uw account gewoon normaal; alleen Geld versturen blijft tot die tijd vergrendeld.",
    'kyc_resubmit_notice' => 'Probeer het hieronder opnieuw met een duidelijke, onbewerkte foto van uw ID en een goed verlichte selfie.',
    'kyc_form_title' => 'Nog één laatste stap',
    'kyc_form_body' => "Upload een foto van een geldig, door de overheid uitgegeven identiteitsbewijs en een selfie van uzelf. Zo bevestigen we dat u echt bent wie u zegt te zijn voordat uw account volledig wordt ontgrendeld. Geen enkele derde partij ziet deze gegevens ooit, alleen ons eigen team beoordeelt ze handmatig. U kunt uw account gewoon normaal blijven gebruiken terwijl dit wordt beoordeeld; alleen Geld versturen wacht tot het is goedgekeurd.",
    'id_type_label' => 'Type ID',
    'select_id_type' => 'Selecteer type ID',
    'id_photo_label' => 'Foto van uw ID (voorkant)',
    'selfie_label' => 'Een selfie van uzelf',
    'selfie_hint' => '(goed verlicht, gezicht duidelijk zichtbaar)',

    // Address verification page (address-verify.blade.php)
    'address_approved_title' => "U bent volledig geverifieerd",
    'address_approved_body' => "Uw adresbewijs is beoordeeld en goedgekeurd. U bent nu Niveau 3 — uw daglimiet is :limit.",
    'address_approved_body_dated' => "Uw adresbewijs is op :date beoordeeld en goedgekeurd. U bent nu Niveau 3 — uw daglimiet is :limit.",
    'address_pending_title' => 'Uw document wordt beoordeeld',
    'address_pending_body' => 'U heeft een :type ingediend :time. Ons team beoordeelt deze handmatig, meestal binnen een dag. Uw huidige daglimiet blijft tot die tijd :limit.',
    'address_resubmit_notice' => 'Probeer het hieronder opnieuw met een duidelijk, recent document.',
    'address_form_title' => 'Verhoog uw daglimiet',
    'address_form_body' => 'Upload een recent document waarop uw naam en huisadres staan — een energierekening, een bankafschrift of een huurovereenkomst voldoen allemaal. Dit is de laatste verificatiestap: het verhoogt uw dagelijkse limiet voor Geld versturen en Opnemen van :from naar :to. Alleen ons eigen team beoordeelt dit, meestal binnen een dag.',
    'document_type_label' => 'Documenttype',
    'select_document_type' => 'Selecteer documenttype',
    'document_label' => 'Document',
    'document_hint' => '(JPG, PNG of PDF, gedateerd binnen de laatste 3 maanden)',

    // Document type option labels (KycController/AddressVerificationController DOCUMENT_TYPES, and the models' documentTypeLabel())
    'doc_drivers_license' => "Rijbewijs",
    'doc_state_id' => 'Door de overheid uitgegeven ID-kaart',
    'doc_passport' => 'Amerikaans paspoort',
    'doc_other_id' => 'Ander door de overheid uitgegeven identiteitsbewijs',
    'doc_utility_bill' => 'Energierekening',
    'doc_bank_statement' => 'Bankafschrift',
    'doc_tenancy_agreement' => 'Huurovereenkomst',
    'doc_other_address' => 'Ander adresbewijs',

    // Controller flash messages
    'kyc_submitted_status' => "Bedankt — we hebben uw ID ontvangen. Uw account is klaar voor gebruik terwijl ons team dit beoordeelt, meestal binnen een dag. We laten het u weten zodra hierover is beslist.",
    'address_submitted_status' => "Bedankt — we hebben uw document ontvangen. We laten het u weten zodra hierover is beslist, meestal binnen een dag.",
    'address_verify_identity_first' => 'Verifieer eerst uw identiteit — adresverificatie is de volgende stap daarna.',
];
