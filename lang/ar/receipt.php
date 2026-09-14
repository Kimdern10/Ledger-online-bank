<?php

return [
    // Page chrome (send-receipt.blade.php)
    'page_title' => 'الإيصال',
    'download_pdf' => 'تنزيل PDF',
    'share' => 'مشاركة',
    'done' => 'تم',
    'send_another' => 'إرسال تحويل آخر',

    // "from"/"to"/"by" word shown before the other party's name, e.g.
    // "to Jane Doe" or "from Ledger" — controllers pass a plain identifier,
    // the blade maps it through these.
    'subverb_from' => 'من',
    'subverb_to' => 'إلى',
    'subverb_by' => 'بواسطة',

    // Row labels — shared across every receipt type (Send Money,
    // Another bank, International, Withdraw, Top up, admin adjustment)
    'row_from' => 'من',
    'row_bank' => 'البنك',
    'row_status' => 'الحالة',
    'row_category' => 'الفئة',
    'row_note' => 'ملاحظة',
    'row_reference' => 'الرقم المرجعي',
    'row_date' => 'التاريخ',
    'row_recipient' => 'المستلم',
    'row_account_number' => 'رقم الحساب',
    'row_routing_number' => 'رقم التوجيه المصرفي',
    'row_scheduled_for' => 'مجدول لـ',
    'row_country' => 'الدولة',
    'row_swift_bic' => 'SWIFT/BIC',
    'row_account_iban' => 'الحساب/IBAN',
    'row_currency' => 'العملة',
    'row_exchange_rate' => 'سعر الصرف',
    'row_recipient_receives_approx' => 'يستلم المستلم (تقريبًا)',
    'row_withdraw_to' => 'السحب إلى',
    'row_fee' => 'الرسوم',
    'row_youll_receive' => 'ستستلم',
    'row_top_up_from' => 'الشحن من',
    'row_card_processing_fee' => 'رسوم معالجة البطاقة',
    'row_account_name' => 'اسم الحساب',

    // Titles
    'title_payment_received' => 'تم استلام الدفعة',
    'title_payment_sent' => 'تم إرسال الدفعة',
    'title_transfer_scheduled' => 'تحويل مجدول',
    'title_transfer_cancelled' => 'تم إلغاء التحويل',
    'title_transfer_failed' => 'فشل التحويل',
    'title_withdrawal' => 'سحب',
    'title_topup' => 'شحن الرصيد',
    'title_deposit_received' => 'تم استلام الإيداع',
    'title_balance_adjustment' => 'تعديل الرصيد',

    // Banners
    'banner_scheduled' => "هذا التحويل مجدول ليوم :date ولم يُرسل بعد. يمكنك إلغاؤه في أي وقت قبل ذلك التاريخ.",
    'banner_cancelled' => 'تم إلغاء هذا التحويل ولم يُرسل قط.',
    'banner_failed' => "لم يتم تنفيذ هذا التحويل — الرصيد غير كافٍ في اليوم المحدد لإرساله.",
    'banner_transfer_sent' => 'تم إرسال تحويلك.',
    'banner_international_transfer_sent' => 'تم إرسال تحويلك الدولي.',
    'banner_withdrawal_bank_days' => 'يصل إلى بنكك خلال 1 إلى 3 أيام عمل.',
    'banner_topup_fee_note' => 'رسوم المعالجة تم تحصيلها من قِبل بطاقتك، ولم تُخصم من المبلغ المُضاف إلى Ledger.',
    'banner_adjustment_credit' => 'تم إيداع هذا المبلغ في حسابك من قِبل Ledger — تواصل مع الدعم إذا كانت لديك أي استفسارات بشأنه.',
    'banner_adjustment_debit' => 'تم إجراء هذا التعديل على حسابك من قِبل Ledger — تواصل مع الدعم إذا كانت لديك أي استفسارات بشأنه.',

    // Status labels (App\Support\TransactionStatus — shared by Transfer,
    // ExternalTransfer, InternationalTransfer, Withdrawal, TopUp)
    'status_completed' => 'مكتمل',
    'status_scheduled' => 'مجدول',
    'status_cancelled' => 'ملغى',
    'status_failed' => 'فشل',

    // JS strings for the Download PDF / Share buttons
    'share_text_prefix' => 'إيصال دفع من Ledger',
    'share_title' => 'إيصال Ledger',
    'js_preparing_pdf' => 'جارٍ تجهيز ملف PDF…',
    'js_pdf_error' => "تعذّر إنشاء ملف PDF. حاول مرة أخرى.",
    'js_preparing_share' => 'جارٍ تجهيز إيصالك للمشاركة…',
    'js_share_error' => "تعذّرت مشاركة الإيصال. جرّب التنزيل بدلاً من ذلك.",
    'js_share_unsupported' => 'المشاركة غير مدعومة في هذا المتصفح. استخدم التنزيل بدلاً من ذلك.',
];
