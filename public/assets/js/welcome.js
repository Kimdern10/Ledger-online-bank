/* ============================================================
   Ledger — marketing/welcome section script
   Extracted from layouts/apps.blade.php, layouts/partials/headers.blade.php,
   layouts/partials/guest-support-widget.blade.php, and welcome.blade.php.
   Loaded once, via layouts/apps.blade.php <body>, on every page that uses
   that layout (currently just the public welcome page).

   Each block below is a guarded, self-contained IIFE (or DOMContentLoaded
   listener) lifted verbatim from its source file, in roughly the same
   order those files render/include in. Anything needing a Blade-only
   dynamic value (CSRF token, route() URLs) reads it from a small
   window.LedgerGuestSupportConfig bootstrap left behind in
   guest-support-widget.blade.php, instead of being inlined here.
   ============================================================ */

/* ================================================================
   SECTION 1 — from layouts/apps.blade.php
   ================================================================ */

// ---------- Preloader ----------
// Hides once the page (images, fonts, etc.) has actually finished loading, with
// a timed fallback in case a slow third-party asset holds the 'load' event up.
(function(){
  var loader = document.getElementById('ledgerLoader');
  if(!loader) return;

  document.documentElement.style.overflow = 'hidden';

  function hideLoader(){
    loader.classList.add('is-hidden');
    document.documentElement.style.overflow = '';
    window.setTimeout(function(){
      if(loader.parentNode) loader.parentNode.removeChild(loader);
    }, 550);
  }

  if(document.readyState === 'complete'){
    hideLoader();
  } else {
    window.addEventListener('load', hideLoader);
    window.setTimeout(hideLoader, 2500);
  }
})();

// ---------- Scroll-triggered reveal for .fade-up elements ----------
// Replaces the old "animate once on page load" behavior: elements now stay
// hidden until they actually scroll into view.
(function(){
  var items = document.querySelectorAll('.fade-up');
  if(!items.length) return;

  var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  if(reduceMotion || !('IntersectionObserver' in window)){
    items.forEach(function(el){ el.classList.add('in-view'); });
    return;
  }

  var io = new IntersectionObserver(function(entries){
    entries.forEach(function(entry){
      if(!entry.isIntersecting) return;
      entry.target.classList.add('in-view');
      io.unobserve(entry.target);
    });
  }, {threshold:0.15, rootMargin:'0px 0px -8% 0px'});

  items.forEach(function(el){ io.observe(el); });
})();

// ---------- Magnetic primary/wheat buttons ----------
// A subtle cursor-follow nudge on the main call-to-action buttons, desktop +
// real pointer only.
(function(){
  var canHover = window.matchMedia && window.matchMedia('(hover: hover)').matches;
  var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  if(!canHover || reduceMotion) return;

  document.querySelectorAll('.btn-primary, .btn-wheat').forEach(function(btn){
    btn.addEventListener('mousemove', function(e){
      var r = btn.getBoundingClientRect();
      var x = (e.clientX - r.left - r.width / 2) * 0.12;
      var y = (e.clientY - r.top - r.height / 2) * 0.25;
      btn.style.transform = 'translate(' + x.toFixed(1) + 'px,' + y.toFixed(1) + 'px)';
    });
    btn.addEventListener('mouseleave', function(){
      btn.style.transform = '';
    });
  });
})();

// ---------- Scroll progress bar + sticky header shadow + back-to-top ----------
(function(){
  var progress = document.getElementById('scrollProgress');
  var header = document.querySelector('.site-header');
  var backToTop = document.getElementById('backToTop');
  var ticking = false;

  function onScroll(){
    var scrollTop = window.scrollY || document.documentElement.scrollTop;
    var docHeight = document.documentElement.scrollHeight - window.innerHeight;
    var pct = docHeight > 0 ? (scrollTop / docHeight) * 100 : 0;
    if(progress) progress.style.width = pct + '%';
    if(header) header.classList.toggle('scrolled', scrollTop > 8);
    if(backToTop) backToTop.classList.toggle('visible', scrollTop > 600);
    ticking = false;
  }

  window.addEventListener('scroll', function(){
    if(!ticking){
      requestAnimationFrame(onScroll);
      ticking = true;
    }
  }, {passive:true});
  onScroll();

  if(backToTop){
    backToTop.addEventListener('click', function(){
      window.scrollTo({top:0, behavior:'smooth'});
    });
  }
})();

