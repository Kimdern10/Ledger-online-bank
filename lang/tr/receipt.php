<?php

return [
    // Page chrome (send-receipt.blade.php)
    'page_title' => 'Makbuz',
    'download_pdf' => 'PDF İndir',
    'share' => 'Paylaş',
    'done' => 'Bitti',
    'send_another' => 'Başka gönder',

    // "from"/"to"/"by" word shown before the other party's name, e.g.
    // "to Jane Doe" or "from Ledger" — controllers pass a plain identifier,
    // the blade maps it through these.
    'subverb_from' => 'Kimden',
    'subverb_to' => 'Kime',
    'subverb_by' => 'Tarafından',

    // Row labels — shared across every receipt type (Send Money,
    // Another bank, International, Withdraw, Top up, admin adjustment)
    'row_from' => 'Gönderen',
    'row_bank' => 'Banka',
    'row_status' => 'Durum',
    'row_category' => 'Kategori',
    'row_note' => 'Not',
    'row_reference' => 'Referans',
    'row_date' => 'Tarih',
    'row_recipient' => 'Alıcı',
    'row_account_number' => 'Hesap numarası',
    'row_routing_number' => 'Yönlendirme numarası',
    'row_scheduled_for' => 'Planlanan tarih',
    'row_country' => 'Ülke',
    'row_swift_bic' => 'SWIFT/BIC',
    'row_account_iban' => 'Hesap/IBAN',
    'row_currency' => 'Para birimi',
    'row_exchange_rate' => 'Döviz kuru',
    'row_recipient_receives_approx' => 'Alıcının alacağı tutar (yaklaşık)',
    'row_withdraw_to' => 'Çekilecek hesap',
    'row_fee' => 'Ücret',
    'row_youll_receive' => 'Alacağınız tutar',
    'row_top_up_from' => 'Yükleme kaynağı',
    'row_card_processing_fee' => 'Kart işlem ücreti',
    'row_account_name' => 'Hesap adı',

    // Titles
    'title_payment_received' => 'Ödeme alındı',
    'title_payment_sent' => 'Ödeme gönderildi',
    'title_transfer_scheduled' => 'Transfer planlandı',
    'title_transfer_cancelled' => 'Transfer iptal edildi',
    'title_transfer_failed' => 'Transfer başarısız oldu',
    'title_withdrawal' => 'Para çekme',
    'title_topup' => 'Bakiye yükleme',
    'title_deposit_received' => 'Yatırım alındı',
    'title_balance_adjustment' => 'Bakiye düzeltmesi',

    // Banners
    'banner_scheduled' => 'Bu transfer :date tarihinde planlandı ve henüz gerçekleşmedi. O tarihe kadar istediğiniz zaman iptal edebilirsiniz.',
    'banner_cancelled' => 'Bu transfer iptal edildi ve hiç gerçekleşmedi.',
    'banner_failed' => 'Bu transfer gerçekleşmedi — planlandığı tarihte bakiye yetersizdi.',
    'banner_transfer_sent' => 'Transferiniz gönderildi.',
    'banner_international_transfer_sent' => 'Uluslararası transferiniz gönderildi.',
    'banner_withdrawal_bank_days' => 'Bankanıza 1–3 iş günü içinde ulaşır.',
    'banner_topup_fee_note' => "İşlem ücreti kartınız tarafından tahsil edildi; Ledger'a eklenen tutardan düşülmedi.",
    'banner_adjustment_credit' => 'Bu yatırım Ledger tarafından hesabınıza yapılmıştır — sorularınız için Destek ile iletişime geçin.',
    'banner_adjustment_debit' => 'Bu düzeltme Ledger tarafından hesabınızda yapılmıştır — sorularınız için Destek ile iletişime geçin.',

    // Status labels (App\Support\TransactionStatus — shared by Transfer,
    // ExternalTransfer, InternationalTransfer, Withdrawal, TopUp)
    'status_completed' => 'Tamamlandı',
    'status_scheduled' => 'Planlandı',
    'status_cancelled' => 'İptal edildi',
    'status_failed' => 'Başarısız',

    // JS strings for the Download PDF / Share buttons
    'share_text_prefix' => 'Ledger ödeme makbuzu',
    'share_title' => 'Ledger makbuzu',
    'js_preparing_pdf' => 'PDF hazırlanıyor…',
    'js_pdf_error' => 'PDF oluşturulamadı. Tekrar deneyin.',
    'js_preparing_share' => 'Makbuzunuz paylaşıma hazırlanıyor…',
    'js_share_error' => "Makbuz paylaşılamadı. Bunun yerine İndir'i deneyin.",
    'js_share_unsupported' => "Paylaşım bu tarayıcıda desteklenmiyor. Bunun yerine İndir'i kullanın.",
];
