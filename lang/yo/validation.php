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
    'accepted' => 'Ó gbọ́dọ̀ tẹ́wọ́ gbà :attribute.',
    'active_url' => ':attribute kì í ṣe URL tí ó tọ́.',
    'after' => ':attribute gbọ́dọ̀ jẹ́ ọjọ́ tí ó tẹ̀lé :date.',
    'after_or_equal' => ':attribute gbọ́dọ̀ jẹ́ ọjọ́ tí ó tẹ̀lé tàbí tí ó dọ́gba pẹ̀lú :date.',
    'alpha' => ':attribute lè ní lẹ́tà nìkan.',
    'alpha_dash' => ':attribute lè ní lẹ́tà, nọ́mbà, àti àmì (- àti _) nìkan.',
    'alpha_num' => ':attribute lè ní lẹ́tà àti nọ́mbà nìkan.',
    'array' => ':attribute gbọ́dọ̀ jẹ́ akójọ (array).',
    'before' => ':attribute gbọ́dọ̀ jẹ́ ọjọ́ tí ó ṣáájú :date.',
    'before_or_equal' => ':attribute gbọ́dọ̀ jẹ́ ọjọ́ tí ó ṣáájú tàbí tí ó dọ́gba pẹ̀lú :date.',
    'between' => [
        'array' => ':attribute gbọ́dọ̀ ní ohun kan láàrín :min àti :max.',
        'file' => ':attribute gbọ́dọ̀ jẹ́ láàrín :min àti :max kìlóbáìtì.',
        'numeric' => ':attribute gbọ́dọ̀ wà láàrín :min àti :max.',
        'string' => ':attribute gbọ́dọ̀ ní lẹ́tà láàrín :min àti :max.',
    ],
    'boolean' => ':attribute gbọ́dọ̀ jẹ́ òtítọ́ tàbí irọ́.',
    'confirmed' => 'Ìjẹ́rìísí :attribute kò bá a mu.',
    'current_password' => 'Ọ̀rọ̀ ìpamọ́ náà kò tọ́.',
    'date' => ':attribute kì í ṣe ọjọ́ tí ó tọ́.',
    'date_equals' => ':attribute gbọ́dọ̀ jẹ́ ọjọ́ tí ó dọ́gba pẹ̀lú :date.',
    'declined' => ':attribute gbọ́dọ̀ jẹ́ èyí tí a kọ̀.',
    'different' => ':attribute àti :other gbọ́dọ̀ yàtọ̀ sí ara wọn.',
    'digits' => ':attribute gbọ́dọ̀ jẹ́ nọ́mbà :digits.',
    'digits_between' => ':attribute gbọ́dọ̀ jẹ́ láàrín nọ́mbà :min àti :max.',
    'distinct' => ':attribute ní ìye tí ó ti wà tẹ́lẹ̀.',
    'email' => ':attribute gbọ́dọ̀ jẹ́ àdírẹ́sì ímeèlì tí ó tọ́.',
    'exists' => ':attribute tí a yàn kò tọ́.',
    'file' => ':attribute gbọ́dọ̀ jẹ́ fáìlì.',
    'filled' => ':attribute gbọ́dọ̀ ní ìye kan.',
    'gt' => [
        'array' => ':attribute gbọ́dọ̀ ní ohun tí ó ju :value lọ.',
        'file' => ':attribute gbọ́dọ̀ tóbi ju :value kìlóbáìtì lọ.',
        'numeric' => ':attribute gbọ́dọ̀ tóbi ju :value lọ.',
        'string' => ':attribute gbọ́dọ̀ ní lẹ́tà tí ó ju :value lọ.',
    ],
    'gte' => [
        'array' => ':attribute gbọ́dọ̀ ní ohun tí ó tó :value tàbí jù bẹ́ẹ̀ lọ.',
        'file' => ':attribute gbọ́dọ̀ tóbi tàbí dọ́gba pẹ̀lú :value kìlóbáìtì.',
        'numeric' => ':attribute gbọ́dọ̀ tóbi tàbí dọ́gba pẹ̀lú :value.',
        'string' => ':attribute gbọ́dọ̀ ní lẹ́tà tí ó tó :value tàbí jù bẹ́ẹ̀ lọ.',
    ],
    'image' => ':attribute gbọ́dọ̀ jẹ́ àwòrán.',
    'in' => ':attribute tí a yàn kò tọ́.',
    'in_array' => ':attribute kò sí nínú :other.',
    'integer' => ':attribute gbọ́dọ̀ jẹ́ odidi nọ́mbà.',
    'ip' => ':attribute gbọ́dọ̀ jẹ́ àdírẹ́sì IP tí ó tọ́.',
    'json' => ':attribute gbọ́dọ̀ jẹ́ ọ̀rọ̀ JSON tí ó tọ́.',
    'lt' => [
        'array' => ':attribute gbọ́dọ̀ ní ohun tí ó dín ju :value lọ.',
        'file' => ':attribute gbọ́dọ̀ kéré ju :value kìlóbáìtì lọ.',
        'numeric' => ':attribute gbọ́dọ̀ kéré ju :value lọ.',
        'string' => ':attribute gbọ́dọ̀ ní lẹ́tà tí ó dín ju :value lọ.',
    ],
    'lte' => [
        'array' => ':attribute kò gbọ́dọ̀ ní ohun tí ó ju :value lọ.',
        'file' => ':attribute gbọ́dọ̀ kéré tàbí dọ́gba pẹ̀lú :value kìlóbáìtì.',
        'numeric' => ':attribute gbọ́dọ̀ kéré tàbí dọ́gba pẹ̀lú :value.',
        'string' => ':attribute kò gbọ́dọ̀ ní lẹ́tà tí ó ju :value lọ.',
    ],
    'max' => [
        'array' => ':attribute kò gbọ́dọ̀ ní ohun tí ó ju :max lọ.',
        'file' => ':attribute kò gbọ́dọ̀ tóbi ju :max kìlóbáìtì lọ.',
        'numeric' => ':attribute kò gbọ́dọ̀ tóbi ju :max lọ.',
        'string' => ':attribute kò gbọ́dọ̀ ní lẹ́tà tí ó ju :max lọ.',
    ],
    'mimes' => ':attribute gbọ́dọ̀ jẹ́ fáìlì onírúurú: :values.',
    'min' => [
        'array' => ':attribute gbọ́dọ̀ ní ohun tí ó kéré jù bí :min.',
        'file' => ':attribute gbọ́dọ̀ jẹ́ o kéré jù bí :min kìlóbáìtì.',
        'numeric' => ':attribute gbọ́dọ̀ jẹ́ o kéré jù bí :min.',
        'string' => ':attribute gbọ́dọ̀ ní lẹ́tà tí ó kéré jù bí :min.',
    ],
    'not_in' => ':attribute tí a yàn kò tọ́.',
    'not_regex' => 'Ọ̀nà kíkọ :attribute kò tọ́.',
    'numeric' => ':attribute gbọ́dọ̀ jẹ́ nọ́mbà.',
    'present' => ':attribute gbọ́dọ̀ wà.',
    'prohibited' => 'A kò gbà láyè fún :attribute.',
    'prohibited_if' => 'A kò gbà láyè fún :attribute nígbà tí :other bá jẹ́ :value.',
    'prohibited_unless' => 'A kò gbà láyè fún :attribute àyàfi tí :other bá wà nínú :values.',
    'regex' => 'Ọ̀nà kíkọ :attribute kò tọ́.',
    'required' => ':attribute jẹ́ dandan.',
    'required_array_keys' => ':attribute gbọ́dọ̀ ní àwọn nǹkan fún: :values.',
    'required_if' => ':attribute jẹ́ dandan nígbà tí :other bá jẹ́ :value.',
    'required_unless' => ':attribute jẹ́ dandan àyàfi tí :other bá wà nínú :values.',
    'required_with' => ':attribute jẹ́ dandan nígbà tí :values bá wà.',
    'required_with_all' => ':attribute jẹ́ dandan nígbà tí :values bá wà.',
    'required_without' => ':attribute jẹ́ dandan nígbà tí :values kò bá sí.',
    'required_without_all' => ':attribute jẹ́ dandan nígbà tí kò sí ọ̀kankan nínú :values.',
    'same' => ':attribute àti :other gbọ́dọ̀ bára wọn mu.',
    'size' => [
        'array' => ':attribute gbọ́dọ̀ ní ohun :size.',
        'file' => ':attribute gbọ́dọ̀ jẹ́ :size kìlóbáìtì.',
        'numeric' => ':attribute gbọ́dọ̀ jẹ́ :size.',
        'string' => ':attribute gbọ́dọ̀ ní lẹ́tà :size.',
    ],
    'starts_with' => ':attribute gbọ́dọ̀ bẹ̀rẹ̀ pẹ̀lú ọ̀kan lára àwọn èyí: :values.',
    'string' => ':attribute gbọ́dọ̀ jẹ́ ọ̀rọ̀.',
    'timezone' => ':attribute gbọ́dọ̀ jẹ́ agbègbè àkókò tí ó tọ́.',
    'unique' => 'A ti lo :attribute yìí tẹ́lẹ̀.',
    'uploaded' => 'Kíkó :attribute wọlé kò yege.',
    'url' => 'Ọ̀nà kíkọ :attribute kò tọ́.',
    'uuid' => ':attribute gbọ́dọ̀ jẹ́ UUID tí ó tọ́.',

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
        'email' => 'àdírẹ́sì ímeèlì',
        'password' => 'ọ̀rọ̀ ìpamọ́',
        'password_confirmation' => 'ìjẹ́rìísí ọ̀rọ̀ ìpamọ́',
        'first_name' => 'orúkọ àkọ́kọ́',
        'last_name' => 'orúkọ ìdílé',
        'middle_name' => 'orúkọ àárín',
        'phone' => 'nọ́mbà fóònù',
        'code' => 'nọ́mbà',
        'recovery_code' => 'nọ́mbà ìgbàpadà',
        'remember' => 'rántí mi',
        'amount' => 'iye owó',
        'reference' => 'àmì ìtọ́kasí',
        'name' => 'orúkọ',
        'address' => 'àdírẹ́sì',
        'city' => 'ìlú',
        'state' => 'ìpínlẹ̀',
        'zip' => 'nọ́mbà agbègbè',
        'country' => 'orílẹ̀-èdè',
        'document' => 'ìwé',
        'document_type' => 'irú ìwé',
        'note' => 'àkọsílẹ̀',
        'category' => 'ẹ̀ka',
        'linked_account_id' => 'àkọọ́lẹ̀ tí a so pọ̀',
        'source_type' => 'orísun',
        'destination_type' => 'ibi tí a ń fi ránṣẹ́ sí',
    ],
];
