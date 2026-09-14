<?php

return [
    // Tiers (App\Support\AccountTier)
    'tier_1' => 'Poziom 1',
    'tier_2' => 'Poziom 2',
    'tier_3' => 'Poziom 3',
    'tier_1_desc' => 'Początkujący — zweryfikuj swoją tożsamość, aby podnieść limit',
    'tier_2_desc' => 'Tożsamość zweryfikowana — zweryfikuj adres, aby podnieść go jeszcze bardziej',
    'tier_3_desc' => 'W pełni zweryfikowany',

    // Shared status labels (KycVerification / AddressVerification)
    'status_approved' => 'Zatwierdzone',
    'status_rejected' => 'Odrzucone',
    'status_pending_review' => 'Oczekuje na weryfikację',

    // Settings hub rows
    'identity_verification' => 'Weryfikacja tożsamości',
    'address_verification' => 'Weryfikacja adresu',
    'badge_verified' => 'Zweryfikowano',
    'badge_pending' => 'Oczekuje',
    'badge_needs_resubmit' => 'Wymaga ponownego przesłania',
    'badge_not_started' => 'Nie rozpoczęto',

    // Shared buttons
    'back_to_dashboard' => 'Wróć do pulpitu',
    'back_to_settings' => 'Wróć do Ustawień',
    'message_support' => 'Napisz do wsparcia',
    'submit_for_review' => 'Prześlij do weryfikacji',
    'verify_identity' => 'Zweryfikuj swoją tożsamość',
    'verify_address' => 'Zweryfikuj swój adres',
    'last_submission_rejected' => 'Twoje ostatnie zgłoszenie nie zostało zatwierdzone.',

    // Send Money gate (kyc-required.blade.php)
    'kyc_gate_pending_title' => 'Wyślij pieniądze odblokuje się, gdy Twój dokument tożsamości zostanie zweryfikowany',
    'kyc_gate_pending_body' => 'Otrzymaliśmy Twój dokument tożsamości i zdjęcie. Nasz zespół właśnie je sprawdza, zwykle w ciągu jednego dnia. W międzyczasie wszystko inne na Twoim koncie działa normalnie.',
    'kyc_gate_rejected_title' => 'Wyślij pieniądze wymaga nowej weryfikacji tożsamości',
    'kyc_gate_rejected_body' => 'Twoje ostatnie zgłoszenie nie zostało zatwierdzone. Sprawdź je jeszcze raz i prześlij ponownie. Zajmie to tylko minutę.',
    'kyc_gate_not_started_title' => 'Zweryfikuj swoją tożsamość, aby wysyłać pieniądze',
    'kyc_gate_not_started_body' => 'Prześlij dokument tożsamości i szybkie selfie. To ostatni krok do pełnego odblokowania konta. Wszystko inne już działa.',

    // Identity verification page (kyc-verify.blade.php)
    'kyc_approved_title' => 'Jesteś zweryfikowany',
    'kyc_approved_body' => 'Twój dokument tożsamości i zdjęcie zostały sprawdzone i zatwierdzone. Funkcja Wyślij pieniądze jest w pełni odblokowana.',
    'kyc_approved_body_dated' => 'Twój dokument tożsamości i zdjęcie zostały sprawdzone i zatwierdzone dnia :date. Funkcja Wyślij pieniądze jest w pełni odblokowana.',
    'kyc_pending_title' => 'Twój dokument tożsamości jest weryfikowany',
    'kyc_pending_body' => 'Przesłałeś :type oraz zdjęcie :time. Nasz zespół sprawdza je ręcznie, zwykle w ciągu jednego dnia. Powiadomimy Cię, gdy tylko zapadnie decyzja. W międzyczasie wszystko inne na Twoim koncie działa normalnie; zablokowana pozostaje jedynie funkcja Wyślij pieniądze.',
    'kyc_resubmit_notice' => 'Spróbuj ponownie poniżej, dodając wyraźne, nieedytowane zdjęcie dokumentu tożsamości oraz dobrze oświetlone selfie.',
    'kyc_form_title' => 'Ostatni krok',
    'kyc_form_body' => 'Prześlij zdjęcie ważnego dokumentu tożsamości wydanego przez organ państwowy oraz swoje selfie. W ten sposób potwierdzamy, że to naprawdę Ty, zanim w pełni odblokujemy Twoje konto. Nikt z zewnątrz nigdy tego nie widzi — sprawdza to ręcznie wyłącznie nasz zespół. W trakcie weryfikacji możesz normalnie korzystać z konta; jedynie funkcja Wyślij pieniądze czeka na zatwierdzenie.',
    'id_type_label' => 'Rodzaj dokumentu tożsamości',
    'select_id_type' => 'Wybierz rodzaj dokumentu tożsamości',
    'id_photo_label' => 'Zdjęcie Twojego dokumentu tożsamości (przód)',
    'selfie_label' => 'Twoje selfie',
    'selfie_hint' => '(dobrze oświetlone, twarz wyraźnie widoczna)',

    // Address verification page (address-verify.blade.php)
    'address_approved_title' => 'Jesteś w pełni zweryfikowany',
    'address_approved_body' => 'Twoje potwierdzenie adresu zostało sprawdzone i zatwierdzone. Jesteś teraz na Poziomie 3 — Twój dzienny limit wynosi :limit.',
    'address_approved_body_dated' => 'Twoje potwierdzenie adresu zostało sprawdzone i zatwierdzone dnia :date. Jesteś teraz na Poziomie 3 — Twój dzienny limit wynosi :limit.',
    'address_pending_title' => 'Twój dokument jest weryfikowany',
    'address_pending_body' => 'Przesłałeś :type :time. Nasz zespół sprawdza je ręcznie, zwykle w ciągu jednego dnia. Twój obecny dzienny limit pozostaje na poziomie :limit do tego czasu.',
    'address_resubmit_notice' => 'Spróbuj ponownie poniżej, dodając wyraźny, aktualny dokument.',
    'address_form_title' => 'Podnieś swój dzienny limit',
    'address_form_body' => 'Prześlij aktualny dokument, który pokazuje Twoje imię i nazwisko oraz adres domowy — rachunek za media, wyciąg bankowy lub umowa najmu — wszystkie te opcje są odpowiednie. To ostatni krok weryfikacji: podnosi on Twój dzienny limit Wyślij pieniądze i Wypłać z :from do :to. Sprawdza to wyłącznie nasz zespół, zwykle w ciągu jednego dnia.',
    'document_type_label' => 'Rodzaj dokumentu',
    'select_document_type' => 'Wybierz rodzaj dokumentu',
    'document_label' => 'Dokument',
    'document_hint' => '(JPG, PNG lub PDF, z datą z ostatnich 3 miesięcy)',

    // Document type option labels (KycController/AddressVerificationController DOCUMENT_TYPES, and the models' documentTypeLabel())
    'doc_drivers_license' => 'Prawo jazdy',
    'doc_state_id' => 'Państwowy dowód osobisty',
    'doc_passport' => 'Paszport USA',
    'doc_other_id' => 'Inny dokument tożsamości wydany przez organ państwowy',
    'doc_utility_bill' => 'Rachunek za media',
    'doc_bank_statement' => 'Wyciąg bankowy',
    'doc_tenancy_agreement' => 'Umowa najmu',
    'doc_other_address' => 'Inne potwierdzenie adresu',

    // Controller flash messages
    'kyc_submitted_status' => 'Dziękujemy — otrzymaliśmy Twój dokument tożsamości. Twoje konto jest gotowe do użycia, podczas gdy nasz zespół je sprawdza, zwykle w ciągu jednego dnia. Powiadomimy Cię, gdy tylko zapadnie decyzja.',
    'address_submitted_status' => 'Dziękujemy — otrzymaliśmy Twój dokument. Powiadomimy Cię, gdy tylko zapadnie decyzja, zwykle w ciągu jednego dnia.',
    'address_verify_identity_first' => 'Najpierw zweryfikuj swoją tożsamość — weryfikacja adresu jest kolejnym krokiem po tym.',
];
