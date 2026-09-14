<?php

return [
    // Tiers (App\Support\AccountTier)
    'tier_1' => 'Ọkwa 1',
    'tier_2' => 'Ọkwa 2',
    'tier_3' => 'Ọkwa 3',
    'tier_1_desc' => 'Mmalite — gosi onye ị bụ iji welie ókè gị elu',
    'tier_2_desc' => 'Egosila onye ị bụ — gosi adreesị gị iji welie ya elu ọzọ',
    'tier_3_desc' => 'Egosila onye ị bụ kpamkpam',

    // Shared status labels (KycVerification / AddressVerification)
    'status_approved' => 'Ekwenyere',
    'status_rejected' => 'Ajụrụ',
    'status_pending_review' => 'Na-eche nyocha',

    // Settings hub rows
    'identity_verification' => 'Nyocha onye ị bụ',
    'address_verification' => 'Nyocha adreesị',
    'badge_verified' => 'Egosila',
    'badge_pending' => 'Na-eche',
    'badge_needs_resubmit' => 'Chọrọ itinyeghachi',
    'badge_not_started' => 'Amalitebeghị',

    // Shared buttons
    'back_to_dashboard' => 'Laghachi na dashboard',
    'back_to_settings' => 'Laghachi na Ntọala',
    'message_support' => 'Zigara enyemaka ozi',
    'submit_for_review' => 'Tinye maka nyocha',
    'verify_identity' => 'Gosi onye ị bụ',
    'verify_address' => 'Gosi adreesị gị',
    'last_submission_rejected' => "Ihe i tinyere ikpeazụ ekwenyeghị ya.",

    // Send Money gate (kyc-required.blade.php)
    'kyc_gate_pending_title' => 'Ziga Ego ga-emeghe ozugbo elele ID gị',
    'kyc_gate_pending_body' => "Anyị enwetala akwụkwọ ọzụzụ gị nke gọọmentị na foto gị. Ndị otu anyị na-elele ha ugbu a, ọ na-erikarị otu ụbọchị. Ihe ndị ọzọ niile na akaụntụ gị na-arụ ọrụ nkịtị n'oge a.",
    'kyc_gate_rejected_title' => 'Ziga Ego chọrọ nyocha onye ị bụ ọhụrụ',
    'kyc_gate_rejected_body' => "Ihe i tinyere ikpeazụ ekwenyeghị ya. Legharịa anya ọzọ ma tinyeghachi ya. Ọ na-ewe naanị otu nkeji.",
    'kyc_gate_not_started_title' => 'Gosi onye ị bụ iji zipu ego',
    'kyc_gate_not_started_body' => "Bulite akwụkwọ ọzụzụ nke gọọmentị na foto onwe gị dị mfe. Ọ bụ nzọụkwụ ikpeazụ imeghepụta akaụntụ gị kpamkpam. Ihe ndị ọzọ niile na-arụ ọrụ ugbua.",

    // Identity verification page (kyc-verify.blade.php)
    'kyc_approved_title' => "Egosila onye ị bụ",
    'kyc_approved_body' => 'A tụlere akwụkwọ ọzụzụ gọọmentị gị na foto gị wee kwenye ha. Ziga Ego emeghela kpamkpam.',
    'kyc_approved_body_dated' => 'A tụlere akwụkwọ ọzụzụ gọọmentị gị na foto gị wee kwenye ha na :date. Ziga Ego emeghela kpamkpam.',
    'kyc_pending_title' => 'A na-elele ID gị',
    'kyc_pending_body' => "I tinyere :type na foto :time. Ndị otu anyị na-elele ihe ndị a n'aka, ọ na-erikarị otu ụbọchị. Anyị ga-agwa gị ozugbo ekpebiri ya. Ihe ndị ọzọ niile na akaụntụ gị na-arụ ọrụ nkịtị n'oge a; ọ bụ naanị Ziga Ego ka a machibidoro ruo mgbe ahụ.",
    'kyc_resubmit_notice' => 'Biko gbalịa ọzọ n\'okpuru a jiri foto doro anya, nke a na-emeghị mgbanwe nke ID gị na selfie nwere ìhè dị mma.',
    'kyc_form_title' => 'Otu nzọụkwụ ikpeazụ',
    'kyc_form_body' => "Bulite foto nke akwụkwọ ọzụzụ gọọmentị kwadoro na selfie nke gị onwe gị. Nke a bụ otu anyị si akwado na ọ bụ gị n'ezie tupu emeghepụta akaụntụ gị kpamkpam. Ọ dịghị onye ọzọ ọ bụla hụrụ ihe ndị a mgbe ọ bụla, ọ bụ naanị ndị otu anyị na-ele ha anya n'aka. Ị nwere ike ịnọgide na-eji akaụntụ gị mee ihe nkịtị ka a na-elele ya; ọ bụ naanị Ziga Ego na-echere ruo mgbe ekwenyere ya.",
    'id_type_label' => 'Ụdị akwụkwọ ọzụzụ',
    'select_id_type' => 'Họrọ ụdị akwụkwọ ọzụzụ',
    'id_photo_label' => 'Foto nke akwụkwọ ọzụzụ gị (ihu)',
    'selfie_label' => 'Selfie nke gị onwe gị',
    'selfie_hint' => '(ìhè dị mma, ihu na-apụta ìhè)',

    // Address verification page (address-verify.blade.php)
    'address_approved_title' => "Egosila gị kpamkpam",
    'address_approved_body' => "A tụlere ihe akaebe adreesị gị wee kwenye ya. Ị nọzi na Ọkwa 3 — ókè gị kwa ụbọchị bụ :limit.",
    'address_approved_body_dated' => "A tụlere ihe akaebe adreesị gị wee kwenye ya na :date. Ị nọzi na Ọkwa 3 — ókè gị kwa ụbọchị bụ :limit.",
    'address_pending_title' => 'A na-elele akwụkwọ gị',
    'address_pending_body' => 'I tinyere :type :time. Ndị otu anyị na-elele ihe ndị a n\'aka, ọ na-erikarị otu ụbọchị. Ókè gị kwa ụbọchị nke ugbu a ga-anọgide na :limit ruo mgbe ahụ.',
    'address_resubmit_notice' => 'Biko gbalịa ọzọ n\'okpuru a jiri akwụkwọ ọhụrụ doro anya.',
    'address_form_title' => 'Welie ókè gị kwa ụbọchị elu',
    'address_form_body' => 'Bulite akwụkwọ ọhụrụ na-egosi aha gị na adreesị ụlọ gị — bịl ọrụ, akwụkwọ akụkọ ụlọ akụ, ma ọ bụ nkwekọrịta mgbazinye ụlọ niile dabara. Nke a bụ nzọụkwụ nyocha ikpeazụ: ọ na-ebuli ókè gị kwa ụbọchị nke Ziga Ego na Wepụta Ego site na :from gaa :to. Ọ bụ naanị ndị otu anyị na-ele ya anya, na-erikarị otu ụbọchị.',
    'document_type_label' => 'Ụdị akwụkwọ',
    'select_document_type' => 'Họrọ ụdị akwụkwọ',
    'document_label' => 'Akwụkwọ',
    'document_hint' => '(JPG, PNG, ma ọ bụ PDF, nke e nyere n\'ime ọnwa atọ gara aga)',

    // Document type option labels (KycController/AddressVerificationController DOCUMENT_TYPES, and the models' documentTypeLabel())
    'doc_drivers_license' => "Akwụkwọ ikike ịnya ụgbọ ala",
    'doc_state_id' => 'Kaadị njirimara gọọmentị',
    'doc_passport' => 'Paspọtụ US',
    'doc_other_id' => 'Njirimara ọzọ nke gọọmentị nyere',
    'doc_utility_bill' => 'Bịl ọrụ',
    'doc_bank_statement' => 'Akwụkwọ akụkọ ụlọ akụ',
    'doc_tenancy_agreement' => 'Nkwekọrịta mgbazinye ụlọ',
    'doc_other_address' => 'Ihe akaebe adreesị ọzọ',

    // Controller flash messages
    'kyc_submitted_status' => "Daalụ — anyị enwetala ID gị. Akaụntụ gị dị njikere iji mee ihe ka ndị otu anyị na-elele ya, ọ na-erikarị otu ụbọchị. Anyị ga-agwa gị ozugbo ekpebiri ya.",
    'address_submitted_status' => "Daalụ — anyị enwetala akwụkwọ gị. Anyị ga-agwa gị ozugbo ekpebiri ya, ọ na-erikarị otu ụbọchị.",
    'address_verify_identity_first' => 'Buru ụzọ gosi onye ị bụ — nyocha adreesị bụ nzọụkwụ na-esote ya.',
];
