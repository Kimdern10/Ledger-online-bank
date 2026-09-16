/* =====================================================================
   Ledger Admin — shared JavaScript
   =====================================================================
   Extracted from inline <script> blocks that used to live in
   resources/views/layouts/admin.blade.php and the individual admin
   pages. Loaded once on every /admin page (near the bottom of <body>,
   in the same spot the old inline scripts ran) via the plain asset()
   helper, not the Vite pipeline.

   Every page-specific block below only acts when the elements it needs
   are actually on the page, so it's safe for all of this to load
   everywhere instead of being split per page.

   Two blocks (the guest conversation thread and the logged-in customer
   conversation thread) need a few per-page dynamic values that can only
   come from Blade — a CSRF token, route URLs, the other person's
   initials. Those still live in a small inline <script> in
   admin/support-guest-show.blade.php and admin/support-show.blade.php,
   which sets window.LedgerGuestChat / window.LedgerAdminChat before
   this file runs. Everything else is pure JS and moved here verbatim.
   ===================================================================== */

/* ---------- Sidebar (shared layout) ---------- */
function openAdminSidebar(){
  document.getElementById('adminSidebar').classList.add('open');
  document.getElementById('adminSidebarOverlay').classList.add('open');
  document.getElementById('adminMenuBtn').setAttribute('aria-expanded', 'true');
}
function closeAdminSidebar(){
  document.getElementById('adminSidebar').classList.remove('open');
  document.getElementById('adminSidebarOverlay').classList.remove('open');
  document.getElementById('adminMenuBtn').setAttribute('aria-expanded', 'false');
}
function toggleAdminSidebar(){
  document.getElementById('adminSidebar').classList.contains('open') ? closeAdminSidebar() : openAdminSidebar();
}
// Closing on nav-link tap means you never end up back on a page with
// the drawer still hanging open over it.
document.querySelectorAll('.admin-sidebar .admin-nav-link').forEach(function(link){
  link.addEventListener('click', closeAdminSidebar);
});
document.addEventListener('keydown', function(e){
  if (e.key === 'Escape') closeAdminSidebar();
});

/* ---------- Bank form (admin/banks-form.blade.php) ---------- */
function toggleBankTypeFields(){
  var typeEl = document.getElementById('type');
  if (!typeEl) return;
  var type = typeEl.value;
  document.getElementById('field-routing').style.display = type === 'external' ? '' : 'none';
  document.getElementById('field-country').style.display = type === 'international' ? '' : 'none';
  document.getElementById('field-swift').style.display = type === 'international' ? '' : 'none';
  document.getElementById('field-currency').style.display = type === 'international' ? '' : 'none';
}
if (document.getElementById('type')) {
  toggleBankTypeFields();
}

/* ---------- Users list (admin/users.blade.php) ---------- */
let currentUserStatusFilter = 'all';

function setUserStatusFilter(el, status){
  document.querySelectorAll('#userStatusTabs .admin-tab').forEach(function(t){ t.classList.remove('active'); });
  el.classList.add('active');
  currentUserStatusFilter = status;
  applyUserFilters();
}

function applyUserFilters(){
  const q = document.getElementById('userSearchInput').value.trim().toLowerCase();
  let anyVisible = false;

  document.querySelectorAll('#usersTable .user-row').forEach(function(row){
    const statusMatch = currentUserStatusFilter === 'all' || row.dataset.status === currentUserStatusFilter;
    const searchMatch = !q || row.dataset.search.includes(q);
    const visible = statusMatch && searchMatch;
    row.style.display = visible ? '' : 'none';
    if (visible) anyVisible = true;
  });

  const noResults = document.getElementById('userNoResults');
  if (noResults) noResults.style.display = anyVisible ? 'none' : 'block';
}

/* ---------- Support inbox (admin/support.blade.php) ---------- */
let currentSupportFilter = 'all';

function setSupportFilter(el, filter){
  document.querySelectorAll('#supportStatusTabs .admin-tab').forEach(function(t){ t.classList.remove('active'); });
  el.classList.add('active');
  currentSupportFilter = filter;
  applySupportFilters();
}

