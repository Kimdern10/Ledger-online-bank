<?php

return [
    // Tiers (App\Support\AccountTier)
    'tier_1' => "Livello 1",
    'tier_2' => "Livello 2",
    'tier_3' => "Livello 3",
    'tier_1_desc' => "Iniziale — verifica la tua identità per aumentare il limite",
    'tier_2_desc' => "Identità verificata — verifica il tuo indirizzo per aumentarlo ulteriormente",
    'tier_3_desc' => "Completamente verificato",

    // Shared status labels (KycVerification / AddressVerification)
    'status_approved' => "Approvato",
    'status_rejected' => "Rifiutato",
    'status_pending_review' => "Revisione in corso",

    // Settings hub rows
    'identity_verification' => "Verifica dell'identità",
    'address_verification' => "Verifica dell'indirizzo",
    'badge_verified' => "Verificato",
    'badge_pending' => "In attesa",
    'badge_needs_resubmit' => "Richiede un nuovo invio",
    'badge_not_started' => "Non iniziato",

    // Shared buttons
    'back_to_dashboard' => "Torna alla dashboard",
    'back_to_settings' => "Torna alle Impostazioni",
    'message_support' => "Contatta l'assistenza",
    'submit_for_review' => "Invia per la revisione",
    'verify_identity' => "Verifica la tua identità",
    'verify_address' => "Verifica il tuo indirizzo",
    'last_submission_rejected' => "Il tuo ultimo invio non è stato approvato.",

    // Send Money gate (kyc-required.blade.php)
    'kyc_gate_pending_title' => "Invia denaro si sblocca quando il tuo documento sarà stato esaminato",
    'kyc_gate_pending_body' => "Abbiamo ricevuto il tuo documento d'identità e la tua foto. Il nostro team li sta esaminando, di solito entro un giorno. Nel frattempo tutto il resto del tuo account funziona normalmente.",
    'kyc_gate_rejected_title' => "Invia denaro richiede una nuova verifica dell'identità",
    'kyc_gate_rejected_body' => "Il tuo ultimo invio non è stato approvato. Dai un'altra occhiata e invialo di nuovo. Richiede solo un minuto.",
    'kyc_gate_not_started_title' => "Verifica la tua identità per inviare denaro",
    'kyc_gate_not_started_body' => "Carica un documento d'identità e un selfie veloce. È l'ultimo passaggio per sbloccare completamente il tuo account. Tutto il resto funziona già.",

    // Identity verification page (kyc-verify.blade.php)
    'kyc_approved_title' => "Sei verificato",
    'kyc_approved_body' => "Il tuo documento d'identità e la tua foto sono stati esaminati e approvati. Invia denaro è completamente sbloccato.",
    'kyc_approved_body_dated' => "Il tuo documento d'identità e la tua foto sono stati esaminati e approvati il :date. Invia denaro è completamente sbloccato.",
    'kyc_pending_title' => "Il tuo documento è in fase di revisione",
    'kyc_pending_body' => "Hai inviato un :type e una foto :time. Il nostro team esamina questi documenti manualmente, di solito entro un giorno. Ti avviseremo non appena verrà presa una decisione. Nel frattempo tutto il resto del tuo account funziona normalmente; solo Invia denaro resta bloccato fino ad allora.",
    'kyc_resubmit_notice' => "Riprova qui sotto con una foto chiara e non modificata del tuo documento e un selfie ben illuminato.",
    'kyc_form_title' => "Un ultimo passaggio",
    'kyc_form_body' => "Carica una foto di un documento d'identità valido e un selfie di te stesso. È così che confermiamo che sei davvero tu prima di sbloccare completamente il tuo account. Nessuna terza parte le vede mai: solo il nostro team le esamina manualmente. Puoi continuare a usare il tuo account normalmente durante la revisione; solo Invia denaro attende l'approvazione.",
    'id_type_label' => "Tipo di documento",
    'select_id_type' => "Seleziona il tipo di documento",
    'id_photo_label' => "Foto del tuo documento (fronte)",
    'selfie_label' => "Un tuo selfie",
    'selfie_hint' => "(ben illuminato, con il viso chiaramente visibile)",

    // Address verification page (address-verify.blade.php)
    'address_approved_title' => "Sei completamente verificato",
    'address_approved_body' => "Il tuo documento di residenza è stato esaminato e approvato. Ora sei al Livello 3 — il tuo limite giornaliero è :limit.",
    'address_approved_body_dated' => "Il tuo documento di residenza è stato esaminato e approvato il :date. Ora sei al Livello 3 — il tuo limite giornaliero è :limit.",
    'address_pending_title' => "Il tuo documento è in fase di revisione",
    'address_pending_body' => "Hai inviato un :type :time. Il nostro team esamina questi documenti manualmente, di solito entro un giorno. Il tuo limite giornaliero attuale resta a :limit fino ad allora.",
    'address_resubmit_notice' => "Riprova qui sotto con un documento chiaro e recente.",
    'address_form_title' => "Aumenta il tuo limite giornaliero",
    'address_form_body' => "Carica un documento recente che mostri il tuo nome e il tuo indirizzo di residenza — vanno bene una bolletta, un estratto conto bancario o un contratto di locazione. Questo è l'ultimo passaggio di verifica: aumenta il tuo limite giornaliero di Invia denaro e Preleva da :from a :to. Solo il nostro team lo esamina, di solito entro un giorno.",
    'document_type_label' => "Tipo di documento",
    'select_document_type' => "Seleziona il tipo di documento",
    'document_label' => "Documento",
    'document_hint' => "(JPG, PNG o PDF, datato negli ultimi 3 mesi)",

    // Document type option labels (KycController/AddressVerificationController DOCUMENT_TYPES, and the models' documentTypeLabel())
    'doc_drivers_license' => "Patente di guida",
    'doc_state_id' => "Carta d'identità rilasciata dallo stato",
    'doc_passport' => "Passaporto statunitense",
    'doc_other_id' => "Altro documento d'identità governativo",
    'doc_utility_bill' => "Bolletta",
    'doc_bank_statement' => "Estratto conto bancario",
    'doc_tenancy_agreement' => "Contratto di locazione",
    'doc_other_address' => "Altro documento di residenza",

    // Controller flash messages
    'kyc_submitted_status' => "Grazie — abbiamo ricevuto il tuo documento d'identità. Il tuo account è pronto per l'uso mentre il nostro team lo esamina, di solito entro un giorno. Ti avviseremo non appena verrà presa una decisione.",
    'address_submitted_status' => "Grazie — abbiamo ricevuto il tuo documento. Ti avviseremo non appena verrà presa una decisione, di solito entro un giorno.",
    'address_verify_identity_first' => "Verifica prima la tua identità: la verifica dell'indirizzo è il passaggio successivo.",
];
