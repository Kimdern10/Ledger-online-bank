<?php

return [
    'page_title' => 'Pagar contas',
    'from_label' => 'De',
    'account_fallback' => 'Conta',
    'account_type_checking' => 'Corrente',
    'account_type_savings' => 'Poupança',
    'available' => 'Disponível',
    'upcoming_bills' => 'Próximas contas',
    'due_prefix' => 'Vence em',
    'add_bill_link' => '+ Adicionar conta',
    'no_bills_yet' => 'Ainda não há contas',
    'no_bills_sub' => 'Adicione uma conta para acompanhar os vencimentos e pagá-la diretamente por aqui.',
    'pay_btn' => 'Pagar',
    'remove_bill_aria' => 'Remover :category',
    'checking_required_title' => 'Conta corrente necessária',
    'checking_required_sub' => 'As contas só podem ser adicionadas e pagas a partir de uma conta corrente. Sua conta é somente poupança, então esta página é apenas para leitura no seu caso.',

    // Add-bill sheet
    'add_bill_heading' => 'Adicionar conta',
    'bill_type_label' => 'Tipo de conta',
    'bill_type_rent_mortgage' => 'Aluguel / Financiamento imobiliário',
    'bill_type_credit_card' => 'Cartão de crédito',
    'bill_type_medical_insurance' => 'Plano de saúde',
    'bill_type_taxes' => 'Impostos',
    'bill_type_student_loan' => 'Financiamento estudantil',
    'bill_type_other' => 'Outro',
    'bill_name_label' => 'Nome da conta',
    'bill_name_placeholder' => 'ex.: Mensalidade da academia',
    'biller_label' => 'Beneficiário',
    'biller_placeholder' => 'Para quem você está pagando',
    'amount_label' => 'Valor',
    'due_date_label' => 'Data de vencimento',
    'add_bill_submit' => 'Adicionar conta',
    'adding' => 'Adicionando…',

    // Add-bill validation errors
    'error_enter_bill_name' => 'Digite um nome para esta conta.',
    'error_choose_bill_type' => 'Escolha um tipo de conta.',
    'error_enter_biller' => 'Informe para quem você está pagando.',
    'error_enter_valid_amount' => 'Digite um valor válido.',
    'error_pick_due_date' => 'Escolha uma data de vencimento.',
    'error_add_bill_failed' => 'Não foi possível adicionar essa conta — tente novamente.',
    'error_add_bill_connection' => 'Não foi possível adicionar essa conta — verifique sua conexão e tente novamente.',

    // Pay-bill confirmation sheet
    'pay_bill_heading' => 'Pagar conta',
    'pay_from' => 'Pagar com',
    'confirm_payment' => 'Confirmar pagamento',
    'payment_scheduled_suffix' => 'pagamento agendado',
    'payment_scheduled_toast' => 'Pagamento agendado',

    // Remove-bill confirmation dialog
    'remove_confirm_title' => 'Remover :name?',
    'remove_confirm_text' => 'Você pode adicioná-la novamente mais tarde.',
    'remove_confirm_button' => 'Remover',
    'remove_cancel_button' => 'Cancelar',
    'this_bill_fallback' => 'esta conta',
    'bill_removed_toast' => ':name removida',
    'bill_added_toast' => ':category adicionada',
    'error_remove_bill_failed' => 'Não foi possível remover essa conta — tente novamente',
    'error_remove_bill_connection' => 'Não foi possível remover essa conta — verifique sua conexão',
];
