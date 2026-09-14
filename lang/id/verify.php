<?php

return [
    // Tiers (App\Support\AccountTier)
    'tier_1' => 'Tingkat 1',
    'tier_2' => 'Tingkat 2',
    'tier_3' => 'Tingkat 3',
    'tier_1_desc' => 'Pemula — verifikasi identitas Anda untuk menaikkan limit Anda',
    'tier_2_desc' => 'Identitas terverifikasi — verifikasi alamat Anda untuk menaikkannya lebih jauh',
    'tier_3_desc' => 'Terverifikasi penuh',

    // Shared status labels (KycVerification / AddressVerification)
    'status_approved' => 'Disetujui',
    'status_rejected' => 'Ditolak',
    'status_pending_review' => 'Menunggu peninjauan',

    // Settings hub rows
    'identity_verification' => 'Verifikasi identitas',
    'address_verification' => 'Verifikasi alamat',
    'badge_verified' => 'Terverifikasi',
    'badge_pending' => 'Menunggu',
    'badge_needs_resubmit' => 'Perlu dikirim ulang',
    'badge_not_started' => 'Belum dimulai',

    // Shared buttons
    'back_to_dashboard' => 'Kembali ke dasbor',
    'back_to_settings' => 'Kembali ke Pengaturan',
    'message_support' => 'Kirim pesan ke dukungan',
    'submit_for_review' => 'Kirim untuk ditinjau',
    'verify_identity' => 'Verifikasi identitas Anda',
    'verify_address' => 'Verifikasi alamat Anda',
    'last_submission_rejected' => 'Pengajuan terakhir Anda tidak disetujui.',

    // Send Money gate (kyc-required.blade.php)
    'kyc_gate_pending_title' => 'Kirim Uang akan terbuka setelah ID Anda ditinjau',
    'kyc_gate_pending_body' => 'Kami sudah menerima ID resmi dan foto Anda. Tim kami sedang meninjaunya sekarang, biasanya dalam waktu satu hari. Sementara itu, semua fitur lain di akun Anda tetap berfungsi normal.',
    'kyc_gate_rejected_title' => 'Kirim Uang memerlukan verifikasi ID baru',
    'kyc_gate_rejected_body' => 'Pengajuan terakhir Anda tidak disetujui. Silakan periksa kembali dan kirim ulang. Hanya perlu waktu semenit.',
    'kyc_gate_not_started_title' => 'Verifikasi identitas Anda untuk mengirim uang',
    'kyc_gate_not_started_body' => 'Unggah ID resmi dan selfie singkat. Ini adalah langkah terakhir untuk membuka akun Anda sepenuhnya. Semua fitur lainnya sudah berfungsi.',

    // Identity verification page (kyc-verify.blade.php)
    'kyc_approved_title' => 'Anda telah terverifikasi',
    'kyc_approved_body' => 'ID resmi dan foto Anda telah ditinjau dan disetujui. Kirim Uang kini sepenuhnya terbuka.',
    'kyc_approved_body_dated' => 'ID resmi dan foto Anda telah ditinjau dan disetujui pada :date. Kirim Uang kini sepenuhnya terbuka.',
    'kyc_pending_title' => 'ID Anda sedang ditinjau',
    'kyc_pending_body' => 'Anda mengirimkan :type dan foto :time. Tim kami meninjaunya secara manual, biasanya dalam waktu satu hari. Kami akan memberi tahu Anda segera setelah keputusan diambil. Sementara itu, semua fitur lain di akun Anda tetap berfungsi normal; hanya Kirim Uang yang tetap terkunci sampai saat itu.',
    'kyc_resubmit_notice' => 'Silakan coba lagi di bawah dengan foto ID yang jelas dan tidak diedit serta selfie dengan pencahayaan yang baik.',
    'kyc_form_title' => 'Satu langkah terakhir',
    'kyc_form_body' => 'Unggah foto ID resmi yang berlaku dan selfie diri Anda. Ini adalah cara kami memastikan bahwa ini benar-benar Anda sebelum membuka akun Anda sepenuhnya. Tidak ada pihak ketiga yang pernah melihat ini, hanya tim kami sendiri yang meninjaunya secara manual. Anda tetap bisa menggunakan akun Anda seperti biasa selama peninjauan; hanya Kirim Uang yang menunggu sampai disetujui.',
    'id_type_label' => 'Jenis ID',
    'select_id_type' => 'Pilih jenis ID',
    'id_photo_label' => 'Foto ID Anda (bagian depan)',
    'selfie_label' => 'Selfie diri Anda',
    'selfie_hint' => '(pencahayaan baik, wajah terlihat jelas)',

    // Address verification page (address-verify.blade.php)
    'address_approved_title' => 'Anda telah terverifikasi penuh',
    'address_approved_body' => 'Bukti alamat Anda telah ditinjau dan disetujui. Anda kini berada di Tingkat 3 — limit harian Anda adalah :limit.',
    'address_approved_body_dated' => 'Bukti alamat Anda telah ditinjau dan disetujui pada :date. Anda kini berada di Tingkat 3 — limit harian Anda adalah :limit.',
    'address_pending_title' => 'Dokumen Anda sedang ditinjau',
    'address_pending_body' => 'Anda mengirimkan :type :time. Tim kami meninjaunya secara manual, biasanya dalam waktu satu hari. Limit harian Anda saat ini tetap di :limit sampai saat itu.',
    'address_resubmit_notice' => 'Silakan coba lagi di bawah dengan dokumen yang jelas dan terbaru.',
    'address_form_title' => 'Naikkan limit harian Anda',
    'address_form_body' => 'Unggah dokumen terbaru yang menunjukkan nama dan alamat rumah Anda — tagihan listrik/air, rekening koran, atau perjanjian sewa semuanya bisa digunakan. Ini adalah langkah verifikasi terakhir: langkah ini menaikkan limit harian Kirim Uang dan Tarik Tunai Anda dari :from menjadi :to. Hanya tim kami sendiri yang meninjaunya, biasanya dalam waktu satu hari.',
    'document_type_label' => 'Jenis dokumen',
    'select_document_type' => 'Pilih jenis dokumen',
    'document_label' => 'Dokumen',
    'document_hint' => '(JPG, PNG, atau PDF, bertanggal dalam 3 bulan terakhir)',

    // Document type option labels (KycController/AddressVerificationController DOCUMENT_TYPES, and the models' documentTypeLabel())
    'doc_drivers_license' => 'SIM',
    'doc_state_id' => 'Kartu identitas resmi negara',
    'doc_passport' => 'Paspor AS',
    'doc_other_id' => 'ID resmi lainnya yang dikeluarkan pemerintah',
    'doc_utility_bill' => 'Tagihan listrik/air',
    'doc_bank_statement' => 'Rekening koran',
    'doc_tenancy_agreement' => 'Perjanjian sewa',
    'doc_other_address' => 'Bukti alamat lainnya',

    // Controller flash messages
    'kyc_submitted_status' => 'Terima kasih — kami telah menerima ID Anda. Akun Anda tetap siap digunakan selama tim kami meninjaunya, biasanya dalam waktu satu hari. Kami akan memberi tahu Anda segera setelah keputusan diambil.',
    'address_submitted_status' => 'Terima kasih — kami telah menerima dokumen Anda. Kami akan memberi tahu Anda segera setelah keputusan diambil, biasanya dalam waktu satu hari.',
    'address_verify_identity_first' => 'Verifikasi identitas Anda terlebih dahulu — verifikasi alamat adalah langkah berikutnya setelah itu.',
];
