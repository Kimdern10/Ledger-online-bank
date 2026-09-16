@extends('layouts.app')

@section('content')
<div class="send-wrap support-wrap">

  <div class="page-header fade-in d1">
    <a href="<?= e(url()->previous()) ?>" class="icon-btn">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
    </a>
    <h1><?= e(__('support.title')) ?></h1>
    <div style="display:flex; align-items:center; gap:6px;">
      <?php if (!$isHistoryView && $conversation->isOpen()): ?>
        <button type="button" class="icon-btn" onclick="confirmStartNew()" title="<?= e(__('support.start_new')) ?>" aria-label="<?= e(__('support.start_new')) ?>">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-2.64-6.36"/><path d="M21 3v6h-6"/></svg>
        </button>
      <?php endif; ?>
      <a href="<?= e(route('dashboard')) ?>" class="icon-btn">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><path d="M3 11l9-8 9 8"/><path d="M5 10v10h14V10"/></svg>
      </a>
    </div>
  </div>

  <div class="support-status fade-in d2">
    <div class="sa-avatar">LS</div>
    <div class="sa-text">
      <p class="n"><?= e(__('support.header_title')) ?></p>
      <p class="s">
        <?php if ($isHistoryView): ?>
          <?= e(__('support.viewing_past')) ?>
        <?php elseif ($conversation->isOpen()): ?>
          <?= e(__('support.assistant_intro')) ?>
        <?php else: ?>
          <?= e($conversation->closedReasonLabel()) ?>
        <?php endif; ?>
      </p>
    </div>
  </div>

  <?php if ($hasHistory): ?>
    <p class="fade-in d2" style="margin:-6px 0 14px; font-size:12.5px;">
      <?php if ($isHistoryView): ?>
        <a href="<?= e(route('support.history')) ?>" style="color:#2F6F62; font-weight:600;">← <?= e(__('support.back_to_conversations')) ?></a>
      <?php else: ?>
        <a href="<?= e(route('support.history')) ?>" style="color:var(--text-3, #5C6B72); font-weight:600;"><?= e(__('support.view_past_conversations')) ?> →</a>
      <?php endif; ?>
    </p>
  <?php endif; ?>

  <?php /* Every row below is a real message from support_messages, scoped to
       this one conversation (see SupportController::index()/showSession()).
       New replies an admin sends while a LIVE conversation is open arrive
       via polling, not a page refresh — see the script at the bottom. */ ?>
  <div
    class="chat-thread fade-in d3"
    id="chatThread"
    data-last-id="<?= e(optional($messages->last())->id ?? 0) ?>"
    data-open="<?= e(($conversation->isOpen() && !$isHistoryView) ? '1' : '0') ?>"
  >

    <?php $__ledger_forelse_1 = true; foreach ($messages as $message): $__ledger_forelse_1 = false; ?>
      <div class="msg-row <?= e($message->isFromAdmin() ? 'support' : 'user') ?>" data-msg-id="<?= e($message->id) ?>">
        <?php if ($message->isFromAdmin()): ?>
          <div class="msg-avatar">LS</div>
        <?php endif; ?>
        <div class="msg-col">
          <div class="msg-bubble">
            <?php if ($message->hasAttachment()): ?>
              <?php if ($message->isImageAttachment()): ?>
                <a href="<?= e($message->attachmentUrl()) ?>" target="_blank" rel="noopener">
                  <img src="<?= e($message->attachmentUrl()) ?>" alt="<?= e($message->attachment_name) ?>" class="msg-attachment-img">
                </a>
              <?php else: ?>
                <a href="<?= e($message->attachmentUrl()) ?>" target="_blank" rel="noopener" class="msg-attachment-file">📎 <?= e($message->attachment_name) ?></a>
              <?php endif; ?>
            <?php endif; ?>
            <?php if (trim($message->body) !== ''): ?>
              <div <?php if ($message->hasAttachment()): ?> style="margin-top:6px;" <?php endif; ?>><?= e($message->body) ?></div>
            <?php endif; ?>
          </div>
          <span class="msg-time"><?= e($message->created_at->format('g:i A')) ?></span>
        </div>
      </div>
    <?php endforeach; if ($__ledger_forelse_1): ?>
      <?php if ($conversation->isOpen() && !$isHistoryView): ?>
        <div class="msg-row support">
          <div class="msg-avatar">LS</div>
          <div class="msg-col">
            <div class="msg-bubble"><?= e(__('support.greeting', ['name' => auth()->user()->first_name])) ?></div>
          </div>
        </div>

        <?php /* The onclick payload sent to the server stays the literal English
             phrase on purpose — SupportController::maybeSendBotReply()
             matches the customer's message text against hardcoded English
             keywords ('card issue', 'payment', etc.) to pick a canned reply
             and auto-categorize the conversation. Only the visible button
             label is translated; sending a translated phrase instead would
             silently break bot matching for every non-English customer. */ ?>
        <div class="quick-replies" id="quickReplies">
          <button type="button" class="quick-amt-btn" onclick="sendMessageText('Card issue')"><?= e(__('support.quick_card_issue')) ?></button>
          <button type="button" class="quick-amt-btn" onclick="sendMessageText('Payment problem')"><?= e(__('support.quick_payment_problem')) ?></button>
          <button type="button" class="quick-amt-btn" onclick="sendMessageText('Account access')"><?= e(__('support.quick_account_access')) ?></button>
          <button type="button" class="quick-amt-btn" onclick="sendMessageText('Something else')"><?= e(__('support.quick_something_else')) ?></button>
        </div>
      <?php endif; ?>
    <?php endif; ?>

    <?php /* Shown/hidden by poll()'s admin_typing flag — see pollForReplies()
         below. Only present at all for a live, open conversation. */ ?>
    <?php if ($conversation->isOpen() && !$isHistoryView): ?>
      <div class="msg-row support" id="typingRow" style="display:none;">
        <div class="msg-avatar">LS</div>
        <div class="msg-col">
          <div class="msg-bubble typing-bubble"><span></span><span></span><span></span></div>
        </div>
      </div>
    <?php endif; ?>

  </div>

  <?php if ($conversation->isOpen() && !$isHistoryView): ?>
    <div class="chat-input-wrap">
      <div class="chat-input-bar">
        <button type="button" class="icon-btn" style="flex-shrink:0;" onclick="document.getElementById('chatAttachmentInput').click();" title="<?= e(__('support.attach_file')) ?>" aria-label="<?= e(__('support.attach_file')) ?>">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21.44 11.05 12.25 20.24a5 5 0 0 1-7.07-7.07l9.19-9.19a3.5 3.5 0 0 1 4.95 4.95L10.83 17.44a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg>
        </button>
        <input type="file" id="chatAttachmentInput" accept="image/png,image/jpeg,image/gif,image/webp,application/pdf" style="display:none;" onchange="sendAttachment(this)">
        <input type="text" id="chatInput" placeholder="<?= e(__('support.type_message')) ?>" onkeydown="if(event.key==='Enter'){ event.preventDefault(); sendMessageText(this.value); this.value=''; }">
        <button type="button" class="chat-send-btn" onclick="sendMessageText(document.getElementById('chatInput').value); document.getElementById('chatInput').value='';">
          <svg viewBox="0 0 24 24" fill="none"><path d="M22 2 11 13"/><path d="M22 2 15 22l-4-9-9-4 20-7Z"/></svg>
        </button>
      </div>
    </div>
  <?php else: ?>
    <?php /* The conversation shown above has ended (or this is a past session
         being viewed read-only) — no input box, just how/why it ended and,
         unless this IS a past-session view already, a way to start fresh. */ ?>
    <div class="support-ended-banner fade-in d3">
      <p><?= e($conversation->closedReasonLabel()) ?></p>
      <?php if (!($isHistoryView)): ?>
        <form method="POST" action="<?= e(route('support.new')) ?>">
          <?= csrf_field() ?>
          <button type="submit" class="quick-amt-btn active"><?= e(__('support.start_new')) ?></button>
        </form>
      <?php endif; ?>
    </div>
  <?php endif; ?>

  <?php /* Backs the header's "start new" icon button above — a plain form
       submit (with a confirm() prompt first) rather than fetch, since a
       full page reload is exactly what should happen afterward anyway. */ ?>
  <form id="startNewForm" method="POST" action="<?= e(route('support.new')) ?>" style="display:none;"><?= csrf_field() ?></form>

</div>

<script>
  window.LedgerSupportChatConfig = {
    csrfToken: '<?= e(csrf_token()) ?>',
    storeUrl: '<?= e(route('support.messages.store')) ?>',
    pollUrl: '<?= e(route('support.messages.poll')) ?>',
    streamUrl: '<?= e(route('support.stream')) ?>',
    i18n: {
      startNewConfirm: <?= json_encode(__('support.start_new_confirm')) ?>,
      startNewButton: <?= json_encode(__('support.start_new_button')) ?>,
      notSentRetry: <?= json_encode(__('support.not_sent_retry')) ?>,
    },
  };
</script>
@endsection