function applySupportFilters(){
  const q = document.getElementById('supportSearchInput').value.trim().toLowerCase();
  const category = document.getElementById('supportCategorySelect').value;
  let anyVisible = false;

  document.querySelectorAll('#supportRows .support-row').forEach(function(row){
    let filterMatch = true;
    if (currentSupportFilter === 'open') filterMatch = row.dataset.filterStatus === 'open';
    else if (currentSupportFilter === 'closed') filterMatch = row.dataset.filterStatus === 'closed';
    else if (currentSupportFilter === 'needs') filterMatch = row.dataset.filterNeeds === '1';
    else if (currentSupportFilter === 'urgent') filterMatch = row.dataset.filterPriority === 'urgent';

    const categoryMatch = !category || row.dataset.filterCategory === category;
    const searchMatch = !q || row.dataset.search.includes(q);
    const visible = filterMatch && categoryMatch && searchMatch;
    row.style.display = visible ? 'flex' : 'none';
    if (visible) anyVisible = true;
  });

  const noResults = document.getElementById('supportNoResults');
  if (noResults) noResults.style.display = anyVisible ? 'none' : 'block';
}

/* ---------- Guest messages list (admin/support-guests.blade.php) ---------- */
let currentGuestFilter = 'all';

function setGuestFilter(el, filter){
  document.querySelectorAll('#guestStatusTabs .admin-tab').forEach(function(t){ t.classList.remove('active'); });
  el.classList.add('active');
  currentGuestFilter = filter;
  applyGuestFilters();
}

function applyGuestFilters(){
  const q = document.getElementById('guestSearchInput').value.trim().toLowerCase();
  let anyVisible = false;

  document.querySelectorAll('#guestRows .support-row').forEach(function(row){
    let filterMatch = true;
    if (currentGuestFilter === 'open') filterMatch = row.dataset.filterStatus === 'open';
    else if (currentGuestFilter === 'closed') filterMatch = row.dataset.filterStatus === 'closed';

    const searchMatch = !q || row.dataset.search.includes(q);
    const visible = filterMatch && searchMatch;
    row.style.display = visible ? 'flex' : 'none';
    if (visible) anyVisible = true;
  });

  const noResults = document.getElementById('guestNoResults');
  if (noResults) noResults.style.display = anyVisible ? 'none' : 'block';
}

/* ---------- Shared chat-bubble renderer ----------
   Identical in both the guest and logged-in conversation threads —
   kept as one function instead of two copies. */
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

/* ---------------------------------------------------------------------
   Guest conversation detail (admin/support-guest-show.blade.php)

   Wrapped in its own scope (and guarded on #guestChatThread existing)
   so it's a harmless no-op on every other admin page, and so its
   variable/function names never collide with the logged-in-customer
   block below.
   --------------------------------------------------------------------- */
