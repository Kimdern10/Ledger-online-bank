<?php

return [
    // Tiers (App\Support\AccountTier)
    'tier_1' => "Stufe 1",
    'tier_2' => "Stufe 2",
    'tier_3' => "Stufe 3",
    'tier_1_desc' => "Einsteiger — verifiziere deine Identität, um dein Limit zu erhöhen",
    'tier_2_desc' => "Identität verifiziert — verifiziere deine Adresse, um es weiter zu erhöhen",
    'tier_3_desc' => "Vollständig verifiziert",

    // Shared status labels (KycVerification / AddressVerification)
    'status_approved' => "Genehmigt",
    'status_rejected' => "Abgelehnt",
    'status_pending_review' => "Prüfung ausstehend",

    // Settings hub rows
    'identity_verification' => "Identitätsprüfung",
    'address_verification' => "Adressprüfung",
    'badge_verified' => "Verifiziert",
    'badge_pending' => "Ausstehend",
    'badge_needs_resubmit' => "Erneute Einreichung nötig",
    'badge_not_started' => "Nicht begonnen",

    // Shared buttons
    'back_to_dashboard' => "Zurück zum Dashboard",
    'back_to_settings' => "Zurück zu den Einstellungen",
    'message_support' => "Support kontaktieren",
    'submit_for_review' => "Zur Prüfung einreichen",
    'verify_identity' => "Verifiziere deine Identität",
    'verify_address' => "Verifiziere deine Adresse",
    'last_submission_rejected' => "Deine letzte Einreichung wurde nicht genehmigt.",

    // Send Money gate (kyc-required.blade.php)
    'kyc_gate_pending_title' => "Geld senden wird freigeschaltet, sobald dein Ausweis geprüft wurde",
    'kyc_gate_pending_body' => "Wir haben deinen amtlichen Ausweis und dein Foto erhalten. Unser Team prüft sie gerade, normalerweise innerhalb eines Tages. Alles andere in deinem Konto funktioniert in der Zwischenzeit ganz normal.",
    'kyc_gate_rejected_title' => "Geld senden erfordert eine neue Identitätsprüfung",
    'kyc_gate_rejected_body' => "Deine letzte Einreichung wurde nicht genehmigt. Schau sie dir noch einmal an und reiche sie erneut ein. Das dauert nur eine Minute.",
    'kyc_gate_not_started_title' => "Verifiziere deine Identität, um Geld zu senden",
    'kyc_gate_not_started_body' => "Lade einen amtlichen Ausweis und ein schnelles Selfie hoch. Das ist der letzte Schritt, um dein Konto vollständig freizuschalten. Alles andere funktioniert bereits.",

    // Identity verification page (kyc-verify.blade.php)
    'kyc_approved_title' => "Du bist verifiziert",
    'kyc_approved_body' => "Dein amtlicher Ausweis und dein Foto wurden geprüft und genehmigt. Geld senden ist vollständig freigeschaltet.",
    'kyc_approved_body_dated' => "Dein amtlicher Ausweis und dein Foto wurden am :date geprüft und genehmigt. Geld senden ist vollständig freigeschaltet.",
    'kyc_pending_title' => "Dein Ausweis wird geprüft",
    'kyc_pending_body' => "Du hast einen/eine :type und ein Foto :time eingereicht. Unser Team prüft dies manuell, normalerweise innerhalb eines Tages. Wir benachrichtigen dich, sobald eine Entscheidung getroffen wurde. Alles andere in deinem Konto funktioniert in der Zwischenzeit ganz normal; nur Geld senden bleibt bis dahin gesperrt.",
    'kyc_resubmit_notice' => "Bitte versuche es unten erneut mit einem klaren, unbearbeiteten Foto deines Ausweises und einem gut beleuchteten Selfie.",
    'kyc_form_title' => "Ein letzter Schritt",
    'kyc_form_body' => "Lade ein Foto eines gültigen amtlichen Ausweises und ein Selfie von dir hoch. So bestätigen wir, dass du es wirklich bist, bevor dein Konto vollständig freigeschaltet wird. Kein Dritter sieht diese jemals, nur unser eigenes Team prüft sie manuell. Du kannst dein Konto währenddessen ganz normal weiter nutzen; nur Geld senden wartet auf die Genehmigung.",
    'id_type_label' => "Ausweistyp",
    'select_id_type' => "Ausweistyp auswählen",
    'id_photo_label' => "Foto deines Ausweises (Vorderseite)",
    'selfie_label' => "Ein Selfie von dir",
    'selfie_hint' => "(gut beleuchtet, Gesicht klar erkennbar)",

    // Address verification page (address-verify.blade.php)
    'address_approved_title' => "Du bist vollständig verifiziert",
    'address_approved_body' => "Dein Adressnachweis wurde geprüft und genehmigt. Du bist jetzt Stufe 3 — dein Tageslimit beträgt :limit.",
    'address_approved_body_dated' => "Dein Adressnachweis wurde am :date geprüft und genehmigt. Du bist jetzt Stufe 3 — dein Tageslimit beträgt :limit.",
    'address_pending_title' => "Dein Dokument wird geprüft",
    'address_pending_body' => "Du hast ein(e) :type :time eingereicht. Unser Team prüft dies manuell, normalerweise innerhalb eines Tages. Dein aktuelles Tageslimit bleibt bis dahin bei :limit.",
    'address_resubmit_notice' => "Bitte versuche es unten erneut mit einem klaren, aktuellen Dokument.",
    'address_form_title' => "Erhöhe dein Tageslimit",
    'address_form_body' => "Lade ein aktuelles Dokument hoch, das deinen Namen und deine Wohnadresse zeigt — eine Nebenkostenabrechnung, ein Kontoauszug oder ein Mietvertrag eignen sich alle. Dies ist der letzte Verifizierungsschritt: Er erhöht dein tägliches Limit für Geld senden und Abheben von :from auf :to. Nur unser eigenes Team prüft es, normalerweise innerhalb eines Tages.",
    'document_type_label' => "Dokumenttyp",
    'select_document_type' => "Dokumenttyp auswählen",
    'document_label' => "Dokument",
    'document_hint' => "(JPG, PNG oder PDF, datiert innerhalb der letzten 3 Monate)",

    // Document type option labels (KycController/AddressVerificationController DOCUMENT_TYPES, and the models' documentTypeLabel())
    'doc_drivers_license' => "Führerschein",
    'doc_state_id' => "Staatlich ausgestellter Ausweis",
    'doc_passport' => "US-Reisepass",
    'doc_other_id' => "Anderer amtlicher Ausweis",
    'doc_utility_bill' => "Nebenkostenabrechnung",
    'doc_bank_statement' => "Kontoauszug",
    'doc_tenancy_agreement' => "Mietvertrag",
    'doc_other_address' => "Anderer Adressnachweis",

    // Controller flash messages
    'kyc_submitted_status' => "Danke — wir haben deinen Ausweis erhalten. Dein Konto ist einsatzbereit, während unser Team ihn prüft, normalerweise innerhalb eines Tages. Wir benachrichtigen dich, sobald eine Entscheidung getroffen wurde.",
    'address_submitted_status' => "Danke — wir haben dein Dokument erhalten. Wir benachrichtigen dich, sobald eine Entscheidung getroffen wurde, normalerweise innerhalb eines Tages.",
    'address_verify_identity_first' => "Verifiziere zuerst deine Identität — die Adressprüfung ist der nächste Schritt danach.",
];
