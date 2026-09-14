<?php

return [
    // Tiers (App\Support\AccountTier)
    'tier_1' => 'टियर 1',
    'tier_2' => 'टियर 2',
    'tier_3' => 'टियर 3',
    'tier_1_desc' => 'स्टार्टर — अपनी सीमा बढ़ाने के लिए अपनी पहचान सत्यापित करें',
    'tier_2_desc' => 'पहचान सत्यापित — इसे और बढ़ाने के लिए अपना पता सत्यापित करें',
    'tier_3_desc' => 'पूर्ण रूप से सत्यापित',

    // Shared status labels (KycVerification / AddressVerification)
    'status_approved' => 'स्वीकृत',
    'status_rejected' => 'अस्वीकृत',
    'status_pending_review' => 'समीक्षा लंबित',

    // Settings hub rows
    'identity_verification' => 'पहचान सत्यापन',
    'address_verification' => 'पता सत्यापन',
    'badge_verified' => 'सत्यापित',
    'badge_pending' => 'लंबित',
    'badge_needs_resubmit' => 'पुनः सबमिट आवश्यक',
    'badge_not_started' => 'शुरू नहीं हुआ',

    // Shared buttons
    'back_to_dashboard' => 'डैशबोर्ड पर वापस जाएं',
    'back_to_settings' => 'सेटिंग्स पर वापस जाएं',
    'message_support' => 'सहायता को संदेश भेजें',
    'submit_for_review' => 'समीक्षा के लिए सबमिट करें',
    'verify_identity' => 'अपनी पहचान सत्यापित करें',
    'verify_address' => 'अपना पता सत्यापित करें',
    'last_submission_rejected' => "आपका पिछला सबमिशन स्वीकृत नहीं हुआ था।",

    // Send Money gate (kyc-required.blade.php)
    'kyc_gate_pending_title' => 'आपकी आईडी की समीक्षा होते ही Send Money अनलॉक हो जाएगा',
    'kyc_gate_pending_body' => "हमें आपका सरकारी पहचान पत्र और फोटो मिल गया है। हमारी टीम अभी इनकी समीक्षा कर रही है, आमतौर पर एक दिन के भीतर। इस बीच आपके खाते की बाकी सभी सुविधाएं सामान्य रूप से काम करती हैं।",
    'kyc_gate_rejected_title' => 'Send Money के लिए एक नए पहचान सत्यापन की आवश्यकता है',
    'kyc_gate_rejected_body' => "आपका पिछला सबमिशन स्वीकृत नहीं हुआ था। एक बार फिर से देखें और पुनः सबमिट करें। इसमें केवल एक मिनट लगेगा।",
    'kyc_gate_not_started_title' => 'पैसे भेजने के लिए अपनी पहचान सत्यापित करें',
    'kyc_gate_not_started_body' => "एक सरकारी पहचान पत्र और एक त्वरित सेल्फी अपलोड करें। यह आपके खाते को पूरी तरह से अनलॉक करने का अंतिम चरण है। बाकी सब कुछ पहले से ही काम करता है।",

    // Identity verification page (kyc-verify.blade.php)
    'kyc_approved_title' => "आप सत्यापित हैं",
    'kyc_approved_body' => 'आपके सरकारी पहचान पत्र और फोटो की समीक्षा की गई और उन्हें स्वीकृत किया गया। Send Money पूरी तरह से अनलॉक हो गया है।',
    'kyc_approved_body_dated' => 'आपके सरकारी पहचान पत्र और फोटो की समीक्षा की गई और उन्हें :date को स्वीकृत किया गया। Send Money पूरी तरह से अनलॉक हो गया है।',
    'kyc_pending_title' => 'आपकी आईडी की समीक्षा हो रही है',
    'kyc_pending_body' => "आपने :time एक :type और एक फोटो सबमिट की। हमारी टीम इन्हें हाथ से समीक्षा करती है, आमतौर पर एक दिन के भीतर। निर्णय होते ही हम आपको सूचित करेंगे। इस बीच आपके खाते की बाकी सभी सुविधाएं सामान्य रूप से काम करती हैं; केवल Send Money तब तक लॉक रहता है।",
    'kyc_resubmit_notice' => 'कृपया नीचे अपनी आईडी की एक स्पष्ट, अनएडिटेड फोटो और अच्छी रोशनी वाली सेल्फी के साथ फिर से प्रयास करें।',
    'kyc_form_title' => 'एक आखिरी कदम',
    'kyc_form_body' => "एक वैध सरकारी पहचान पत्र की फोटो और अपनी एक सेल्फी अपलोड करें। यह हमारे लिए यह पुष्टि करने का तरीका है कि यह वाकई आप हैं, इससे पहले कि हम आपके खाते को पूरी तरह से अनलॉक करें। इन्हें कोई तीसरा पक्ष कभी नहीं देखता, केवल हमारी अपनी टीम इन्हें हाथ से समीक्षा करती है। समीक्षा के दौरान आप अपने खाते का सामान्य रूप से उपयोग जारी रख सकते हैं; केवल Send Money स्वीकृत होने तक प्रतीक्षा करता है।",
    'id_type_label' => 'आईडी प्रकार',
    'select_id_type' => 'आईडी प्रकार चुनें',
    'id_photo_label' => 'आपकी आईडी की फोटो (सामने की ओर)',
    'selfie_label' => 'आपकी एक सेल्फी',
    'selfie_hint' => '(अच्छी रोशनी में, चेहरा स्पष्ट रूप से दिखाई देना चाहिए)',

    // Address verification page (address-verify.blade.php)
    'address_approved_title' => "आप पूरी तरह से सत्यापित हैं",
    'address_approved_body' => "आपके पते के प्रमाण की समीक्षा की गई और उसे स्वीकृत किया गया। अब आप टियर 3 पर हैं — आपकी दैनिक सीमा :limit है।",
    'address_approved_body_dated' => "आपके पते के प्रमाण की समीक्षा की गई और उसे :date को स्वीकृत किया गया। अब आप टियर 3 पर हैं — आपकी दैनिक सीमा :limit है।",
    'address_pending_title' => 'आपके दस्तावेज़ की समीक्षा हो रही है',
    'address_pending_body' => 'आपने :time एक :type सबमिट किया। हमारी टीम इन्हें हाथ से समीक्षा करती है, आमतौर पर एक दिन के भीतर। तब तक आपकी वर्तमान दैनिक सीमा :limit ही रहेगी।',
    'address_resubmit_notice' => 'कृपया नीचे एक स्पष्ट, हालिया दस्तावेज़ के साथ फिर से प्रयास करें।',
    'address_form_title' => 'अपनी दैनिक सीमा बढ़ाएं',
    'address_form_body' => 'एक हालिया दस्तावेज़ अपलोड करें जिसमें आपका नाम और घर का पता दिखाई दे — एक उपयोगिता बिल, एक बैंक स्टेटमेंट, या एक किराया समझौता, सभी काम करते हैं। यह अंतिम सत्यापन चरण है: यह आपकी दैनिक Send Money और Withdraw सीमा को :from से :to तक बढ़ाता है। इसकी समीक्षा केवल हमारी अपनी टीम करती है, आमतौर पर एक दिन के भीतर।',
    'document_type_label' => 'दस्तावेज़ प्रकार',
    'select_document_type' => 'दस्तावेज़ प्रकार चुनें',
    'document_label' => 'दस्तावेज़',
    'document_hint' => '(JPG, PNG, या PDF, पिछले 3 महीनों के भीतर की तारीख वाला)',

    // Document type option labels (KycController/AddressVerificationController DOCUMENT_TYPES, and the models' documentTypeLabel())
    'doc_drivers_license' => "ड्राइविंग लाइसेंस",
    'doc_state_id' => 'राज्य द्वारा जारी आईडी कार्ड',
    'doc_passport' => 'यूएस पासपोर्ट',
    'doc_other_id' => 'अन्य सरकार द्वारा जारी आईडी',
    'doc_utility_bill' => 'उपयोगिता बिल',
    'doc_bank_statement' => 'बैंक स्टेटमेंट',
    'doc_tenancy_agreement' => 'किराया समझौता',
    'doc_other_address' => 'पते का अन्य प्रमाण',

    // Controller flash messages
    'kyc_submitted_status' => "धन्यवाद — हमें आपकी आईडी मिल गई है। हमारी टीम की समीक्षा के दौरान आपका खाता उपयोग के लिए तैयार है, जिसमें आमतौर पर एक दिन लगता है। निर्णय होते ही हम आपको बताएंगे।",
    'address_submitted_status' => "धन्यवाद — हमें आपका दस्तावेज़ मिल गया है। निर्णय होते ही हम आपको बताएंगे, आमतौर पर एक दिन के भीतर।",
    'address_verify_identity_first' => 'पहले अपनी पहचान सत्यापित करें — पता सत्यापन उसके बाद अगला चरण है।',
];
