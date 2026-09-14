<?php

return [
    // Tiers (App\Support\AccountTier)
    'tier_1' => 'Seviye 1',
    'tier_2' => 'Seviye 2',
    'tier_3' => 'Seviye 3',
    'tier_1_desc' => 'Başlangıç — limitinizi yükseltmek için kimliğinizi doğrulayın',
    'tier_2_desc' => 'Kimlik doğrulandı — daha da yükseltmek için adresinizi doğrulayın',
    'tier_3_desc' => 'Tamamen doğrulandı',

    // Shared status labels (KycVerification / AddressVerification)
    'status_approved' => 'Onaylandı',
    'status_rejected' => 'Reddedildi',
    'status_pending_review' => 'İnceleme bekliyor',

    // Settings hub rows
    'identity_verification' => 'Kimlik doğrulama',
    'address_verification' => 'Adres doğrulama',
    'badge_verified' => 'Doğrulandı',
    'badge_pending' => 'Beklemede',
    'badge_needs_resubmit' => 'Yeniden gönderim gerekli',
    'badge_not_started' => 'Başlanmadı',

    // Shared buttons
    'back_to_dashboard' => 'Panoya dön',
    'back_to_settings' => 'Ayarlara dön',
    'message_support' => "Destek'e mesaj gönder",
    'submit_for_review' => 'İncelemeye gönder',
    'verify_identity' => 'Kimliğinizi doğrulayın',
    'verify_address' => 'Adresinizi doğrulayın',
    'last_submission_rejected' => 'Son gönderiminiz onaylanmadı.',

    // Send Money gate (kyc-required.blade.php)
    'kyc_gate_pending_title' => 'Kimliğiniz incelendiğinde Para Gönder açılacak',
    'kyc_gate_pending_body' => 'Devlet kimliğinizi ve fotoğrafınızı aldık. Ekibimiz şu anda bunları inceliyor, genellikle bir gün içinde tamamlanır. Bu arada hesabınızdaki diğer her şey normal şekilde çalışmaya devam eder.',
    'kyc_gate_rejected_title' => 'Para Gönder için yeni bir kimlik doğrulaması gerekiyor',
    'kyc_gate_rejected_body' => 'Son gönderiminiz onaylanmadı. Tekrar gözden geçirip yeniden gönderin. Sadece bir dakikanızı alır.',
    'kyc_gate_not_started_title' => 'Para göndermek için kimliğinizi doğrulayın',
    'kyc_gate_not_started_body' => 'Bir devlet kimliği ve hızlı bir selfie yükleyin. Hesabınızı tamamen açmak için son adım budur. Diğer her şey zaten çalışıyor.',

    // Identity verification page (kyc-verify.blade.php)
    'kyc_approved_title' => 'Doğrulandınız',
    'kyc_approved_body' => 'Devlet kimliğiniz ve fotoğrafınız incelendi ve onaylandı. Para Gönder tamamen açıldı.',
    'kyc_approved_body_dated' => 'Devlet kimliğiniz ve fotoğrafınız :date tarihinde incelendi ve onaylandı. Para Gönder tamamen açıldı.',
    'kyc_pending_title' => 'Kimliğiniz inceleniyor',
    'kyc_pending_body' => ':time bir :type ve bir fotoğraf gönderdiniz. Ekibimiz bunları elle inceliyor, genellikle bir gün içinde tamamlanır. Karar verilir vermez size bildireceğiz. Bu arada hesabınızdaki diğer her şey normal şekilde çalışır; yalnızca Para Gönder o zamana kadar kilitli kalır.',
    'kyc_resubmit_notice' => 'Lütfen aşağıdan kimliğinizin net, düzenlenmemiş bir fotoğrafını ve iyi aydınlatılmış bir selfie ile tekrar deneyin.',
    'kyc_form_title' => 'Son bir adım',
    'kyc_form_body' => "Geçerli bir devlet tarafından verilmiş kimliğin fotoğrafını ve kendinizin bir selfie'sini yükleyin. Hesabınızı tamamen açmadan önce gerçekten siz olduğunuzu bu şekilde doğrularız. Bunları hiçbir üçüncü taraf görmez, yalnızca kendi ekibimiz elle inceler. İnceleme sürerken hesabınızı normal şekilde kullanmaya devam edebilirsiniz; yalnızca Para Gönder onaylanana kadar bekler.",
    'id_type_label' => 'Kimlik türü',
    'select_id_type' => 'Kimlik türünü seçin',
    'id_photo_label' => 'Kimliğinizin fotoğrafı (ön yüz)',
    'selfie_label' => 'Kendinizin bir selfie\'si',
    'selfie_hint' => '(iyi aydınlatılmış, yüz net görünür durumda)',

    // Address verification page (address-verify.blade.php)
    'address_approved_title' => 'Tamamen doğrulandınız',
    'address_approved_body' => 'Adres kanıtınız incelendi ve onaylandı. Artık Seviye 3\'tesiniz — günlük limitiniz :limit.',
    'address_approved_body_dated' => 'Adres kanıtınız :date tarihinde incelendi ve onaylandı. Artık Seviye 3\'tesiniz — günlük limitiniz :limit.',
    'address_pending_title' => 'Belgeniz inceleniyor',
    'address_pending_body' => ':time bir :type gönderdiniz. Ekibimiz bunları elle inceliyor, genellikle bir gün içinde tamamlanır. O zamana kadar mevcut günlük limitiniz :limit olarak kalır.',
    'address_resubmit_notice' => 'Lütfen aşağıdan net, güncel bir belge ile tekrar deneyin.',
    'address_form_title' => 'Günlük limitinizi yükseltin',
    'address_form_body' => 'Adınızı ve ev adresinizi gösteren güncel bir belge yükleyin — bir fatura, banka ekstresi veya kira sözleşmesi işe yarar. Bu son doğrulama adımıdır: günlük Para Gönder ve Çek limitinizi :from tutarından :to tutarına yükseltir. Bunu yalnızca kendi ekibimiz inceler, genellikle bir gün içinde.',
    'document_type_label' => 'Belge türü',
    'select_document_type' => 'Belge türünü seçin',
    'document_label' => 'Belge',
    'document_hint' => '(JPG, PNG veya PDF, son 3 ay içinde tarihli)',

    // Document type option labels (KycController/AddressVerificationController DOCUMENT_TYPES, and the models' documentTypeLabel())
    'doc_drivers_license' => 'Sürücü belgesi',
    'doc_state_id' => 'Devlet tarafından verilmiş kimlik kartı',
    'doc_passport' => 'ABD pasaportu',
    'doc_other_id' => 'Diğer devlet tarafından verilmiş kimlik',
    'doc_utility_bill' => 'Fatura',
    'doc_bank_statement' => 'Banka ekstresi',
    'doc_tenancy_agreement' => 'Kira sözleşmesi',
    'doc_other_address' => 'Diğer adres kanıtı',

    // Controller flash messages
    'kyc_submitted_status' => 'Teşekkürler — kimliğinizi aldık. Ekibimiz incelerken, genellikle bir gün içinde, hesabınız kullanıma hazır. Karar verilir verilmez size bildireceğiz.',
    'address_submitted_status' => 'Teşekkürler — belgenizi aldık. Karar verilir verilmez, genellikle bir gün içinde, size bildireceğiz.',
    'address_verify_identity_first' => 'Önce kimliğinizi doğrulayın — adres doğrulama bundan sonraki adımdır.',
];
