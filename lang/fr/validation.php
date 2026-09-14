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
    'accepted' => ':attribute doit être accepté.',
    'active_url' => ':attribute n\'est pas une URL valide.',
    'after' => ':attribute doit être une date postérieure à :date.',
    'after_or_equal' => ':attribute doit être une date postérieure ou égale à :date.',
    'alpha' => ':attribute ne peut contenir que des lettres.',
    'alpha_dash' => ':attribute ne peut contenir que des lettres, des chiffres, des tirets et des underscores.',
    'alpha_num' => ':attribute ne peut contenir que des lettres et des chiffres.',
    'array' => ':attribute doit être un tableau.',
    'before' => ':attribute doit être une date antérieure à :date.',
    'before_or_equal' => ':attribute doit être une date antérieure ou égale à :date.',
    'between' => [
        'array' => ':attribute doit contenir entre :min et :max éléments.',
        'file' => ':attribute doit peser entre :min et :max kilo-octets.',
        'numeric' => ':attribute doit être compris entre :min et :max.',
        'string' => ':attribute doit contenir entre :min et :max caractères.',
    ],
    'boolean' => 'Le champ :attribute doit être vrai ou faux.',
    'confirmed' => 'La confirmation de :attribute ne correspond pas.',
    'current_password' => 'Le mot de passe est incorrect.',
    'date' => ':attribute n\'est pas une date valide.',
    'date_equals' => ':attribute doit être une date égale à :date.',
    'declined' => ':attribute doit être refusé.',
    'different' => ':attribute et :other doivent être différents.',
    'digits' => ':attribute doit comporter :digits chiffres.',
    'digits_between' => ':attribute doit comporter entre :min et :max chiffres.',
    'distinct' => 'Le champ :attribute contient une valeur en double.',
    'email' => ':attribute doit être une adresse e-mail valide.',
    'exists' => 'Le champ :attribute sélectionné n\'est pas valide.',
    'file' => ':attribute doit être un fichier.',
    'filled' => 'Le champ :attribute doit avoir une valeur.',
    'gt' => [
        'array' => ':attribute doit contenir plus de :value éléments.',
        'file' => ':attribute doit peser plus de :value kilo-octets.',
        'numeric' => ':attribute doit être supérieur à :value.',
        'string' => ':attribute doit contenir plus de :value caractères.',
    ],
    'gte' => [
        'array' => ':attribute doit contenir :value éléments ou plus.',
        'file' => ':attribute doit peser :value kilo-octets ou plus.',
        'numeric' => ':attribute doit être supérieur ou égal à :value.',
        'string' => ':attribute doit contenir :value caractères ou plus.',
    ],
    'image' => ':attribute doit être une image.',
    'in' => 'Le champ :attribute sélectionné n\'est pas valide.',
    'in_array' => 'Le champ :attribute n\'existe pas dans :other.',
    'integer' => ':attribute doit être un entier.',
    'ip' => ':attribute doit être une adresse IP valide.',
    'json' => ':attribute doit être une chaîne JSON valide.',
    'lt' => [
        'array' => ':attribute doit contenir moins de :value éléments.',
        'file' => ':attribute doit peser moins de :value kilo-octets.',
        'numeric' => ':attribute doit être inférieur à :value.',
        'string' => ':attribute doit contenir moins de :value caractères.',
    ],
    'lte' => [
        'array' => ':attribute ne doit pas contenir plus de :value éléments.',
        'file' => ':attribute doit peser :value kilo-octets ou moins.',
        'numeric' => ':attribute doit être inférieur ou égal à :value.',
        'string' => ':attribute ne doit pas contenir plus de :value caractères.',
    ],
    'max' => [
        'array' => ':attribute ne doit pas contenir plus de :max éléments.',
        'file' => ':attribute ne doit pas peser plus de :max kilo-octets.',
        'numeric' => ':attribute ne doit pas être supérieur à :max.',
        'string' => ':attribute ne doit pas contenir plus de :max caractères.',
    ],
    'mimes' => ':attribute doit être un fichier de type : :values.',
    'min' => [
        'array' => ':attribute doit contenir au moins :min éléments.',
        'file' => ':attribute doit peser au moins :min kilo-octets.',
        'numeric' => ':attribute doit être au moins :min.',
        'string' => ':attribute doit contenir au moins :min caractères.',
    ],
    'not_in' => 'Le champ :attribute sélectionné n\'est pas valide.',
    'not_regex' => 'Le format de :attribute est invalide.',
    'numeric' => ':attribute doit être un nombre.',
    'present' => 'Le champ :attribute doit être présent.',
    'prohibited' => 'Le champ :attribute est interdit.',
    'prohibited_if' => 'Le champ :attribute est interdit lorsque :other est :value.',
    'prohibited_unless' => 'Le champ :attribute est interdit sauf si :other figure dans :values.',
    'regex' => 'Le format de :attribute est invalide.',
    'required' => 'Le champ :attribute est obligatoire.',
    'required_array_keys' => 'Le champ :attribute doit contenir des entrées pour : :values.',
    'required_if' => 'Le champ :attribute est obligatoire lorsque :other est :value.',
    'required_unless' => 'Le champ :attribute est obligatoire sauf si :other figure dans :values.',
    'required_with' => 'Le champ :attribute est obligatoire lorsque :values est présent.',
    'required_with_all' => 'Le champ :attribute est obligatoire lorsque :values sont présents.',
    'required_without' => 'Le champ :attribute est obligatoire lorsque :values n\'est pas présent.',
    'required_without_all' => 'Le champ :attribute est obligatoire lorsque aucun de :values n\'est présent.',
    'same' => ':attribute et :other doivent correspondre.',
    'size' => [
        'array' => ':attribute doit contenir :size éléments.',
        'file' => ':attribute doit peser :size kilo-octets.',
        'numeric' => ':attribute doit être :size.',
        'string' => ':attribute doit contenir :size caractères.',
    ],
    'starts_with' => ':attribute doit commencer par l\'une des valeurs suivantes : :values.',
    'string' => ':attribute doit être une chaîne de caractères.',
    'timezone' => ':attribute doit être un fuseau horaire valide.',
    'unique' => ':attribute est déjà utilisé.',
    'uploaded' => 'Le téléversement de :attribute a échoué.',
    'url' => 'Le format de :attribute est invalide.',
    'uuid' => ':attribute doit être un UUID valide.',

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
        'email' => 'adresse e-mail',
        'password' => 'mot de passe',
        'password_confirmation' => 'confirmation du mot de passe',
        'first_name' => 'prénom',
        'last_name' => 'nom de famille',
        'middle_name' => 'deuxième prénom',
        'phone' => 'numéro de téléphone',
        'code' => 'code',
        'recovery_code' => 'code de récupération',
        'remember' => 'se souvenir de moi',
        'amount' => 'montant',
        'reference' => 'référence',
        'name' => 'nom',
        'address' => 'adresse',
        'city' => 'ville',
        'state' => 'région',
        'zip' => 'code postal',
        'country' => 'pays',
        'document' => 'document',
        'document_type' => 'type de document',
        'note' => 'note',
        'category' => 'catégorie',
        'linked_account_id' => 'compte lié',
        'source_type' => 'source',
        'destination_type' => 'destination',
    ],
];
