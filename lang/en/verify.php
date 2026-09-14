<?php

return [
    // Tiers (App\Support\AccountTier)
    'tier_1' => 'Tier 1',
    'tier_2' => 'Tier 2',
    'tier_3' => 'Tier 3',
    'tier_1_desc' => 'Starter — verify your identity to raise your limit',
    'tier_2_desc' => 'Identity verified — verify your address to raise it further',
    'tier_3_desc' => 'Fully verified',

    // Shared status labels (KycVerification / AddressVerification)
    'status_approved' => 'Approved',
    'status_rejected' => 'Rejected',
    'status_pending_review' => 'Pending review',

    // Settings hub rows
    'identity_verification' => 'Identity verification',
    'address_verification' => 'Address verification',
    'badge_verified' => 'Verified',
    'badge_pending' => 'Pending',
    'badge_needs_resubmit' => 'Needs resubmit',
    'badge_not_started' => 'Not started',

    // Shared buttons
    'back_to_dashboard' => 'Back to dashboard',
    'back_to_settings' => 'Back to Settings',
    'message_support' => 'Message support',
    'submit_for_review' => 'Submit for review',
    'verify_identity' => 'Verify your identity',
    'verify_address' => 'Verify your address',
    'last_submission_rejected' => "Your last submission wasn't approved.",

    // Send Money gate (kyc-required.blade.php)
    'kyc_gate_pending_title' => 'Send Money unlocks once your ID is reviewed',
    'kyc_gate_pending_body' => "We've got your government ID and photo. Our team is reviewing them now, usually within a day. Everything else on your account works normally in the meantime.",
    'kyc_gate_rejected_title' => 'Send Money needs a new ID verification',
    'kyc_gate_rejected_body' => "Your last submission wasn't approved. Take another look and resubmit. It only takes a minute.",
    'kyc_gate_not_started_title' => 'Verify your identity to send money',
    'kyc_gate_not_started_body' => "Upload a government ID and a quick selfie. It's the last step to fully unlock your account. Everything else already works.",

    // Identity verification page (kyc-verify.blade.php)
    'kyc_approved_title' => "You're verified",
    'kyc_approved_body' => 'Your government ID and photo were reviewed and approved. Send Money is fully unlocked.',
    'kyc_approved_body_dated' => 'Your government ID and photo were reviewed and approved on :date. Send Money is fully unlocked.',
    'kyc_pending_title' => 'Your ID is under review',
    'kyc_pending_body' => "You submitted a :type and a photo :time. Our team reviews these by hand, usually within a day. We'll notify you the moment it's decided. Everything else on your account works normally in the meantime; only Send Money stays locked until then.",
    'kyc_resubmit_notice' => 'Please try again below with a clear, unedited photo of your ID and a well-lit selfie.',
    'kyc_form_title' => 'One last step',
    'kyc_form_body' => "Upload a photo of a valid government-issued ID and a selfie of yourself. This is how we confirm you're really you before fully unlocking your account. No third party ever sees these, only our own team reviews them by hand. You can keep using your account normally while it's reviewed; only Send Money waits until it's approved.",
    'id_type_label' => 'ID type',
    'select_id_type' => 'Select ID type',
    'id_photo_label' => 'Photo of your ID (front)',
    'selfie_label' => 'A selfie of you',
    'selfie_hint' => '(well lit, face clearly visible)',

    // Address verification page (address-verify.blade.php)
    'address_approved_title' => "You're fully verified",
    'address_approved_body' => "Your proof of address was reviewed and approved. You're now Tier 3 — your daily limit is :limit.",
    'address_approved_body_dated' => "Your proof of address was reviewed and approved on :date. You're now Tier 3 — your daily limit is :limit.",
    'address_pending_title' => 'Your document is under review',
    'address_pending_body' => 'You submitted a :type :time. Our team reviews these by hand, usually within a day. Your current daily limit stays at :limit until then.',
    'address_resubmit_notice' => 'Please try again below with a clear, recent document.',
    'address_form_title' => 'Raise your daily limit',
    'address_form_body' => 'Upload a recent document that shows your name and home address — a utility bill, a bank statement, or a tenancy agreement all work. This is the last verification step: it raises your daily Send Money and Withdraw limit from :from to :to. Only our own team ever reviews it, usually within a day.',
    'document_type_label' => 'Document type',
    'select_document_type' => 'Select document type',
    'document_label' => 'Document',
    'document_hint' => '(JPG, PNG, or PDF, dated within the last 3 months)',

    // Document type option labels (KycController/AddressVerificationController DOCUMENT_TYPES, and the models' documentTypeLabel())
    'doc_drivers_license' => "Driver's license",
    'doc_state_id' => 'State-issued ID card',
    'doc_passport' => 'US passport',
    'doc_other_id' => 'Other government-issued ID',
    'doc_utility_bill' => 'Utility bill',
    'doc_bank_statement' => 'Bank statement',
    'doc_tenancy_agreement' => 'Tenancy agreement',
    'doc_other_address' => 'Other proof of address',

    // Controller flash messages
    'kyc_submitted_status' => "Thanks — we've received your ID. Your account is ready to use while our team reviews it, usually within a day. We'll let you know as soon as it's decided.",
    'address_submitted_status' => "Thanks — we've received your document. We'll let you know as soon as it's decided, usually within a day.",
    'address_verify_identity_first' => 'Verify your identity first — address verification is the next step after that.',
];
