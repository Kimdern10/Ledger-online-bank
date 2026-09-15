{{--
  Floating "chat with us" widget for the public marketing page. Included
  once from layouts/apps.blade.php (right alongside back-to-top, the other
  fixed-position control that layout already has), so it shows up on every
  page that uses that layout without welcome.blade.php needing to know
  anything about it.

  Talks to GuestSupportController's JSON endpoints — a visitor is
  identified purely by the guest_support_token cookie that controller sets,
  never anything stored in the page itself, so this keeps working across a
  page refresh or a return visit. Uses the site-wide SweetAlert2 helper
  (partials/sweetalert.blade.php, included by this same layout) for the
  "End chat" confirmation.
--}}
<div class="gs-widget">
  <button type="button" class="gs-launcher" id="gsLauncher" aria-label="Chat with support">
    <svg class="gs-icon-chat" viewBox="0 0 24 24" fill="none"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
    <svg class="gs-icon-close" viewBox="0 0 24 24" fill="none"><path d="M18 6 6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
    <span class="gs-badge" id="gsBadge" hidden></span>
  </button>

  <div class="gs-panel" id="gsPanel" hidden>
    <div class="gs-panel-header">
      <div>
        <p class="gs-panel-title">Ledger Support</p>
        <p class="gs-panel-sub">We usually reply within a few minutes</p>
      </div>
      <div class="gs-header-actions">
        <button type="button" class="gs-end-chat-btn" id="gsEndChatBtn" hidden>End chat</button>
        <button type="button" class="gs-panel-close" id="gsPanelClose" aria-label="Close chat">
          <svg viewBox="0 0 24 24" fill="none"><path d="M18 6 6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>
      </div>
    </div>

    <div class="gs-panel-body" id="gsPanelBody">
      <div class="gs-loading" id="gsLoading"><div class="gs-spinner"></div></div>
    </div>
  </div>
</div>

