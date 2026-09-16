@extends('layouts.admin')

@section('title', 'Support · '.$customer->name)

@section('content')
  <div class="admin-header">
    <p class="eyebrow"><a href="<?= e(route('admin.support')) ?>">← Support inbox</a></p>
    <h1>
      <?= e($customer->name) ?>
      <?php if ($conversation): ?>
        <span class="admin-pill <?= e($conversation->isOpen() ? 'status-active' : 'status-frozen') ?>" style="margin-left:8px; vertical-align:middle;"><?= e($conversation->statusLabel()) ?></span>
        <?php if ($conversation->isUrgent()): ?>
          <span class="admin-pill status-disabled" style="vertical-align:middle;">Urgent</span>
        <?php endif; ?>
        <span class="admin-badge" style="vertical-align:middle;"><?= e($conversation->categoryLabel()) ?></span>
      <?php endif; ?>
    </h1>
    <p>
      <?= e($customer->email) ?>
      <?php if ($earlierCount > 0): ?>
        &middot; <a href="<?= e(route('admin.support.history', $customer)) ?>" style="color:var(--admin-accent); font-weight:600;"><?= e($earlierCount) ?> earlier conversation<?= e($earlierCount === 1 ? '' : 's') ?> →</a>
      <?php endif; ?>
      <?php if ($conversation): ?>
        &middot;
        <form method="POST" action="<?= e(route('admin.support.priority', $customer)) ?>" style="display:inline;">
          <?= csrf_field() ?>
          <button type="submit" class="admin-btn admin-btn-outline" style="padding:2px 10px; font-size:11.5px;">
            <?= e($conversation->isUrgent() ? 'Clear urgent' : 'Mark urgent') ?>
          </button>
        </form>
      <?php endif; ?>
    </p>
  </div>

  <?php if (! $isLatest): ?>
    <div class="admin-status" style="background:rgba(90,150,220,0.1); color:#3568A8; border-color:rgba(90,150,220,0.3);">
      You're viewing an earlier conversation, not the current one. <a href="<?= e(route('admin.support.show', $customer)) ?>" style="font-weight:700; color:inherit;">Go to the current conversation →</a>
    </div>
  <?php endif; ?>

  <div class="admin-card">
    <?php if (! $conversation): ?>
      <p class="admin-empty"><?= e($customer->name) ?> hasn't started a conversation yet.</p>
    <?php else: ?>
      <div
        class="chat-thread"
        id="adminChatThread"
        data-last-id="<?= e(optional($messages->last())->id ?? 0) ?>"
        data-open="<?= e(($conversation->isOpen() && $isLatest) ? '1' : '0') ?>"
        style="max-height:56vh; overflow-y:auto; padding-right:4px;"
      >
        <?php $__ledger_forelse_1 = true; foreach ($messages as $message): $__ledger_forelse_1 = false; ?>
          <div class="msg-row <?= e($message->isFromAdmin() ? 'support' : 'user') ?>" data-msg-id="<?= e($message->id) ?>">
            <div class="msg-avatar">
              <?php if (! $message->isFromAdmin() && $customer->avatar): ?>
                <img src="<?= e($customer->avatar) ?>" alt="<?= e($customer->name) ?>" style="width:100%; height:100%; border-radius:50%; object-fit:cover;">
              <?php else: ?>
                <?= e($message->isFromAdmin() ? 'LS' : $customer->initials()) ?>
              <?php endif; ?>
            </div>
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
              <span class="msg-time">
                <?= e($message->created_at->format('M j, g:i A')) ?>
                <?php if ($message->is_bot): ?>
                  <span class="admin-badge" style="margin-left:4px;">Bot</span>
                <?php endif; ?>
              </span>
            </div>
          </div>
        <?php endforeach; if ($__ledger_forelse_1): ?>
          <p class="admin-empty">No messages yet. <?= e($customer->name) ?> hasn't written in.</p>
        <?php endif; ?>
      </div>

      <?php /* Tells the admin at a glance whether the bot is still answering
           this conversation or whether it's already gone quiet because
           someone has replied for real — see
           SupportController::maybeSendBotReply() for the exact rule (any
           real, non-bot admin reply turns this off permanently for this
           one conversation; starting a new conversation resets it). */ ?>
      <div class="support-status-bar" style="margin-top:16px; padding-top:16px; border-top:1px solid var(--admin-border); border-bottom:none; margin-bottom:0;">
        <div class="msg-avatar" style="background:var(--admin-dark); color:#fff;">LS</div>
        <div>
          <?php if ($conversation->isOpen()): ?>
            <?php if ($messages->contains(fn ($m) => $m->isFromRealAdmin())): ?>
              <p style="margin:0; font-size:12.5px; color:var(--admin-text-2);">You've already replied here. The bot has stopped answering for <?= e($customer->name) ?>.</p>
            <?php else: ?>
              <p style="margin:0; font-size:12.5px; color:var(--admin-text-2);">The bot is still answering <?= e($customer->name) ?>. Send a reply below to take over yourself.</p>
            <?php endif; ?>
          <?php else: ?>
            <p style="margin:0; font-size:12.5px; color:var(--admin-text-2);">
              <?= e($conversation->closedReasonLabel()) ?>
              <?php if ($conversation->closed_at): ?> Ended <?= e($conversation->closed_at->diffForHumans()) ?>. <?php endif; ?>
            </p>
          <?php endif; ?>
        </div>
      </div>

      <?php if ($conversation->isOpen() && $isLatest): ?>
        <div class="chat-input-bar" style="margin-top:16px;">
          <button type="button" class="icon-btn" style="flex-shrink:0;" onclick="document.getElementById('adminAttachmentInput').click();" title="Attach a photo or PDF" aria-label="Attach a file">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21.44 11.05 12.25 20.24a5 5 0 0 1-7.07-7.07l9.19-9.19a3.5 3.5 0 0 1 4.95 4.95L10.83 17.44a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg>
          </button>
          <input type="file" id="adminAttachmentInput" accept="image/png,image/jpeg,image/gif,image/webp,application/pdf" style="display:none;" onchange="sendAdminAttachment(this)">
          <input type="text" id="adminChatInput" placeholder="Reply as Ledger Support…" oninput="pingAdminTyping()" onkeydown="if(event.key==='Enter'){ event.preventDefault(); sendAdminReply(); }">
          <button type="button" class="chat-send-btn" onclick="sendAdminReply()">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 2 11 13"/><path d="M22 2 15 22l-4-9-9-4 20-7Z"/></svg>
          </button>
        </div>

        <div class="admin-conversation-actions">
          <form method="POST" action="<?= e(route('admin.support.timer', $customer)) ?>" style="display:flex; align-items:center; gap:8px;">
            <?= csrf_field() ?>
            <label style="font-size:12.5px; color:var(--admin-text-2);">Auto-close if no reply within</label>
            <select name="hours" class="admin-select">
              <option value="1">1 hour</option>
              <option value="24" selected>24 hours</option>
              <option value="72">3 days</option>
              <option value="168">7 days</option>
            </select>
            <button type="submit" class="admin-btn admin-btn-outline">Set</button>
          </form>

          <form method="POST" action="<?= e(route('admin.support.end', $customer)) ?>" data-confirm="End this conversation? <?= e($customer->first_name) ?> will be able to start a new one any time." data-confirm-danger="1" data-confirm-button="End conversation">
            <?= csrf_field() ?>
            <button type="submit" class="admin-btn admin-btn-danger">End conversation</button>
          </form>
        </div>

        <?php if ($conversation->timeout_at): ?>
          <p style="margin:10px 0 0; font-size:12px; color:var(--admin-text-2);">
            This conversation will auto-close <?= e($conversation->timeout_at->diffForHumans()) ?> if <?= e($customer->first_name) ?> hasn't replied by then.
          </p>
        <?php endif; ?>
      <?php endif; ?>
    <?php endif; ?>
  </div>
@endsection

@section('scripts')
<script>
  // Small Blade-rendered bootstrap for this one page — the CSRF token,
  // route URLs, this customer's initials/avatar can't live in the static
  // admin.js file, so they're handed off here as data instead. The rest
  // of the chat logic (polling, the live stream, sending, rendering
  // bubbles) lives in admin_asset/js/admin.js, which reads this object
  // when it loads right after this block.
  window.LedgerAdminChat = {
    csrfToken: '<?= e(csrf_token()) ?>',
    storeUrl: '<?= e(route('admin.support.messages.store', $customer)) ?>',
    pollUrl: '<?= e(route('admin.support.messages.poll', $customer)) ?>',
    streamUrl: '<?= e(route('admin.support.stream', $customer)) ?>',
    typingUrl: '<?= e(route('admin.support.typing', $customer)) ?>',
    customerInitials: '<?= e($customer->initials()) ?>',
    // Null when the customer has no approved KYC selfie yet — see
    // User::avatar(). Kept here (rather than re-reading $customer via a
    // second inline JSON call from admin.js) so the live-appended
    // messages show the same photo the page-loaded ones already do,
    // without a second round trip.
    customerAvatarUrl: <?= json_encode($customer->avatar) ?>,
  };
</script>
@endsection
