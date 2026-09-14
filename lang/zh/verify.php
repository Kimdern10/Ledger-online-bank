<?php

return [
    // Tiers (App\Support\AccountTier)
    'tier_1' => '等级 1',
    'tier_2' => '等级 2',
    'tier_3' => '等级 3',
    'tier_1_desc' => '入门级 — 验证您的身份以提高限额',
    'tier_2_desc' => '身份已验证 — 验证您的地址以进一步提高限额',
    'tier_3_desc' => '已完全验证',

    // Shared status labels (KycVerification / AddressVerification)
    'status_approved' => '已批准',
    'status_rejected' => '已拒绝',
    'status_pending_review' => '待审核',

    // Settings hub rows
    'identity_verification' => '身份验证',
    'address_verification' => '地址验证',
    'badge_verified' => '已验证',
    'badge_pending' => '待处理',
    'badge_needs_resubmit' => '需要重新提交',
    'badge_not_started' => '尚未开始',

    // Shared buttons
    'back_to_dashboard' => '返回仪表盘',
    'back_to_settings' => '返回设置',
    'message_support' => '联系客服',
    'submit_for_review' => '提交审核',
    'verify_identity' => '验证您的身份',
    'verify_address' => '验证您的地址',
    'last_submission_rejected' => "您上次提交的申请未获批准。",

    // Send Money gate (kyc-required.blade.php)
    'kyc_gate_pending_title' => '身份审核通过后即可使用转账功能',
    'kyc_gate_pending_body' => "我们已收到您的政府身份证件和照片。我们的团队正在审核，通常在一天内完成。在此期间，您账户中的其他功能均可正常使用。",
    'kyc_gate_rejected_title' => '转账功能需要重新进行身份验证',
    'kyc_gate_rejected_body' => "您上次提交的申请未获批准。请重新检查并重新提交，只需一分钟即可完成。",
    'kyc_gate_not_started_title' => '验证身份以使用转账功能',
    'kyc_gate_not_started_body' => "上传一份政府身份证件和一张快速自拍照。这是完全解锁您账户的最后一步。其他所有功能均已可用。",

    // Identity verification page (kyc-verify.blade.php)
    'kyc_approved_title' => "您已通过验证",
    'kyc_approved_body' => '您的政府身份证件和照片已经过审核并获批准。转账功能现已完全解锁。',
    'kyc_approved_body_dated' => '您的政府身份证件和照片已于 :date 通过审核并获批准。转账功能现已完全解锁。',
    'kyc_pending_title' => '您的身份证件正在审核中',
    'kyc_pending_body' => "您已于 :time 提交了 :type 和一张照片。我们的团队会进行人工审核，通常在一天内完成。审核结果一经确定，我们会立即通知您。在此期间，您账户中的其他功能均可正常使用；仅转账功能会保持锁定，直到审核完成。",
    'kyc_resubmit_notice' => '请在下方重新提交一张清晰、未经编辑的证件照片和一张光线充足的自拍照。',
    'kyc_form_title' => '最后一步',
    'kyc_form_body' => "请上传一张有效的政府颁发身份证件照片和一张您本人的自拍照。这是我们在完全解锁您账户之前确认您本人身份的方式。任何第三方都不会看到这些内容，只有我们自己的团队会进行人工审核。审核期间您可以照常使用账户，仅转账功能需等待审核通过。",
    'id_type_label' => '证件类型',
    'select_id_type' => '选择证件类型',
    'id_photo_label' => '您的证件照片（正面）',
    'selfie_label' => '您本人的自拍照',
    'selfie_hint' => '（光线充足，面部清晰可见）',

    // Address verification page (address-verify.blade.php)
    'address_approved_title' => "您已完成全面验证",
    'address_approved_body' => "您的地址证明已经过审核并获批准。您现在已是等级 3 — 您的每日限额为 :limit。",
    'address_approved_body_dated' => "您的地址证明已于 :date 通过审核并获批准。您现在已是等级 3 — 您的每日限额为 :limit。",
    'address_pending_title' => '您的文件正在审核中',
    'address_pending_body' => '您已于 :time 提交了 :type。我们的团队会进行人工审核，通常在一天内完成。在此之前，您当前的每日限额将保持为 :limit。',
    'address_resubmit_notice' => '请在下方重新提交一份清晰的近期文件。',
    'address_form_title' => '提高您的每日限额',
    'address_form_body' => '请上传一份能显示您姓名和居住地址的近期文件 — 水电费账单、银行对账单或租赁协议均可。这是最后一步验证：它会将您每日的转账和提现限额从 :from 提高到 :to。仅由我们自己的团队进行审核，通常在一天内完成。',
    'document_type_label' => '文件类型',
    'select_document_type' => '选择文件类型',
    'document_label' => '文件',
    'document_hint' => '（JPG、PNG 或 PDF 格式，日期需在最近 3 个月内）',

    // Document type option labels (KycController/AddressVerificationController DOCUMENT_TYPES, and the models' documentTypeLabel())
    'doc_drivers_license' => "驾驶执照",
    'doc_state_id' => '州政府颁发的身份证',
    'doc_passport' => '美国护照',
    'doc_other_id' => '其他政府颁发的身份证件',
    'doc_utility_bill' => '水电费账单',
    'doc_bank_statement' => '银行对账单',
    'doc_tenancy_agreement' => '租赁协议',
    'doc_other_address' => '其他地址证明',

    // Controller flash messages
    'kyc_submitted_status' => "谢谢 — 我们已收到您的身份证件。在我们团队审核期间（通常在一天内完成），您的账户可以正常使用。审核结果一经确定，我们会立即通知您。",
    'address_submitted_status' => "谢谢 — 我们已收到您的文件。审核结果一经确定，我们会立即通知您，通常在一天内完成。",
    'address_verify_identity_first' => '请先验证您的身份 — 地址验证是接下来的下一步。',
];
