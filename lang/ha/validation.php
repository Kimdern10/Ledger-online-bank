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
    'accepted' => 'Dole ne a yarda da :attribute.',
    'active_url' => ':attribute ba ingantacciyar URL ba ce.',
    'after' => ':attribute dole ne ya zama kwanan wata bayan :date.',
    'after_or_equal' => ':attribute dole ne ya zama kwanan wata bayan ko daidai da :date.',
    'alpha' => ':attribute na iya ƙunsar haruffa kawai.',
    'alpha_dash' => ':attribute na iya ƙunsar haruffa, lambobi, alamun raba kalma da underscore kawai.',
    'alpha_num' => ':attribute na iya ƙunsar haruffa da lambobi kawai.',
    'array' => ':attribute dole ne ya zama tsararru (array).',
    'before' => ':attribute dole ne ya zama kwanan wata kafin :date.',
    'before_or_equal' => ':attribute dole ne ya zama kwanan wata kafin ko daidai da :date.',
    'between' => [
        'array' => ':attribute dole ne ya ƙunshi tsakanin abubuwa :min zuwa :max.',
        'file' => ':attribute dole ne ya kasance tsakanin kilobyte :min zuwa :max.',
        'numeric' => ':attribute dole ne ya kasance tsakanin :min zuwa :max.',
        'string' => ':attribute dole ne ya ƙunshi haruffa tsakanin :min zuwa :max.',
    ],
    'boolean' => 'Filin :attribute dole ne ya zama gaskiya ko ƙarya.',
    'confirmed' => 'Tabbatarwar :attribute bata dace ba.',
    'current_password' => 'Kalmar sirri ba daidai ba ce.',
    'date' => ':attribute ba ingantaccen kwanan wata ba ne.',
    'date_equals' => ':attribute dole ne ya zama kwanan wata daidai da :date.',
    'declined' => 'Dole ne a ƙi :attribute.',
    'different' => ':attribute da :other dole ne su bambanta.',
    'digits' => ':attribute dole ne ya kasance lamba mai lambobi :digits.',
    'digits_between' => ':attribute dole ne ya ƙunshi lambobi tsakanin :min zuwa :max.',
    'distinct' => 'Filin :attribute yana da ƙimar da aka maimaita.',
    'email' => ':attribute dole ne ya zama ingantacciyar adireshin imel.',
    'exists' => ':attribute da aka zaɓa ba ingantacce ba ne.',
    'file' => ':attribute dole ne ya zama fayil.',
    'filled' => 'Filin :attribute dole ne ya kasance da ƙima.',
    'gt' => [
        'array' => ':attribute dole ne ya ƙunshi abubuwa fiye da :value.',
        'file' => ':attribute dole ne ya fi kilobyte :value girma.',
        'numeric' => ':attribute dole ne ya fi :value girma.',
        'string' => ':attribute dole ne ya fi haruffa :value yawa.',
    ],
    'gte' => [
        'array' => ':attribute dole ne ya ƙunshi abubuwa :value ko fiye.',
        'file' => ':attribute dole ne ya kai ko ya fi kilobyte :value girma.',
        'numeric' => ':attribute dole ne ya kai ko ya fi :value girma.',
        'string' => ':attribute dole ne ya kai ko ya fi haruffa :value yawa.',
    ],
    'image' => ':attribute dole ne ya zama hoto.',
    'in' => ':attribute da aka zaɓa ba ingantacce ba ne.',
    'in_array' => 'Filin :attribute babu shi a cikin :other.',
    'integer' => ':attribute dole ne ya zama cikakkiyar lamba.',
    'ip' => ':attribute dole ne ya zama ingantacciyar adireshin IP.',
    'json' => ':attribute dole ne ya zama ingantacciyar sigar JSON.',
    'lt' => [
        'array' => ':attribute dole ne ya ƙunshi abubuwa ƙasa da :value.',
        'file' => ':attribute dole ne ya kasance ƙasa da kilobyte :value.',
        'numeric' => ':attribute dole ne ya kasance ƙasa da :value.',
        'string' => ':attribute dole ne ya kasance ƙasa da haruffa :value.',
    ],
    'lte' => [
        'array' => ':attribute bai kamata ya ƙunshi abubuwa fiye da :value ba.',
        'file' => ':attribute dole ne ya kasance daidai ko ƙasa da kilobyte :value.',
        'numeric' => ':attribute dole ne ya kasance daidai ko ƙasa da :value.',
        'string' => ':attribute bai kamata ya fi haruffa :value yawa ba.',
    ],
    'max' => [
        'array' => ':attribute bai kamata ya ƙunshi abubuwa fiye da :max ba.',
        'file' => ':attribute bai kamata ya fi kilobyte :max girma ba.',
        'numeric' => ':attribute bai kamata ya fi :max girma ba.',
        'string' => ':attribute bai kamata ya fi haruffa :max yawa ba.',
    ],
    'mimes' => ':attribute dole ne ya zama fayil na irin: :values.',
    'min' => [
        'array' => ':attribute dole ne ya ƙunshi aƙalla abubuwa :min.',
        'file' => ':attribute dole ne ya kasance aƙalla kilobyte :min.',
        'numeric' => ':attribute dole ne ya kasance aƙalla :min.',
        'string' => ':attribute dole ne ya ƙunshi aƙalla haruffa :min.',
    ],
    'not_in' => ':attribute da aka zaɓa ba ingantacce ba ne.',
    'not_regex' => 'Tsarin :attribute ba daidai ba ne.',
    'numeric' => ':attribute dole ne ya zama lamba.',
    'present' => 'Filin :attribute dole ne ya kasance a wurin.',
    'prohibited' => 'An hana filin :attribute.',
    'prohibited_if' => 'An hana filin :attribute idan :other shine :value.',
    'prohibited_unless' => 'An hana filin :attribute sai dai idan :other yana cikin :values.',
    'regex' => 'Tsarin :attribute ba daidai ba ne.',
    'required' => 'Filin :attribute wajibi ne.',
    'required_array_keys' => 'Filin :attribute dole ne ya ƙunshi shigarwa don: :values.',
    'required_if' => 'Filin :attribute wajibi ne idan :other shine :value.',
    'required_unless' => 'Filin :attribute wajibi ne sai dai idan :other yana cikin :values.',
    'required_with' => 'Filin :attribute wajibi ne idan akwai :values.',
    'required_with_all' => 'Filin :attribute wajibi ne idan akwai :values.',
    'required_without' => 'Filin :attribute wajibi ne idan babu :values.',
    'required_without_all' => 'Filin :attribute wajibi ne idan babu ko ɗaya daga cikin :values.',
    'same' => ':attribute da :other dole ne su yi daidai.',
    'size' => [
        'array' => ':attribute dole ne ya ƙunshi abubuwa :size.',
        'file' => ':attribute dole ne ya kasance kilobyte :size.',
        'numeric' => ':attribute dole ne ya kasance :size.',
        'string' => ':attribute dole ne ya ƙunshi haruffa :size.',
    ],
    'starts_with' => ':attribute dole ne ya fara da ɗaya daga cikin waɗannan ƙimomi: :values.',
    'string' => ':attribute dole ne ya zama jerin haruffa (string).',
    'timezone' => ':attribute dole ne ya zama ingantacciyar yankin lokaci.',
    'unique' => 'An riga an ɗauki :attribute.',
    'uploaded' => 'Ɗora :attribute ya kasa.',
    'url' => 'Tsarin :attribute ba daidai ba ne.',
    'uuid' => ':attribute dole ne ya zama ingantaccen UUID.',

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
        'email' => 'adireshin imel',
        'password' => 'kalmar sirri',
        'password_confirmation' => 'tabbatar da kalmar sirri',
        'first_name' => 'sunan farko',
        'last_name' => 'sunan mahaifi',
        'middle_name' => 'sunan tsakiya',
        'phone' => 'lambar waya',
        'code' => 'lambar sirri',
        'recovery_code' => 'lambar dawowa',
        'remember' => 'tuna ni',
        'amount' => 'adadi',
        'reference' => 'lambar tunani',
        'name' => 'suna',
        'address' => 'adireshi',
        'city' => 'birni',
        'state' => 'jiha',
        'zip' => 'lambar ZIP',
        'country' => 'ƙasa',
        'document' => 'takarda',
        'document_type' => 'nau\'in takarda',
        'note' => 'bayani',
        'category' => 'nau\'i',
        'linked_account_id' => 'asusun da aka haɗa',
        'source_type' => 'tushe',
        'destination_type' => 'wurin isarwa',
    ],
];
