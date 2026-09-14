@extends('layouts.admin')

@section('title', 'Guest message · '.$conversation->guest_name)

@section('content')
  <div class="admin-header">
    <p class="eyebrow"><a href="{{ route('admin.support.guests') }}">← Guest messages</a></p>
    <h1>
      {{ $conversation->guest_name }}
      <span class="admin-pill {{ $conversation->isOpen() ? 'status-active' : 'status-frozen' }}" style="margin-left:8px; vertical-align:middle;">{{ $conversation->statusLabel() }}</span>
    </h1>
    <p>{{ $conversation->guest_email }} &middot; hasn't signed up for an account</p>
  </div>

  <div class="admin-card">
    <div
      class="chat-thread"
      id="guestChatThread"
      data-last-id="{{ optional($messages->last())->id ?? 0 }}"
      data-open="{{ $conversation->isOpen() ? '1' : '0' }}"
      style="max-height:56vh; overflow-y:auto; padding-right:4px;"
    >
      @forelse($messages as $message)
        <div class="msg-row {{ $message->isFromAdmin() ? 'support' : 'user' }}" data-msg-id="{{ $message->id }}">
          <div class="msg-avatar">
            {{ $message->isFromAdmin() ? 'LS' : $conversation->initials() }}
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
              @if($message->is_system)
                <span class="admin-badge" style="margin-left:4px;">Auto-reply</span>
              @endif
            </span>
          </div>
        </div>
      @empty
        <p class="admin-empty">No messages yet.</p>
      @endforelse
    </div>

    @if($conversation->isOpen())
      <div class="chat-input-bar" style="margin-top:16px;">
        <button type="button" class="icon-btn" style="flex-shrink:0;" onclick="document.getElementById('guestAttachmentInput').click();" title="Attach a photo or PDF" aria-label="Attach a file">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21.44 11.05 12.25 20.24a5 5 0 0 1-7.07-7.07l9.19-9.19a3.5 3.5 0 0 1 4.95 4.95L10.83 17.44a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg>
        </button>
        <input type="file" id="guestAttachmentInput" accept="image/png,image/jpeg,image/gif,image/webp,application/pdf" style="display:none;" onchange="sendGuestAttachment(this)">
        <input type="text" id="guestChatInput" placeholder="Reply as Ledger Support…" oninput="pingTyping()" onkeydown="if(event.key==='Enter'){ event.preventDefault(); sendGuestReply(); }">
        <button type="button" class="chat-send-btn" onclick="sendGuestReply()">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 2 11 13"/><path d="M22 2 15 22l-4-9-9-4 20-7Z"/></svg>
        </button>
      </div>

      <div class="admin-conversation-actions">
        <span></span>
        <form method="POST" action="{{ route('admin.support.guests.end', $conversation) }}" data-confirm="End this conversation? The visitor will need to start a new one to reach you again." data-confirm-danger="1" data-confirm-button="End conversation">
          @csrf
          <button type="submit" class="admin-btn admin-btn-danger">End conversation</button>
        </form>
      </div>
    @else
      <p style="margin:16px 0 0; font-size:12.5px; color:var(--admin-text-2);">
        This conversation has ended.
        @if($conversation->closed_at) Ended {{ $conversation->closed_at->diffForHumans() }}. @endif
      </p>
    @endif
  </div>

  <style>
    /* Same chat-bubble classes as admin/support-show.blade.php — kept as
       its own local copy for the same reason that file's own doc comment
       gives: layouts/admin.blade.php's stylesheet only defines .admin-*
       classes, so every admin page with a chat thread styles its own. */
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
      white-space:pre-line;
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
    .admin-conversation-actions{
      display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;
      margin-top:16px; padding-top:16px; border-top:1px solid var(--admin-border);
    }
    .icon-btn{
      width:32px; height:32px; border-radius:50%; border:none; background:none; color:var(--admin-text-2);
      display:flex; align-items:center; justify-content:center; cursor:pointer; flex-shrink:0;
    }
    .icon-btn:hover{ background:rgba(16,32,47,0.06); color:var(--admin-text); }
    .icon-btn svg{ width:17px; height:17px; }
    .msg-attachment-img{ max-width:220px; max-height:220px; border-radius:10px; display:block; }
    .msg-attachment-file{ display:inline-flex; align-items:center; gap:6px; font-weight:600; color:inherit; text-decoration:underline; }
  </style>