// ---------- Button ripple effect ----------
(function(){
  document.addEventListener('click', function(e){
    var btn = e.target.closest('.btn');
    if(!btn) return;
    var rect = btn.getBoundingClientRect();
    var size = Math.max(rect.width, rect.height);
    var ripple = document.createElement('span');
    ripple.className = 'ripple';
    ripple.style.width = ripple.style.height = size + 'px';
    ripple.style.left = (e.clientX - rect.left - size / 2) + 'px';
    ripple.style.top = (e.clientY - rect.top - size / 2) + 'px';
    btn.appendChild(ripple);
    ripple.addEventListener('animationend', function(){ ripple.remove(); });
  });
})();

/* ================================================================
   SECTION 2 — from layouts/partials/headers.blade.php
   ================================================================ */

(function(){
  var toggle = document.getElementById('menuToggle');
  var nav = document.getElementById('mainNav');
  var backdrop = document.getElementById('navBackdrop');
  if(!toggle || !nav) return;

  var iconMenu = '<svg viewBox="0 0 24 24" fill="none"><path d="M4 7h16M4 12h16M4 17h16" stroke-linecap="round"/></svg>';
  var iconClose = '<svg viewBox="0 0 24 24" fill="none"><path d="M6 6l12 12M18 6L6 18" stroke-linecap="round"/></svg>';

  function openNav(){
    nav.classList.add('open');
    if(backdrop) backdrop.classList.add('open');
    toggle.setAttribute('aria-expanded', 'true');
    toggle.innerHTML = iconClose;
    document.body.style.overflow = 'hidden';
    // Lets the floating support-chat bubble (.gs-launcher, see
    // guest-support-widget.blade.php) move out from behind the open
    // drawer instead of floating awkwardly over it — see the
    // body.nav-open rule next to .gs-launcher's own CSS.
    document.body.classList.add('nav-open');
  }

  function closeNav(){
    nav.classList.remove('open');
    if(backdrop) backdrop.classList.remove('open');
    toggle.setAttribute('aria-expanded', 'false');
    toggle.innerHTML = iconMenu;
    document.body.style.overflow = '';
    document.body.classList.remove('nav-open');
  }

  toggle.addEventListener('click', function(){
    if(nav.classList.contains('open')){
      closeNav();
    } else {
      openNav();
    }
  });

  if(backdrop){
    backdrop.addEventListener('click', closeNav);
  }

  nav.querySelectorAll('a').forEach(function(link){
    link.addEventListener('click', closeNav);
  });

  document.addEventListener('keydown', function(e){
    if(e.key === 'Escape') closeNav();
  });

  // if the viewport is resized past the mobile breakpoint while the drawer
  // is open (e.g. rotating a tablet), reset state so it isn't stuck "open"
  // once it's back to being an inline desktop nav.
  window.addEventListener('resize', function(){
    if(window.innerWidth > 960 && nav.classList.contains('open')){
      closeNav();
    }
  });
})();

// ---------- Scrollspy: highlight the nav link for the section in view ----------
// Deferred to DOMContentLoaded: this script sits in the header, above the
// yielded page content in the layout, so the section elements it looks for
// (#about, #services, etc.) don't exist in the DOM yet at parse time.
document.addEventListener('DOMContentLoaded', function(){
  var links = document.querySelectorAll('.main-nav a[data-section]');
  if(!links.length || !('IntersectionObserver' in window)) return;

  var sections = [];
  links.forEach(function(link){
    var id = link.getAttribute('data-section');
    var el = document.getElementById(id);
    if(el) sections.push({id: id, el: el});
  });
  if(!sections.length) return;

  var io = new IntersectionObserver(function(entries){
    entries.forEach(function(entry){
      if(!entry.isIntersecting) return;
      var id = entry.target.id;
      links.forEach(function(link){
        link.classList.toggle('active', link.getAttribute('data-section') === id);
      });
    });
  }, {rootMargin:'-45% 0px -45% 0px', threshold:0});

  sections.forEach(function(s){ io.observe(s.el); });
});

