<?php

return [
    // One-time transfer codes — see App\Support\TransferCode and
    // openPinModal() in dashboard.js.
    'sending' => 'Sending a 4-digit code to your email…',
    'sent_to' => 'We sent a 4-digit code to :email. It expires in 10 minutes.',
    'send_failed' => "We couldn't send your code. Try again.",
    'resend' => 'Resend code',
    'incorrect' => 'That code is incorrect or has expired. Request a new one and try again.',

    // By text message instead (App\Support\Sms).
    'sms_body' => 'Ledger: your code to confirm this payment is :code. It expires in 10 minutes. Never share it with anyone.',
    'sending_text' => 'Texting a 4-digit code to your phone…',
    'texted_to' => 'We texted a 4-digit code to :phone. It expires in 10 minutes.',
    'text_instead' => 'Text it to me instead',
    'email_instead' => 'Email it instead',
    'no_phone' => 'To get codes by text, add your phone number with its country code (for example +234 801 234 5678) in Profile settings.',
    'sms_failed' => "We couldn't send a text right now. Try again, or get the code by email.",
    'sms_too_many' => 'Too many texts were sent to your phone. Please wait 10 minutes, or get the code by email.',
];
