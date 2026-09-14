<?php

return [
    // Tiers (App\Support\AccountTier)
    'tier_1' => 'المستوى 1',
    'tier_2' => 'المستوى 2',
    'tier_3' => 'المستوى 3',
    'tier_1_desc' => 'مبتدئ — تحقق من هويتك لرفع حدك',
    'tier_2_desc' => 'تم التحقق من الهوية — تحقق من عنوانك لرفعه أكثر',
    'tier_3_desc' => 'تم التحقق الكامل',

    // Shared status labels (KycVerification / AddressVerification)
    'status_approved' => 'تمت الموافقة',
    'status_rejected' => 'مرفوض',
    'status_pending_review' => 'قيد المراجعة',

    // Settings hub rows
    'identity_verification' => 'التحقق من الهوية',
    'address_verification' => 'التحقق من العنوان',
    'badge_verified' => 'موثّق',
    'badge_pending' => 'قيد الانتظار',
    'badge_needs_resubmit' => 'يحتاج إلى إعادة إرسال',
    'badge_not_started' => 'لم يبدأ',

    // Shared buttons
    'back_to_dashboard' => 'العودة إلى لوحة التحكم',
    'back_to_settings' => 'العودة إلى الإعدادات',
    'message_support' => 'مراسلة الدعم',
    'submit_for_review' => 'إرسال للمراجعة',
    'verify_identity' => 'تحقق من هويتك',
    'verify_address' => 'تحقق من عنوانك',
    'last_submission_rejected' => "لم تتم الموافقة على آخر طلب أرسلته.",

    // Send Money gate (kyc-required.blade.php)
    'kyc_gate_pending_title' => 'ستُفتح ميزة إرسال الأموال بمجرد مراجعة هويتك',
    'kyc_gate_pending_body' => "لقد استلمنا هويتك الحكومية وصورتك. فريقنا يراجعها الآن، عادةً خلال يوم واحد. كل شيء آخر في حسابك يعمل بشكل طبيعي في هذه الأثناء.",
    'kyc_gate_rejected_title' => 'إرسال الأموال يحتاج إلى تحقق جديد من الهوية',
    'kyc_gate_rejected_body' => "لم تتم الموافقة على آخر طلب أرسلته. ألقِ نظرة أخرى وأعد الإرسال. لن يستغرق الأمر سوى دقيقة.",
    'kyc_gate_not_started_title' => 'تحقق من هويتك لإرسال الأموال',
    'kyc_gate_not_started_body' => "قم برفع هوية حكومية وصورة سيلفي سريعة. إنها الخطوة الأخيرة لفتح حسابك بالكامل. كل شيء آخر يعمل بالفعل.",

    // Identity verification page (kyc-verify.blade.php)
    'kyc_approved_title' => "تم التحقق من هويتك",
    'kyc_approved_body' => 'تمت مراجعة هويتك الحكومية وصورتك والموافقة عليهما. ميزة إرسال الأموال مفتوحة بالكامل الآن.',
    'kyc_approved_body_dated' => 'تمت مراجعة هويتك الحكومية وصورتك والموافقة عليهما بتاريخ :date. ميزة إرسال الأموال مفتوحة بالكامل الآن.',
    'kyc_pending_title' => 'هويتك قيد المراجعة',
    'kyc_pending_body' => "لقد أرسلت :type وصورة :time. يراجع فريقنا هذه الطلبات يدويًا، عادةً خلال يوم واحد. سنُخطرك بمجرد اتخاذ القرار. كل شيء آخر في حسابك يعمل بشكل طبيعي في هذه الأثناء؛ ميزة إرسال الأموال فقط تبقى مقفلة حتى ذلك الحين.",
    'kyc_resubmit_notice' => 'يرجى المحاولة مرة أخرى أدناه بصورة واضحة وغير مُعدّلة لهويتك وصورة سيلفي بإضاءة جيدة.',
    'kyc_form_title' => 'خطوة أخيرة واحدة',
    'kyc_form_body' => "قم برفع صورة لهوية حكومية سارية المفعول وصورة سيلفي لك. هذه هي الطريقة التي نتحقق بها من أنك حقًا صاحب الحساب قبل فتحه بالكامل. لا يرى أي طرف ثالث هذه الصور أبدًا، فريقنا فقط يراجعها يدويًا. يمكنك الاستمرار في استخدام حسابك بشكل طبيعي أثناء المراجعة؛ فقط ميزة إرسال الأموال تنتظر حتى تتم الموافقة.",
    'id_type_label' => 'نوع الهوية',
    'select_id_type' => 'اختر نوع الهوية',
    'id_photo_label' => 'صورة هويتك (الوجه الأمامي)',
    'selfie_label' => 'صورة سيلفي لك',
    'selfie_hint' => '(إضاءة جيدة، والوجه واضح المعالم)',

    // Address verification page (address-verify.blade.php)
    'address_approved_title' => "تم التحقق الكامل من حسابك",
    'address_approved_body' => "تمت مراجعة إثبات عنوانك والموافقة عليه. أنت الآن في المستوى 3 — حدك اليومي هو :limit.",
    'address_approved_body_dated' => "تمت مراجعة إثبات عنوانك والموافقة عليه بتاريخ :date. أنت الآن في المستوى 3 — حدك اليومي هو :limit.",
    'address_pending_title' => 'مستندك قيد المراجعة',
    'address_pending_body' => 'لقد أرسلت :type :time. يراجع فريقنا هذه المستندات يدويًا، عادةً خلال يوم واحد. يبقى حدك اليومي الحالي عند :limit حتى ذلك الحين.',
    'address_resubmit_notice' => 'يرجى المحاولة مرة أخرى أدناه بمستند واضح وحديث.',
    'address_form_title' => 'ارفع حدك اليومي',
    'address_form_body' => 'قم برفع مستند حديث يُظهر اسمك وعنوان منزلك — فاتورة مرافق، أو كشف حساب مصرفي، أو عقد إيجار، كلها مقبولة. هذه هي الخطوة الأخيرة للتحقق: فهي ترفع حدك اليومي لإرسال الأموال والسحب من :from إلى :to. فريقنا فقط يراجعه، عادةً خلال يوم واحد.',
    'document_type_label' => 'نوع المستند',
    'select_document_type' => 'اختر نوع المستند',
    'document_label' => 'المستند',
    'document_hint' => '(JPG أو PNG أو PDF، بتاريخ خلال آخر 3 أشهر)',

    // Document type option labels (KycController/AddressVerificationController DOCUMENT_TYPES, and the models' documentTypeLabel())
    'doc_drivers_license' => "رخصة القيادة",
    'doc_state_id' => 'بطاقة هوية صادرة عن الولاية',
    'doc_passport' => 'جواز سفر أمريكي',
    'doc_other_id' => 'هوية حكومية أخرى',
    'doc_utility_bill' => 'فاتورة مرافق',
    'doc_bank_statement' => 'كشف حساب مصرفي',
    'doc_tenancy_agreement' => 'عقد إيجار',
    'doc_other_address' => 'إثبات عنوان آخر',

    // Controller flash messages
    'kyc_submitted_status' => "شكرًا — لقد استلمنا هويتك. حسابك جاهز للاستخدام بينما يراجعه فريقنا، عادةً خلال يوم واحد. سنُعلمك بمجرد اتخاذ القرار.",
    'address_submitted_status' => "شكرًا — لقد استلمنا مستندك. سنُعلمك بمجرد اتخاذ القرار، عادةً خلال يوم واحد.",
    'address_verify_identity_first' => 'تحقق من هويتك أولًا — التحقق من العنوان هو الخطوة التالية بعد ذلك.',
];
