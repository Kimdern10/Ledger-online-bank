@extends('layouts.admin')

@section('title', 'Support · '.$customer->name)

@section('content')
  <div class="admin-header">
    <p class="eyebrow"><a href="{{ route('admin.support') }}">← Support inbox</a></p>
    <h1>
      {{ $customer->name }}
      @if($conversation)
        <span class="admin-pill {{ $conversation->isOpen() ? 'status-active' : 'status-frozen' }}" style="margin-left:8px; vertical-align:middle;">{{ $conversation->statusLabel() }}</span>
        @if($conversation->isUrgent())
          <span class="admin-pill status-disabled" style="vertical-align:middle;">Urgent</span>
        @endif
        <span class="admin-badge" style="vertical-align:middle;">{{ $conversation->categoryLabel() }}</span>
      @endif
    </h1>
    <p>
      {{ $customer->email }}
      @if($earlierCount > 0)
        &middot; <a href="{{ route('admin.support.history', $customer) }}" style="color:var(--admin-accent); font-weight:600;">{{ $earlierCount }} earlier conversation{{ $earlierCount === 1 ? '' : 's' }} →</a>
      @endif
      @if($conversation)
        &middot;
        <form method="POST" action="{{ route('admin.support.priority', $customer) }}" style="display:inline;">
          @csrf
          <button type="submit" class="admin-btn admin-btn-outline" style="padding:2px 10px; font-size:11.5px;">
            {{ $conversation->isUrgent() ? 'Clear urgent' : 'Mark urgent' }}
          </button>
        </form>
      @endif
    </p>
  </div>

  @if(! $isLatest)
    <div class="admin-status" style="background:rgba(90,150,220,0.1); color:#3568A8; border-color:rgba(90,150,220,0.3);">
      You're viewing an earlier conversation, not the current one. <a href="{{ route('admin.support.show', $customer) }}" style="font-weight:700; color:inherit;">Go to the current conversation →</a>
    </div>
  @endif

  <div class="admin-card">
    @if(! $conversation)
      <p class="admin-empty">{{ $customer->name }} hasn't started a conversation yet.</p>
    @else
      <div
        class="chat-thread"
        id="adminChatThread"
        data-last-id="{{ optional($messages->last())->id ?? 0 }}"
        data-open="{{ ($conversation->isOpen() && $isLatest) ? '1' : '0' }}"
        style="max-height:56vh; overflow-y:auto; padding-right:4px;"
      >
        @forelse($messages as $message)
          <div class="msg-row {{ $message->isFromAdmin() ? 'support' : 'user' }}" data-msg-id="{{ $message->id }}">
            <div class="msg-avatar">
              @if(! $message->isFromAdmin() && $customer->avatar)
                <img src="{{ $customer->avatar }}" alt="{{ $customer->name }}" style="width:100%; height:100%; border-radius:50%; object-fit:cover;">
              @else
                {{ $message->isFromAdmin() ? 'LS' : $customer->initials() }}
              @endif
            </div>
            <div class="msg-col">
              <div class="msg-bubble">
                @if($message->hasAttachment())
                  @if($message->isImageAttachment())
                    <a href="{{ $message->attachmentUrl() }}" target="_blank" rel="noopener">
                      <img src="{{ $message->attachmentUrl() }}" alt="{{ $message->attachment_name }}" class="msg-attachment-img">
                    </a>
                  @else
                    <a href="{{ $message->attachmentUrl() }}" target="_blank" rel="noopener" class="msg-attachment-file">📎 {{ $message->attachment_name }}</a>
                  @endif
                @endif
                @if(trim($message->body) !== '')
                  <div @if($message->hasAttachment()) style="margin-top:6px;" @endif>{{ $message->body }}</div>
                @endif
              </div>
              <span class="msg-time">
                {{ $message->created_at->format('M j, g:i A') }}
                @if($message->is_bot)
                  <span class="admin-badge" style="margin-left:4px;">Bot</span>
                @endif
              </span>
            </div>
          </div>
        @empty
          <p class="admin-empty">No messages yet. {{ $customer->name }} hasn't written in.</p>
        @endforelse
      </div>

      {{-- Tells the admin at a glance whether the bot is still answering
           this conversation or whether it's already gone quiet because
           someone has replied for real — see
           SupportController::maybeSendBotReply() for the exact rule (any
           real, non-bot admin reply turns this off permanently for this
           one conversation; starting a new conversation resets it). --}}
      <div class="support-status-bar" style="margin-top:16px; padding-top:16px; border-top:1px solid var(--admin-border); border-bottom:none; margin-bottom:0;">
        <div class="msg-avatar" style="background:var(--admin-dark); color:#fff;">LS</div>
        <div>
          @if($conversation->isOpen())
            @if($messages->contains(fn ($m) => $m->isFromRealAdmin()))
              <p style="margin:0; font-size:12.5px; color:var(--admin-text-2);">You've already replied here. The bot has stopped answering for {{ $customer->name }}.</p>
            @else
              <p style="margin:0; font-size:12.5px; color:var(--admin-text-2);">The bot is still answering {{ $customer->name }}. Send a reply below to take over yourself.</p>
            @endif
          @else
            <p style="margin:0; font-size:12.5px; color:var(--admin-text-2);">
              {{ $conversation->closedReasonLabel() }}
              @if($conversation->closed_at) Ended {{ $conversation->closed_at->diffForHumans() }}. @endif
            </p>
          @endif
        </div>
      </div>

      @if($conversation->isOpen() && $isLatest)
        <div class="chat-input-bar" style="margin-top:16px;">
          <button type="button" class="icon-btn" style="flex-shrink:0;" onclick="document.getElementById('adminAttachmentInput').click();" title="Attach a photo or PDF" aria-label="Attach a file">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21.44 11.05 12.25 20.24a5 5 0 0 1-7.07-7.07l9.19-9.19a3.5 3.5 0 0 1 4.95 4.95L10.83 17.44a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg>
          </button>
          <input type="file" id="adminAttachmentInput" accept="image/png,image/jpeg,image/gif,image/webp,application/pdf" style="display:none;" onchange="sendAdminAttachment(this)">
          <input type="text" id="adminChatInput" placeholder="Reply as Ledger Support…" oninput="pingTyping()" onkeydown="if(event.key==='Enter'){ event.preventDefault(); sendAdminReply(); }">
          <button type="button" class="chat-send-btn" onclick="sendAdminReply()">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 2 11 13"/><path d="M22 2 15 22l-4-9-9-4 20-7Z"/></svg>
          </button>
        </div>

        <div class="admin-conversation-actions">
          <form method="POST" action="{{ route('admin.support.timer', $customer) }}" style="display:flex; align-items:center; gap:8px;">
            @csrf
            <label style="font-size:12.5px; color:var(--admin-text-2);">Auto-close if no reply within</label>
            <select name="hours" class="admin-select">
              <option value="1">1 hour</option>
              <option value="24" selected>24 hours</option>
              <option value="72">3 days</option>
              <option value="168">7 days</option>
            </select>
            <button type="submit" class="admin-btn admin-btn-outline">Set</button>
          </form>

          <form method="POST" action="{{ route('admin.support.end', $customer) }}" data-confirm="End this conversation? {{ $customer->first_name }} will be able to start a new one any time." data-confirm-danger="1" data-confirm-button="End conversation">
            @csrf
            <button type="submit" class="admin-btn admin-btn-danger">End conversation</button>
          </form>
        </div>

        @if($conversation->timeout_at)
          <p style="margin:10px 0 0; font-size:12px; color:var(--admin-text-2);">
            This conversation will auto-close {{ $conversation->timeout_at->diffForHumans() }} if {{ $customer->first_name }} hasn't replied by then.
          </p>
        @endif
      @endif
    @endif
  </div>

  <style>
    /* The admin dashboard's own stylesheet (layouts/admin.blade.php) only
       defines .admin-* classes — it never styled .chat-thread/.msg-row/
       .msg-bubble/etc., so this thread was rendering as plain unstyled
       text before. This recreates the same chat-bubble look the
       customer-facing support page has, using the admin panel's own
       green/dark palette for consistency with the rest of /admin. */
    .chat-thread{ display:flex; flex-direction:column; gap:14px; }
    .msg-row{ display:flex; gap:10px; align-items:flex-end; max-width:78%; }
    .msg-row.user{ align-self:flex-start; }
    .msg-row.support{ align-self:flex-end; flex-direction:row-reverse; }
    .msg-avatar{
      width:32px; height:32px; border-radius:50%; flex-shrink:0;
      display:flex; align-items:center; justify-content:center;
      font-size:11.5px; font-weight:700; background:rgba(47,111,98,0.14); color:var(--admin-accent);
    }
    .msg-row.support .msg-avatar{ background:var(--admin-dark); color:#fff; }
    .msg-col{ display:flex; flex-direction:column; gap:4px; min-width:0; }
    .msg-row.support .msg-col{ align-items:flex-end; }
    .msg-bubble{
      background:var(--admin-bg); border:1px solid var(--admin-border); color:var(--admin-text);
      padding:10px 14px; border-radius:16px; font-size:13.5px; line-height:1.45; word-wrap:break-word;
    }
    .msg-row.support .msg-bubble{ background:var(--admin-dark); color:#fff; border-color:var(--admin-dark); }
    .msg-time{ font-size:11px; color:var(--admin-text-2); padding:0 4px; }
    .chat-input-bar{
      display:flex; align-items:center; gap:6px; border:1px solid var(--admin-border);
      border-radius:24px; padding:5px 6px 5px 16px; background:#fff;
    }
    .chat-input-bar input[type="text"]{
      flex:1; border:none; outline:none; font-size:13.5px; font-family:inherit; background:none;
      color:var(--admin-text); padding:8px 0;
    }
    .chat-send-btn{
      width:34px; height:34px; border-radius:50%; border:none; background:var(--admin-accent); color:#fff;
      display:flex; align-items:center; justify-content:center; cursor:pointer; flex-shrink:0;
    }
    .chat-send-btn svg{ width:15px; height:15px; }
    .chat-send-btn:hover{ opacity:0.9; }
    .icon-btn{
      width:32px; height:32px; border-radius:50%; border:none; background:none; color:var(--admin-text-2);
      display:flex; align-items:center; justify-content:center; cursor:pointer; flex-shrink:0;
    }
    .icon-btn:hover{ background:rgba(16,32,47,0.06); color:var(--admin-text); }
    .icon-btn svg{ width:17px; height:17px; }
    .support-status-bar{
      display:flex; align-items:center; gap:10px; padding:10px 2px 16px; margin-bottom:16px;
      border-bottom:1px solid var(--admin-border);
    }
    .admin-select{
      border:1px solid var(--admin-border); border-radius:8px; padding:6px 10px; font-size:12.5px;
      font-family:inherit; background:#fff; color:var(--admin-text);
    }
    .admin-conversation-actions{
      display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;
      margin-top:16px; padding-top:16px; border-top:1px solid var(--admin-border);
    }
    .msg-attachment-img{ max-width:220px; max-height:220px; border-radius:10px; display:block; }
    .msg-attachment-file{ display:inline-flex; align-items:center; gap:6px; font-weight:600; color:inherit; text-decoration:underline; }
  </style>
@endsection

@section('scripts')
<script>
  const csrfToken = '{{ csrf_token() }}';
  const storeUrl = '{{ route('admin.support.messages.store', $customer) }}';
  const pollUrl = '{{ route('admin.support.messages.poll', $customer) }}';
  const streamUrl = '{{ route('admin.support.stream', $customer) }}';
  const typingUrl = '{{ route('admin.support.typing', $customer) }}';
  const customerInitials = '{{ $customer->initials() }}';
  // Null when the customer has no approved KYC selfie yet — see
  // User::avatar(). Kept as a JS var (rather than re-reading $customer via
  // a second inline JSON call) so the live-appended messages below show the
  // same photo the page-loaded ones already do, without a second round trip.
  const customerAvatarUrl = @json($customer->avatar);
  const adminThreadEl = document.getElementById('adminChatThread');
  const conversationOpen = adminThreadEl ? adminThreadEl.dataset.open === '1' : false;
  let lastId = adminThreadEl ? parseInt(adminThreadEl.dataset.lastId || '0', 10) : 0;
  let lastTypingPingAt = 0;

  function scrollThreadToBottom(){
    if (adminThreadEl) adminThreadEl.scrollTop = adminThreadEl.scrollHeight;
  }

  // Pinged on every keystroke in the reply box, but only actually sent to
  // the server at most once every 2.5 seconds — the customer's "Support is
  // typing…" indicator only needs to be roughly live, not exact, and this
  // keeps a long reply from firing dozens of requests.
  function pingTyping(){
    const now = Date.now();
    if (now - lastTypingPingAt < 2500) return;
    lastTypingPingAt = now;
    fetch(typingUrl, {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
    }).catch(function(){});
  }

  function renderBubbleContent(bubbleEl, text, attachment){
    bubbleEl.innerHTML = '';

    if (attachment) {
      const a = document.createElement('a');
      a.href = attachment.url;
      a.target = '_blank';
      a.rel = 'noopener';

      if (attachment.isImage) {
        const img = document.createElement('img');
        img.src = attachment.url;
        img.alt = attachment.name;
        img.className = 'msg-attachment-img';
        a.appendChild(img);
      } else {
        a.className = 'msg-attachment-file';
        a.textContent = '📎 ' + attachment.name;
      }

      bubbleEl.appendChild(a);
    }

    if (text) {
      const div = document.createElement('div');
      if (attachment) div.style.marginTop = '6px';
      div.textContent = text;
      bubbleEl.appendChild(div);
    }
  }

  // Same dedupe rule as the customer-facing page (support.blade.php) —
  // see its appendMessage() comment for why.
  function appendAdminMessage(id, text, isAdmin, time, attachment){
    if (id !== null && document.querySelector('#adminChatThread [data-msg-id="' + id + '"]')) return null;

    const row = document.createElement('div');
    row.className = 'msg-row ' + (isAdmin ? 'support' : 'user');
    if (id !== null) row.dataset.msgId = id;
    const avatarInner = isAdmin
      ? 'LS'
      : (customerAvatarUrl ? '<img src="' + customerAvatarUrl + '" alt="" style="width:100%;height:100%;border-radius:50%;object-fit:cover;">' : customerInitials);
    row.innerHTML =
      '<div class="msg-avatar">' + avatarInner + '</div>' +
      '<div class="msg-col">' +
        '<div class="msg-bubble"></div>' +
        '<span class="msg-time">' + time + '</span>' +
      '</div>';
    renderBubbleContent(row.querySelector('.msg-bubble'), text, attachment || null);
    adminThreadEl.appendChild(row);
    scrollThreadToBottom();
    return row;
  }

  function adminMessageFromPayload(m){
    return appendAdminMessage(
      m.id, m.body, m.is_from_admin, m.time,
      m.attachment_url ? { url: m.attachment_url, name: m.attachment_name, isImage: m.is_image } : null
    );
  }

  // Shared by the text box and the attachment picker — always sent as
  // multipart/form-data since a JSON body can't carry a file.
  function sendAdminPayload(text, file){
    if (!text && !file) return;

    const localAttachment = file ? { url: URL.createObjectURL(file), name: file.name, isImage: file.type.indexOf('image/') === 0 } : null;
    const optimisticRow = appendAdminMessage(null, text, true, 'Sending…', localAttachment);

    const formData = new FormData();
    if (text) formData.append('body', text);
    if (file) formData.append('attachment', file);

    fetch(storeUrl, {
      method: 'POST',
      headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
      body: formData,
    })
      .then(function(res){
        if (res.status === 422) { window.location.reload(); throw new Error('conversation ended'); }
        if(!res.ok) throw new Error('failed');
        return res.json();
      })
      .then(function(json){
        if (optimisticRow) {
          optimisticRow.dataset.msgId = json.id;
          const t = optimisticRow.querySelector('.msg-time');
          if (t) t.textContent = json.time;
        }
        if (json.id > lastId) lastId = json.id;
      })
      .catch(function(){
        if (optimisticRow) {
          optimisticRow.querySelector('.msg-bubble').style.opacity = '0.55';
          const t = optimisticRow.querySelector('.msg-time');
          if (t) t.textContent = 'Not sent. Try again.';
        }
      });
  }

  function sendAdminReply(){
    const input = document.getElementById('adminChatInput');
    const text = input.value.trim();
    if(!text) return;
    input.value = '';
    sendAdminPayload(text, null);
  }

  function sendAdminAttachment(inputEl){
    const file = inputEl.files && inputEl.files[0];
    if (!file) return;
    sendAdminPayload('', file);
    inputEl.value = '';
  }

  // Slow safety-net poll — see AdminSupportController::poll(). Runs
  // alongside the live stream below in case that connection ever silently
  // stalls.
  function pollAdminThread(){
    fetch(pollUrl + '?after=' + lastId, { headers: { 'Accept': 'application/json' } })
      .then(function(res){ return res.json(); })
      .then(function(json){
        if (json.conversation_open === false) { window.location.reload(); return; }

        (json.messages || []).forEach(function(m){
          adminMessageFromPayload(m);
          if (m.id > lastId) lastId = m.id;
        });
      })
      .catch(function(){});
  }

  // The live channel — same Server-Sent-Events mechanism as the
  // customer-facing page (see support.blade.php's connectStream()).
  function connectAdminStream(){
    if (typeof EventSource === 'undefined') return;

    const es = new EventSource(streamUrl + '?after=' + lastId);

    es.addEventListener('message', function(e){
      const m = JSON.parse(e.data);
      adminMessageFromPayload(m);
      if (m.id > lastId) lastId = m.id;
    });

    es.addEventListener('status', function(e){
      const s = JSON.parse(e.data);
      if (!s.open) { es.close(); window.location.reload(); }
    });

    es.onerror = function(){
      // EventSource retries on its own; pollAdminThread() below covers the
      // gap if it can't reconnect for a while.
    };
  }

  scrollThreadToBottom();
  if (conversationOpen) {
    connectAdminStream();
    setInterval(pollAdminThread, 15000);
  }
</script>
@endsection
