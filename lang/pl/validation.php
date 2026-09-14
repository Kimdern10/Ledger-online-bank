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
    'accepted' => ':attribute musi zostać zaakceptowane.',
    'active_url' => ':attribute nie jest prawidłowym adresem URL.',
    'after' => ':attribute musi być datą późniejszą niż :date.',
    'after_or_equal' => ':attribute musi być datą późniejszą lub równą :date.',
    'alpha' => ':attribute może zawierać wyłącznie litery.',
    'alpha_dash' => ':attribute może zawierać wyłącznie litery, cyfry, myślniki i podkreślenia.',
    'alpha_num' => ':attribute może zawierać wyłącznie litery i cyfry.',
    'array' => ':attribute musi być tablicą.',
    'before' => ':attribute musi być datą wcześniejszą niż :date.',
    'before_or_equal' => ':attribute musi być datą wcześniejszą lub równą :date.',
    'between' => [
        'array' => ':attribute musi zawierać od :min do :max elementów.',
        'file' => ':attribute musi mieć rozmiar od :min do :max kilobajtów.',
        'numeric' => ':attribute musi mieścić się w przedziale od :min do :max.',
        'string' => ':attribute musi zawierać od :min do :max znaków.',
    ],
    'boolean' => 'Pole :attribute musi mieć wartość true lub false.',
    'confirmed' => 'Potwierdzenie pola :attribute nie zgadza się.',
    'current_password' => 'Podane hasło jest nieprawidłowe.',
    'date' => ':attribute nie jest prawidłową datą.',
    'date_equals' => ':attribute musi być datą równą :date.',
    'declined' => ':attribute musi zostać odrzucone.',
    'different' => 'Pola :attribute i :other muszą się różnić.',
    'digits' => ':attribute musi mieć :digits cyfr.',
    'digits_between' => ':attribute musi mieć od :min do :max cyfr.',
    'distinct' => 'Pole :attribute zawiera zduplikowaną wartość.',
    'email' => ':attribute musi być prawidłowym adresem e-mail.',
    'exists' => 'Wybrana wartość pola :attribute jest nieprawidłowa.',
    'file' => ':attribute musi być plikiem.',
    'filled' => 'Pole :attribute musi mieć wartość.',
    'gt' => [
        'array' => ':attribute musi zawierać więcej niż :value elementów.',
        'file' => ':attribute musi być większe niż :value kilobajtów.',
        'numeric' => ':attribute musi być większe niż :value.',
        'string' => ':attribute musi mieć więcej niż :value znaków.',
    ],
    'gte' => [
        'array' => ':attribute musi zawierać co najmniej :value elementów.',
        'file' => ':attribute musi być większe lub równe :value kilobajtów.',
        'numeric' => ':attribute musi być większe lub równe :value.',
        'string' => ':attribute musi mieć co najmniej :value znaków.',
    ],
    'image' => ':attribute musi być obrazem.',
    'in' => 'Wybrana wartość pola :attribute jest nieprawidłowa.',
    'in_array' => 'Pole :attribute nie istnieje w :other.',
    'integer' => ':attribute musi być liczbą całkowitą.',
    'ip' => ':attribute musi być prawidłowym adresem IP.',
    'json' => ':attribute musi być prawidłowym ciągiem JSON.',
    'lt' => [
        'array' => ':attribute musi zawierać mniej niż :value elementów.',
        'file' => ':attribute musi być mniejsze niż :value kilobajtów.',
        'numeric' => ':attribute musi być mniejsze niż :value.',
        'string' => ':attribute musi mieć mniej niż :value znaków.',
    ],
    'lte' => [
        'array' => ':attribute nie może zawierać więcej niż :value elementów.',
        'file' => ':attribute musi być mniejsze lub równe :value kilobajtów.',
        'numeric' => ':attribute musi być mniejsze lub równe :value.',
        'string' => ':attribute nie może mieć więcej niż :value znaków.',
    ],
    'max' => [
        'array' => ':attribute nie może zawierać więcej niż :max elementów.',
        'file' => ':attribute nie może być większe niż :max kilobajtów.',
        'numeric' => ':attribute nie może być większe niż :max.',
        'string' => ':attribute nie może mieć więcej niż :max znaków.',
    ],
    'mimes' => ':attribute musi być plikiem typu: :values.',
    'min' => [
        'array' => ':attribute musi zawierać co najmniej :min elementów.',
        'file' => ':attribute musi mieć co najmniej :min kilobajtów.',
        'numeric' => ':attribute musi wynosić co najmniej :min.',
        'string' => ':attribute musi mieć co najmniej :min znaków.',
    ],
    'not_in' => 'Wybrana wartość pola :attribute jest nieprawidłowa.',
    'not_regex' => 'Format pola :attribute jest nieprawidłowy.',
    'numeric' => ':attribute musi być liczbą.',
    'present' => 'Pole :attribute musi być obecne.',
    'prohibited' => 'Pole :attribute jest niedozwolone.',
    'prohibited_if' => 'Pole :attribute jest niedozwolone, gdy :other ma wartość :value.',
    'prohibited_unless' => 'Pole :attribute jest niedozwolone, chyba że :other znajduje się wśród :values.',
    'regex' => 'Format pola :attribute jest nieprawidłowy.',
    'required' => 'Pole :attribute jest wymagane.',
    'required_array_keys' => 'Pole :attribute musi zawierać wpisy dla: :values.',
    'required_if' => 'Pole :attribute jest wymagane, gdy :other ma wartość :value.',
    'required_unless' => 'Pole :attribute jest wymagane, chyba że :other znajduje się wśród :values.',
    'required_with' => 'Pole :attribute jest wymagane, gdy podano :values.',
    'required_with_all' => 'Pole :attribute jest wymagane, gdy podano :values.',
    'required_without' => 'Pole :attribute jest wymagane, gdy nie podano :values.',
    'required_without_all' => 'Pole :attribute jest wymagane, gdy żadne z :values nie zostało podane.',
    'same' => 'Pola :attribute i :other muszą się zgadzać.',
    'size' => [
        'array' => ':attribute musi zawierać :size elementów.',
        'file' => ':attribute musi mieć :size kilobajtów.',
        'numeric' => ':attribute musi wynosić :size.',
        'string' => ':attribute musi mieć :size znaków.',
    ],
    'starts_with' => ':attribute musi zaczynać się od jednego z następujących: :values.',
    'string' => ':attribute musi być ciągiem znaków.',
    'timezone' => ':attribute musi być prawidłową strefą czasową.',
    'unique' => 'Podana wartość pola :attribute jest już zajęta.',
    'uploaded' => 'Przesyłanie pliku :attribute nie powiodło się.',
    'url' => 'Format pola :attribute jest nieprawidłowy.',
    'uuid' => ':attribute musi być prawidłowym UUID.',

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
        'email' => 'adres e-mail',
        'password' => 'hasło',
        'password_confirmation' => 'potwierdzenie hasła',
        'first_name' => 'imię',
        'last_name' => 'nazwisko',
        'middle_name' => 'drugie imię',
        'phone' => 'numer telefonu',
        'code' => 'kod',
        'recovery_code' => 'kod odzyskiwania',
        'remember' => 'zapamiętaj mnie',
        'amount' => 'kwota',
        'reference' => 'numer referencyjny',
        'name' => 'nazwa',
        'address' => 'adres',
        'city' => 'miasto',
        'state' => 'województwo',
        'zip' => 'kod pocztowy',
        'country' => 'kraj',
        'document' => 'dokument',
        'document_type' => 'typ dokumentu',
        'note' => 'notatka',
        'category' => 'kategoria',
        'linked_account_id' => 'powiązane konto',
        'source_type' => 'źródło',
        'destination_type' => 'miejsce docelowe',
    ],
];
