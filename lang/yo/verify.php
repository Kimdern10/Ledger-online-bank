<?php

return [
    // Tiers (App\Support\AccountTier)
    'tier_1' => 'Ìpele 1',
    'tier_2' => 'Ìpele 2',
    'tier_3' => 'Ìpele 3',
    'tier_1_desc' => 'Ìbẹ̀rẹ̀ — fi ìdánimọ̀ rẹ hàn láti gbé ààlà rẹ ga',
    'tier_2_desc' => 'A ti fi ìdánimọ̀ hàn — fi àdírẹ́sì rẹ hàn láti gbé e ga sí i',
    'tier_3_desc' => 'A ti fi ìdánimọ̀ hàn ní kíkún',

    // Shared status labels (KycVerification / AddressVerification)
    'status_approved' => 'A ti fọwọ́sí',
    'status_rejected' => 'A ti kọ̀',
    'status_pending_review' => 'Ó ń dúró de àyẹ̀wò',

    // Settings hub rows
    'identity_verification' => 'Ìfihàn ìdánimọ̀',
    'address_verification' => 'Ìfihàn àdírẹ́sì',
    'badge_verified' => 'A ti fi hàn',
    'badge_pending' => 'Ó ń dúró',
    'badge_needs_resubmit' => 'Ó nílò àtúnfiránṣẹ́',
    'badge_not_started' => 'Kò tíì bẹ̀rẹ̀',

    // Shared buttons
    'back_to_dashboard' => 'Padà sí dashboard',
    'back_to_settings' => 'Padà sí Ètò',
    'message_support' => 'Fi ìránṣẹ́ ránṣẹ́ sí ìrànlọ́wọ́',
    'submit_for_review' => 'Fi ránṣẹ́ fún àyẹ̀wò',
    'verify_identity' => 'Fi ìdánimọ̀ rẹ hàn',
    'verify_address' => 'Fi àdírẹ́sì rẹ hàn',
    'last_submission_rejected' => "A kò fọwọ́sí ohun tí o fi ránṣẹ́ kẹ́yìn.",

    // Send Money gate (kyc-required.blade.php)
    'kyc_gate_pending_title' => 'Fífi Owó Ránṣẹ́ yóò ṣí sílẹ̀ ní kété tí a bá ti ṣàyẹ̀wò ìwé ìdánimọ̀ rẹ',
    'kyc_gate_pending_body' => "A ti gba ìwé ìdánimọ̀ ìjọba àti fọ́tò rẹ. Ẹgbẹ́ wa ń ṣàyẹ̀wò wọn nísinsìnyí, ó sábà máa ń gbà ọjọ́ kan péré. Ohun gbogbo yòókù lórí àkọọ́lẹ̀ rẹ ń ṣiṣẹ́ ní ìwà déédéé nígbà tí èyí bá ń ṣẹlẹ̀.",
    'kyc_gate_rejected_title' => 'Fífi Owó Ránṣẹ́ nílò ìfihàn ìdánimọ̀ tuntun',
    'kyc_gate_rejected_body' => "A kò fọwọ́sí ohun tí o fi ránṣẹ́ kẹ́yìn. Wo o padà, kí o sì tún fi ránṣẹ́. Ìṣẹ́jú kan péré ni yóò gbà.",
    'kyc_gate_not_started_title' => 'Fi ìdánimọ̀ rẹ hàn láti fi owó ránṣẹ́',
    'kyc_gate_not_started_body' => "Gbé ìwé ìdánimọ̀ ìjọba àti sẹ́lifì kíákíá kan sórí ẹ̀rọ. Ó jẹ́ ìṣísẹ̀ tí ó kẹ́yìn láti ṣí àkọọ́lẹ̀ rẹ sílẹ̀ ní kíkún. Ohun gbogbo yòókù ti ń ṣiṣẹ́ tẹ́lẹ̀.",

    // Identity verification page (kyc-verify.blade.php)
    'kyc_approved_title' => "A ti fi ìdánimọ̀ rẹ hàn",
    'kyc_approved_body' => 'A ti ṣàyẹ̀wò ìwé ìdánimọ̀ ìjọba àti fọ́tò rẹ, a sì ti fọwọ́sí wọn. Fífi Owó Ránṣẹ́ ti ṣí sílẹ̀ ní kíkún.',
    'kyc_approved_body_dated' => 'A ti ṣàyẹ̀wò ìwé ìdánimọ̀ ìjọba àti fọ́tò rẹ, a sì ti fọwọ́sí wọn ní ọjọ́ :date. Fífi Owó Ránṣẹ́ ti ṣí sílẹ̀ ní kíkún.',
    'kyc_pending_title' => 'A ń ṣàyẹ̀wò ìwé ìdánimọ̀ rẹ',
    'kyc_pending_body' => "O ti fi :type àti fọ́tò kan ránṣẹ́ :time. Ẹgbẹ́ wa ń ṣàyẹ̀wò àwọn wọ̀nyí pẹ̀lú ọwọ́, ó sábà máa ń gbà ọjọ́ kan péré. A óò fún ọ ní ìtàn ní kété tí a bá ṣe ìpinnu. Ohun gbogbo yòókù lórí àkọọ́lẹ̀ rẹ ń ṣiṣẹ́ ní ìwà déédéé nígbà tí èyí bá ń ṣẹlẹ̀; Fífi Owó Ránṣẹ́ nìkan ni yóò dí títí di ìgbà náà.",
    'kyc_resubmit_notice' => 'Jọ̀wọ́ tún gbìyànjú ní ìsàlẹ̀ pẹ̀lú fọ́tò tí ó ṣe kedere, tí a kò ṣàtúnṣe, ti ìwé ìdánimọ̀ rẹ àti sẹ́lifì tí ìmọ́lẹ̀ tó dáadáa wà.',
    'kyc_form_title' => 'Ìṣísẹ̀ tí ó kẹ́yìn',
    'kyc_form_body' => "Gbé fọ́tò ìwé ìdánimọ̀ ìjọba tí ó bá ẹ̀tọ́ mu àti sẹ́lifì ara rẹ sórí ẹ̀rọ. Bẹ́ẹ̀ ni a ṣe ń rí i dájú pé ìwọ gan-an ni kí a tó ṣí àkọọ́lẹ̀ rẹ sílẹ̀ ní kíkún. Kò sí ẹnìkẹ́ta kankan tí yóò rí àwọn èyí rí, ẹgbẹ́ tiwa nìkan ni ó ń ṣàyẹ̀wò wọn pẹ̀lú ọwọ́. O lè máa lo àkọọ́lẹ̀ rẹ déédéé nígbà tí a bá ń ṣàyẹ̀wò rẹ̀; Fífi Owó Ránṣẹ́ nìkan ni yóò dúró de ìgbà tí a óò fi fọwọ́sí i.",
    'id_type_label' => 'Irú ìwé ìdánimọ̀',
    'select_id_type' => 'Yan irú ìwé ìdánimọ̀',
    'id_photo_label' => 'Fọ́tò ìwé ìdánimọ̀ rẹ (iwájú)',
    'selfie_label' => 'Sẹ́lifì tirẹ',
    'selfie_hint' => '(ìmọ́lẹ̀ tó dáadáa, ojú tí ó hàn kedere)',

    // Address verification page (address-verify.blade.php)
    'address_approved_title' => "A ti fi ìdánimọ̀ rẹ hàn ní kíkún",
    'address_approved_body' => "A ti ṣàyẹ̀wò ẹ̀rí àdírẹ́sì rẹ, a sì ti fọwọ́sí i. O wà ní Ìpele 3 nísinsìnyí — ààlà rẹ ojoojúmọ́ ni :limit.",
    'address_approved_body_dated' => "A ti ṣàyẹ̀wò ẹ̀rí àdírẹ́sì rẹ, a sì ti fọwọ́sí i ní ọjọ́ :date. O wà ní Ìpele 3 nísinsìnyí — ààlà rẹ ojoojúmọ́ ni :limit.",
    'address_pending_title' => 'A ń ṣàyẹ̀wò ìwé rẹ',
    'address_pending_body' => 'O ti fi :type ránṣẹ́ :time. Ẹgbẹ́ wa ń ṣàyẹ̀wò àwọn wọ̀nyí pẹ̀lú ọwọ́, ó sábà máa ń gbà ọjọ́ kan péré. Ààlà rẹ ojoojúmọ́ tí ó wà lọ́wọ́lọ́wọ́ yóò dúró ní :limit títí di ìgbà náà.',
    'address_resubmit_notice' => 'Jọ̀wọ́ tún gbìyànjú ní ìsàlẹ̀ pẹ̀lú ìwé tuntun tí ó ṣe kedere.',
    'address_form_title' => 'Gbé ààlà rẹ ojoojúmọ́ ga',
    'address_form_body' => 'Gbé ìwé tuntun kan tí ó fi orúkọ rẹ àti àdírẹ́sì ilé rẹ hàn sórí ẹ̀rọ — bíl iná mọ̀nàmọ́ná, ìwé àkọsílẹ̀ báńkì, tàbí àdéhùn ìyáwó ilé gbogbo wọn dára. Èyí ni ìṣísẹ̀ ìfihàn tí ó kẹ́yìn: ó ń gbé ààlà rẹ ojoojúmọ́ fún Fífi Owó Ránṣẹ́ àti Yíyọ Owó láti :from sí :to. Ẹgbẹ́ tiwa nìkan ni ó ń ṣàyẹ̀wò rẹ̀, ó sábà máa ń gbà ọjọ́ kan péré.',
    'document_type_label' => 'Irú ìwé',
    'select_document_type' => 'Yan irú ìwé',
    'document_label' => 'Ìwé',
    'document_hint' => '(JPG, PNG, tàbí PDF, tí a fi ọjọ́ sí láàrin oṣù mẹ́ta tí ó kọjá)',

    // Document type option labels (KycController/AddressVerificationController DOCUMENT_TYPES, and the models' documentTypeLabel())
    'doc_drivers_license' => "Ìwé àṣẹ awakọ̀",
    'doc_state_id' => 'Káàdì ìdánimọ̀ ìjọba ìpínlẹ̀',
    'doc_passport' => 'Ìwé àṣẹ ìrìnàjò US',
    'doc_other_id' => 'Ìwé ìdánimọ̀ ìjọba mìíràn',
    'doc_utility_bill' => 'Bíl iná mọ̀nàmọ́ná',
    'doc_bank_statement' => 'Ìwé àkọsílẹ̀ báńkì',
    'doc_tenancy_agreement' => 'Àdéhùn ìyáwó ilé',
    'doc_other_address' => 'Ẹ̀rí àdírẹ́sì mìíràn',

    // Controller flash messages
    'kyc_submitted_status' => "Ẹ ṣé — a ti gba ìwé ìdánimọ̀ rẹ. Àkọọ́lẹ̀ rẹ ti ṣetán láti lò nígbà tí ẹgbẹ́ wa bá ń ṣàyẹ̀wò rẹ̀, ó sábà máa ń gbà ọjọ́ kan péré. A óò fún ọ ní ìtàn ní kété tí a bá ṣe ìpinnu.",
    'address_submitted_status' => "Ẹ ṣé — a ti gba ìwé rẹ. A óò fún ọ ní ìtàn ní kété tí a bá ṣe ìpinnu, ó sábà máa ń gbà ọjọ́ kan péré.",
    'address_verify_identity_first' => 'Kọ́kọ́ fi ìdánimọ̀ rẹ hàn — ìfihàn àdírẹ́sì ni ìṣísẹ̀ tí ó tẹ̀lé e.',
];
