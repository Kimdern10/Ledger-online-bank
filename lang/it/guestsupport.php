<?php

// Floating "chat with us" widget on the public marketing pages
// (layouts/partials/guest-support-widget.blade.php). Distinct from the
// signed-in dashboard support chat (see lang/en/support.php) — this one
// talks to GuestSupportController and starts with a name/email intake form.
// "Ledger" itself stays untranslated (see lang/it/authpage.php).
return [
    'chat_with_support' => 'Chatta con l\'assistenza',
    'panel_title' => 'Assistenza Ledger',
    'panel_sub' => 'Di solito rispondiamo entro pochi minuti',
    'end_chat' => 'Termina chat',
    'close_chat' => 'Chiudi chat',
    'field_your_name' => 'Il tuo nome',
    'field_email' => 'Indirizzo email',
    'field_how_can_we_help' => 'Come possiamo aiutarti?',
    'start_chat' => 'Avvia chat',
    'error_generic' => 'Si è verificato un errore. Riprova.',
    'attach_a_photo' => 'Allega una foto',
    'type_a_message' => 'Scrivi un messaggio…',
    'send' => 'Invia',
    'conversation_ended' => 'Questa conversazione è terminata.',
    'start_new_conversation' => 'Avvia una nuova conversazione',
    'end_chat_confirm' => 'Terminare questa conversazione? Dovrai avviarne una nuova per contattarci di nuovo.',
];
