@extends('layouts.app')

@section('content')
<div class="send-wrap support-wrap">

  <div class="page-header fade-in d1">
    <a href="{{ url()->previous() }}" class="icon-btn">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
    </a>
    <h1>{{ __('support.title') }}</h1>
    <div style="display:flex; align-items:center; gap:6px;">
      @if(!$isHistoryView && $conversation->isOpen())
        <button type="button" class="icon-btn" onclick="confirmStartNew()" title="{{ __('support.start_new') }}" aria-label="{{ __('support.start_new') }}">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-2.64-6.36"/><path d="M21 3v6h-6"/></svg>
        </button>
      @endif
      <a href="{{ route('dashboard') }}" class="icon-btn">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><path d="M3 11l9-8 9 8"/><path d="M5 10v10h14V10"/></svg>
      </a>
    </div>
  </div>

  <div class="support-status fade-in d2">
    <div class="sa-avatar">LS</div>
    <div class="sa-text">
      <p class="n">{{ __('support.header_title') }}</p>
      <p class="s">
        @if($isHistoryView)
          {{ __('support.viewing_past') }}
        @elseif($conversation->isOpen())
          {{ __('support.assistant_intro') }}
        @else
          {{ $conversation->closedReasonLabel() }}
        @endif
      </p>
    </div>
  </div>

  @if($hasHistory)
    <p class="fade-in d2" style="margin:-6px 0 14px; font-size:12.5px;">
      @if($isHistoryView)
        <a href="{{ route('support.history') }}" style="color:#2F6F62; font-weight:600;">← {{ __('support.back_to_conversations') }}</a>
      @else
        <a href="{{ route('support.history') }}" style="color:var(--text-3, #5C6B72); font-weight:600;">{{ __('support.view_past_conversations') }} →</a>
      @endif
    </p>
  @endif

  {{-- Every row below is a real message from support_messages, scoped to
       this one conversation (see SupportController::index()/showSession()).
       New replies an admin sends while a LIVE conversation is open arrive
       via polling, not a page refresh — see the script at the bottom. --}}
  <div
    class="chat-thread fade-in d3"
    id="chatThread"
    data-last-id="{{ optional($messages->last())->id ?? 0 }}"
    data-open="{{ ($conversation->isOpen() && !$isHistoryView) ? '1' : '0' }}"
  >

    @forelse($messages as $message)
      <div class="msg-row {{ $message->isFromAdmin() ? 'support' : 'user' }}" data-msg-id="{{ $message->id }}">
        @if($message->isFromAdmin())
          <div class="msg-avatar">LS</div>
        @endif
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
          <span class="msg-time">{{ $message->created_at->format('g:i A') }}</span>
        </div>
      </div>
    @empty
      @if($conversation->isOpen() && !$isHistoryView)
        <div class="msg-row support">
          <div class="msg-avatar">LS</div>
          <div class="msg-col">
            <div class="msg-bubble">{{ __('support.greeting', ['name' => auth()->user()->first_name]) }}</div>
          </div>
        </div>

        {{-- The onclick payload sent to the server stays the literal English
             phrase on purpose — SupportController::maybeSendBotReply()
             matches the customer's message text against hardcoded English
             keywords ('card issue', 'payment', etc.) to pick a canned reply
             and auto-categorize the conversation. Only the visible button
             label is translated; sending a translated phrase instead would
             silently break bot matching for every non-English customer. --}}
        <div class="quick-replies" id="quickReplies">
          <button type="button" class="quick-amt-btn" onclick="sendMessageText('Card issue')">{{ __('support.quick_card_issue') }}</button>
          <button type="button" class="quick-amt-btn" onclick="sendMessageText('Payment problem')">{{ __('support.quick_payment_problem') }}</button>
          <button type="button" class="quick-amt-btn" onclick="sendMessageText('Account access')">{{ __('support.quick_account_access') }}</button>
          <button type="button" class="quick-amt-btn" onclick="sendMessageText('Something else')">{{ __('support.quick_something_else') }}</button>
        </div>
      @endif
    @endforelse

    {{-- Shown/hidden by poll()'s admin_typing flag — see pollForReplies()
         below. Only present at all for a live, open conversation. --}}
    @if($conversation->isOpen() && !$isHistoryView)
      <div class="msg-row support" id="typingRow" style="display:none;">
        <div class="msg-avatar">LS</div>
        <div class="msg-col">
          <div class="msg-bubble typing-bubble"><span></span><span></span><span></span></div>
        </div>
      </div>
    @endif

  </div>

  @if($conversation->isOpen() && !$isHistoryView)
    <div class="chat-input-wrap">
      <div class="chat-input-bar">
        <button type="button" class="icon-btn" style="flex-shrink:0;" onclick="document.getElementById('chatAttachmentInput').click();" title="{{ __('support.attach_file') }}" aria-label="{{ __('support.attach_file') }}">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21.44 11.05 12.25 20.24a5 5 0 0 1-7.07-7.07l9.19-9.19a3.5 3.5 0 0 1 4.95 4.95L10.83 17.44a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg>
        </button>
        <input type="file" id="chatAttachmentInput" accept="image/png,image/jpeg,image/gif,image/webp,application/pdf" style="display:none;" onchange="sendAttachment(this)">
        <input type="text" id="chatInput" placeholder="{{ __('support.type_message') }}" onkeydown="if(event.key==='Enter'){ event.preventDefault(); sendMessageText(this.value); this.value=''; }">
        <button type="button" class="chat-send-btn" onclick="sendMessageText(document.getElementById('chatInput').value); document.getElementById('chatInput').value='';">
          <svg viewBox="0 0 24 24" fill="none"><path d="M22 2 11 13"/><path d="M22 2 15 22l-4-9-9-4 20-7Z"/></svg>
        </button>
      </div>
    </div>
  @else
    {{-- The conversation shown above has ended (or this is a past session
         being viewed read-only) — no input box, just how/why it ended and,
         unless this IS a past-session view already, a way to start fresh. --}}
    <div class="support-ended-banner fade-in d3">
      <p>{{ $conversation->closedReasonLabel() }}</p>
      @unless($isHistoryView)
        <form method="POST" action="{{ route('support.new') }}">
          @csrf
          <button type="submit" class="quick-amt-btn active">{{ __('support.start_new') }}</button>
        </form>
      @endunless
    </div>
  @endif

  {{-- Backs the header's "start new" icon button above — a plain form
       submit (with a confirm() prompt first) rather than fetch, since a
       full page reload is exactly what should happen afterward anyway. --}}
  <form id="startNewForm" method="POST" action="{{ route('support.new') }}" style="display:none;">@csrf</form>

