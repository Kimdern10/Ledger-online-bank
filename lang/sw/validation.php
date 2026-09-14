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
    'accepted' => 'Lazima :attribute ikubaliwe.',
    'active_url' => ':attribute si URL sahihi.',
    'after' => ':attribute lazima iwe tarehe baada ya :date.',
    'after_or_equal' => ':attribute lazima iwe tarehe baada ya au sawa na :date.',
    'alpha' => ':attribute inaweza kuwa na herufi tu.',
    'alpha_dash' => ':attribute inaweza kuwa na herufi, nambari, mistari mifupi na chini pekee.',
    'alpha_num' => ':attribute inaweza kuwa na herufi na nambari tu.',
    'array' => ':attribute lazima iwe orodha (array).',
    'before' => ':attribute lazima iwe tarehe kabla ya :date.',
    'before_or_equal' => ':attribute lazima iwe tarehe kabla ya au sawa na :date.',
    'between' => [
        'array' => ':attribute lazima iwe na kati ya vitu :min na :max.',
        'file' => ':attribute lazima iwe kati ya kilobaiti :min na :max.',
        'numeric' => ':attribute lazima iwe kati ya :min na :max.',
        'string' => ':attribute lazima iwe kati ya herufi :min na :max.',
    ],
    'boolean' => 'Sehemu ya :attribute lazima iwe kweli au si kweli.',
    'confirmed' => 'Uthibitisho wa :attribute hauendani.',
    'current_password' => 'Nenosiri si sahihi.',
    'date' => ':attribute si tarehe sahihi.',
    'date_equals' => ':attribute lazima iwe tarehe sawa na :date.',
    'declined' => ':attribute lazima ikatawe.',
    'different' => ':attribute na :other lazima viwe tofauti.',
    'digits' => ':attribute lazima iwe na tarakimu :digits.',
    'digits_between' => ':attribute lazima iwe na tarakimu kati ya :min na :max.',
    'distinct' => 'Sehemu ya :attribute ina thamani inayorudiwa.',
    'email' => ':attribute lazima iwe barua pepe sahihi.',
    'exists' => ':attribute iliyochaguliwa si sahihi.',
    'file' => ':attribute lazima iwe faili.',
    'filled' => 'Sehemu ya :attribute lazima iwe na thamani.',
    'gt' => [
        'array' => ':attribute lazima iwe na zaidi ya vitu :value.',
        'file' => ':attribute lazima iwe kubwa kuliko kilobaiti :value.',
        'numeric' => ':attribute lazima iwe kubwa kuliko :value.',
        'string' => ':attribute lazima iwe na herufi zaidi ya :value.',
    ],
    'gte' => [
        'array' => ':attribute lazima iwe na vitu :value au zaidi.',
        'file' => ':attribute lazima iwe kubwa kuliko au sawa na kilobaiti :value.',
        'numeric' => ':attribute lazima iwe kubwa kuliko au sawa na :value.',
        'string' => ':attribute lazima iwe na herufi kubwa kuliko au sawa na :value.',
    ],
    'image' => ':attribute lazima iwe picha.',
    'in' => ':attribute iliyochaguliwa si sahihi.',
    'in_array' => 'Sehemu ya :attribute haipo katika :other.',
    'integer' => ':attribute lazima iwe nambari kamili.',
    'ip' => ':attribute lazima iwe anwani sahihi ya IP.',
    'json' => ':attribute lazima iwe herufi za JSON sahihi.',
    'lt' => [
        'array' => ':attribute lazima iwe na vitu chini ya :value.',
        'file' => ':attribute lazima iwe chini ya kilobaiti :value.',
        'numeric' => ':attribute lazima iwe chini ya :value.',
        'string' => ':attribute lazima iwe na herufi chini ya :value.',
    ],
    'lte' => [
        'array' => ':attribute lazima isiwe na vitu zaidi ya :value.',
        'file' => ':attribute lazima iwe chini ya au sawa na kilobaiti :value.',
        'numeric' => ':attribute lazima iwe chini ya au sawa na :value.',
        'string' => ':attribute lazima iwe na herufi chini ya au sawa na :value.',
    ],
    'max' => [
        'array' => ':attribute lazima isiwe na vitu zaidi ya :max.',
        'file' => ':attribute lazima isizidi kilobaiti :max.',
        'numeric' => ':attribute lazima isizidi :max.',
        'string' => ':attribute lazima isizidi herufi :max.',
    ],
    'mimes' => ':attribute lazima iwe faili ya aina: :values.',
    'min' => [
        'array' => ':attribute lazima iwe na angalau vitu :min.',
        'file' => ':attribute lazima iwe angalau kilobaiti :min.',
        'numeric' => ':attribute lazima iwe angalau :min.',
        'string' => ':attribute lazima iwe angalau herufi :min.',
    ],
    'not_in' => ':attribute iliyochaguliwa si sahihi.',
    'not_regex' => 'Muundo wa :attribute si sahihi.',
    'numeric' => ':attribute lazima iwe nambari.',
    'present' => 'Sehemu ya :attribute lazima iwepo.',
    'prohibited' => 'Sehemu ya :attribute imekatazwa.',
    'prohibited_if' => 'Sehemu ya :attribute imekatazwa wakati :other ni :value.',
    'prohibited_unless' => 'Sehemu ya :attribute imekatazwa isipokuwa :other iko ndani ya :values.',
    'regex' => 'Muundo wa :attribute si sahihi.',
    'required' => 'Sehemu ya :attribute inahitajika.',
    'required_array_keys' => 'Sehemu ya :attribute lazima iwe na viingizo vya: :values.',
    'required_if' => 'Sehemu ya :attribute inahitajika wakati :other ni :value.',
    'required_unless' => 'Sehemu ya :attribute inahitajika isipokuwa :other iko ndani ya :values.',
    'required_with' => 'Sehemu ya :attribute inahitajika wakati :values ipo.',
    'required_with_all' => 'Sehemu ya :attribute inahitajika wakati :values zipo.',
    'required_without' => 'Sehemu ya :attribute inahitajika wakati :values haipo.',
    'required_without_all' => 'Sehemu ya :attribute inahitajika wakati hakuna hata mojawapo ya :values iliyopo.',
    'same' => ':attribute na :other lazima vifanane.',
    'size' => [
        'array' => ':attribute lazima iwe na vitu :size.',
        'file' => ':attribute lazima iwe kilobaiti :size.',
        'numeric' => ':attribute lazima iwe :size.',
        'string' => ':attribute lazima iwe na herufi :size.',
    ],
    'starts_with' => ':attribute lazima ianze na mojawapo ya yafuatayo: :values.',
    'string' => ':attribute lazima iwe maandishi (string).',
    'timezone' => ':attribute lazima iwe eneo la saa sahihi.',
    'unique' => ':attribute tayari imechukuliwa.',
    'uploaded' => ':attribute imeshindwa kupakiwa.',
    'url' => 'Muundo wa :attribute si sahihi.',
    'uuid' => ':attribute lazima iwe UUID sahihi.',

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
        'email' => 'barua pepe',
        'password' => 'nenosiri',
        'password_confirmation' => 'uthibitisho wa nenosiri',
        'first_name' => 'jina la kwanza',
        'last_name' => 'jina la ukoo',
        'middle_name' => 'jina la kati',
        'phone' => 'namba ya simu',
        'code' => 'msimbo',
        'recovery_code' => 'msimbo wa dharura',
        'remember' => 'nikumbuke',
        'amount' => 'kiasi',
        'reference' => 'kumbukumbu',
        'name' => 'jina',
        'address' => 'anwani',
        'city' => 'jiji',
        'state' => 'jimbo',
        'zip' => 'msimbo wa posta',
        'country' => 'nchi',
        'document' => 'hati',
        'document_type' => 'aina ya hati',
        'note' => 'ujumbe',
        'category' => 'kundi',
        'linked_account_id' => 'akaunti iliyounganishwa',
        'source_type' => 'chanzo',
        'destination_type' => 'marudio',
    ],
];
