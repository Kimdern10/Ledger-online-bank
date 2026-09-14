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
    'accepted' => 'Поле :attribute должно быть принято.',
    'active_url' => 'Поле :attribute не является корректным URL-адресом.',
    'after' => 'Поле :attribute должно содержать дату позже :date.',
    'after_or_equal' => 'Поле :attribute должно содержать дату не ранее :date.',
    'alpha' => 'Поле :attribute может содержать только буквы.',
    'alpha_dash' => 'Поле :attribute может содержать только буквы, цифры, дефисы и подчёркивания.',
    'alpha_num' => 'Поле :attribute может содержать только буквы и цифры.',
    'array' => 'Поле :attribute должно быть массивом.',
    'before' => 'Поле :attribute должно содержать дату раньше :date.',
    'before_or_equal' => 'Поле :attribute должно содержать дату не позднее :date.',
    'between' => [
        'array' => 'Поле :attribute должно содержать от :min до :max элементов.',
        'file' => 'Размер файла :attribute должен быть от :min до :max килобайт.',
        'numeric' => 'Значение :attribute должно быть от :min до :max.',
        'string' => 'Поле :attribute должно содержать от :min до :max символов.',
    ],
    'boolean' => 'Поле :attribute должно быть true или false.',
    'confirmed' => 'Подтверждение поля :attribute не совпадает.',
    'current_password' => 'Указанный пароль неверен.',
    'date' => 'Поле :attribute не является корректной датой.',
    'date_equals' => 'Поле :attribute должно содержать дату, равную :date.',
    'declined' => 'Поле :attribute должно быть отклонено.',
    'different' => 'Поля :attribute и :other должны различаться.',
    'digits' => 'Поле :attribute должно состоять из :digits цифр.',
    'digits_between' => 'Поле :attribute должно содержать от :min до :max цифр.',
    'distinct' => 'Поле :attribute содержит повторяющееся значение.',
    'email' => 'Поле :attribute должно быть корректным адресом электронной почты.',
    'exists' => 'Выбранное значение :attribute недопустимо.',
    'file' => 'Поле :attribute должно быть файлом.',
    'filled' => 'Поле :attribute должно иметь значение.',
    'gt' => [
        'array' => 'Поле :attribute должно содержать более :value элементов.',
        'file' => 'Размер файла :attribute должен быть больше :value килобайт.',
        'numeric' => 'Значение :attribute должно быть больше :value.',
        'string' => 'Поле :attribute должно содержать более :value символов.',
    ],
    'gte' => [
        'array' => 'Поле :attribute должно содержать :value элементов или больше.',
        'file' => 'Размер файла :attribute должен быть больше или равен :value килобайт.',
        'numeric' => 'Значение :attribute должно быть больше или равно :value.',
        'string' => 'Поле :attribute должно содержать :value символов или больше.',
    ],
    'image' => 'Поле :attribute должно быть изображением.',
    'in' => 'Выбранное значение :attribute недопустимо.',
    'in_array' => 'Поле :attribute отсутствует в :other.',
    'integer' => 'Поле :attribute должно быть целым числом.',
    'ip' => 'Поле :attribute должно быть корректным IP-адресом.',
    'json' => 'Поле :attribute должно быть корректной JSON-строкой.',
    'lt' => [
        'array' => 'Поле :attribute должно содержать менее :value элементов.',
        'file' => 'Размер файла :attribute должен быть меньше :value килобайт.',
        'numeric' => 'Значение :attribute должно быть меньше :value.',
        'string' => 'Поле :attribute должно содержать менее :value символов.',
    ],
    'lte' => [
        'array' => 'Поле :attribute не должно содержать более :value элементов.',
        'file' => 'Размер файла :attribute должен быть меньше или равен :value килобайт.',
        'numeric' => 'Значение :attribute должно быть меньше или равно :value.',
        'string' => 'Поле :attribute не должно содержать более :value символов.',
    ],
    'max' => [
        'array' => 'Поле :attribute не должно содержать более :max элементов.',
        'file' => 'Размер файла :attribute не должен превышать :max килобайт.',
        'numeric' => 'Значение :attribute не должно превышать :max.',
        'string' => 'Поле :attribute не должно содержать более :max символов.',
    ],
    'mimes' => 'Поле :attribute должно быть файлом одного из типов: :values.',
    'min' => [
        'array' => 'Поле :attribute должно содержать не менее :min элементов.',
        'file' => 'Размер файла :attribute должен быть не менее :min килобайт.',
        'numeric' => 'Значение :attribute должно быть не менее :min.',
        'string' => 'Поле :attribute должно содержать не менее :min символов.',
    ],
    'not_in' => 'Выбранное значение :attribute недопустимо.',
    'not_regex' => 'Формат поля :attribute недопустим.',
    'numeric' => 'Поле :attribute должно быть числом.',
    'present' => 'Поле :attribute должно присутствовать.',
    'prohibited' => 'Поле :attribute запрещено.',
    'prohibited_if' => 'Поле :attribute запрещено, если :other равно :value.',
    'prohibited_unless' => 'Поле :attribute запрещено, если :other не входит в :values.',
    'regex' => 'Формат поля :attribute недопустим.',
    'required' => 'Поле :attribute обязательно для заполнения.',
    'required_array_keys' => 'Поле :attribute должно содержать записи для: :values.',
    'required_if' => 'Поле :attribute обязательно, если :other равно :value.',
    'required_unless' => 'Поле :attribute обязательно, если :other не входит в :values.',
    'required_with' => 'Поле :attribute обязательно, если указано :values.',
    'required_with_all' => 'Поле :attribute обязательно, если указаны :values.',
    'required_without' => 'Поле :attribute обязательно, если :values не указано.',
    'required_without_all' => 'Поле :attribute обязательно, если ни одно из :values не указано.',
    'same' => 'Поля :attribute и :other должны совпадать.',
    'size' => [
        'array' => 'Поле :attribute должно содержать :size элементов.',
        'file' => 'Размер файла :attribute должен быть :size килобайт.',
        'numeric' => 'Значение :attribute должно быть равно :size.',
        'string' => 'Поле :attribute должно содержать :size символов.',
    ],
    'starts_with' => 'Поле :attribute должно начинаться с одного из следующих значений: :values.',
    'string' => 'Поле :attribute должно быть строкой.',
    'timezone' => 'Поле :attribute должно быть корректным часовым поясом.',
    'unique' => 'Такое значение поля :attribute уже занято.',
    'uploaded' => 'Не удалось загрузить файл :attribute.',
    'url' => 'Формат поля :attribute недопустим.',
    'uuid' => 'Поле :attribute должно быть корректным UUID.',

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
        'email' => 'адрес электронной почты',
        'password' => 'пароль',
        'password_confirmation' => 'подтверждение пароля',
        'first_name' => 'имя',
        'last_name' => 'фамилия',
        'middle_name' => 'отчество',
        'phone' => 'номер телефона',
        'code' => 'код',
        'recovery_code' => 'код восстановления',
        'remember' => 'запомнить меня',
        'amount' => 'сумма',
        'reference' => 'референс',
        'name' => 'имя',
        'address' => 'адрес',
        'city' => 'город',
        'state' => 'область',
        'zip' => 'почтовый индекс',
        'country' => 'страна',
        'document' => 'документ',
        'document_type' => 'тип документа',
        'note' => 'примечание',
        'category' => 'категория',
        'linked_account_id' => 'связанный счёт',
        'source_type' => 'источник',
        'destination_type' => 'получатель',
    ],
];
