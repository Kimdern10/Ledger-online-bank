<?php

return [
    // Tiers (App\Support\AccountTier)
    'tier_1' => 'Cấp 1',
    'tier_2' => 'Cấp 2',
    'tier_3' => 'Cấp 3',
    'tier_1_desc' => 'Khởi đầu — xác minh danh tính của bạn để nâng hạn mức',
    'tier_2_desc' => 'Danh tính đã được xác minh — xác minh địa chỉ để nâng hạn mức hơn nữa',
    'tier_3_desc' => 'Đã xác minh đầy đủ',

    // Shared status labels (KycVerification / AddressVerification)
    'status_approved' => 'Đã duyệt',
    'status_rejected' => 'Bị từ chối',
    'status_pending_review' => 'Đang chờ xét duyệt',

    // Settings hub rows
    'identity_verification' => 'Xác minh danh tính',
    'address_verification' => 'Xác minh địa chỉ',
    'badge_verified' => 'Đã xác minh',
    'badge_pending' => 'Đang chờ',
    'badge_needs_resubmit' => 'Cần gửi lại',
    'badge_not_started' => 'Chưa bắt đầu',

    // Shared buttons
    'back_to_dashboard' => 'Quay lại trang tổng quan',
    'back_to_settings' => 'Quay lại Cài đặt',
    'message_support' => 'Nhắn tin cho bộ phận hỗ trợ',
    'submit_for_review' => 'Gửi để xét duyệt',
    'verify_identity' => 'Xác minh danh tính của bạn',
    'verify_address' => 'Xác minh địa chỉ của bạn',
    'last_submission_rejected' => 'Yêu cầu gần nhất của bạn chưa được duyệt.',

    // Send Money gate (kyc-required.blade.php)
    'kyc_gate_pending_title' => 'Chuyển tiền sẽ được mở khóa ngay khi giấy tờ tùy thân của bạn được xét duyệt',
    'kyc_gate_pending_body' => 'Chúng tôi đã nhận được giấy tờ tùy thân và ảnh của bạn. Đội ngũ của chúng tôi đang xét duyệt, thường trong vòng một ngày. Trong lúc đó, mọi tính năng khác trên tài khoản của bạn vẫn hoạt động bình thường.',
    'kyc_gate_rejected_title' => 'Chuyển tiền cần một lượt xác minh giấy tờ tùy thân mới',
    'kyc_gate_rejected_body' => 'Yêu cầu gần nhất của bạn chưa được duyệt. Hãy xem lại và gửi lại. Việc này chỉ mất một phút.',
    'kyc_gate_not_started_title' => 'Xác minh danh tính để chuyển tiền',
    'kyc_gate_not_started_body' => 'Tải lên giấy tờ tùy thân và một ảnh selfie nhanh. Đây là bước cuối cùng để mở khóa hoàn toàn tài khoản của bạn. Mọi thứ khác đã hoạt động sẵn.',

    // Identity verification page (kyc-verify.blade.php)
    'kyc_approved_title' => 'Bạn đã được xác minh',
    'kyc_approved_body' => 'Giấy tờ tùy thân và ảnh của bạn đã được xét duyệt và chấp thuận. Chuyển tiền đã được mở khóa hoàn toàn.',
    'kyc_approved_body_dated' => 'Giấy tờ tùy thân và ảnh của bạn đã được xét duyệt và chấp thuận vào ngày :date. Chuyển tiền đã được mở khóa hoàn toàn.',
    'kyc_pending_title' => 'Giấy tờ tùy thân của bạn đang được xét duyệt',
    'kyc_pending_body' => 'Bạn đã gửi một :type và một ảnh :time. Đội ngũ của chúng tôi xét duyệt thủ công, thường trong vòng một ngày. Chúng tôi sẽ thông báo cho bạn ngay khi có quyết định. Trong lúc đó, mọi thứ khác trên tài khoản của bạn vẫn hoạt động bình thường; chỉ có Chuyển tiền là bị khóa cho đến lúc đó.',
    'kyc_resubmit_notice' => 'Vui lòng thử lại bên dưới với một ảnh giấy tờ tùy thân rõ nét, chưa chỉnh sửa và một ảnh selfie đủ sáng.',
    'kyc_form_title' => 'Bước cuối cùng',
    'kyc_form_body' => 'Tải lên ảnh chụp một giấy tờ tùy thân hợp lệ do chính phủ cấp và một ảnh selfie của chính bạn. Đây là cách chúng tôi xác nhận đúng là bạn trước khi mở khóa hoàn toàn tài khoản của bạn. Không bên thứ ba nào nhìn thấy những ảnh này, chỉ có đội ngũ của chúng tôi xét duyệt thủ công. Bạn có thể tiếp tục sử dụng tài khoản bình thường trong khi chờ xét duyệt; chỉ có Chuyển tiền phải chờ đến khi được chấp thuận.',
    'id_type_label' => 'Loại giấy tờ',
    'select_id_type' => 'Chọn loại giấy tờ',
    'id_photo_label' => 'Ảnh giấy tờ tùy thân của bạn (mặt trước)',
    'selfie_label' => 'Một ảnh selfie của bạn',
    'selfie_hint' => '(đủ sáng, khuôn mặt hiện rõ)',

    // Address verification page (address-verify.blade.php)
    'address_approved_title' => 'Bạn đã được xác minh đầy đủ',
    'address_approved_body' => 'Bằng chứng địa chỉ của bạn đã được xét duyệt và chấp thuận. Bạn hiện đã ở Cấp 3 — hạn mức hàng ngày của bạn là :limit.',
    'address_approved_body_dated' => 'Bằng chứng địa chỉ của bạn đã được xét duyệt và chấp thuận vào ngày :date. Bạn hiện đã ở Cấp 3 — hạn mức hàng ngày của bạn là :limit.',
    'address_pending_title' => 'Tài liệu của bạn đang được xét duyệt',
    'address_pending_body' => 'Bạn đã gửi một :type :time. Đội ngũ của chúng tôi xét duyệt thủ công, thường trong vòng một ngày. Hạn mức hàng ngày hiện tại của bạn vẫn giữ ở mức :limit cho đến lúc đó.',
    'address_resubmit_notice' => 'Vui lòng thử lại bên dưới với một tài liệu rõ nét, gần đây.',
    'address_form_title' => 'Nâng hạn mức hàng ngày của bạn',
    'address_form_body' => 'Tải lên một tài liệu gần đây thể hiện tên và địa chỉ nhà của bạn — hóa đơn tiện ích, sao kê ngân hàng hoặc hợp đồng thuê nhà đều được chấp nhận. Đây là bước xác minh cuối cùng: nó nâng hạn mức Chuyển tiền và Rút tiền hàng ngày của bạn từ :from lên :to. Chỉ đội ngũ của chúng tôi xét duyệt tài liệu này, thường trong vòng một ngày.',
    'document_type_label' => 'Loại tài liệu',
    'select_document_type' => 'Chọn loại tài liệu',
    'document_label' => 'Tài liệu',
    'document_hint' => '(JPG, PNG hoặc PDF, có ngày trong vòng 3 tháng gần đây)',

    // Document type option labels (KycController/AddressVerificationController DOCUMENT_TYPES, and the models' documentTypeLabel())
    'doc_drivers_license' => 'Giấy phép lái xe',
    'doc_state_id' => 'Thẻ căn cước do nhà nước cấp',
    'doc_passport' => 'Hộ chiếu Hoa Kỳ',
    'doc_other_id' => 'Giấy tờ tùy thân khác do chính phủ cấp',
    'doc_utility_bill' => 'Hóa đơn tiện ích',
    'doc_bank_statement' => 'Sao kê ngân hàng',
    'doc_tenancy_agreement' => 'Hợp đồng thuê nhà',
    'doc_other_address' => 'Bằng chứng địa chỉ khác',

    // Controller flash messages
    'kyc_submitted_status' => 'Cảm ơn bạn — chúng tôi đã nhận được giấy tờ tùy thân của bạn. Tài khoản của bạn vẫn sẵn sàng sử dụng trong khi đội ngũ của chúng tôi xét duyệt, thường trong vòng một ngày. Chúng tôi sẽ báo cho bạn ngay khi có quyết định.',
    'address_submitted_status' => 'Cảm ơn bạn — chúng tôi đã nhận được tài liệu của bạn. Chúng tôi sẽ báo cho bạn ngay khi có quyết định, thường trong vòng một ngày.',
    'address_verify_identity_first' => 'Hãy xác minh danh tính trước — xác minh địa chỉ là bước tiếp theo sau đó.',
];
