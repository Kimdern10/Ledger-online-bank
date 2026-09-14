<?php

return [
    // Tiers (App\Support\AccountTier)
    'tier_1' => 'Mataki na 1',
    'tier_2' => 'Mataki na 2',
    'tier_3' => 'Mataki na 3',
    'tier_1_desc' => 'Farawa — tabbatar da ainihinka don ƙara iyakarka',
    'tier_2_desc' => 'An tabbatar da ainihi — tabbatar da adireshinka don ƙara ta ƙarin',
    'tier_3_desc' => 'An tabbatar sosai',

    // Shared status labels (KycVerification / AddressVerification)
    'status_approved' => 'An amince',
    'status_rejected' => 'An ƙi',
    'status_pending_review' => 'Ana jiran dubawa',

    // Settings hub rows
    'identity_verification' => 'Tabbatar da ainihi',
    'address_verification' => 'Tabbatar da adireshi',
    'badge_verified' => 'An tabbatar',
    'badge_pending' => 'Ana jira',
    'badge_needs_resubmit' => 'Ana buƙatar sake miƙawa',
    'badge_not_started' => 'Ba a fara ba',

    // Shared buttons
    'back_to_dashboard' => 'Koma zuwa dashboard',
    'back_to_settings' => 'Koma zuwa Saituna',
    'message_support' => 'Aika saƙo ga tallafi',
    'submit_for_review' => 'Miƙa don dubawa',
    'verify_identity' => 'Tabbatar da ainihinka',
    'verify_address' => 'Tabbatar da adireshinka',
    'last_submission_rejected' => "Ba a amince da abin da ka miƙa na ƙarshe ba.",

    // Send Money gate (kyc-required.blade.php)
    'kyc_gate_pending_title' => 'Aika Kuɗi zai buɗe da zarar an duba shaidarka',
    'kyc_gate_pending_body' => "Mun karɓi katin shaidarka na gwamnati da hoto. Ƙungiyarmu tana duba su yanzu, yawanci a cikin kwana ɗaya. Sauran abubuwa a asusunka suna aiki yadda aka saba a wannan lokacin.",
    'kyc_gate_rejected_title' => 'Aika Kuɗi na buƙatar sabon tabbaci na shaida',
    'kyc_gate_rejected_body' => "Ba a amince da abin da ka miƙa na ƙarshe ba. Sake dubawa sannan ka sake miƙawa. Yana ɗaukar minti ɗaya kawai.",
    'kyc_gate_not_started_title' => 'Tabbatar da ainihinka don aika kuɗi',
    'kyc_gate_not_started_body' => "Loda katin shaida na gwamnati da saurin selfie. Wannan shine matakin ƙarshe na buɗe asusunka gaba ɗaya. Sauran duk abubuwa suna aiki tuni.",

    // Identity verification page (kyc-verify.blade.php)
    'kyc_approved_title' => "An tabbatar da kai",
    'kyc_approved_body' => 'An duba katin shaidarka na gwamnati da hoto kuma an amince da su. Aika Kuɗi ya buɗe gaba ɗaya.',
    'kyc_approved_body_dated' => 'An duba katin shaidarka na gwamnati da hoto kuma an amince da su a ranar :date. Aika Kuɗi ya buɗe gaba ɗaya.',
    'kyc_pending_title' => 'Ana bin diddigin shaidarka',
    'kyc_pending_body' => "Ka miƙa :type da hoto :time. Ƙungiyarmu tana duba waɗannan da hannu, yawanci a cikin kwana ɗaya. Za mu sanar da kai da zarar an yanke shawara. Sauran abubuwa a asusunka suna aiki yadda aka saba a wannan lokacin; Aika Kuɗi kaɗai ke rufe har sai lokacin.",
    'kyc_resubmit_notice' => 'Da fatan za ka sake gwadawa a ƙasa da hoto mai fili, wanda ba a gyara ba na katin shaidarka da selfie mai kyakkyawan haske.',
    'kyc_form_title' => 'Matakin ƙarshe',
    'kyc_form_body' => "Loda hoton katin shaida na gwamnati mai inganci da selfie na kanka. Ta haka ne muke tabbatar da kai ne da gaske kafin a buɗe asusunka gaba ɗaya. Babu wani ɓangare na uku da zai taɓa ganin waɗannan, ƙungiyarmu kaɗai ce ke duba su da hannu. Za ka iya ci gaba da amfani da asusunka yadda aka saba yayin da ake dubawa; Aika Kuɗi kaɗai zai jira har sai an amince da shi.",
    'id_type_label' => 'Nau\'in shaida',
    'select_id_type' => 'Zaɓi nau\'in shaida',
    'id_photo_label' => 'Hoton shaidarka (gaba)',
    'selfie_label' => 'Selfie naka',
    'selfie_hint' => '(haske mai kyau, fuska a bayyane)',

    // Address verification page (address-verify.blade.php)
    'address_approved_title' => "An tabbatar da kai gaba ɗaya",
    'address_approved_body' => "An duba hujjar adireshinka kuma an amince da ita. Yanzu kana Mataki na 3 — iyakarka ta yau da kullum ita ce :limit.",
    'address_approved_body_dated' => "An duba hujjar adireshinka kuma an amince da ita a ranar :date. Yanzu kana Mataki na 3 — iyakarka ta yau da kullum ita ce :limit.",
    'address_pending_title' => 'Ana bin diddigin takardarka',
    'address_pending_body' => 'Ka miƙa :type :time. Ƙungiyarmu tana duba waɗannan da hannu, yawanci a cikin kwana ɗaya. Iyakarka ta yanzu ta yau da kullum ta tsaya a :limit har sai lokacin.',
    'address_resubmit_notice' => 'Da fatan za ka sake gwadawa a ƙasa da sabuwar takarda mai fili.',
    'address_form_title' => 'Ƙara iyakarka ta yau da kullum',
    'address_form_body' => 'Loda sabuwar takarda da ke nuna sunanka da adireshin gidanka — takardar kuɗin ruwa/wutar lantarki, bayanin banki, ko yarjejeniyar haya duk sun dace. Wannan shine matakin ƙarshe na tabbaci: yana ƙara iyakarka ta yau da kullum ta Aika Kuɗi da Cire Kuɗi daga :from zuwa :to. Ƙungiyarmu kaɗai ce ke dubawa, yawanci a cikin kwana ɗaya.',
    'document_type_label' => 'Nau\'in takarda',
    'select_document_type' => 'Zaɓi nau\'in takarda',
    'document_label' => 'Takarda',
    'document_hint' => '(JPG, PNG, ko PDF, wanda aka yi kwanan wata cikin watanni 3 da suka gabata)',

    // Document type option labels (KycController/AddressVerificationController DOCUMENT_TYPES, and the models' documentTypeLabel())
    'doc_drivers_license' => "Lasisin tuƙi",
    'doc_state_id' => 'Katin shaida na gwamnati',
    'doc_passport' => 'Fasfo na Amurka',
    'doc_other_id' => 'Wata shaidar gwamnati',
    'doc_utility_bill' => 'Takardar kuɗin ruwa/wutar lantarki',
    'doc_bank_statement' => 'Bayanin banki',
    'doc_tenancy_agreement' => 'Yarjejeniyar haya',
    'doc_other_address' => 'Wata hujjar adireshi',

    // Controller flash messages
    'kyc_submitted_status' => "Na gode — mun karɓi shaidarka. Asusunka a shirye yake ake amfani da shi yayin da ƙungiyarmu ke dubawa, yawanci a cikin kwana ɗaya. Za mu sanar da kai da zarar an yanke shawara.",
    'address_submitted_status' => "Na gode — mun karɓi takardarka. Za mu sanar da kai da zarar an yanke shawara, yawanci a cikin kwana ɗaya.",
    'address_verify_identity_first' => 'Da farko ka tabbatar da ainihinka — tabbatar da adireshi shine mataki na gaba bayan haka.',
];
