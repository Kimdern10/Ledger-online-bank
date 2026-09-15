<?php

return [
    'page_title' => 'Pay Bills',
    'from_label' => 'From',
    'account_fallback' => 'Account',
    'account_type_checking' => 'Checking',
    'account_type_savings' => 'Savings',
    'available' => 'Available',
    'upcoming_bills' => 'Upcoming bills',
    'due_prefix' => 'Due',
    'add_bill_link' => '+ Add bill',
    'no_bills_yet' => 'No bills yet',
    'no_bills_sub' => "Add a bill to start tracking what's due and pay it right from here.",
    'pay_btn' => 'Pay',
    'remove_bill_aria' => 'Remove :category',
    'checking_required_title' => 'Checking account required',
    'checking_required_sub' => 'Bills can only be added and paid from a checking account. Your account is savings-only, so this page is read-only for you.',

    // Add-bill sheet
    'add_bill_heading' => 'Add bill',
    'bill_type_label' => 'Bill type',
    'bill_type_rent_mortgage' => 'Rent / Mortgage',
    'bill_type_credit_card' => 'Credit Card',
    'bill_type_medical_insurance' => 'Medical Insurance',
    'bill_type_taxes' => 'Taxes',
    'bill_type_student_loan' => 'Student Loan',
    'bill_type_other' => 'Other',
    'bill_name_label' => 'Bill name',
    'bill_name_placeholder' => 'e.g. Gym Membership',
    'biller_label' => 'Biller',
    'biller_placeholder' => "Who you're paying",
    'amount_label' => 'Amount',
    'due_date_label' => 'Due date',
    'add_bill_submit' => 'Add bill',
    'adding' => 'Adding…',

    // Add-bill validation errors
    'error_enter_bill_name' => 'Enter a name for this bill.',
    'error_choose_bill_type' => 'Choose a bill type.',
    'error_enter_biller' => "Enter who you're paying.",
    'error_enter_valid_amount' => 'Enter a valid amount.',
    'error_pick_due_date' => 'Pick a due date.',
    'error_add_bill_failed' => 'Could not add that bill — try again.',
    'error_add_bill_connection' => 'Could not add that bill — check your connection and try again.',

    // Pay-bill confirmation sheet
    'pay_bill_heading' => 'Pay bill',
    'pay_from' => 'Pay from',
    'confirm_payment' => 'Confirm payment',
    'payment_scheduled_suffix' => 'payment scheduled',
    'payment_scheduled_toast' => 'Payment scheduled',

    // Remove-bill confirmation dialog
    'remove_confirm_title' => 'Remove :name?',
    'remove_confirm_text' => 'You can always add it back later.',
    'remove_confirm_button' => 'Remove',
    'remove_cancel_button' => 'Cancel',
    'this_bill_fallback' => 'this bill',
    'bill_removed_toast' => ':name removed',
    'bill_added_toast' => ':category added',
    'error_remove_bill_failed' => 'Could not remove that bill — try again',
    'error_remove_bill_connection' => 'Could not remove that bill — check your connection',
];
