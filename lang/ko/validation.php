<?php

/**
 * Laravel never shipped a published copy of this file in this app, so every
 * validation error on every form (register, login, KYC uploads, transfer
 * amounts, everything) was always rendered in English regardless of locale
 * — Laravel silently falls back to its own internal English copy when an
 * app doesn't have its own lang/{locale}/validation.php. This is a curated
 * subset covering the rules this app's forms actually use (not Laravel's
 * full ~100-key default), so it's worth checking here first before adding a
 * brand new validation rule to any form — any rule key not listed below
 * still falls back to Laravel's built-in English message rather than
 * erroring, since app.fallback_locale is 'en'.
 */
return [
    'accepted' => ':attribute 항목에 동의해야 합니다.',
    'active_url' => ':attribute 항목은 유효한 URL이 아닙니다.',
    'after' => ':attribute 항목은 :date 이후의 날짜여야 합니다.',
    'after_or_equal' => ':attribute 항목은 :date 이후이거나 같은 날짜여야 합니다.',
    'alpha' => ':attribute 항목은 문자만 포함할 수 있습니다.',
    'alpha_dash' => ':attribute 항목은 문자, 숫자, 대시(-), 밑줄(_)만 포함할 수 있습니다.',
    'alpha_num' => ':attribute 항목은 문자와 숫자만 포함할 수 있습니다.',
    'array' => ':attribute 항목은 배열이어야 합니다.',
    'before' => ':attribute 항목은 :date 이전의 날짜여야 합니다.',
    'before_or_equal' => ':attribute 항목은 :date 이전이거나 같은 날짜여야 합니다.',
    'between' => [
        'array' => ':attribute 항목은 :min개에서 :max개 사이여야 합니다.',
        'file' => ':attribute 항목은 :min에서 :max 킬로바이트 사이여야 합니다.',
        'numeric' => ':attribute 항목은 :min에서 :max 사이여야 합니다.',
        'string' => ':attribute 항목은 :min자에서 :max자 사이여야 합니다.',
    ],
    'boolean' => ':attribute 필드는 true 또는 false여야 합니다.',
    'confirmed' => ':attribute 확인 값이 일치하지 않습니다.',
    'current_password' => '비밀번호가 올바르지 않습니다.',
    'date' => ':attribute 항목은 유효한 날짜가 아닙니다.',
    'date_equals' => ':attribute 항목은 :date와 같은 날짜여야 합니다.',
    'declined' => ':attribute 항목을 거부해야 합니다.',
    'different' => ':attribute 항목과 :other 항목은 서로 달라야 합니다.',
    'digits' => ':attribute 항목은 :digits자리 숫자여야 합니다.',
    'digits_between' => ':attribute 항목은 :min자리에서 :max자리 사이여야 합니다.',
    'distinct' => ':attribute 필드에 중복된 값이 있습니다.',
    'email' => ':attribute 항목은 유효한 이메일 주소여야 합니다.',
    'exists' => '선택한 :attribute 항목이 유효하지 않습니다.',
    'file' => ':attribute 항목은 파일이어야 합니다.',
    'filled' => ':attribute 필드에는 값이 있어야 합니다.',
    'gt' => [
        'array' => ':attribute 항목은 :value개보다 많아야 합니다.',
        'file' => ':attribute 항목은 :value 킬로바이트보다 커야 합니다.',
        'numeric' => ':attribute 항목은 :value보다 커야 합니다.',
        'string' => ':attribute 항목은 :value자보다 많아야 합니다.',
    ],
    'gte' => [
        'array' => ':attribute 항목은 :value개 이상이어야 합니다.',
        'file' => ':attribute 항목은 :value 킬로바이트 이상이어야 합니다.',
        'numeric' => ':attribute 항목은 :value 이상이어야 합니다.',
        'string' => ':attribute 항목은 :value자 이상이어야 합니다.',
    ],
    'image' => ':attribute 항목은 이미지여야 합니다.',
    'in' => '선택한 :attribute 항목이 유효하지 않습니다.',
    'in_array' => ':attribute 필드가 :other에 존재하지 않습니다.',
    'integer' => ':attribute 항목은 정수여야 합니다.',
    'ip' => ':attribute 항목은 유효한 IP 주소여야 합니다.',
    'json' => ':attribute 항목은 유효한 JSON 문자열이어야 합니다.',
    'lt' => [
        'array' => ':attribute 항목은 :value개보다 적어야 합니다.',
        'file' => ':attribute 항목은 :value 킬로바이트보다 작아야 합니다.',
        'numeric' => ':attribute 항목은 :value보다 작아야 합니다.',
        'string' => ':attribute 항목은 :value자보다 적어야 합니다.',
    ],
    'lte' => [
        'array' => ':attribute 항목은 :value개를 초과할 수 없습니다.',
        'file' => ':attribute 항목은 :value 킬로바이트 이하여야 합니다.',
        'numeric' => ':attribute 항목은 :value 이하여야 합니다.',
        'string' => ':attribute 항목은 :value자 이하여야 합니다.',
    ],
    'max' => [
        'array' => ':attribute 항목은 :max개를 초과할 수 없습니다.',
        'file' => ':attribute 항목은 :max 킬로바이트를 초과할 수 없습니다.',
        'numeric' => ':attribute 항목은 :max를 초과할 수 없습니다.',
        'string' => ':attribute 항목은 :max자를 초과할 수 없습니다.',
    ],
    'mimes' => ':attribute 항목은 다음 형식의 파일이어야 합니다: :values.',
    'min' => [
        'array' => ':attribute 항목은 최소 :min개 이상이어야 합니다.',
        'file' => ':attribute 항목은 최소 :min 킬로바이트여야 합니다.',
        'numeric' => ':attribute 항목은 최소 :min 이상이어야 합니다.',
        'string' => ':attribute 항목은 최소 :min자 이상이어야 합니다.',
    ],
    'not_in' => '선택한 :attribute 항목이 유효하지 않습니다.',
    'not_regex' => ':attribute 형식이 유효하지 않습니다.',
    'numeric' => ':attribute 항목은 숫자여야 합니다.',
    'present' => ':attribute 필드는 존재해야 합니다.',
    'prohibited' => ':attribute 필드는 허용되지 않습니다.',
    'prohibited_if' => ':other가 :value일 때 :attribute 필드는 허용되지 않습니다.',
    'prohibited_unless' => ':other가 :values 중 하나가 아니면 :attribute 필드는 허용되지 않습니다.',
    'regex' => ':attribute 형식이 유효하지 않습니다.',
    'required' => ':attribute 필드는 필수입니다.',
    'required_array_keys' => ':attribute 필드에는 다음 항목이 포함되어야 합니다: :values.',
    'required_if' => ':other가 :value일 때 :attribute 필드는 필수입니다.',
    'required_unless' => ':other가 :values 중 하나가 아니면 :attribute 필드는 필수입니다.',
    'required_with' => ':values가 있을 때 :attribute 필드는 필수입니다.',
    'required_with_all' => ':values가 모두 있을 때 :attribute 필드는 필수입니다.',
    'required_without' => ':values가 없을 때 :attribute 필드는 필수입니다.',
    'required_without_all' => ':values가 모두 없을 때 :attribute 필드는 필수입니다.',
    'same' => ':attribute 항목과 :other 항목이 일치해야 합니다.',
    'size' => [
        'array' => ':attribute 항목은 :size개의 항목을 포함해야 합니다.',
        'file' => ':attribute 항목은 :size 킬로바이트여야 합니다.',
        'numeric' => ':attribute 항목은 :size여야 합니다.',
        'string' => ':attribute 항목은 :size자여야 합니다.',
    ],
    'starts_with' => ':attribute 항목은 다음 중 하나로 시작해야 합니다: :values.',
    'string' => ':attribute 항목은 문자열이어야 합니다.',
    'timezone' => ':attribute 항목은 유효한 시간대여야 합니다.',
    'unique' => ':attribute 항목은 이미 사용 중입니다.',
    'uploaded' => ':attribute 업로드에 실패했습니다.',
    'url' => ':attribute 형식이 유효하지 않습니다.',
    'uuid' => ':attribute 항목은 유효한 UUID여야 합니다.',

    /*
     * Custom validation lines — 'attribute.rule' => 'message', to override a
     * specific field+rule combination without changing the message for
     * every other field using the same rule.
     */
    'custom' => [],

    /*
     * Human-readable field names substituted into ":attribute" above.
     * Covers the field names actually used across this app's forms; any
     * field not listed here falls back to its raw snake_case name.
     */
    'attributes' => [
        'email' => '이메일 주소',
        'password' => '비밀번호',
        'password_confirmation' => '비밀번호 확인',
        'first_name' => '이름',
        'last_name' => '성',
        'middle_name' => '중간 이름',
        'phone' => '전화번호',
        'code' => '코드',
        'recovery_code' => '복구 코드',
        'remember' => '로그인 상태 유지',
        'amount' => '금액',
        'reference' => '참조 번호',
        'name' => '이름',
        'address' => '주소',
        'city' => '시/군/구',
        'state' => '주/도',
        'zip' => '우편번호',
        'country' => '국가',
        'document' => '문서',
        'document_type' => '문서 유형',
        'note' => '메모',
        'category' => '카테고리',
        'linked_account_id' => '연결된 계좌',
        'source_type' => '출처',
        'destination_type' => '대상',
    ],
];
