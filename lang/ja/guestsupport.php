<?php

// Floating "chat with us" widget on the public marketing pages
// (layouts/partials/guest-support-widget.blade.php). Distinct from the
// signed-in dashboard support chat (see lang/en/support.php) — this one
// talks to GuestSupportController and starts with a name/email intake form.
// "Ledger" itself stays untranslated (see lang/ja/authpage.php).
return [
    'chat_with_support' => 'サポートとチャットする',
    'panel_title' => 'Ledgerサポート',
    'panel_sub' => '通常、数分以内に返信いたします',
    'end_chat' => 'チャットを終了',
    'close_chat' => 'チャットを閉じる',
    'field_your_name' => 'お名前',
    'field_email' => 'メールアドレス',
    'field_how_can_we_help' => 'どのようなご用件でしょうか?',
    'start_chat' => 'チャットを開始',
    'error_generic' => '問題が発生しました。もう一度お試しください。',
    'attach_a_photo' => '写真を添付',
    'type_a_message' => 'メッセージを入力…',
    'send' => '送信',
    'conversation_ended' => 'この会話は終了しました。',
    'start_new_conversation' => '新しい会話を始める',
    'end_chat_confirm' => 'この会話を終了しますか?再度お問い合わせいただくには、新しい会話を開始する必要があります。',
];
