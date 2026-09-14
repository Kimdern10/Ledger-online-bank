<?php

// Floating "chat with us" widget on the public marketing pages
// (layouts/partials/guest-support-widget.blade.php). Distinct from the
// signed-in dashboard support chat (see lang/en/support.php) — this one
// talks to GuestSupportController and starts with a name/email intake form.
return [
    'chat_with_support' => 'Chat with support',
    'panel_title' => 'Ledger Support',
    'panel_sub' => 'We usually reply within a few minutes',
    'end_chat' => 'End chat',
    'close_chat' => 'Close chat',
    'field_your_name' => 'Your name',
    'field_email' => 'Email address',
    'field_how_can_we_help' => 'How can we help?',
    'start_chat' => 'Start chat',
    'error_generic' => 'Something went wrong. Try again.',
    'attach_a_photo' => 'Attach a photo',
    'type_a_message' => 'Type a message…',
    'send' => 'Send',
    'conversation_ended' => 'This conversation has ended.',
    'start_new_conversation' => 'Start a new conversation',
    'end_chat_confirm' => "End this conversation? You'll need to start a new one to reach us again.",
];
