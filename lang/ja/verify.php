<?php

return [
    // Tiers (App\Support\AccountTier)
    'tier_1' => 'ティア1',
    'tier_2' => 'ティア2',
    'tier_3' => 'ティア3',
    'tier_1_desc' => 'スターター — 本人確認を行って限度額を引き上げましょう',
    'tier_2_desc' => '本人確認済み — 住所確認を行うとさらに限度額を引き上げられます',
    'tier_3_desc' => '完全に確認済み',

    // Shared status labels (KycVerification / AddressVerification)
    'status_approved' => '承認済み',
    'status_rejected' => '却下',
    'status_pending_review' => '審査中',

    // Settings hub rows
    'identity_verification' => '本人確認',
    'address_verification' => '住所確認',
    'badge_verified' => '確認済み',
    'badge_pending' => '保留中',
    'badge_needs_resubmit' => '再提出が必要',
    'badge_not_started' => '未着手',

    // Shared buttons
    'back_to_dashboard' => 'ダッシュボードに戻る',
    'back_to_settings' => '設定に戻る',
    'message_support' => 'サポートに連絡',
    'submit_for_review' => '審査に提出',
    'verify_identity' => '本人確認を行う',
    'verify_address' => '住所確認を行う',
    'last_submission_rejected' => "前回の提出は承認されませんでした。",

    // Send Money gate (kyc-required.blade.php)
    'kyc_gate_pending_title' => '本人確認の審査が完了すると送金機能が利用可能になります',
    'kyc_gate_pending_body' => "政府発行の身分証明書と写真を受け取りました。現在当社チームが審査を行っており、通常1日以内に完了します。それまでの間、アカウントの他の機能は通常どおりご利用いただけます。",
    'kyc_gate_rejected_title' => '送金機能の利用には本人確認の再提出が必要です',
    'kyc_gate_rejected_body' => "前回の提出は承認されませんでした。内容をもう一度確認し、再提出してください。所要時間はわずか1分です。",
    'kyc_gate_not_started_title' => '送金するには本人確認を行ってください',
    'kyc_gate_not_started_body' => "政府発行の身分証明書と簡単な自撮り写真をアップロードしてください。これがアカウントを完全に解放するための最後のステップです。その他の機能はすでにすべてご利用いただけます。",

    // Identity verification page (kyc-verify.blade.php)
    'kyc_approved_title' => "本人確認が完了しました",
    'kyc_approved_body' => 'ご提出いただいた政府発行の身分証明書と写真は審査の上、承認されました。送金機能が完全に解放されました。',
    'kyc_approved_body_dated' => 'ご提出いただいた政府発行の身分証明書と写真は:dateに審査の上、承認されました。送金機能が完全に解放されました。',
    'kyc_pending_title' => '身分証明書を審査中です',
    'kyc_pending_body' => ":timeに:typeと写真をご提出いただきました。当社チームが手作業で審査しており、通常1日以内に完了します。結果が確定次第すぐにお知らせします。それまでの間、アカウントの他の機能は通常どおりご利用いただけます。送金機能のみ、審査完了までロックされたままとなります。",
    'kyc_resubmit_notice' => '下記より、鮮明で加工されていない身分証明書の写真と、明るい場所で撮影した自撮り写真を再度ご提出ください。',
    'kyc_form_title' => '最後のステップです',
    'kyc_form_body' => "有効な政府発行の身分証明書の写真と、ご自身の自撮り写真をアップロードしてください。これは、アカウントを完全に解放する前に本人であることを確認するためのものです。これらの情報を第三者が閲覧することは一切なく、当社自身のチームのみが手作業で審査します。審査中もアカウントは通常どおりご利用いただけますが、送金機能のみ承認されるまでお待ちいただきます。",
    'id_type_label' => '身分証明書の種類',
    'select_id_type' => '身分証明書の種類を選択',
    'id_photo_label' => '身分証明書の写真（表面）',
    'selfie_label' => 'ご自身の自撮り写真',
    'selfie_hint' => '（明るい場所で、顔がはっきり見えるもの）',

    // Address verification page (address-verify.blade.php)
    'address_approved_title' => "完全に確認が完了しました",
    'address_approved_body' => "ご提出いただいた住所証明は審査の上、承認されました。現在ティア3となり、1日の限度額は:limitです。",
    'address_approved_body_dated' => "ご提出いただいた住所証明は:dateに審査の上、承認されました。現在ティア3となり、1日の限度額は:limitです。",
    'address_pending_title' => '書類を審査中です',
    'address_pending_body' => ':timeに:typeをご提出いただきました。当社チームが手作業で審査しており、通常1日以内に完了します。それまでは現在の1日の限度額（:limit）が適用されます。',
    'address_resubmit_notice' => '下記より、鮮明で最近発行された書類を再度ご提出ください。',
    'address_form_title' => '1日の限度額を引き上げる',
    'address_form_body' => 'お名前とご自宅の住所が記載された最近の書類をアップロードしてください。公共料金の請求書、銀行取引明細書、賃貸契約書のいずれでも構いません。これが最後の確認ステップで、1日の送金・出金限度額が:fromから:toに引き上げられます。審査を行うのは当社自身のチームのみで、通常1日以内に完了します。',
    'document_type_label' => '書類の種類',
    'select_document_type' => '書類の種類を選択',
    'document_label' => '書類',
    'document_hint' => '（JPG、PNG、またはPDF形式。過去3か月以内の日付のもの）',

    // Document type option labels (KycController/AddressVerificationController DOCUMENT_TYPES, and the models' documentTypeLabel())
    'doc_drivers_license' => "運転免許証",
    'doc_state_id' => '州発行の身分証明書',
    'doc_passport' => '米国パスポート',
    'doc_other_id' => 'その他の政府発行の身分証明書',
    'doc_utility_bill' => '公共料金の請求書',
    'doc_bank_statement' => '銀行取引明細書',
    'doc_tenancy_agreement' => '賃貸契約書',
    'doc_other_address' => 'その他の住所証明',

    // Controller flash messages
    'kyc_submitted_status' => "ありがとうございます — 身分証明書を受け取りました。当社チームが審査する間（通常1日以内）も、アカウントは通常どおりご利用いただけます。結果が決まり次第、すぐにお知らせします。",
    'address_submitted_status' => "ありがとうございます — 書類を受け取りました。結果が決まり次第、通常1日以内にお知らせします。",
    'address_verify_identity_first' => 'まず本人確認を行ってください — 住所確認はその後の次のステップです。',
];
