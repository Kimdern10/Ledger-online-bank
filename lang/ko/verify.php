<?php

return [
    // Tiers (App\Support\AccountTier)
    'tier_1' => '티어 1',
    'tier_2' => '티어 2',
    'tier_3' => '티어 3',
    'tier_1_desc' => '스타터 — 본인 확인을 하면 한도가 올라갑니다',
    'tier_2_desc' => '신원 확인 완료 — 주소 확인을 하면 한도를 더 올릴 수 있습니다',
    'tier_3_desc' => '전체 인증 완료',

    // Shared status labels (KycVerification / AddressVerification)
    'status_approved' => '승인됨',
    'status_rejected' => '거부됨',
    'status_pending_review' => '검토 대기 중',

    // Settings hub rows
    'identity_verification' => '신원 확인',
    'address_verification' => '주소 확인',
    'badge_verified' => '인증됨',
    'badge_pending' => '대기 중',
    'badge_needs_resubmit' => '재제출 필요',
    'badge_not_started' => '시작 안 함',

    // Shared buttons
    'back_to_dashboard' => '대시보드로 돌아가기',
    'back_to_settings' => '설정으로 돌아가기',
    'message_support' => '지원팀에 메시지 보내기',
    'submit_for_review' => '검토 제출',
    'verify_identity' => '신원 확인하기',
    'verify_address' => '주소 확인하기',
    'last_submission_rejected' => "마지막 제출이 승인되지 않았습니다.",

    // Send Money gate (kyc-required.blade.php)
    'kyc_gate_pending_title' => '신분증 검토가 완료되면 송금 기능이 활성화됩니다',
    'kyc_gate_pending_body' => "정부 발급 신분증과 사진을 받았습니다. 저희 팀이 현재 검토 중이며, 보통 하루 이내에 완료됩니다. 그동안 계정의 다른 모든 기능은 정상적으로 이용하실 수 있습니다.",
    'kyc_gate_rejected_title' => '송금 기능을 이용하려면 신원 확인을 다시 해야 합니다',
    'kyc_gate_rejected_body' => "마지막 제출이 승인되지 않았습니다. 다시 확인하고 재제출해 주세요. 1분밖에 걸리지 않습니다.",
    'kyc_gate_not_started_title' => '송금하려면 신원을 확인하세요',
    'kyc_gate_not_started_body' => "정부 발급 신분증과 간단한 셀카를 업로드하세요. 계정을 완전히 활성화하는 마지막 단계입니다. 다른 모든 기능은 이미 이용 가능합니다.",

    // Identity verification page (kyc-verify.blade.php)
    'kyc_approved_title' => "인증이 완료되었습니다",
    'kyc_approved_body' => '제출하신 정부 발급 신분증과 사진이 검토 후 승인되었습니다. 송금 기능이 완전히 활성화되었습니다.',
    'kyc_approved_body_dated' => '제출하신 정부 발급 신분증과 사진이 :date에 검토 후 승인되었습니다. 송금 기능이 완전히 활성화되었습니다.',
    'kyc_pending_title' => '신분증이 검토 중입니다',
    'kyc_pending_body' => ":time에 :type과 사진을 제출하셨습니다. 저희 팀이 직접 검토하며, 보통 하루 이내에 완료됩니다. 결정되는 즉시 알려드립니다. 그동안 계정의 다른 모든 기능은 정상적으로 이용하실 수 있으며, 송금 기능만 검토가 완료될 때까지 잠겨 있습니다.",
    'kyc_resubmit_notice' => '아래에서 선명하고 수정되지 않은 신분증 사진과 조명이 밝은 셀카를 다시 제출해 주세요.',
    'kyc_form_title' => '마지막 한 단계',
    'kyc_form_body' => "유효한 정부 발급 신분증 사진과 본인의 셀카를 업로드하세요. 계정을 완전히 활성화하기 전에 본인 확인을 하기 위한 절차입니다. 제3자는 이를 절대 볼 수 없으며, 저희 팀만이 직접 검토합니다. 검토가 진행되는 동안에도 계정을 평소대로 사용하실 수 있으며, 승인될 때까지 송금 기능만 대기 상태입니다.",
    'id_type_label' => '신분증 종류',
    'select_id_type' => '신분증 종류 선택',
    'id_photo_label' => '신분증 사진 (앞면)',
    'selfie_label' => '본인 셀카',
    'selfie_hint' => '(조명이 밝고 얼굴이 명확히 보이는 사진)',

    // Address verification page (address-verify.blade.php)
    'address_approved_title' => "전체 인증이 완료되었습니다",
    'address_approved_body' => "제출하신 주소 증빙이 검토 후 승인되었습니다. 이제 티어 3이며, 일일 한도는 :limit입니다.",
    'address_approved_body_dated' => "제출하신 주소 증빙이 :date에 검토 후 승인되었습니다. 이제 티어 3이며, 일일 한도는 :limit입니다.",
    'address_pending_title' => '서류가 검토 중입니다',
    'address_pending_body' => ':time에 :type을(를) 제출하셨습니다. 저희 팀이 직접 검토하며, 보통 하루 이내에 완료됩니다. 그때까지 현재 일일 한도인 :limit이 유지됩니다.',
    'address_resubmit_notice' => '아래에서 선명하고 최근 발급된 서류를 다시 제출해 주세요.',
    'address_form_title' => '일일 한도 올리기',
    'address_form_body' => '본인의 이름과 자택 주소가 표시된 최근 서류를 업로드하세요 — 공과금 고지서, 은행 명세서, 임대차 계약서 모두 가능합니다. 이것이 마지막 확인 단계이며, 일일 송금 및 출금 한도가 :from에서 :to로 올라갑니다. 저희 팀만이 검토하며, 보통 하루 이내에 완료됩니다.',
    'document_type_label' => '서류 종류',
    'select_document_type' => '서류 종류 선택',
    'document_label' => '서류',
    'document_hint' => '(JPG, PNG 또는 PDF, 최근 3개월 이내 발급)',

    // Document type option labels (KycController/AddressVerificationController DOCUMENT_TYPES, and the models' documentTypeLabel())
    'doc_drivers_license' => "운전면허증",
    'doc_state_id' => '주 발급 신분증',
    'doc_passport' => '미국 여권',
    'doc_other_id' => '기타 정부 발급 신분증',
    'doc_utility_bill' => '공과금 고지서',
    'doc_bank_statement' => '은행 명세서',
    'doc_tenancy_agreement' => '임대차 계약서',
    'doc_other_address' => '기타 주소 증빙',

    // Controller flash messages
    'kyc_submitted_status' => "감사합니다 — 신분증을 받았습니다. 저희 팀이 검토하는 동안(보통 하루 이내) 계정을 정상적으로 사용하실 수 있습니다. 결정되는 즉시 알려드리겠습니다.",
    'address_submitted_status' => "감사합니다 — 서류를 받았습니다. 결정되는 즉시, 보통 하루 이내에 알려드리겠습니다.",
    'address_verify_identity_first' => '먼저 신원을 확인하세요 — 주소 확인은 그다음 단계입니다.',
];