/* ================================================================
   SECTION 3 — from layouts/partials/guest-support-widget.blade.php
   Reads csrfToken/routes from window.LedgerGuestSupportConfig (set by a
   small inline bootstrap script left in that partial) instead of having
   them inlined here, since this file is static and can't run Blade.
   ================================================================ */
(function(){
  var cfg = window.LedgerGuestSupportConfig || {};
  var csrfToken = cfg.csrfToken;
  var routes = cfg.routes || {};

  var launcher = document.getElementById('gsLauncher');
  var panel = document.getElementById('gsPanel');
  var closeBtn = document.getElementById('gsPanelClose');
  var endChatBtn = document.getElementById('gsEndChatBtn');
  var badge = document.getElementById('gsBadge');
  var body = document.getElementById('gsPanelBody');
  if(!launcher || !panel || !closeBtn || !endChatBtn || !badge || !body) return;

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

/* ================================================================
   SECTION 4 — from welcome.blade.php
   ================================================================ */

// ---------- Fade-up on scroll ----------
(function(){
  var items = document.querySelectorAll('.fade-up');
  if(!('IntersectionObserver' in window) || !items.length){ return; }
  var io = new IntersectionObserver(function(entries){
    entries.forEach(function(entry){
      if(entry.isIntersecting){
        entry.target.style.animationPlayState = 'running';
        io.unobserve(entry.target);
      }
    });
  }, {threshold:0.15});
  items.forEach(function(el){
    el.style.animationPlayState = 'paused';
    io.observe(el);
  });
})();

// ---------- Hero card 3D tilt ----------
(function(){
  var card = document.getElementById('tiltCard');
  if(!card || window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
  if(window.matchMedia('(hover: none)').matches) return; // skip on touch devices

  var bounds;
  card.addEventListener('mouseenter', function(){
    bounds = card.getBoundingClientRect();
  });
  card.addEventListener('mousemove', function(e){
    if(!bounds) bounds = card.getBoundingClientRect();
    var px = (e.clientX - bounds.left) / bounds.width;
    var py = (e.clientY - bounds.top) / bounds.height;
    var rotateY = (px - 0.5) * 14;
    var rotateX = (0.5 - py) * 14;
    card.style.transform = 'perspective(800px) rotateX(' + rotateX + 'deg) rotateY(' + rotateY + 'deg) scale3d(1.02,1.02,1.02)';
  });
  card.addEventListener('mouseleave', function(){
    card.style.transform = 'perspective(800px) rotateX(0deg) rotateY(0deg) scale3d(1,1,1)';
  });
})();

// ---------- Hero stat count-up ----------
(function(){
  var items = document.querySelectorAll('.count-up');
  if(!items.length) return;

  function animate(el){
    var target = parseFloat(el.getAttribute('data-target'));
    var decimals = parseInt(el.getAttribute('data-decimals') || '0', 10);
    var prefix = el.getAttribute('data-prefix') || '';
    var suffix = el.getAttribute('data-suffix') || '';
    var start = null, duration = 1300;

    function step(ts){
      if(!start) start = ts;
      var progress = Math.min((ts - start) / duration, 1);
      var eased = 1 - Math.pow(1 - progress, 3);
      var val = (target * eased).toFixed(decimals);
      el.textContent = prefix + val + suffix;
      if(progress < 1) requestAnimationFrame(step);
    }
    requestAnimationFrame(step);
  }

  if('IntersectionObserver' in window){
    var io2 = new IntersectionObserver(function(entries){
      entries.forEach(function(entry){
        if(entry.isIntersecting){
          animate(entry.target);
          io2.unobserve(entry.target);
        }
      });
    }, {threshold:0.4});
    items.forEach(function(el){ io2.observe(el); });
  } else {
    items.forEach(animate);
  }
})();

// ---------- Benefits carousel ----------
(function(){
  var track = document.getElementById('benefitTrack');
  var prev = document.getElementById('benefitPrev');
  var next = document.getElementById('benefitNext');
  if(!track || !prev || !next) return;
  var index = 0;

  function slidesPerView(){
    var w = window.innerWidth;
    if(w <= 600) return 1;
    if(w <= 900) return 2;
    return 4;
  }

  function update(){
    var total = track.children.length;
    var perView = slidesPerView();
    var max = Math.max(0, total - perView);
    if(index > max) index = max;
    if(index < 0) index = 0;
    var pct = (100 / total) * index;
    track.style.transform = 'translateX(-' + pct + '%)';
  }

  next.addEventListener('click', function(){
    var total = track.children.length;
    var perView = slidesPerView();
    if(index < total - perView) index++;
    update();
  });
  prev.addEventListener('click', function(){
    if(index > 0) index--;
    update();
  });
  window.addEventListener('resize', update);
  update();
})();

// ---------- Trust stat rings ----------
(function(){
  var rings = document.querySelectorAll('.stat-ring');
  if(!rings.length) return;
  var CIRC = 2 * Math.PI * 52;

  function animate(ring){
    var target = parseFloat(ring.getAttribute('data-target'));
    var suffix = ring.getAttribute('data-suffix') || '';
    var fg = ring.querySelector('.stat-ring-fg');
    var label = ring.querySelector('.stat-ring-value');
    var offset = CIRC - (target / 100) * CIRC;
    fg.style.strokeDasharray = CIRC;
    fg.style.strokeDashoffset = CIRC;
    requestAnimationFrame(function(){ fg.style.strokeDashoffset = offset; });

    var start = null, duration = 1200;
    function step(ts){
      if(!start) start = ts;
      var progress = Math.min((ts - start) / duration, 1);
      var val = (target * progress).toFixed(target % 1 !== 0 ? 1 : 0);
      label.textContent = val + suffix;
      if(progress < 1) requestAnimationFrame(step);
    }
    requestAnimationFrame(step);
  }

  if('IntersectionObserver' in window){
    var io = new IntersectionObserver(function(entries){
      entries.forEach(function(entry){
        if(entry.isIntersecting){
          animate(entry.target);
          io.unobserve(entry.target);
        }
      });
    }, {threshold:0.4});
    rings.forEach(function(r){ io.observe(r); });
  } else {
    rings.forEach(animate);
  }
})();

// ---------- Calculator tabs ----------
(function(){
  var tabs = document.querySelectorAll('.calc-tab');
  var panels = document.querySelectorAll('.calc-panel');
  tabs.forEach(function(tab){
    tab.addEventListener('click', function(){
      tabs.forEach(function(t){ t.classList.remove('active'); });
      panels.forEach(function(p){ p.classList.remove('active'); });
      tab.classList.add('active');
      document.getElementById('calc-' + tab.getAttribute('data-tab')).classList.add('active');
    });
  });
})();

function fmt(n){
  return '$' + Math.round(n).toLocaleString('en-US');
}

// ---------- Savings growth calculator ----------
(function(){
  var start = document.getElementById('sv-start');
  var deposit = document.getElementById('sv-deposit');
  var years = document.getElementById('sv-years');
  var rate = document.getElementById('sv-rate');
  if(!start) return;

  function calc(){
    var s = parseFloat(start.value);
    var d = parseFloat(deposit.value);
    var y = parseInt(years.value, 10);
    var r = parseFloat(rate.value) / 100;
    var months = y * 12;
    var monthlyRate = r / 12;

    document.getElementById('sv-start-out').textContent = fmt(s);
    document.getElementById('sv-deposit-out').textContent = fmt(d);
    document.getElementById('sv-years-out').textContent = y;
    document.getElementById('sv-years-label').textContent = y;
    document.getElementById('sv-rate-out').textContent = rate.value + '%';

    var balance = s;
    var bars = [];
    for(var m = 1; m <= months; m++){
      balance = balance * (1 + monthlyRate) + d;
      if(m % 12 === 0) bars.push(balance);
    }
    if(bars.length === 0) bars.push(balance);

    var contributed = s + d * months;
    var growth = balance - contributed;

    document.getElementById('sv-total').textContent = fmt(balance);
    document.getElementById('sv-contributed').textContent = fmt(contributed);
    document.getElementById('sv-growth').textContent = fmt(growth);

    var barsEl = document.getElementById('sv-bars');
    var maxBar = Math.max.apply(null, bars);
    barsEl.innerHTML = '';
    bars.forEach(function(v){
      var bar = document.createElement('div');
      bar.style.height = Math.max(4, (v / maxBar) * 100) + '%';
      barsEl.appendChild(bar);
    });
  }
  [start, deposit, years, rate].forEach(function(el){ el.addEventListener('input', calc); });
  calc();
})();

// ---------- Monthly budget calculator ----------
(function(){
  var income = document.getElementById('bg-income');
  var living = document.getElementById('bg-living');
  var travel = document.getElementById('bg-travel');
  var dining = document.getElementById('bg-dining');
  if(!income) return;

  function calc(){
    var inc = parseFloat(income.value);
    var l = parseFloat(living.value);
    var t = parseFloat(travel.value);
    var d = parseFloat(dining.value);

    document.getElementById('bg-income-out').textContent = fmt(inc);
    document.getElementById('bg-living-out').textContent = l + '%';
    document.getElementById('bg-travel-out').textContent = t + '%';
    document.getElementById('bg-dining-out').textContent = d + '%';

    var livingAmt = inc * (l / 100);
    var travelAmt = inc * (t / 100);
    var diningAmt = inc * (d / 100);
    var leftover = inc - livingAmt - travelAmt - diningAmt;

    document.getElementById('bg-living-amt').textContent = fmt(livingAmt);
    document.getElementById('bg-travel-amt').textContent = fmt(travelAmt);
    document.getElementById('bg-dining-amt').textContent = fmt(diningAmt);
    document.getElementById('bg-leftover').textContent = fmt(Math.max(0, leftover));
  }
  [income, living, travel, dining].forEach(function(el){ el.addEventListener('input', calc); });
  calc();
})();

// ---------- Loan payoff calculator ----------
(function(){
  var amount = document.getElementById('ln-amount');
  var rate = document.getElementById('ln-rate');
  var term = document.getElementById('ln-term');
  if(!amount) return;

  function calc(){
    var p = parseFloat(amount.value);
    var r = parseFloat(rate.value) / 100 / 12;
    var n = parseInt(term.value, 10) * 12;

    document.getElementById('ln-amount-out').textContent = fmt(p);
    document.getElementById('ln-rate-out').textContent = rate.value + '%';
    document.getElementById('ln-term-out').textContent = term.value + (term.value == 1 ? ' year' : ' years');

    var payment = r === 0 ? p / n : (p * r) / (1 - Math.pow(1 + r, -n));
    var total = payment * n;
    var interest = total - p;

    document.getElementById('ln-payment').textContent = fmt(payment);
    document.getElementById('ln-total').textContent = fmt(total);
    document.getElementById('ln-interest').textContent = fmt(interest);
  }
  [amount, rate, term].forEach(function(el){ el.addEventListener('input', calc); });
  calc();
})();

// ---------- FAQ accordion ----------
// max-height is measured from each answer's real rendered height (via
// scrollHeight) instead of a fixed pixel cap, so a longer translation
// never gets its bottom silently clipped by .faq-answer's overflow:hidden.
(function(){
  var items = document.querySelectorAll('.faq-item');

  function openItem(item){
    var answer = item.querySelector('.faq-answer');
    item.classList.add('open');
    answer.style.maxHeight = answer.scrollHeight + 'px';
  }

  function closeItem(item){
    item.classList.remove('open');
    item.querySelector('.faq-answer').style.maxHeight = '';
  }

  items.forEach(function(item){
    var btn = item.querySelector('.faq-question');

    // The first FAQ starts already open (server-rendered with the
    // "open" class) — measure it on load too, not just on click.
    if(item.classList.contains('open')) openItem(item);

    btn.addEventListener('click', function(){
      var wasOpen = item.classList.contains('open');
      items.forEach(closeItem);
      if(!wasOpen) openItem(item);
    });
  });

  // Longer translated text can reflow onto a different number of lines
  // when the viewport is resized — recheck the open answer's height so
  // it's never left clipped (or oversized) after a resize.
  window.addEventListener('resize', function(){
    var openEl = document.querySelector('.faq-item.open');
    if(openEl) openItem(openEl);
  });
})();
