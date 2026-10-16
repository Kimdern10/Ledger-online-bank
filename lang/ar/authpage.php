<?php

return [
    // ---- Shared auth-layout decorative side panel (layouts/auth/simple.blade.php)
    // — shown on every auth page (login, register, forgot/reset password, 2FA,
    // confirm password, verify email). "Ledger" itself stays untranslated
    // everywhere, same convention as the rest of the app.
    'panel_eyebrow' => 'الخدمات المصرفية، منظمة',
    'panel_headline_line1' => 'كل دولار،',
    'panel_headline_accent' => 'مُحتسب.',
    'panel_sub' => 'افتح حسابًا، وحوّل الأموال، ونمِّ رصيدك في دفتر حسابات هادئ وصادق.',
    'mock_balance_label' => 'الرصيد',
    'mock_account_label' => 'حساب · جاري',
    'entry_fdic' => 'مؤمَّن من قِبل FDIC حتى 250,000 دولار',
    'entry_no_fees' => 'بدون رسوم خفية، أبدًا',
    'entry_instant' => 'تحويلات فورية على مدار الساعة',

    // ---- Shared field labels (reused across several forms below)
    'field_email' => 'البريد الإلكتروني',
    'field_email_short' => 'البريد الإلكتروني',
    'field_password' => 'كلمة المرور',
    'field_confirm_password' => 'تأكيد كلمة المرور',

    // ---- Login (pages/auth/login.blade.php)
    'login_title' => 'تسجيل الدخول',
    'login_heading' => 'سجّل الدخول إلى حسابك',
    'login_description' => 'أدخل بريدك الإلكتروني وكلمة المرور أدناه لتسجيل الدخول',
    'forgot_password_link' => 'هل نسيت كلمة المرور؟',
    'remember_me' => 'تذكّرني',
    'no_account' => 'ليس لديك حساب؟',
    'sign_up' => 'إنشاء حساب',
    'passkey_signin' => 'تسجيل الدخول باستخدام مفتاح مرور',
    'passkey_authenticating' => 'جارٍ التحقق...',
    'passkey_or_email' => 'أو تابع باستخدام البريد الإلكتروني',

    // ---- Register (pages/auth/register.blade.php)
    'register_title' => 'أنشئ حسابك',
    'register_subtitle' => 'افتح حساب Ledger في دقيقتين تقريبًا.',
    'field_first_name' => 'الاسم الأول',
    'field_last_name' => 'اسم العائلة',
    'field_middle_name' => 'الاسم الأوسط (اختياري)',
    'field_phone' => 'رقم الهاتف (اختياري)',
    'register_button' => 'إنشاء حساب',
    'already_have_account' => 'لديك حساب بالفعل؟',

    // ---- Confirm password (pages/auth/confirm-password.blade.php)
    'confirm_password_title' => 'تأكيد كلمة المرور',
    'confirm_password_description' => 'هذه منطقة آمنة من التطبيق. يرجى تأكيد كلمة المرور قبل المتابعة.',
    'confirm_with_passkey' => 'التأكيد باستخدام مفتاح مرور',
    'confirming' => 'جارٍ التأكيد...',
    'or_confirm_with_password' => 'أو أكّد باستخدام كلمة المرور',
    'confirm_button' => 'تأكيد',

    // ---- Forgot / reset password — the live 6-digit-code flow
    // (livewire/auth/forgot-password-code.blade.php, both steps)
    'forgot_password_title' => 'نسيت كلمة المرور',
    'forgot_password_description' => 'أدخل بريدك الإلكتروني وسنرسل لك رمزًا مكوّنًا من 6 أرقام لإعادة تعيينها.',
    'send_reset_code' => 'إرسال رمز إعادة التعيين',
    'or_return_to' => 'أو، العودة إلى',
    'log_in_lowercase' => 'تسجيل الدخول',
    'enter_your_code' => 'أدخل رمزك',
    'code_sent_to_prefix' => 'أدخل الرمز المكوّن من 6 أرقام الذي أرسلناه إلى',
    'code_sent_to_suffix' => 'واختر كلمة مرور جديدة.',
    'field_reset_code' => 'رمز إعادة التعيين',
    'field_new_password' => 'كلمة المرور الجديدة',
    'field_confirm_new_password' => 'تأكيد كلمة المرور الجديدة',
    'use_different_email' => 'استخدام بريد إلكتروني مختلف',
    'resend_code' => 'إعادة إرسال الرمز',
    'status_code_sent_known' => 'إذا كان هناك حساب مرتبط بـ :email، فقد أرسلنا إليه رمزًا مكوّنًا من 6 أرقام.',
    'status_code_resent' => 'تم إرسال رمز جديد، إذا كان هذا البريد الإلكتروني مرتبطًا بحساب.',
    'error_code_invalid' => 'هذا الرمز غير صالح أو منتهي الصلاحية. اطلب رمزًا جديدًا أدناه.',
    'status_password_reset_done' => 'تمت إعادة تعيين كلمة المرور الخاصة بك. سجّل الدخول بكلمة المرور الجديدة.',
    'js_code_expires_in' => 'ينتهي الرمز خلال',
    'js_code_expired_resend' => 'انتهت صلاحية الرمز. استخدم "إعادة إرسال الرمز" أدناه.',

    // ---- Reset password — the classic email-link flow (pages/auth/reset-password.blade.php)
    'reset_password_title' => 'إعادة تعيين كلمة المرور',
    'reset_password_description' => 'يرجى إدخال كلمة المرور الجديدة أدناه',

    // ---- Two-factor challenge (pages/auth/two-factor-challenge.blade.php)
    'two_factor_title' => 'المصادقة الثنائية',
    'auth_code_title' => 'رمز المصادقة',
    'auth_code_description' => 'أدخل رمز المصادقة الذي يوفره تطبيق المصادقة الخاص بك.',
    'field_otp_code' => 'رمز OTP',
    'recovery_code_title' => 'رمز الاسترداد',
    'recovery_code_description' => 'يرجى تأكيد الوصول إلى حسابك بإدخال أحد رموز الاسترداد الطارئة الخاصة بك.',
    'continue_button' => 'متابعة',
    'or_you_can' => 'أو يمكنك',
    'login_using_recovery_code' => 'تسجيل الدخول باستخدام رمز استرداد',
    'login_using_auth_code' => 'تسجيل الدخول باستخدام رمز مصادقة',

    // ---- Verify email — the live 6-digit-code flow
    // (resources/views/auth/verify-email-page.blade.php + livewire/auth/verify-code.blade.php)
    'verify_email_title' => 'تحقّق من بريدك الإلكتروني',
    'verify_sent_prefix' => 'أرسلنا رمزًا مكوّنًا من 6 أرقام إلى',
    'verify_sent_suffix' => 'أدخله أدناه للمتابعة.',
    'field_verification_code' => 'رمز التحقق',
    'verify_button' => 'تحقّق',
    'status_new_code_sent' => 'تم إرسال رمز جديد إلى بريدك الإلكتروني.',
    'status_account_verified' => 'تم التحقق من حسابك. سجّل الدخول للبدء.',
    'js_code_expired_request' => 'انتهت صلاحية الرمز. اطلب رمزًا جديدًا أدناه.',
    'js_no_active_code' => 'لا يوجد رمز نشط مسجّل. استخدم "إعادة إرسال الرمز" أدناه للحصول على واحد.',

    // ---- App\Models\User::accountBlockedMessage() — shown as the login-form
    // error for a frozen/suspended/disabled account, and mid-session by
    // EnsureAccountIsActive middleware.
    'account_frozen' => 'هذا الحساب مجمّد. تواصل مع الدعم للحصول على المساعدة.',
    'account_suspended' => 'هذا الحساب موقوف. تواصل مع الدعم للحصول على المساعدة.',
    'account_disabled' => 'تم تعطيل هذا الحساب.',
    'account_blocked_default' => 'لا يمكن لهذا الحساب تسجيل الدخول في الوقت الحالي.',
    'idle_warning_title' => 'هل ما زلت هنا؟',
    'idle_warning_body' => 'حفاظًا على أمانك، سيتم تسجيل خروجك قريبًا لأنه لم يحدث أي نشاط منذ فترة. سيتم تسجيل الخروج خلال',
    'idle_stay_signed_in' => 'البقاء مسجلًا',
    'idle_signed_out' => 'تم تسجيل خروجك لأنه لم يحدث أي نشاط منذ فترة. يرجى تسجيل الدخول مرة أخرى.',
    'wallet_frozen' => 'محفظتك مجمدة. لا يزال بإمكانك تسجيل الدخول والاطلاع على حسابك، لكن لا يمكن إرسال الأموال أو استلامها. تواصل مع الدعم لإلغاء التجميد.',
    'sms_verification_code' => 'Ledger: رمز التحقق الخاص بك هو :code. تنتهي صلاحيته خلال 10 دقائق. لا تشاركه مع أي أحد.',
    'sms_reset_code' => 'Ledger: رمز إعادة تعيين كلمة المرور الخاص بك هو :code. تنتهي صلاحيته خلال 10 دقائق. إذا لم تطلبه، فتجاهله.',
    'text_code_instead' => 'أرسل الرمز برسالة نصية إلى :phone بدلًا من ذلك',
    'status_code_texted' => 'أرسلنا رمزًا جديدًا برسالة نصية إلى :phone.',
    'sms_add_phone' => 'لتصلك الرموز برسالة نصية، أضف رقم هاتفك مع رمز البلد (مثلًا +234 801 234 5678) في إعدادات الملف الشخصي.',
    'sms_failed' => 'تعذّر إرسال رسالة نصية الآن. استخدم الرمز الذي أرسلناه إلى بريدك الإلكتروني، أو حاول لاحقًا.',
    'sms_too_many' => 'أُرسل عدد كبير جدًا من الرسائل النصية إلى هذا الرقم. يرجى الانتظار 10 دقائق ثم المحاولة مرة أخرى.',
    'reset_send_by' => 'إرسال الرمز عبر',
    'reset_by_email' => 'البريد الإلكتروني',
    'reset_by_text' => 'رسالة نصية',
    'reset_by_text_note' => 'سنرسله برسالة نصية إلى رقم الهاتف المسجل في حسابك.',
    'code_texted_for_prefix' => 'أدخل الرمز المكوّن من 6 أرقام الذي أرسلناه برسالة نصية إلى هاتف الحساب الخاص بـ',
    'code_texted_for_suffix' => '، ثم اختر كلمة مرور جديدة.',
    'status_code_texted_known' => 'إذا كان لدى :email حساب برقم هاتف، فقد أرسلنا إليه رمزًا من 6 أرقام برسالة نصية.',
];
