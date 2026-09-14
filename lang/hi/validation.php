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
    'accepted' => ':attribute को स्वीकार किया जाना चाहिए।',
    'active_url' => ':attribute एक मान्य URL नहीं है।',
    'after' => ':attribute :date के बाद की तारीख होनी चाहिए।',
    'after_or_equal' => ':attribute :date के बाद या उसके बराबर तारीख होनी चाहिए।',
    'alpha' => ':attribute में केवल अक्षर हो सकते हैं।',
    'alpha_dash' => ':attribute में केवल अक्षर, संख्याएँ, डैश और अंडरस्कोर हो सकते हैं।',
    'alpha_num' => ':attribute में केवल अक्षर और संख्याएँ हो सकती हैं।',
    'array' => ':attribute एक array होना चाहिए।',
    'before' => ':attribute :date से पहले की तारीख होनी चाहिए।',
    'before_or_equal' => ':attribute :date से पहले या उसके बराबर तारीख होनी चाहिए।',
    'between' => [
        'array' => ':attribute में :min और :max आइटम के बीच होने चाहिए।',
        'file' => ':attribute :min और :max किलोबाइट के बीच होना चाहिए।',
        'numeric' => ':attribute :min और :max के बीच होना चाहिए।',
        'string' => ':attribute :min और :max अक्षरों के बीच होना चाहिए।',
    ],
    'boolean' => ':attribute फ़ील्ड true या false होनी चाहिए।',
    'confirmed' => ':attribute की पुष्टि मेल नहीं खाती।',
    'current_password' => 'पासवर्ड गलत है।',
    'date' => ':attribute एक मान्य तारीख नहीं है।',
    'date_equals' => ':attribute :date के बराबर तारीख होनी चाहिए।',
    'declined' => ':attribute को अस्वीकार किया जाना चाहिए।',
    'different' => ':attribute और :other अलग होने चाहिए।',
    'digits' => ':attribute :digits अंकों का होना चाहिए।',
    'digits_between' => ':attribute :min और :max अंकों के बीच होना चाहिए।',
    'distinct' => ':attribute फ़ील्ड का मान डुप्लिकेट है।',
    'email' => ':attribute एक मान्य ईमेल पता होना चाहिए।',
    'exists' => 'चयनित :attribute अमान्य है।',
    'file' => ':attribute एक फ़ाइल होनी चाहिए।',
    'filled' => ':attribute फ़ील्ड में एक मान होना चाहिए।',
    'gt' => [
        'array' => ':attribute में :value से अधिक आइटम होने चाहिए।',
        'file' => ':attribute :value किलोबाइट से बड़ा होना चाहिए।',
        'numeric' => ':attribute :value से बड़ा होना चाहिए।',
        'string' => ':attribute :value अक्षरों से बड़ा होना चाहिए।',
    ],
    'gte' => [
        'array' => ':attribute में :value या उससे अधिक आइटम होने चाहिए।',
        'file' => ':attribute :value किलोबाइट से बड़ा या उसके बराबर होना चाहिए।',
        'numeric' => ':attribute :value से बड़ा या उसके बराबर होना चाहिए।',
        'string' => ':attribute :value अक्षरों से बड़ा या उसके बराबर होना चाहिए।',
    ],
    'image' => ':attribute एक छवि होनी चाहिए।',
    'in' => 'चयनित :attribute अमान्य है।',
    'in_array' => ':attribute फ़ील्ड :other में मौजूद नहीं है।',
    'integer' => ':attribute एक पूर्णांक होना चाहिए।',
    'ip' => ':attribute एक मान्य IP पता होना चाहिए।',
    'json' => ':attribute एक मान्य JSON स्ट्रिंग होनी चाहिए।',
    'lt' => [
        'array' => ':attribute में :value से कम आइटम होने चाहिए।',
        'file' => ':attribute :value किलोबाइट से कम होना चाहिए।',
        'numeric' => ':attribute :value से कम होना चाहिए।',
        'string' => ':attribute :value अक्षरों से कम होना चाहिए।',
    ],
    'lte' => [
        'array' => ':attribute में :value से अधिक आइटम नहीं होने चाहिए।',
        'file' => ':attribute :value किलोबाइट से कम या उसके बराबर होना चाहिए।',
        'numeric' => ':attribute :value से कम या उसके बराबर होना चाहिए।',
        'string' => ':attribute :value अक्षरों से कम या उसके बराबर होना चाहिए।',
    ],
    'max' => [
        'array' => ':attribute में :max से अधिक आइटम नहीं होने चाहिए।',
        'file' => ':attribute :max किलोबाइट से बड़ा नहीं होना चाहिए।',
        'numeric' => ':attribute :max से बड़ा नहीं होना चाहिए।',
        'string' => ':attribute :max अक्षरों से बड़ा नहीं होना चाहिए।',
    ],
    'mimes' => ':attribute इस प्रकार की फ़ाइल होनी चाहिए: :values।',
    'min' => [
        'array' => ':attribute में कम से कम :min आइटम होने चाहिए।',
        'file' => ':attribute कम से कम :min किलोबाइट का होना चाहिए।',
        'numeric' => ':attribute कम से कम :min होना चाहिए।',
        'string' => ':attribute कम से कम :min अक्षरों का होना चाहिए।',
    ],
    'not_in' => 'चयनित :attribute अमान्य है।',
    'not_regex' => ':attribute प्रारूप अमान्य है।',
    'numeric' => ':attribute एक संख्या होनी चाहिए।',
    'present' => ':attribute फ़ील्ड मौजूद होनी चाहिए।',
    'prohibited' => ':attribute फ़ील्ड निषिद्ध है।',
    'prohibited_if' => 'जब :other, :value हो तो :attribute फ़ील्ड निषिद्ध है।',
    'prohibited_unless' => 'जब तक :other, :values में न हो, :attribute फ़ील्ड निषिद्ध है।',
    'regex' => ':attribute प्रारूप अमान्य है।',
    'required' => ':attribute फ़ील्ड आवश्यक है।',
    'required_array_keys' => ':attribute फ़ील्ड में इनके लिए प्रविष्टियाँ होनी चाहिए: :values।',
    'required_if' => 'जब :other, :value हो तो :attribute फ़ील्ड आवश्यक है।',
    'required_unless' => 'जब तक :other, :values में न हो, :attribute फ़ील्ड आवश्यक है।',
    'required_with' => 'जब :values मौजूद हो तो :attribute फ़ील्ड आवश्यक है।',
    'required_with_all' => 'जब :values मौजूद हों तो :attribute फ़ील्ड आवश्यक है।',
    'required_without' => 'जब :values मौजूद न हो तो :attribute फ़ील्ड आवश्यक है।',
    'required_without_all' => 'जब :values में से कोई भी मौजूद न हो तो :attribute फ़ील्ड आवश्यक है।',
    'same' => ':attribute और :other मेल खाने चाहिए।',
    'size' => [
        'array' => ':attribute में :size आइटम होने चाहिए।',
        'file' => ':attribute :size किलोबाइट का होना चाहिए।',
        'numeric' => ':attribute :size होना चाहिए।',
        'string' => ':attribute :size अक्षरों का होना चाहिए।',
    ],
    'starts_with' => ':attribute इनमें से किसी एक से शुरू होना चाहिए: :values।',
    'string' => ':attribute एक स्ट्रिंग होनी चाहिए।',
    'timezone' => ':attribute एक मान्य टाइमज़ोन होना चाहिए।',
    'unique' => ':attribute पहले से लिया जा चुका है।',
    'uploaded' => ':attribute अपलोड होने में विफल रहा।',
    'url' => ':attribute प्रारूप अमान्य है।',
    'uuid' => ':attribute एक मान्य UUID होना चाहिए।',

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
        'email' => 'ईमेल पता',
        'password' => 'पासवर्ड',
        'password_confirmation' => 'पासवर्ड की पुष्टि',
        'first_name' => 'पहला नाम',
        'last_name' => 'अंतिम नाम',
        'middle_name' => 'मध्य नाम',
        'phone' => 'फ़ोन नंबर',
        'code' => 'कोड',
        'recovery_code' => 'रिकवरी कोड',
        'remember' => 'मुझे याद रखें',
        'amount' => 'राशि',
        'reference' => 'संदर्भ',
        'name' => 'नाम',
        'address' => 'पता',
        'city' => 'शहर',
        'state' => 'राज्य',
        'zip' => 'ZIP कोड',
        'country' => 'देश',
        'document' => 'दस्तावेज़',
        'document_type' => 'दस्तावेज़ प्रकार',
        'note' => 'नोट',
        'category' => 'श्रेणी',
        'linked_account_id' => 'लिंक किया गया खाता',
        'source_type' => 'स्रोत',
        'destination_type' => 'गंतव्य',
    ],
];