</div>

<style>
  .support-ended-banner{ text-align:center; padding:20px 16px; border:1px dashed rgba(16,32,47,0.15); border-radius:14px; margin-top:4px; }
  .support-ended-banner p{ margin:0 0 12px; font-size:13px; color:var(--text-3, #5C6B72); line-height:1.5; }
  .typing-bubble{ display:inline-flex; gap:4px; align-items:center; padding:13px 14px; }
  .typing-bubble span{ width:6px; height:6px; border-radius:50%; background:currentColor; opacity:0.35; display:inline-block; animation:supportTypingDot 1.1s infinite ease-in-out; }
  .typing-bubble span:nth-child(2){ animation-delay:0.15s; }
  .typing-bubble span:nth-child(3){ animation-delay:0.3s; }
  @keyframes supportTypingDot{ 0%,80%,100%{ opacity:0.3; transform:translateY(0); } 40%{ opacity:0.95; transform:translateY(-2px); } }
  .msg-attachment-img{ max-width:220px; max-height:220px; border-radius:10px; display:block; }
  .msg-attachment-file{ display:inline-flex; align-items:center; gap:6px; font-weight:600; color:inherit; text-decoration:underline; }
</style>

<script>
  const csrfToken = '{{ csrf_token() }}';
  const storeUrl = '{{ route('support.messages.store') }}';
  const pollUrl = '{{ route('support.messages.poll') }}';
  const streamUrl = '{{ route('support.stream') }}';
  const chatThreadEl = document.getElementById('chatThread');
  const conversationOpen = chatThreadEl.dataset.open === '1';
  let lastId = parseInt(chatThreadEl.dataset.lastId || '0', 10);

  function timeNow(){
    const d = new Date();
    let h = d.getHours();
    const m = d.getMinutes().toString().padStart(2,'0');
    const ampm = h >= 12 ? 'PM' : 'AM';
    h = h % 12 || 12;
    return h + ':' + m + ' ' + ampm;
  }

  function scrollToBottom(){
    chatThreadEl.scrollIntoView({ behavior:'smooth', block:'end' });
  }

  function removeQuickReplies(){
    const qr = document.getElementById('quickReplies');
    if(qr) qr.remove();
  }

  const i18nStartNewConfirm = @json(__('support.start_new_confirm'));
  const i18nStartNewButton = @json(__('support.start_new_button'));
  const i18nNotSentRetry = @json(__('support.not_sent_retry'));

  function confirmStartNew(){
    ledgerConfirm(i18nStartNewConfirm, function(){
      document.getElementById('startNewForm').submit();
    }, { confirmButtonText: i18nStartNewButton });
  }

  // Fills in one bubble's contents: any attachment first (an image renders
  // inline, anything else — a PDF — renders as a plain download link),
  // then the text underneath if there is any. attachment is either null or
  // {url, name, isImage}.
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

  // Renders one bubble. `id` is the real database id once known — passing
  // null (for a message this browser just sent, before the server has
  // responded) skips the dedupe check below, since there's nothing to
  // compare against yet. Every OTHER row (server-rendered on load, or
  // returned by the live stream/poll) carries its real id via data-msg-id,
  // so if the exact same message is ever seen twice — e.g. the stream and
  // the backup poll both returning it — it's drawn once, not twice.
  function appendMessage(id, text, isAdmin, time, attachment){
    if (id !== null && document.querySelector('[data-msg-id="' + id + '"]')) return null;

    const typingRow = document.getElementById('typingRow');
    const row = document.createElement('div');
    row.className = 'msg-row ' + (isAdmin ? 'support' : 'user');
    if (id !== null) row.dataset.msgId = id;
    row.innerHTML =
      (isAdmin ? '<div class="msg-avatar">LS</div>' : '') +
      '<div class="msg-col">' +
        '<div class="msg-bubble"></div>' +
        '<span class="msg-time">' + time + '</span>' +
      '</div>';
    renderBubbleContent(row.querySelector('.msg-bubble'), text, attachment || null);
    if (typingRow) { chatThreadEl.insertBefore(row, typingRow); } else { chatThreadEl.appendChild(row); }
    scrollToBottom();
    return row;
  }

  // Shared by both the text box and the attachment picker — always sent as
  // multipart/form-data (rather than JSON) since a JSON body can't carry a
  // file. text and/or file may each be empty, but not both.
  function sendPayload(text, file){
    if (!conversationOpen) return;
    if (!text && !file) return;

    removeQuickReplies();
    const localAttachment = file ? { url: URL.createObjectURL(file), name: file.name, isImage: file.type.indexOf('image/') === 0 } : null;
    const optimisticRow = appendMessage(null, text, false, timeNow(), localAttachment);

    const formData = new FormData();
    if (text) formData.append('body', text);
    if (file) formData.append('attachment', file);

    fetch(storeUrl, {
      method: 'POST',
      headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
      body: formData,
    })
      .then(function(res){
        // The conversation ended (e.g. an admin closed it, or a timeout
        // finally got noticed) in the moment between this tab loading and
        // this message being sent — reload to show the real, current state
        // rather than a bubble that looks sent but never reached anyone.
        if (res.status === 422) { window.location.reload(); throw new Error('conversation ended'); }
        if(!res.ok) throw new Error('failed');
        return res.json();
      })
      .then(function(json){
        // Tags the bubble already on screen with its real id, so the
        // stream/poll returning this same message doesn't draw it again.
        if (optimisticRow) optimisticRow.dataset.msgId = json.id;
        if (json.id > lastId) lastId = json.id;

        // While no real admin has joined this conversation yet, the
        // server generates an automatic reply and hands it straight back
        // here — see SupportController::maybeSendBotReply() — so it shows
        // up immediately instead of waiting for the stream/poll. Once a
        // real admin has replied even once, the server stops sending this
        // back at all.
        if (json.bot_reply) {
          appendMessage(json.bot_reply.id, json.bot_reply.body, true, json.bot_reply.time, null);
          if (json.bot_reply.id > lastId) lastId = json.bot_reply.id;
        }
      })
      .catch(function(){
        if (optimisticRow) {
          optimisticRow.querySelector('.msg-bubble').style.opacity = '0.55';
          const t = optimisticRow.querySelector('.msg-time');
          if (t) t.textContent += i18nNotSentRetry;
        }
      });
  }

  function sendMessageText(rawText){
    sendPayload((rawText || '').trim(), null);
  }

  function sendAttachment(inputEl){
    const file = inputEl.files && inputEl.files[0];
    if (!file) return;
    sendPayload('', file);
    inputEl.value = '';
  }

  function messageFromPayload(m){
    return appendMessage(
      m.id, m.body, m.is_from_admin, m.time,
      m.attachment_url ? { url: m.attachment_url, name: m.attachment_name, isImage: m.is_image } : null
    );
  }

  // Slow safety-net poll — see SupportController::poll(). Runs alongside
  // the live stream below in case that connection ever silently stalls.
  function pollForReplies(){
    fetch(pollUrl + '?after=' + lastId, { headers: { 'Accept': 'application/json' } })
      .then(function(res){ return res.json(); })
      .then(function(json){
        if (json.conversation_open === false) { window.location.reload(); return; }

        (json.messages || []).forEach(function(m){
          removeQuickReplies();
          messageFromPayload(m);
          if (m.id > lastId) lastId = m.id;
        });

        const typingRow = document.getElementById('typingRow');
        if (typingRow) {
          typingRow.style.display = json.admin_typing ? 'flex' : 'none';
          if (json.admin_typing) scrollToBottom();
        }
      })
      .catch(function(){ /* a missed tick just tries again next time */ });
  }

  // The live channel — a Server-Sent-Events connection (see
  // SupportController::stream()) that pushes new messages and typing/open
  // status roughly once a second, no websocket server required. The
  // browser's built-in EventSource reconnects on its own if the connection
  // drops or hits its ~25s cap, resuming from the last message id it saw.
  function connectStream(){
    if (typeof EventSource === 'undefined') return;

    const es = new EventSource(streamUrl + '?after=' + lastId);

    es.addEventListener('message', function(e){
      const m = JSON.parse(e.data);
      removeQuickReplies();
      messageFromPayload(m);
      if (m.id > lastId) lastId = m.id;
    });

    es.addEventListener('status', function(e){
      const s = JSON.parse(e.data);
      if (!s.open) { es.close(); window.location.reload(); return; }

      const typingRow = document.getElementById('typingRow');
      if (typingRow) {
        typingRow.style.display = s.typing ? 'flex' : 'none';
        if (s.typing) scrollToBottom();
      }
    });

    es.onerror = function(){
      // EventSource retries this same connection on its own with a short
      // backoff; pollForReplies() below is what covers the gap if it can't
      // reconnect for a while.
    };
  }

  scrollToBottom();
  if (conversationOpen) {
    connectStream();
    setInterval(pollForReplies, 15000);
  }
</script>
@endsection