<style>
  .gs-launcher{
    position:fixed; right:24px; bottom:86px; z-index:1050;
    width:46px; height:46px; border-radius:50%; border:none;
    background:var(--sage); color:var(--paper);
    display:flex; align-items:center; justify-content:center;
    cursor:pointer; box-shadow:0 10px 26px rgba(47,111,98,0.38);
    transition:background .15s ease, transform .15s ease;
  }
  .gs-launcher:hover{ background:#28594e; transform:scale(1.05); }
  .gs-launcher:active{ transform:scale(0.95); }
  .gs-launcher svg{ width:19px; height:19px; position:absolute; transition:opacity .15s ease, transform .15s ease; }
  .gs-launcher .gs-icon-close{ opacity:0; transform:rotate(-45deg) scale(0.6); }
  .gs-launcher .gs-icon-chat{ opacity:1; transform:none; }
  .gs-launcher.gs-open .gs-icon-chat{ opacity:0; transform:rotate(45deg) scale(0.6); }
  .gs-launcher.gs-open .gs-icon-close{ opacity:1; transform:none; }

  .gs-badge{
    position:absolute; top:-2px; right:-2px; z-index:1;
    min-width:16px; height:16px; padding:0 4px; border-radius:8px;
    background:var(--coral); color:#fff; font-size:10px; font-weight:700;
    display:flex; align-items:center; justify-content:center; line-height:1;
    box-shadow:0 0 0 2px var(--paper);
  }
  .gs-launcher.gs-open .gs-badge{ display:none; }

  /* Same reasoning as .gs-panel[hidden] just below — the badge starts (and
     mostly stays) hidden via the [hidden] attribute in the markup, driven
     by updateBadge() in the script. Without this rule .gs-badge's own
     "display:flex" would beat the browser's default [hidden]{display:none}
     and a blank dot would always show, whether or not there's actually an
     unread reply. */
  .gs-badge[hidden]{ display:none; }

  /* The [hidden] attribute is how the panel starts (and stays) closed —
     only the round launcher button shows until it's clicked. Without this
     rule, .gs-panel's own "display:flex" below would beat the browser's
     default [hidden]{display:none} and the panel would render open. */
  .gs-panel[hidden]{ display:none; }

  .gs-panel{
    position:fixed; right:24px; bottom:142px; z-index:1050;
    width:368px; max-width:calc(100vw - 32px);
    height:min(560px, calc(100vh - 190px));
    background:var(--paper-2); border-radius:20px; border:1px solid var(--mist);
    box-shadow:0 24px 60px rgba(16,32,47,0.28);
    display:flex; flex-direction:column; overflow:hidden;
  }
  @media (max-width:480px){
    .gs-launcher{ right:16px; bottom:16px; }
    .gs-panel{ right:12px; left:12px; bottom:76px; width:auto; height:min(72vh, 560px); }
  }

  /* While the mobile hamburger drawer is open (see headers.blade.php's
     openNav()/closeNav(), which toggle body.nav-open), this bubble sits
     right on top of the nav links at its usual bottom-right spot — .gs-
     launcher's z-index is deliberately higher than .main-nav's so it's
     never hidden BEHIND the drawer. Moving it to top:16px/right:16px used
     to be the fix, but that's the exact spot apps.blade.php's own
     .menu-toggle (the hamburger/X button that opens and closes this same
     drawer) sits in, so the two ended up stacked on top of each other —
     the launcher looked like it was "floating" over the toggle button.
     Simplest correct fix: just hide the launcher while the drawer is
     open. It reappears the instant the drawer closes. */
  body.nav-open .gs-launcher{
    display:none;
  }

  .gs-panel-header{
    flex-shrink:0; padding:18px 18px; background:var(--ink); color:var(--paper);
    display:flex; align-items:flex-start; justify-content:space-between; gap:12px;
  }
  .gs-panel-title{ font-family:'Newsreader', serif; font-size:17px; font-weight:600; }
  .gs-panel-sub{ font-size:12px; color:#9FB0BC; margin-top:3px; }
  .gs-header-actions{ display:flex; align-items:center; gap:10px; flex-shrink:0; }
  .gs-end-chat-btn{
    background:transparent; border:1px solid rgba(246,244,238,0.3); color:var(--paper);
    border-radius:100px; padding:5px 12px; font-size:11.5px; font-weight:600;
    cursor:pointer; white-space:nowrap; transition:border-color .15s ease, color .15s ease;
  }
  .gs-end-chat-btn:hover{ border-color:var(--wheat); color:var(--wheat); }
  .gs-panel-close{
    background:transparent; border:none; color:var(--paper); cursor:pointer;
    padding:2px; flex-shrink:0; opacity:.8; transition:opacity .15s ease;
  }
  .gs-panel-close:hover{ opacity:1; }
  .gs-panel-close svg{ width:18px; height:18px; }

  .gs-panel-body{
    flex:1; overflow-y:auto; background:var(--paper);
    display:flex; flex-direction:column;
  }
  .gs-loading{ flex:1; display:flex; align-items:center; justify-content:center; }
  .gs-spinner{
    width:26px; height:26px; border-radius:50%;
    border:3px solid var(--mist); border-top-color:var(--sage);
    animation:gsSpin .7s linear infinite;
  }
  @keyframes gsSpin{ to{ transform:rotate(360deg); } }

  /* ---------- opening form ---------- */
  .gs-form{ padding:20px; }
  .gs-field{ margin-bottom:14px; }
  .gs-field label{
    display:block; font-size:12.5px; font-weight:600; color:var(--text-2); margin-bottom:6px;
  }
  .gs-field input, .gs-field textarea{
    width:100%; padding:11px 13px; border-radius:12px; border:1px solid var(--mist);
    background:var(--paper-2); font-family:'Inter', sans-serif; font-size:14px;
    color:var(--text-1); resize:vertical;
  }
  .gs-field input:focus, .gs-field textarea:focus{ outline:none; border-color:var(--sage); }
  .gs-form-error{
    background:var(--coral-light); color:#8a3626; border-radius:10px;
    padding:9px 12px; font-size:12.5px; margin-bottom:14px;
  }

  /* ---------- thread ---------- */
  .gs-thread{ flex:1; padding:16px 16px 8px; display:flex; flex-direction:column; gap:10px; }
  .gs-msg-row{ display:flex; }
  .gs-msg-row.gs-from-guest{ justify-content:flex-end; }
  .gs-bubble-wrap{ max-width:80%; display:flex; flex-direction:column; }
  .gs-from-guest .gs-bubble-wrap{ align-items:flex-end; }
  .gs-bubble{
    padding:10px 13px; border-radius:15px; font-size:13.5px; line-height:1.5;
    word-wrap:break-word; white-space:pre-line;
  }
  .gs-from-guest .gs-bubble{ background:var(--sage); color:var(--paper); border-bottom-right-radius:4px; }
  .gs-from-admin .gs-bubble{ background:var(--paper-2); color:var(--text-1); border:1px solid var(--mist); border-bottom-left-radius:4px; }
  .gs-bubble-time{ font-family:'IBM Plex Mono', monospace; font-size:10.5px; color:var(--text-3); margin-top:4px; }
  .gs-attachment-img{ max-width:200px; max-height:200px; border-radius:10px; display:block; }
  .gs-attachment-file{ display:inline-flex; align-items:center; gap:6px; font-weight:600; color:inherit; text-decoration:underline; }

  .gs-ended-banner{
    margin:14px 16px; background:var(--wheat-light); color:#7a5c1f;
    border-radius:12px; padding:11px 14px; font-size:12.5px; text-align:center; line-height:1.5;
  }

  /* ---------- typing indicator ---------- */
  .gs-typing-dots{ display:inline-flex; gap:4px; align-items:center; padding:3px 2px; }
  .gs-typing-dots span{
    width:6px; height:6px; border-radius:50%; background:currentColor; opacity:.35;
    display:inline-block; animation:gsTypingDot 1.1s infinite ease-in-out;
  }
  .gs-typing-dots span:nth-child(2){ animation-delay:.15s; }
  .gs-typing-dots span:nth-child(3){ animation-delay:.3s; }
  @keyframes gsTypingDot{ 0%,80%,100%{ opacity:.3; transform:translateY(0); } 40%{ opacity:.95; transform:translateY(-2px); } }

  .gs-input-row{
    flex-shrink:0; display:flex; align-items:flex-end; gap:6px;
    padding:12px 14px; border-top:1px solid var(--mist); background:var(--paper-2);
  }
  .gs-attach-btn{
    width:36px; height:36px; border-radius:50%; border:none; flex-shrink:0;
    background:transparent; color:var(--text-3); cursor:pointer;
    display:flex; align-items:center; justify-content:center;
    transition:background .15s ease, color .15s ease;
  }
  .gs-attach-btn:hover{ background:var(--sage-light); color:var(--sage); }
  .gs-attach-btn svg{ width:18px; height:18px; }
  .gs-input-row textarea{
    flex:1; resize:none; max-height:80px; border-radius:14px; border:1px solid var(--mist);
    padding:10px 13px; font-family:'Inter', sans-serif; font-size:13.5px; color:var(--text-1);
  }
  .gs-input-row textarea:focus{ outline:none; border-color:var(--sage); }
  .gs-send-btn{
    width:38px; height:38px; border-radius:50%; border:none; flex-shrink:0;
    background:var(--sage); color:var(--paper); cursor:pointer;
    display:flex; align-items:center; justify-content:center;
    transition:background .15s ease;
  }
  .gs-send-btn:hover{ background:#28594e; }
  .gs-send-btn:disabled{ opacity:.5; cursor:default; }
  .gs-send-btn svg{ width:16px; height:16px; }
</style>

<script>
(function(){
  var csrfToken = '{{ csrf_token() }}';
  var routes = {
    init: '{{ route('support.guest.init') }}',
    start: '{{ route('support.guest.start') }}',
    send: '{{ route('support.guest.messages.store') }}',
    poll: '{{ route('support.guest.messages.poll') }}',
    end: '{{ route('support.guest.end') }}'
  };

  var launcher = document.getElementById('gsLauncher');
  var panel = document.getElementById('gsPanel');
  var closeBtn = document.getElementById('gsPanelClose');
  var endChatBtn = document.getElementById('gsEndChatBtn');
  var badge = document.getElementById('gsBadge');
  var body = document.getElementById('gsPanelBody');

  var initialized = false;
  var conversationOpen = false;
  var lastMessageId = 0;
  var pollTimer = null;
  var bgPollTimer = null;
  var unreadCount = 0;
  var typingVisible = false;

  function escapeHtml(str){
    var div = document.createElement('div');
    div.textContent = str == null ? '' : String(str);
    return div.innerHTML;
  }

  function togglePanel(){
    if(panel.hidden){
      panel.hidden = false;
      launcher.classList.add('gs-open');
      stopBackgroundPolling();
      unreadCount = 0;
      updateBadge();
      if(!initialized){
        initialized = true;
        loadConversation();
      } else if(conversationOpen){
        // Re-opening an already-loaded chat — catch up on anything sent
        // while it was closed, then resume live polling (stopped below,
        // on close, so it doesn't run in the background while hidden).
        doPoll();
        startPolling();
      }
    } else {
      panel.hidden = true;
      launcher.classList.remove('gs-open');
      stopPolling();
      if(conversationOpen){
        startBackgroundPolling();
      }
    }
  }

  function updateBadge(){
    if(unreadCount > 0){
      badge.hidden = false;
      badge.textContent = unreadCount > 9 ? '9+' : String(unreadCount);
    } else {
      badge.hidden = true;
    }
  }

  // Runs once on page load (and resumes any time the panel is closed on an
  // open conversation) so a guest who has stepped away from the widget
  // still gets a badge on the launcher icon when support replies — without
  // ever rendering or opening the panel itself.
  function startBackgroundPolling(){
    stopBackgroundPolling();
    bgPollTimer = window.setInterval(backgroundPoll, 5000);
  }

  function stopBackgroundPolling(){
    if(bgPollTimer){
      window.clearInterval(bgPollTimer);
      bgPollTimer = null;
    }
  }

  function backgroundPoll(){
    fetch(routes.poll + '?after=' + lastMessageId, {
      headers:{ 'Accept':'application/json' },
      credentials:'same-origin'
    })
      .then(function(r){ return r.json(); })
      .then(function(data){
        (data.messages || []).forEach(function(m){
          lastMessageId = Math.max(lastMessageId, m.id);
          if(m.is_from_admin) unreadCount++;
        });
        updateBadge();

        if(!data.conversation_open){
          conversationOpen = false;
          stopBackgroundPolling();
        }
      })
      .catch(function(){});
  }

  // Silent check on page load — establishes whether this visitor already
  // has an open conversation (via their guest_support_token cookie) and,
  // if so, starts the background poll above so replies can be badged even
  // before they ever open the panel this visit.
  function silentCheck(){
    fetch(routes.init, { headers:{ 'Accept':'application/json' }, credentials:'same-origin' })
      .then(function(r){ return r.json(); })
      .then(function(data){
        if(!data.has_conversation) return;
        conversationOpen = data.conversation_open;
        if(!initialized){
          var msgs = data.messages || [];
          if(msgs.length) lastMessageId = msgs[msgs.length - 1].id;
        }
        if(conversationOpen && panel.hidden){
          startBackgroundPolling();
        }
      })
      .catch(function(){});
  }

  launcher.addEventListener('click', togglePanel);
  closeBtn.addEventListener('click', togglePanel);

  endChatBtn.addEventListener('click', function(){
    window.ledgerConfirm(
      "End this conversation? You'll need to start a new one to reach us again.",
      function(){
        fetch(routes.end, {
          method:'POST',
          credentials:'same-origin',
          headers:{ 'Accept':'application/json', 'X-CSRF-TOKEN': csrfToken }
        }).then(function(){
          conversationOpen = false;
          stopPolling();
          endChatBtn.hidden = true;
          showEndedBanner();
        }).catch(function(){});
      },
      { confirmButtonText: 'End chat', danger: true }
    );
  });

  function loadConversation(){
    fetch(routes.init, { headers:{ 'Accept':'application/json' }, credentials:'same-origin' })
      .then(function(r){ return r.json(); })
      .then(function(data){
        if(data.has_conversation){
          conversationOpen = data.conversation_open;
          renderThread(data.messages, conversationOpen);
          setTyping(!!data.admin_typing);
          if(conversationOpen) startPolling();
        } else {
          renderStartForm();
        }
      })
      .catch(function(){ renderStartForm(); });
  }

  function renderStartForm(errorText){
    endChatBtn.hidden = true;
    body.innerHTML =
      '<form class="gs-form" id="gsStartForm">' +
        (errorText ? '<div class="gs-form-error">' + escapeHtml(errorText) + '</div>' : '') +
        '<div class="gs-field"><label for="gsName">Your name</label>' +
          '<input type="text" id="gsName" name="guest_name" maxlength="100" required></div>' +
        '<div class="gs-field"><label for="gsEmail">Email address</label>' +
          '<input type="email" id="gsEmail" name="guest_email" maxlength="255" required></div>' +
        '<div class="gs-field"><label for="gsMessage">How can we help?</label>' +
          '<textarea id="gsMessage" name="body" rows="3" maxlength="2000" required></textarea></div>' +
        '<button type="submit" class="btn btn-primary btn-block" id="gsStartSubmit">Start chat</button>' +
      '</form>';

    document.getElementById('gsStartForm').addEventListener('submit', submitStartForm);
  }

  function submitStartForm(e){
    e.preventDefault();
    var submitBtn = document.getElementById('gsStartSubmit');
    submitBtn.disabled = true;

    var payload = {
      guest_name: document.getElementById('gsName').value,
      guest_email: document.getElementById('gsEmail').value,
      body: document.getElementById('gsMessage').value
    };

    fetch(routes.start, {
      method:'POST',
      credentials:'same-origin',
      headers:{
        'Content-Type':'application/json',
        'Accept':'application/json',
        'X-CSRF-TOKEN': csrfToken
      },
      body: JSON.stringify(payload)
    })
      .then(function(r){ return r.json().then(function(data){ return { ok:r.ok, data:data }; }); })
      .then(function(res){
        if(!res.ok){
          var firstError = res.data.errors ? Object.values(res.data.errors)[0][0] : (res.data.message || 'Something went wrong. Try again.');
          renderStartForm(firstError);
          return;
        }
        conversationOpen = true;
        renderThread(res.data.messages, true);
        startPolling();
      })
      .catch(function(){
        submitBtn.disabled = false;
      });
  }

  function attachmentHtml(m){
    if(!m.attachment_url) return '';
    if(m.is_image){
      return '<a href="' + m.attachment_url + '" target="_blank" rel="noopener">' +
        '<img src="' + m.attachment_url + '" alt="' + escapeHtml(m.attachment_name) + '" class="gs-attachment-img"></a>';
    }
    return '<a href="' + m.attachment_url + '" target="_blank" rel="noopener" class="gs-attachment-file">📎 ' + escapeHtml(m.attachment_name) + '</a>';
  }

  function bubbleHtml(m){
    var side = m.is_from_admin ? 'gs-from-admin' : 'gs-from-guest';
    var attachment = attachmentHtml(m);
    var text = m.body ? '<div' + (attachment ? ' style="margin-top:6px;"' : '') + '>' + escapeHtml(m.body) + '</div>' : '';
    return '<div class="gs-msg-row ' + side + '" data-id="' + m.id + '">' +
      '<div class="gs-bubble-wrap">' +
        '<div class="gs-bubble">' + attachment + text + '</div>' +
        '<div class="gs-bubble-time">' + escapeHtml(m.time) + '</div>' +
      '</div>' +
    '</div>';
  }

  function renderThread(messages, isOpen){
    endChatBtn.hidden = !isOpen;

    var html = '<div class="gs-thread" id="gsThread">';
    messages.forEach(function(m){
      html += bubbleHtml(m);
      lastMessageId = Math.max(lastMessageId, m.id);
    });
    html += '</div>';

    if(isOpen){
      html +=
        '<div class="gs-input-row">' +
          '<button type="button" class="gs-attach-btn" id="gsAttachBtn" title="Attach a photo" aria-label="Attach a photo">' +
            '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21.44 11.05 12.25 20.24a5 5 0 0 1-7.07-7.07l9.19-9.19a3.5 3.5 0 0 1 4.95 4.95L10.83 17.44a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg>' +
          '</button>' +
          '<input type="file" id="gsAttachInput" accept="image/png,image/jpeg,image/gif,image/webp,application/pdf" hidden>' +
          '<textarea id="gsInput" rows="1" placeholder="Type a message&hellip;" maxlength="2000"></textarea>' +
          '<button type="button" class="gs-send-btn" id="gsSendBtn" aria-label="Send">' +
            '<svg viewBox="0 0 24 24" fill="none"><path d="M22 2 11 13M22 2l-7 20-4-9-9-4 20-7z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>' +
          '</button>' +
        '</div>';
    } else {
      html += '<div class="gs-ended-banner">This conversation has ended.<br><a href="#" id="gsStartNew" style="color:var(--sage); font-weight:600;">Start a new conversation</a></div>';
    }

    body.innerHTML = html;
    typingVisible = false;
    scrollToBottom();

    if(isOpen){
      var input = document.getElementById('gsInput');
      var sendBtn = document.getElementById('gsSendBtn');
      var attachBtn = document.getElementById('gsAttachBtn');
      var attachInput = document.getElementById('gsAttachInput');

      sendBtn.addEventListener('click', function(){ sendMessage(); });
      input.addEventListener('keydown', function(e){
        if(e.key === 'Enter' && !e.shiftKey){
          e.preventDefault();
          sendMessage();
        }
      });
      attachBtn.addEventListener('click', function(){ attachInput.click(); });
      attachInput.addEventListener('change', function(){
        var file = attachInput.files && attachInput.files[0];
        if(!file) return;
        sendMessage(file);
        attachInput.value = '';
      });
      input.focus();
    } else {
      document.getElementById('gsStartNew').addEventListener('click', function(e){
        e.preventDefault();
        lastMessageId = 0;
        conversationOpen = false;
        renderStartForm();
      });
    }
  }

  function showEndedBanner(){
    var thread = document.getElementById('gsThread');
    if(!thread) return;
    setTyping(false);
    var banner = document.createElement('div');
    banner.className = 'gs-ended-banner';
    banner.innerHTML = 'This conversation has ended.<br><a href="#" id="gsStartNew" style="color:var(--sage); font-weight:600;">Start a new conversation</a>';
    var inputRow = body.querySelector('.gs-input-row');
    if(inputRow) inputRow.remove();
    body.appendChild(banner);
    document.getElementById('gsStartNew').addEventListener('click', function(e){
      e.preventDefault();
      lastMessageId = 0;
      renderStartForm();
    });
  }

  function scrollToBottom(){
    body.scrollTop = body.scrollHeight;
  }

  // Shows/hides the animated "Support is typing…" bubble at the bottom of
  // the thread. Kept as its own row (removed and re-appended, never left
  // in the middle) so it always reads as "the newest thing", same as a
  // real chat app.
  function setTyping(isTyping){
    var thread = document.getElementById('gsThread');
    if(!thread) return;

    var existing = document.getElementById('gsTypingRow');

    if(isTyping && !typingVisible){
      typingVisible = true;
      var row = document.createElement('div');
      row.className = 'gs-msg-row gs-from-admin';
      row.id = 'gsTypingRow';
      row.innerHTML = '<div class="gs-bubble-wrap"><div class="gs-bubble"><span class="gs-typing-dots"><span></span><span></span><span></span></span></div></div>';
      thread.appendChild(row);
      scrollToBottom();
    } else if(!isTyping && typingVisible){
      typingVisible = false;
      if(existing) existing.remove();
    }
  }

  function sendMessage(file){
    var input = document.getElementById('gsInput');
    var value = input.value.trim();
    if(!value && !file) return;

    var sendBtn = document.getElementById('gsSendBtn');
    sendBtn.disabled = true;
    input.value = '';

    var formData = new FormData();
    if(value) formData.append('body', value);
    if(file) formData.append('attachment', file);

    fetch(routes.send, {
      method:'POST',
      credentials:'same-origin',
      headers:{ 'Accept':'application/json', 'X-CSRF-TOKEN': csrfToken },
      body: formData
    })
      .then(function(r){ return r.json().then(function(data){ return { ok:r.ok, data:data }; }); })
      .then(function(res){
        sendBtn.disabled = false;
        if(!res.ok){
          if(res.data.error && res.data.error.indexOf('ended') !== -1){
            conversationOpen = false;
            stopPolling();
            renderThread([], false);
          }
          return;
        }
        setTyping(false);
        appendMessage(res.data);
      })
      .catch(function(){ sendBtn.disabled = false; });
  }

  function appendMessage(m){
    if(m.id <= lastMessageId) return;
    lastMessageId = m.id;

    var thread = document.getElementById('gsThread');
    if(!thread) return;

    setTyping(false);

    var wrap = document.createElement('div');
    wrap.innerHTML = bubbleHtml(m);
    thread.appendChild(wrap.firstChild);
    scrollToBottom();
  }

  function startPolling(){
    stopPolling();
    pollTimer = window.setInterval(doPoll, 4000);
  }

  function stopPolling(){
    if(pollTimer){
      window.clearInterval(pollTimer);
      pollTimer = null;
    }
  }

  function doPoll(){
    fetch(routes.poll + '?after=' + lastMessageId, {
      headers:{ 'Accept':'application/json' },
      credentials:'same-origin'
    })
      .then(function(r){ return r.json(); })
      .then(function(data){
        (data.messages || []).forEach(appendMessage);
        setTyping(!!data.admin_typing);

        if(!data.conversation_open && conversationOpen){
          conversationOpen = false;
          stopPolling();
          endChatBtn.hidden = true;
          showEndedBanner();
        }
      })
      .catch(function(){});
  }

  silentCheck();
})();
</script>
