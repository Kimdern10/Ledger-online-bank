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
    'accepted' => ':attribute moet worden geaccepteerd.',
    'active_url' => ':attribute is geen geldige URL.',
    'after' => ':attribute moet een datum na :date zijn.',
    'after_or_equal' => ':attribute moet een datum na of gelijk aan :date zijn.',
    'alpha' => ':attribute mag alleen letters bevatten.',
    'alpha_dash' => ':attribute mag alleen letters, cijfers, streepjes en underscores bevatten.',
    'alpha_num' => ':attribute mag alleen letters en cijfers bevatten.',
    'array' => ':attribute moet een array zijn.',
    'before' => ':attribute moet een datum vóór :date zijn.',
    'before_or_equal' => ':attribute moet een datum vóór of gelijk aan :date zijn.',
    'between' => [
        'array' => ':attribute moet tussen :min en :max items bevatten.',
        'file' => ':attribute moet tussen :min en :max kilobytes zijn.',
        'numeric' => ':attribute moet tussen :min en :max liggen.',
        'string' => ':attribute moet tussen :min en :max tekens bevatten.',
    ],
    'boolean' => ':attribute moet waar of onwaar zijn.',
    'confirmed' => ':attribute komt niet overeen met de bevestiging.',
    'current_password' => 'Het wachtwoord is onjuist.',
    'date' => ':attribute is geen geldige datum.',
    'date_equals' => ':attribute moet een datum gelijk aan :date zijn.',
    'declined' => ':attribute moet worden geweigerd.',
    'different' => ':attribute en :other moeten verschillend zijn.',
    'digits' => ':attribute moet :digits cijfers bevatten.',
    'digits_between' => ':attribute moet tussen :min en :max cijfers bevatten.',
    'distinct' => ':attribute bevat een dubbele waarde.',
    'email' => ':attribute moet een geldig e-mailadres zijn.',
    'exists' => 'Het geselecteerde :attribute is ongeldig.',
    'file' => ':attribute moet een bestand zijn.',
    'filled' => ':attribute moet een waarde bevatten.',
    'gt' => [
        'array' => ':attribute moet meer dan :value items bevatten.',
        'file' => ':attribute moet groter zijn dan :value kilobytes.',
        'numeric' => ':attribute moet groter zijn dan :value.',
        'string' => ':attribute moet langer zijn dan :value tekens.',
    ],
    'gte' => [
        'array' => ':attribute moet :value items of meer bevatten.',
        'file' => ':attribute moet groter dan of gelijk aan :value kilobytes zijn.',
        'numeric' => ':attribute moet groter dan of gelijk aan :value zijn.',
        'string' => ':attribute moet minimaal :value tekens lang zijn.',
    ],
    'image' => ':attribute moet een afbeelding zijn.',
    'in' => 'Het geselecteerde :attribute is ongeldig.',
    'in_array' => ':attribute bestaat niet in :other.',
    'integer' => ':attribute moet een geheel getal zijn.',
    'ip' => ':attribute moet een geldig IP-adres zijn.',
    'json' => ':attribute moet een geldige JSON-string zijn.',
    'lt' => [
        'array' => ':attribute moet minder dan :value items bevatten.',
        'file' => ':attribute moet kleiner zijn dan :value kilobytes.',
        'numeric' => ':attribute moet kleiner zijn dan :value.',
        'string' => ':attribute moet korter zijn dan :value tekens.',
    ],
    'lte' => [
        'array' => ':attribute mag niet meer dan :value items bevatten.',
        'file' => ':attribute moet kleiner dan of gelijk aan :value kilobytes zijn.',
        'numeric' => ':attribute moet kleiner dan of gelijk aan :value zijn.',
        'string' => ':attribute mag niet langer zijn dan :value tekens.',
    ],
    'max' => [
        'array' => ':attribute mag niet meer dan :max items bevatten.',
        'file' => ':attribute mag niet groter zijn dan :max kilobytes.',
        'numeric' => ':attribute mag niet groter zijn dan :max.',
        'string' => ':attribute mag niet langer zijn dan :max tekens.',
    ],
    'mimes' => ':attribute moet een bestand van het type: :values zijn.',
    'min' => [
        'array' => ':attribute moet minimaal :min items bevatten.',
        'file' => ':attribute moet minimaal :min kilobytes zijn.',
        'numeric' => ':attribute moet minimaal :min zijn.',
        'string' => ':attribute moet minimaal :min tekens bevatten.',
    ],
    'not_in' => 'Het geselecteerde :attribute is ongeldig.',
    'not_regex' => 'Het formaat van :attribute is ongeldig.',
    'numeric' => ':attribute moet een getal zijn.',
    'present' => ':attribute moet aanwezig zijn.',
    'prohibited' => ':attribute is niet toegestaan.',
    'prohibited_if' => ':attribute is niet toegestaan wanneer :other :value is.',
    'prohibited_unless' => ':attribute is niet toegestaan tenzij :other voorkomt in :values.',
    'regex' => 'Het formaat van :attribute is ongeldig.',
    'required' => ':attribute is verplicht.',
    'required_array_keys' => ':attribute moet items bevatten voor: :values.',
    'required_if' => ':attribute is verplicht wanneer :other :value is.',
    'required_unless' => ':attribute is verplicht tenzij :other voorkomt in :values.',
    'required_with' => ':attribute is verplicht wanneer :values aanwezig is.',
    'required_with_all' => ':attribute is verplicht wanneer :values aanwezig zijn.',
    'required_without' => ':attribute is verplicht wanneer :values niet aanwezig is.',
    'required_without_all' => ':attribute is verplicht wanneer geen van :values aanwezig is.',
    'same' => ':attribute en :other moeten overeenkomen.',
    'size' => [
        'array' => ':attribute moet :size items bevatten.',
        'file' => ':attribute moet :size kilobytes zijn.',
        'numeric' => ':attribute moet :size zijn.',
        'string' => ':attribute moet :size tekens bevatten.',
    ],
    'starts_with' => ':attribute moet beginnen met een van de volgende: :values.',
    'string' => ':attribute moet een string zijn.',
    'timezone' => ':attribute moet een geldige tijdzone zijn.',
    'unique' => ':attribute is al in gebruik.',
    'uploaded' => 'Het uploaden van :attribute is mislukt.',
    'url' => 'Het formaat van :attribute is ongeldig.',
    'uuid' => ':attribute moet een geldige UUID zijn.',

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
        'email' => 'e-mailadres',
        'password' => 'wachtwoord',
        'password_confirmation' => 'wachtwoordbevestiging',
        'first_name' => 'voornaam',
        'last_name' => 'achternaam',
        'middle_name' => 'tussenvoegsel',
        'phone' => 'telefoonnummer',
        'code' => 'code',
        'recovery_code' => 'herstelcode',
        'remember' => 'onthoud mij',
        'amount' => 'bedrag',
        'reference' => 'referentie',
        'name' => 'naam',
        'address' => 'adres',
        'city' => 'plaats',
        'state' => 'provincie',
        'zip' => 'postcode',
        'country' => 'land',
        'document' => 'document',
        'document_type' => 'documenttype',
        'note' => 'notitie',
        'category' => 'categorie',
        'linked_account_id' => 'gekoppelde rekening',
        'source_type' => 'bron',
        'destination_type' => 'bestemming',
    ],
];
