<?php

return [
    // Tiers (App\Support\AccountTier)
    'tier_1' => 'Kiwango 1',
    'tier_2' => 'Kiwango 2',
    'tier_3' => 'Kiwango 3',
    'tier_1_desc' => 'Mwanzo — thibitisha utambulisho wako ili kuongeza kikomo chako',
    'tier_2_desc' => 'Utambulisho umethibitishwa — thibitisha anwani yako ili kukiongeza zaidi',
    'tier_3_desc' => 'Umethibitishwa kikamilifu',

    // Shared status labels (KycVerification / AddressVerification)
    'status_approved' => 'Imeidhinishwa',
    'status_rejected' => 'Imekataliwa',
    'status_pending_review' => 'Inasubiri ukaguzi',

    // Settings hub rows
    'identity_verification' => 'Uthibitishaji wa utambulisho',
    'address_verification' => 'Uthibitishaji wa anwani',
    'badge_verified' => 'Imethibitishwa',
    'badge_pending' => 'Inasubiri',
    'badge_needs_resubmit' => 'Inahitaji kuwasilishwa upya',
    'badge_not_started' => 'Haijaanza',

    // Shared buttons
    'back_to_dashboard' => 'Rudi kwenye dashibodi',
    'back_to_settings' => 'Rudi kwenye Mipangilio',
    'message_support' => 'Tuma ujumbe kwa msaada',
    'submit_for_review' => 'Wasilisha kwa ukaguzi',
    'verify_identity' => 'Thibitisha utambulisho wako',
    'verify_address' => 'Thibitisha anwani yako',
    'last_submission_rejected' => "Uwasilishaji wako wa mwisho haukuidhinishwa.",

    // Send Money gate (kyc-required.blade.php)
    'kyc_gate_pending_title' => 'Tuma Fedha itafunguliwa mara kitambulisho chako kitakapokaguliwa',
    'kyc_gate_pending_body' => "Tumepokea kitambulisho chako cha serikali na picha. Timu yetu inavikagua sasa, kwa kawaida ndani ya siku moja. Kila kitu kingine kwenye akaunti yako kinafanya kazi kama kawaida wakati huu.",
    'kyc_gate_rejected_title' => 'Tuma Fedha inahitaji uthibitisho mpya wa kitambulisho',
    'kyc_gate_rejected_body' => "Uwasilishaji wako wa mwisho haukuidhinishwa. Angalia tena na uwasilishe upya. Inachukua dakika moja tu.",
    'kyc_gate_not_started_title' => 'Thibitisha utambulisho wako ili kutuma fedha',
    'kyc_gate_not_started_body' => "Pakia kitambulisho cha serikali na selfie ya haraka. Ni hatua ya mwisho kufungua akaunti yako kikamilifu. Kila kitu kingine tayari kinafanya kazi.",

    // Identity verification page (kyc-verify.blade.php)
    'kyc_approved_title' => "Umethibitishwa",
    'kyc_approved_body' => 'Kitambulisho chako cha serikali na picha vilikaguliwa na kuidhinishwa. Tuma Fedha imefunguliwa kikamilifu.',
    'kyc_approved_body_dated' => 'Kitambulisho chako cha serikali na picha vilikaguliwa na kuidhinishwa tarehe :date. Tuma Fedha imefunguliwa kikamilifu.',
    'kyc_pending_title' => 'Kitambulisho chako kiko chini ya ukaguzi',
    'kyc_pending_body' => "Uliwasilisha :type na picha :time. Timu yetu inakagua haya kwa mkono, kwa kawaida ndani ya siku moja. Tutakujulisha mara uamuzi utakapofanywa. Kila kitu kingine kwenye akaunti yako kinafanya kazi kama kawaida wakati huu; Tuma Fedha pekee inabaki imefungwa hadi wakati huo.",
    'kyc_resubmit_notice' => 'Tafadhali jaribu tena hapa chini na picha wazi, isiyohaririwa ya kitambulisho chako na selfie yenye mwanga mzuri.',
    'kyc_form_title' => 'Hatua moja ya mwisho',
    'kyc_form_body' => "Pakia picha ya kitambulisho halali cha serikali na selfie yako mwenyewe. Hivi ndivyo tunavyothibitisha ni wewe kweli kabla ya kufungua akaunti yako kikamilifu. Hakuna mtu wa tatu anayeona haya, ni timu yetu tu inayoyakagua kwa mkono. Unaweza kuendelea kutumia akaunti yako kama kawaida wakati inakaguliwa; Tuma Fedha pekee inasubiri hadi iidhinishwe.",
    'id_type_label' => 'Aina ya kitambulisho',
    'select_id_type' => 'Chagua aina ya kitambulisho',
    'id_photo_label' => 'Picha ya kitambulisho chako (mbele)',
    'selfie_label' => 'Selfie yako',
    'selfie_hint' => '(yenye mwanga mzuri, uso ukionekana wazi)',

    // Address verification page (address-verify.blade.php)
    'address_approved_title' => "Umethibitishwa kikamilifu",
    'address_approved_body' => "Uthibitisho wako wa anwani ulikaguliwa na kuidhinishwa. Sasa uko Kiwango 3 — kikomo chako cha kila siku ni :limit.",
    'address_approved_body_dated' => "Uthibitisho wako wa anwani ulikaguliwa na kuidhinishwa tarehe :date. Sasa uko Kiwango 3 — kikomo chako cha kila siku ni :limit.",
    'address_pending_title' => 'Hati yako iko chini ya ukaguzi',
    'address_pending_body' => 'Uliwasilisha :type :time. Timu yetu inakagua haya kwa mkono, kwa kawaida ndani ya siku moja. Kikomo chako cha sasa cha kila siku kinabaki :limit hadi wakati huo.',
    'address_resubmit_notice' => 'Tafadhali jaribu tena hapa chini na hati wazi na mpya.',
    'address_form_title' => 'Ongeza kikomo chako cha kila siku',
    'address_form_body' => 'Pakia hati mpya inayoonyesha jina lako na anwani ya nyumbani — bili ya huduma, taarifa ya benki, au mkataba wa kupanga vyote vinafaa. Hii ni hatua ya mwisho ya uthibitishaji: inaongeza kikomo chako cha kila siku cha Tuma Fedha na Toa kutoka :from hadi :to. Timu yetu tu ndiyo inayokagua, kwa kawaida ndani ya siku moja.',
    'document_type_label' => 'Aina ya hati',
    'select_document_type' => 'Chagua aina ya hati',
    'document_label' => 'Hati',
    'document_hint' => '(JPG, PNG, au PDF, iliyotolewa ndani ya miezi 3 iliyopita)',

    // Document type option labels (KycController/AddressVerificationController DOCUMENT_TYPES, and the models' documentTypeLabel())
    'doc_drivers_license' => "Leseni ya udereva",
    'doc_state_id' => 'Kitambulisho cha serikali',
    'doc_passport' => 'Pasipoti ya Marekani',
    'doc_other_id' => 'Kitambulisho kingine cha serikali',
    'doc_utility_bill' => 'Bili ya huduma',
    'doc_bank_statement' => 'Taarifa ya benki',
    'doc_tenancy_agreement' => 'Mkataba wa kupanga',
    'doc_other_address' => 'Uthibitisho mwingine wa anwani',

    // Controller flash messages
    'kyc_submitted_status' => "Asante — tumepokea kitambulisho chako. Akaunti yako iko tayari kutumika wakati timu yetu inaikagua, kwa kawaida ndani ya siku moja. Tutakujulisha mara uamuzi utakapofanywa.",
    'address_submitted_status' => "Asante — tumepokea hati yako. Tutakujulisha mara uamuzi utakapofanywa, kwa kawaida ndani ya siku moja.",
    'address_verify_identity_first' => 'Thibitisha utambulisho wako kwanza — uthibitishaji wa anwani ni hatua inayofuata baada ya hapo.',
];
