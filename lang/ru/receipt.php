<?php

return [
    // Page chrome (send-receipt.blade.php)
    'page_title' => 'Квитанция',
    'download_pdf' => 'Скачать PDF',
    'share' => 'Поделиться',
    'done' => 'Готово',
    'send_another' => 'Отправить ещё',

    // "from"/"to"/"by" word shown before the other party's name, e.g.
    // "to Jane Doe" or "from Ledger" — controllers pass a plain identifier,
    // the blade maps it through these.
    'subverb_from' => 'от',
    'subverb_to' => 'кому',
    'subverb_by' => 'от',

    // Row labels — shared across every receipt type (Send Money,
    // Another bank, International, Withdraw, Top up, admin adjustment)
    'row_from' => 'От',
    'row_bank' => 'Банк',
    'row_status' => 'Статус',
    'row_category' => 'Категория',
    'row_note' => 'Примечание',
    'row_reference' => 'Референс',
    'row_date' => 'Дата',
    'row_recipient' => 'Получатель',
    'row_account_number' => 'Номер счёта',
    'row_routing_number' => 'Номер маршрутизации',
    'row_scheduled_for' => 'Запланировано на',
    'row_country' => 'Страна',
    'row_swift_bic' => 'SWIFT/BIC',
    'row_account_iban' => 'Счёт/IBAN',
    'row_currency' => 'Валюта',
    'row_exchange_rate' => 'Обменный курс',
    'row_recipient_receives_approx' => 'Получатель получит (примерно)',
    'row_withdraw_to' => 'Снятие на',
    'row_fee' => 'Комиссия',
    'row_youll_receive' => 'Вы получите',
    'row_top_up_from' => 'Пополнение с',
    'row_card_processing_fee' => 'Комиссия за обработку карты',
    'row_account_name' => 'Имя владельца счёта',

    // Titles
    'title_payment_received' => 'Платёж получен',
    'title_payment_sent' => 'Платёж отправлен',
    'title_transfer_scheduled' => 'Перевод запланирован',
    'title_transfer_cancelled' => 'Перевод отменён',
    'title_transfer_failed' => 'Перевод не выполнен',
    'title_withdrawal' => 'Снятие средств',
    'title_topup' => 'Пополнение',
    'title_deposit_received' => 'Депозит получен',
    'title_balance_adjustment' => 'Корректировка баланса',

    // Banners
    'banner_scheduled' => 'Этот перевод запланирован на :date и ещё не выполнен. Вы можете отменить его в любое время до этой даты.',
    'banner_cancelled' => 'Этот перевод был отменён и так и не был выполнен.',
    'banner_failed' => 'Этот перевод не прошёл — недостаточно средств на дату, когда он должен был быть выполнен.',
    'banner_transfer_sent' => 'Ваш перевод был отправлен.',
    'banner_international_transfer_sent' => 'Ваш международный перевод был отправлен.',
    'banner_withdrawal_bank_days' => 'Поступит на ваш банковский счёт в течение 1–3 рабочих дней.',
    'banner_topup_fee_note' => 'Комиссия за обработку была списана вашей картой, а не удержана из суммы, добавленной в Ledger.',
    'banner_adjustment_credit' => 'Этот депозит был зачислен на ваш счёт компанией Ledger — обратитесь в поддержку, если у вас есть вопросы.',
    'banner_adjustment_debit' => 'Эта корректировка была внесена на ваш счёт компанией Ledger — обратитесь в поддержку, если у вас есть вопросы.',

    // Status labels (App\Support\TransactionStatus — shared by Transfer,
    // ExternalTransfer, InternationalTransfer, Withdrawal, TopUp)
    'status_completed' => 'Завершено',
    'status_scheduled' => 'Запланировано',
    'status_cancelled' => 'Отменено',
    'status_failed' => 'Не выполнено',

    // JS strings for the Download PDF / Share buttons
    'share_text_prefix' => 'Квитанция об оплате Ledger',
    'share_title' => 'Квитанция Ledger',
    'js_preparing_pdf' => 'Подготовка PDF…',
    'js_pdf_error' => 'Не удалось создать PDF. Попробуйте снова.',
    'js_preparing_share' => 'Подготовка квитанции к отправке…',
    'js_share_error' => 'Не удалось поделиться квитанцией. Попробуйте скачать вместо этого.',
    'js_share_unsupported' => 'Функция «Поделиться» не поддерживается в этом браузере. Используйте скачивание вместо этого.',
];
