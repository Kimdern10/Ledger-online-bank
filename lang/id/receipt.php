<?php

return [
    // Page chrome (send-receipt.blade.php)
    'page_title' => 'Struk',
    'download_pdf' => 'Unduh PDF',
    'share' => 'Bagikan',
    'done' => 'Selesai',
    'send_another' => 'Kirim lagi',

    // "from"/"to"/"by" word shown before the other party's name, e.g.
    // "to Jane Doe" or "from Ledger" — controllers pass a plain identifier,
    // the blade maps it through these.
    'subverb_from' => 'dari',
    'subverb_to' => 'ke',
    'subverb_by' => 'oleh',

    // Row labels — shared across every receipt type (Send Money,
    // Another bank, International, Withdraw, Top up, admin adjustment)
    'row_from' => 'Dari',
    'row_bank' => 'Bank',
    'row_status' => 'Status',
    'row_category' => 'Kategori',
    'row_note' => 'Catatan',
    'row_reference' => 'Referensi',
    'row_date' => 'Tanggal',
    'row_recipient' => 'Penerima',
    'row_account_number' => 'Nomor rekening',
    'row_routing_number' => 'Nomor routing',
    'row_scheduled_for' => 'Dijadwalkan untuk',
    'row_country' => 'Negara',
    'row_swift_bic' => 'SWIFT/BIC',
    'row_account_iban' => 'Rekening/IBAN',
    'row_currency' => 'Mata uang',
    'row_exchange_rate' => 'Kurs tukar',
    'row_recipient_receives_approx' => 'Penerima menerima (perkiraan)',
    'row_withdraw_to' => 'Tarik ke',
    'row_fee' => 'Biaya',
    'row_youll_receive' => 'Anda akan menerima',
    'row_top_up_from' => 'Top up dari',
    'row_card_processing_fee' => 'Biaya pemrosesan kartu',
    'row_account_name' => 'Nama rekening',

    // Titles
    'title_payment_received' => 'Pembayaran diterima',
    'title_payment_sent' => 'Pembayaran terkirim',
    'title_transfer_scheduled' => 'Transfer dijadwalkan',
    'title_transfer_cancelled' => 'Transfer dibatalkan',
    'title_transfer_failed' => 'Transfer gagal',
    'title_withdrawal' => 'Penarikan',
    'title_topup' => 'Top-up',
    'title_deposit_received' => 'Setoran diterima',
    'title_balance_adjustment' => 'Penyesuaian saldo',

    // Banners
    'banner_scheduled' => 'Transfer ini dijadwalkan pada :date dan belum terkirim. Anda dapat membatalkannya kapan saja sebelum itu.',
    'banner_cancelled' => 'Transfer ini dibatalkan dan tidak pernah terkirim.',
    'banner_failed' => 'Transfer ini gagal — saldo tidak mencukupi pada hari yang dijadwalkan untuk pengiriman.',
    'banner_transfer_sent' => 'Transfer Anda telah terkirim.',
    'banner_international_transfer_sent' => 'Transfer internasional Anda telah terkirim.',
    'banner_withdrawal_bank_days' => 'Akan masuk ke bank Anda dalam 1–3 hari kerja.',
    'banner_topup_fee_note' => 'Biaya pemrosesan dikenakan oleh kartu Anda, tidak dipotong dari jumlah yang ditambahkan ke Ledger.',
    'banner_adjustment_credit' => 'Setoran ini dilakukan ke akun Anda oleh Ledger — hubungi Dukungan jika Anda memiliki pertanyaan.',
    'banner_adjustment_debit' => 'Penyesuaian ini dilakukan pada akun Anda oleh Ledger — hubungi Dukungan jika Anda memiliki pertanyaan.',

    // Status labels (App\Support\TransactionStatus — shared by Transfer,
    // ExternalTransfer, InternationalTransfer, Withdrawal, TopUp)
    'status_completed' => 'Selesai',
    'status_scheduled' => 'Dijadwalkan',
    'status_cancelled' => 'Dibatalkan',
    'status_failed' => 'Gagal',

    // JS strings for the Download PDF / Share buttons
    'share_text_prefix' => 'Struk pembayaran Ledger',
    'share_title' => 'Struk Ledger',
    'js_preparing_pdf' => 'Menyiapkan PDF Anda…',
    'js_pdf_error' => 'PDF tidak dapat dibuat. Coba lagi.',
    'js_preparing_share' => 'Menyiapkan struk Anda untuk dibagikan…',
    'js_share_error' => 'Struk tidak dapat dibagikan. Coba Unduh sebagai gantinya.',
    'js_share_unsupported' => 'Berbagi tidak didukung di browser ini. Gunakan Unduh sebagai gantinya.',
];