@endsection

@section('scripts')
<script>
  const csrfToken = '{{ csrf_token() }}';
  const storeUrl = '{{ route('admin.support.guests.messages.store', $conversation) }}';
  const pollUrl = '{{ route('admin.support.guests.messages.poll', $conversation) }}';
  const typingUrl = '{{ route('admin.support.guests.typing', $conversation) }}';
  const guestInitials = '{{ $conversation->initials() }}';
  const guestThreadEl = document.getElementById('guestChatThread');
  const conversationOpen = guestThreadEl ? guestThreadEl.dataset.open === '1' : false;
  let lastId = guestThreadEl ? parseInt(guestThreadEl.dataset.lastId || '0', 10) : 0;
  let lastTypingPingAt = 0;

  function scrollThreadToBottom(){
    if (guestThreadEl) guestThreadEl.scrollTop = guestThreadEl.scrollHeight;
  }

  // Pinged on every keystroke in the reply box, but only actually sent to
  // the server at most once every 2.5 seconds — same throttling as
  // admin/support-show.blade.php's pingTyping(), which this mirrors.
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

  function appendGuestMessage(id, text, isAdmin, time, attachment){
    if (id !== null && document.querySelector('#guestChatThread [data-msg-id="' + id + '"]')) return null;

    const row = document.createElement('div');
    row.className = 'msg-row ' + (isAdmin ? 'support' : 'user');
    if (id !== null) row.dataset.msgId = id;
    row.innerHTML =
      '<div class="msg-avatar">' + (isAdmin ? 'LS' : guestInitials) + '</div>' +
      '<div class="msg-col">' +
        '<div class="msg-bubble"></div>' +
        '<span class="msg-time">' + time + '</span>' +
      '</div>';
    renderBubbleContent(row.querySelector('.msg-bubble'), text, attachment || null);
    guestThreadEl.appendChild(row);
    scrollThreadToBottom();
    return row;
  }

  function guestMessageFromPayload(m){
    return appendGuestMessage(
      m.id, m.body, m.is_from_admin, m.time,
      m.attachment_url ? { url: m.attachment_url, name: m.attachment_name, isImage: m.is_image } : null
    );
  }

  // Shared by the text box and the attachment picker — always sent as
  // multipart/form-data since a JSON body can't carry a file, same as the
  // logged-in support page.
  function sendGuestPayload(text, file){
    if (!text && !file) return;

    const localAttachment = file ? { url: URL.createObjectURL(file), name: file.name, isImage: file.type.indexOf('image/') === 0 } : null;
    const optimisticRow = appendGuestMessage(null, text, true, 'Sending…', localAttachment);

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
        if (!res.ok) throw new Error('failed');
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

  function sendGuestReply(){
    const input = document.getElementById('guestChatInput');
    const text = input.value.trim();
    if(!text) return;
    input.value = '';
    sendGuestPayload(text, null);
  }

  function sendGuestAttachment(inputEl){
    const file = inputEl.files && inputEl.files[0];
    if (!file) return;
    sendGuestPayload('', file);
    inputEl.value = '';
  }

  function pollGuestThread(){
    fetch(pollUrl + '?after=' + lastId, { headers: { 'Accept': 'application/json' } })
      .then(function(res){ return res.json(); })
      .then(function(json){
        if (json.conversation_open === false && conversationOpen) { window.location.reload(); return; }

        (json.messages || []).forEach(function(m){
          guestMessageFromPayload(m);
          if (m.id > lastId) lastId = m.id;
        });
      })
      .catch(function(){});
  }

  scrollThreadToBottom();
  if (conversationOpen) {
    setInterval(pollGuestThread, 4000);
  }
</script>
@endsection