(function(){
  const cfg = window.LedgerGuestChat;
  const guestThreadEl = document.getElementById('guestChatThread');
  if (!cfg || !guestThreadEl) return;

  const conversationOpen = guestThreadEl.dataset.open === '1';
  let lastId = parseInt(guestThreadEl.dataset.lastId || '0', 10);
  let lastTypingPingAt = 0;

  function scrollThreadToBottom(){
    guestThreadEl.scrollTop = guestThreadEl.scrollHeight;
  }

  // Pinged on every keystroke in the reply box, but only actually sent to
  // the server at most once every 2.5 seconds — same throttling as the
  // logged-in support page's version below.
  function pingTyping(){
    const now = Date.now();
    if (now - lastTypingPingAt < 2500) return;
    lastTypingPingAt = now;
    fetch(cfg.typingUrl, {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': cfg.csrfToken, 'Accept': 'application/json' },
    }).catch(function(){});
  }

  function appendGuestMessage(id, text, isAdmin, time, attachment){
    if (id !== null && document.querySelector('#guestChatThread [data-msg-id="' + id + '"]')) return null;

    const row = document.createElement('div');
    row.className = 'msg-row ' + (isAdmin ? 'support' : 'user');
    if (id !== null) row.dataset.msgId = id;
    row.innerHTML =
      '<div class="msg-avatar">' + (isAdmin ? 'LS' : cfg.guestInitials) + '</div>' +
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

    fetch(cfg.storeUrl, {
      method: 'POST',
      headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': cfg.csrfToken },
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
    fetch(cfg.pollUrl + '?after=' + lastId, { headers: { 'Accept': 'application/json' } })
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

  // The reply box's oninput/onclick attributes call these by name, and
  // the attachment button's onclick lives on the page too, so both need
  // to be reachable from global scope even though this block is an IIFE.
  window.pingGuestTyping = pingTyping;
  window.sendGuestReply = sendGuestReply;
  window.sendGuestAttachment = sendGuestAttachment;

  scrollThreadToBottom();
  if (conversationOpen) {
    setInterval(pollGuestThread, 4000);
  }
})();

/* ---------------------------------------------------------------------
   Logged-in customer conversation detail (admin/support-show.blade.php)
   --------------------------------------------------------------------- */
(function(){
  const cfg = window.LedgerAdminChat;
  const adminThreadEl = document.getElementById('adminChatThread');
  if (!cfg || !adminThreadEl) return;

  const conversationOpen = adminThreadEl.dataset.open === '1';
  let lastId = parseInt(adminThreadEl.dataset.lastId || '0', 10);
  let lastTypingPingAt = 0;

  function scrollThreadToBottom(){
    adminThreadEl.scrollTop = adminThreadEl.scrollHeight;
  }

  // Pinged on every keystroke in the reply box, but only actually sent to
  // the server at most once every 2.5 seconds — the customer's "Support is
  // typing…" indicator only needs to be roughly live, not exact, and this
  // keeps a long reply from firing dozens of requests.
  function pingTyping(){
    const now = Date.now();
    if (now - lastTypingPingAt < 2500) return;
    lastTypingPingAt = now;
    fetch(cfg.typingUrl, {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': cfg.csrfToken, 'Accept': 'application/json' },
    }).catch(function(){});
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
      : (cfg.customerAvatarUrl ? '<img src="' + cfg.customerAvatarUrl + '" alt="" style="width:100%;height:100%;border-radius:50%;object-fit:cover;">' : cfg.customerInitials);
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

    fetch(cfg.storeUrl, {
      method: 'POST',
      headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': cfg.csrfToken },
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
    fetch(cfg.pollUrl + '?after=' + lastId, { headers: { 'Accept': 'application/json' } })
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

    const es = new EventSource(cfg.streamUrl + '?after=' + lastId);

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

  window.pingAdminTyping = pingTyping;
  window.sendAdminReply = sendAdminReply;
  window.sendAdminAttachment = sendAdminAttachment;

  scrollThreadToBottom();
  if (conversationOpen) {
    connectAdminStream();
    setInterval(pollAdminThread, 15000);
  }
})();

/* ---------------------------------------------------------------------
   User detail page (admin/users-show.blade.php)
   --------------------------------------------------------------------- */
function openCreditDebit(){
  document.getElementById('cbSlideover').classList.add('open');
  document.getElementById('cbOverlay').classList.add('open');
}
function closeCreditDebit(){
  // Guarded (unlike the original inline version) because this now shares
  // a global Escape-key listener with every other admin page, most of
  // which don't have a #cbSlideover/#cbOverlay to close.
  const slideover = document.getElementById('cbSlideover');
  const overlay = document.getElementById('cbOverlay');
  if (slideover) slideover.classList.remove('open');
  if (overlay) overlay.classList.remove('open');
}

function selectDirection(btn, value){
  document.querySelectorAll('#directionGroup .toggle-btn').forEach(function(b){ b.classList.remove('active'); });
  btn.classList.add('active');
  document.getElementById('cbDirection').value = value;
  // Sender details only make sense for money coming IN — hiding them for
  // a debit isn't a hard rule the backend enforces, just a UX nudge
  // since they'd be meaningless for money going out.
  document.getElementById('senderDetailsSection').style.display = value === 'credit' ? 'block' : 'none';
}

function copyAccountNumber(btn){
  const value = btn.dataset.value;
  navigator.clipboard?.writeText(value).then(function(){
    const original = btn.innerHTML;
    btn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>';
    setTimeout(function(){ btn.innerHTML = original; }, 1200);
  });
}

document.addEventListener('keydown', function(e){
  if (e.key === 'Escape') closeCreditDebit();
});
