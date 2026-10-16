<?php

return [
    'title' => 'History',
    'money_in' => 'Money in',
    'money_out' => 'Money out',
    'search_placeholder' => 'Search transactions…',
    'filter_all' => 'All',
    'filter_sent' => 'Sent',
    'filter_received' => 'Received',
    'no_transactions_yet' => "No transactions yet — once you send or receive money, it'll show up here.",
    'no_matches' => 'No transactions match your search.',
    'cancel' => 'Cancel',

    // Email me a statement (history.blade.php, HistoryController::emailStatement())
    'statement_button' => 'Get a statement',
    'showing_latest' => 'Showing your :count most recent transactions. Use Get a statement to receive older ones by email.',
    'statement_title' => 'Email a statement',
    'statement_intro' => 'Choose the dates. We will email your statement for that period, as a PDF, to the email address on your account.',
    'statement_from' => 'From',
    'statement_to' => 'To',
    'statement_email' => 'Send to',
    'statement_email_note' => 'Statements only go to the email address on your account.',
    'statement_send' => 'Send to my email',
    'statement_sending' => 'Sending…',
    'statement_sent' => 'Your statement for :period was sent to :email.',
    'statement_future' => 'Choose today or an earlier date.',
    'statement_too_early' => 'Your account didn\'t exist yet on that date. Choose a later date.',
    'statement_to_before_from' => 'The To date must be on or after the From date.',
    'statement_too_long' => 'A statement can cover up to one year. Choose a shorter period.',
];
