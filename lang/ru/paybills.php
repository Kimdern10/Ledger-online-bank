<?php

return [
    'page_title' => 'Оплата счетов',
    'from_label' => 'Со счёта',
    'account_fallback' => 'Счёт',
    'account_type_checking' => 'Расчётный',
    'account_type_savings' => 'Сберегательный',
    'available' => 'Доступно',
    'upcoming_bills' => 'Предстоящие счета',
    'due_prefix' => 'Срок оплаты',
    'add_bill_link' => '+ Добавить счёт',
    'no_bills_yet' => 'Пока нет счетов',
    'no_bills_sub' => 'Добавьте счёт, чтобы отслеживать сроки оплаты и платить прямо отсюда.',
    'pay_btn' => 'Оплатить',
    'remove_bill_aria' => 'Удалить :category',
    'checking_required_title' => 'Требуется расчётный счёт',
    'checking_required_sub' => 'Счета можно добавлять и оплачивать только с расчётного счёта. У вас только сберегательный счёт, поэтому эта страница доступна вам только для просмотра.',

    // Add-bill sheet
    'add_bill_heading' => 'Добавить счёт',
    'bill_type_label' => 'Тип счёта',
    'bill_type_rent_mortgage' => 'Аренда / Ипотека',
    'bill_type_credit_card' => 'Кредитная карта',
    'bill_type_medical_insurance' => 'Медицинская страховка',
    'bill_type_taxes' => 'Налоги',
    'bill_type_student_loan' => 'Студенческий кредит',
    'bill_type_other' => 'Другое',
    'bill_name_label' => 'Название счёта',
    'bill_name_placeholder' => 'например, абонемент в спортзал',
    'biller_label' => 'Получатель платежа',
    'biller_placeholder' => 'Кому вы платите',
    'amount_label' => 'Сумма',
    'due_date_label' => 'Срок оплаты',
    'add_bill_submit' => 'Добавить счёт',
    'adding' => 'Добавление…',

    // Add-bill validation errors
    'error_enter_bill_name' => 'Введите название этого счёта.',
    'error_choose_bill_type' => 'Выберите тип счёта.',
    'error_enter_biller' => 'Укажите, кому вы платите.',
    'error_enter_valid_amount' => 'Введите корректную сумму.',
    'error_pick_due_date' => 'Выберите срок оплаты.',
    'error_add_bill_failed' => 'Не удалось добавить этот счёт — попробуйте снова.',
    'error_add_bill_connection' => 'Не удалось добавить этот счёт — проверьте соединение и попробуйте снова.',

    // Pay-bill confirmation sheet
    'pay_bill_heading' => 'Оплата счёта',
    'pay_from' => 'Оплатить со счёта',
    'confirm_payment' => 'Подтвердить платёж',
    'payment_scheduled_suffix' => 'платёж запланирован',
    'payment_scheduled_toast' => 'Платёж запланирован',

    // Remove-bill confirmation dialog
    'remove_confirm_title' => 'Удалить :name?',
    'remove_confirm_text' => 'Вы всегда сможете добавить его снова позже.',
    'remove_confirm_button' => 'Удалить',
    'remove_cancel_button' => 'Отмена',
    'this_bill_fallback' => 'этот счёт',
    'bill_removed_toast' => ':name удалён',
    'bill_added_toast' => ':category добавлен',
    'error_remove_bill_failed' => 'Не удалось удалить этот счёт — попробуйте снова',
    'error_remove_bill_connection' => 'Не удалось удалить этот счёт — проверьте соединение',
];
