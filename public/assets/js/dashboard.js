/* ==========================================================
   Ledger dashboard — shared script
   Extracted from resources/views/layouts/app.blade.php and every
   dashboard-section page that had its own inline <script> block.
   Loaded once via layouts/app.blade.php, after @yield('content') so
   each page's own small inline bootstrap config (window.LedgerXConfig)
   has already run by the time this file executes.

   Structure:
   1. Shared utilities used by more than one page (identical bodies in
      the original inline scripts, so consolidated here instead of
      duplicated).
   2. The transaction PIN confirmation modal shared by Send, Top Up and
      Withdraw.
   3. One guarded block per page — each checks for a DOM element or a
      config object that only exists on its own page before doing
      anything, so this file is safe to load unconditionally on every
      dashboard-section page.
   ========================================================== */

// ---------- shared utilities ----------

function parseAmount(raw){
  const n = parseFloat(String(raw).replace(/[^0-9.]/g, ''));
  return isNaN(n) ? 0 : n;
}

function formatUsd(n){
  return '$' + n.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2});
}

function firstChipId(panelId){
  const chip = document.querySelector('#' + panelId + ' .linked-chip');
  return chip ? chip.dataset.accountId : '';
}

// A handful of stable, readable colors for the bank "mark" avatar — which
// one a bank gets is just a deterministic hash of its name, so the same
// bank always gets the same color across searches. Shared by send.blade.php
// (both the domestic and international bank pickers) and link.blade.php.
const BANK_MARK_COLORS = [
  '#0A3161', '#0F4C97', '#B4121B', '#C8102E', '#0C2074', '#652D92',
  '#004977', '#00587C', '#5A2A82', '#7C11E0', '#FF6000', '#00274D',
  '#00A0DF', '#003057', '#DA291C', '#00693C', '#00854A',
];

function bankMarkColorFor(name){
  let hash = 0;
  for(let i = 0; i < name.length; i++){
    hash = (hash * 31 + name.charCodeAt(i)) >>> 0;
  }
  return BANK_MARK_COLORS[hash % BANK_MARK_COLORS.length];
}

// Small toast shown via #ledgerToast/#toastText — several pages (cards,
// scan, link, receive) each had an identical copy of this (differing only
// in how long the toast stayed up), so it's a single shared helper now,
// with that duration passed in instead of hardcoded. Safe to call
// unconditionally: it simply does nothing on a page that has no toast
// markup.
function showToast(msg, duration){
  const toast = document.getElementById('ledgerToast');
  const text = document.getElementById('toastText');
  if(!toast || !text) return;
  text.textContent = msg;
  toast.classList.add('show');
  clearTimeout(window._toastTimer);
  window._toastTimer = setTimeout(function(){ toast.classList.remove('show'); }, duration || 2000);
}

// ---------- transaction PIN confirmation modal ----------
// Shared by send.blade.php, topup.blade.php and withdraw.blade.php — all
// three show the exact same modal before actually submitting (see
// RequiresTransactionPin server-side). The only per-page differences are
// the localized error text (passed to openPinModal) and which extra
// buttons to disable while the request is in flight: querying
// "#<formId> button[type=submit], button[form=<formId>]" covers both a
// submit button inside the form and — on send.blade.php only — its extra
// fixed-bottom duplicate button, which sits outside the <form> and points
// back at it via the form="sendForm" attribute.
let pinModalTargetForm = null;
let pinModalErrorText = '';

function openPinModal(form, errorText){
  pinModalTargetForm = form;
  pinModalErrorText = errorText || '';
  const input = document.getElementById('pinModalInput');
  const err = document.getElementById('pinModalError');
  const overlay = document.getElementById('pinModalOverlay');
  if(!input || !err || !overlay) return;
  input.value = '';
  err.style.display = 'none';
  overlay.classList.add('open');
  setTimeout(function(){ input.focus(); }, 150);
}

function closePinModal(e){
  if(e) e.stopPropagation();
  const overlay = document.getElementById('pinModalOverlay');
  if(overlay) overlay.classList.remove('open');
  pinModalTargetForm = null;
}

function confirmPin(){
  const input = document.getElementById('pinModalInput');
  if(!input) return;
  const pin = input.value.trim();
  if(!/^\d{4}$/.test(pin)){
    const err = document.getElementById('pinModalError');
    if(err){
      err.textContent = pinModalErrorText;
      err.style.display = 'block';
    }
    return;
  }

  const form = pinModalTargetForm;
  if(!form) return;

  let pinField = form.querySelector('input[name="transaction_pin"]');
  if(!pinField){
    pinField = document.createElement('input');
    pinField.type = 'hidden';
    pinField.name = 'transaction_pin';
    form.appendChild(pinField);
  }
  pinField.value = pin;

  const overlay = document.getElementById('pinModalOverlay');
  if(overlay) overlay.classList.remove('open');
  const processing = document.getElementById('processingOverlay');
  if(processing) processing.classList.add('show');

  if(form.id){
    document.querySelectorAll('#' + form.id + ' button[type="submit"], button[form="' + form.id + '"]').forEach(function(btn){
      btn.disabled = true;
    });
  }
  form.submit();
}

// ============================================================
// Dashboard home (dashboard.blade.php)
// ============================================================
(function(){
  const cfg = window.LedgerDashboardConfig;
  if(!cfg) return;

  // ---- More sheet ----
      function openMoreSheet(){ document.getElementById('moreSheetOverlay').classList.add('open'); }
      function closeMoreSheet(e){
        if(e) e.stopPropagation();
        document.getElementById('moreSheetOverlay').classList.remove('open');
      }
    

  window.openMoreSheet = openMoreSheet;
  window.closeMoreSheet = closeMoreSheet;

  // ---- balance / spend-ring count-up animation ----
      // Shared count-up/draw-in helper used by the balance and the spending
      // ring below — runs an eased animation over `duration` ms, calling
      // onUpdate(easedProgress) on every frame with a value from 0 to 1.
      // Respects prefers-reduced-motion by jumping straight to the end
      // instead of animating, rather than ignoring that setting.
      function animateEased(opts){
        const duration = opts.duration || 900;
        const prefersReducedMotion = window.matchMedia
          && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        if (prefersReducedMotion) {
          opts.onUpdate(1);
          if (opts.onDone) opts.onDone();
          return;
        }

        function easeOutCubic(t){ return 1 - Math.pow(1 - t, 3); }

        const start = performance.now();
        function step(now){
          const t = Math.min((now - start) / duration, 1);
          opts.onUpdate(easeOutCubic(t));
          if (t < 1) {
            requestAnimationFrame(step);
          } else if (opts.onDone) {
            opts.onDone();
          }
        }
        requestAnimationFrame(step);
      }

      // Balance counts up from $0.00 to the real balance on page load,
      // instead of just appearing.
      (function animateBalance(){
        const target = cfg.balanceValue;
        const wholeEl = document.getElementById('balanceWhole');
        const centsEl = document.getElementById('balanceCents');
        if (!wholeEl || !centsEl) return;

        animateEased({
          duration: 900,
          onUpdate: function(eased){
            const formatted = (target * eased).toLocaleString('en-US', {
              minimumFractionDigits: 2,
              maximumFractionDigits: 2,
            });
            const [whole, cents] = formatted.split('.');
            wholeEl.textContent = whole;
            centsEl.textContent = cents;
          },
        });
      })();

      // Spending ring draws in from empty to its real percentage, and the
      // "65%" label counts up in step with it, instead of both just
      // appearing already filled in.
      (function animateSpendRing(){
        const ring = document.getElementById('spendRingProgress');
        const label = document.getElementById('spendPercentLabel');
        if (!ring) return;

        const circumference = parseFloat(ring.dataset.circumference);
        const targetOffset = parseFloat(ring.dataset.targetOffset);
        const targetPercent = cfg.spendPercent;
        const startOffset = circumference; // fully empty

        ring.style.strokeDashoffset = startOffset;
        if (label) label.textContent = '0%';

        animateEased({
          duration: 900,
          onUpdate: function(eased){
            ring.style.strokeDashoffset = startOffset - (startOffset - targetOffset) * eased;
            if (label) label.textContent = Math.round(targetPercent * eased) + '%';
          },
        });
      })();
    

  // ---- account chip cycling ----
      // Cycles the balance card's account chip between Checking and Savings
      // when the user has both — click, tap, or Enter/Space to switch, or
      // just wait and it advances on its own. Does nothing (no listeners,
      // no timer) when there's only one account, since there's nothing to
      // cycle to.
      (function initAccountChip(){
        const accounts = cfg.accounts;
        // Same :label pattern as dashboard.acct_aria, translated server-side
        // — see the blade aria-label above this script for the non-JS
        // (first-paint) render of the exact same text.
        const acctAriaTemplate = cfg.acctAriaTemplate;
        if (accounts.length < 2) return;

        const chip = document.getElementById('acctChip');
        const typeEl = document.getElementById('acctChipType');
        const numEl = document.getElementById('acctChipNum');
        const dots = document.querySelectorAll('#acctChipDots .acct-dot');
        if (!chip) return;

        const SWITCH_MS = 220;
        const AUTO_CYCLE_MS = 4000;
        let index = 0;
        let autoTimer = null;

        function render(i){
          const acc = accounts[i];
          chip.classList.add('is-switching');
          setTimeout(function(){
            typeEl.textContent = acc.label;
            numEl.textContent = '•••• ' + acc.last4;
            chip.setAttribute('aria-label', acctAriaTemplate.replace('__LABEL__', acc.label));
            chip.classList.remove('is-switching');
            dots.forEach(function(dot, di){ dot.classList.toggle('active', di === i); });
          }, SWITCH_MS);
        }

        function goToNext(){
          index = (index + 1) % accounts.length;
          render(index);
        }

        function stopAutoCycle(){
          if (autoTimer) {
            clearInterval(autoTimer);
            autoTimer = null;
          }
        }

        function startAutoCycle(){
          stopAutoCycle();
          if (document.visibilityState === 'visible') {
            autoTimer = setInterval(goToNext, AUTO_CYCLE_MS);
          }
        }

        chip.addEventListener('click', function(){
          goToNext();
          startAutoCycle();
        });

        chip.addEventListener('keydown', function(e){
          if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            goToNext();
            startAutoCycle();
          }
        });

        // Pause while someone's actually looking at/focused on it, resume
        // once they're not — an auto-swap mid-read is just annoying.
        chip.addEventListener('mouseenter', stopAutoCycle);
        chip.addEventListener('mouseleave', startAutoCycle);
        chip.addEventListener('focus', stopAutoCycle);
        chip.addEventListener('blur', startAutoCycle);

        // Don't keep cycling (or burn battery) on a backgrounded tab.
        document.addEventListener('visibilitychange', function(){
          if (document.visibilityState === 'visible') {
            startAutoCycle();
          } else {
            stopAutoCycle();
          }
        });

        startAutoCycle();
      })();
    

  // ---- notification bell ----
  (function initNotifBell(){
    const bell = document.getElementById('notifBell');
    if(!bell) return;

      // The notification bell — polls /notifications/poll every ~20s (same
      // "slow safety-net poll" idea as support.blade.php's message poll) so
      // the badge count and dropdown list both stay live while the
      // dashboard is open, without needing a websocket server. Every list
      // item is built with textContent (never innerHTML) since a
      // notification's title/body can echo back another user's name (e.g.
      // "You received $50 from {sender's name}") — building it as raw HTML
      // would let a name containing HTML/script run on whoever's dashboard
      // displays it.
      const csrfToken = cfg.csrfToken;
      const notifEmptyText = cfg.notifEmptyText;
      let notifDropdownOpen = false;

      function toggleNotifDropdown(){
        notifDropdownOpen = !notifDropdownOpen;
        const dropdown = document.getElementById('notifDropdown');
        const bell = document.getElementById('notifBell');
        if (dropdown) dropdown.classList.toggle('open', notifDropdownOpen);
        if (bell) bell.setAttribute('aria-expanded', notifDropdownOpen ? 'true' : 'false');
        if (notifDropdownOpen) pollNotifications();
      }

      document.addEventListener('click', function(e){
        const wrap = document.querySelector('.bell-wrap');
        if (notifDropdownOpen && wrap && !wrap.contains(e.target)) {
          notifDropdownOpen = false;
          document.getElementById('notifDropdown').classList.remove('open');
          document.getElementById('notifBell').setAttribute('aria-expanded', 'false');
        }
      });

      function renderNotifBadge(count){
        const badge = document.getElementById('notifBadge');
        if (!badge) return;
        if (count > 0) {
          badge.textContent = count > 9 ? '9+' : String(count);
          badge.hidden = false;
        } else {
          badge.hidden = true;
        }
      }

      function renderNotifList(items){
        const list = document.getElementById('notifList');
        if (!list) return;
        list.innerHTML = '';

        if (!items.length) {
          const empty = document.createElement('p');
          empty.className = 'notif-empty';
          empty.textContent = notifEmptyText;
          list.appendChild(empty);
          return;
        }

        items.forEach(function(n){
          const item = document.createElement('div');
          item.className = 'notif-item' + (n.unread ? ' unread' : '');
          item.addEventListener('click', function(){ openNotification(n); });

          const title = document.createElement('p');
          title.className = 'notif-title';
          title.textContent = n.title;
          item.appendChild(title);

          if (n.body) {
            const body = document.createElement('p');
            body.className = 'notif-body';
            body.textContent = n.body;
            item.appendChild(body);
          }

          const time = document.createElement('p');
          time.className = 'notif-time';
          time.textContent = n.time;
          item.appendChild(time);

          list.appendChild(item);
        });
      }

      function openNotification(n){
        fetch('/notifications/' + n.id + '/read', {
          method: 'POST',
          headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        }).finally(function(){
          if (n.url) window.location = n.url;
        });
      }

      function markAllNotificationsRead(){
        fetch('/notifications/read-all', {
          method: 'POST',
          headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        }).finally(pollNotifications);
      }

      function clearAllNotifications(){
        window.ledgerConfirm(
          cfg.clearAllConfirmText,
          function(){
            fetch('/notifications/clear-all', {
              method: 'POST',
              headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            }).finally(pollNotifications);
          },
          { confirmButtonText: cfg.clearAllText, danger: true }
        );
      }

      function pollNotifications(){
        fetch('/notifications/poll', { headers: { 'Accept': 'application/json' } })
          .then(function(res){ return res.ok ? res.json() : null; })
          .then(function(data){
            if (!data) return;
            renderNotifBadge(data.unread_count);
            renderNotifList(data.notifications);
          })
          .catch(function(){ /* quiet — the next poll just tries again */ });
      }

      setInterval(pollNotifications, 20000);
    

    window.toggleNotifDropdown = toggleNotifDropdown;
    window.markAllNotificationsRead = markAllNotificationsRead;
    window.clearAllNotifications = clearAllNotifications;
  })();
})();

// ============================================================
// Transaction history (history.blade.php)
// ============================================================
(function(){
  const searchInput = document.getElementById('historySearch');
  if(!searchInput) return;

  let currentFilter = 'all';

  function setFilter(el, type){
    document.querySelectorAll('.filter-row .quick-amt-btn').forEach(b => b.classList.remove('active'));
    el.classList.add('active');
    currentFilter = type;
    applyFilters();
  }

  function applyFilters(){
    const q = document.getElementById('historySearch').value.trim().toLowerCase();
    const totalGroups = document.querySelectorAll('.date-group').length;
    let anyVisibleAtAll = false;

    document.querySelectorAll('.date-group').forEach(group => {
      let groupHasVisible = false;
      group.querySelectorAll('.tx-row').forEach(row => {
        const typeMatch = currentFilter === 'all' || row.dataset.type === currentFilter;
        const searchMatch = !q || (row.dataset.name || '').includes(q);
        const visible = typeMatch && searchMatch;
        row.style.display = visible ? 'flex' : 'none';
        if(visible){ groupHasVisible = true; anyVisibleAtAll = true; }
      });
      group.style.display = groupHasVisible ? 'block' : 'none';
    });

    // Only show "no matches" when there WAS something to filter — a
    // brand-new account with zero transactions already shows its own
    // empty state above and shouldn't also show this one.
    document.getElementById('noResults').style.display = (totalGroups > 0 && !anyVisibleAtAll) ? 'block' : 'none';
  }

  window.setFilter = setFilter;
  window.applyFilters = applyFilters;
})();

// ============================================================
// Receive money (receive.blade.php)
// ============================================================
(function(){
  const cfg = window.LedgerReceiveConfig;
  const nameEl = document.getElementById('valName');
  if(!cfg || !nameEl) return;

  const receiveI18n = cfg.i18n;

  document.addEventListener('DOMContentLoaded', function(){
    if(window.QRCode){
      new QRCode(document.getElementById('qrcode'), {
        text: cfg.qrText,
        width: 148,
        height: 148,
        colorDark: '#10202F',
        colorLight: '#FFFFFF'
      });
    }
  });

  function copyField(id, label){
    const text = document.getElementById(id).textContent.trim();
    navigator.clipboard?.writeText(text).then(()=> showToast(receiveI18n.fieldCopiedTemplate.replace('__LABEL__', label)))
      .catch(()=> showToast(receiveI18n.unableToCopy));
  }

  // Only pulls in a Checking / Savings line if that row actually exists on
  // the page — since receive.blade.php now only renders the row(s)
  // matching the user's chosen account type, this stays correct no matter
  // which one (or both) that turns out to be.
  function buildDetailsText(firstLine){
    const lines = [firstLine];
    const checking = document.getElementById('valChecking');
    const savings = document.getElementById('valSavings');
    if(checking) lines.push(receiveI18n.checkingAccountNumber + ': ' + checking.textContent.trim());
    if(savings) lines.push(receiveI18n.savingsAccountNumber + ': ' + savings.textContent.trim());
    lines.push(receiveI18n.bankName + ': ' + document.getElementById('valBank').textContent.trim());
    return lines.join('\n');
  }

  function copyAllDetails(){
    const text = buildDetailsText(receiveI18n.accountName + ': ' + document.getElementById('valName').textContent.trim());
    navigator.clipboard?.writeText(text).then(()=> showToast(receiveI18n.accountDetailsCopied))
      .catch(()=> showToast(receiveI18n.unableToCopy));
  }

  function shareDetails(){
    const text = buildDetailsText(receiveI18n.sendMoneyToTemplate.replace('__NAME__', document.getElementById('valName').textContent.trim()));

    if(navigator.share){
      navigator.share({ title: receiveI18n.myAccountDetails, text: text }).catch(()=>{});
    } else {
      navigator.clipboard?.writeText(text).then(()=> showToast(receiveI18n.detailsCopiedToShare));
    }
  }

  window.copyField = copyField;
  window.copyAllDetails = copyAllDetails;
  window.shareDetails = shareDetails;
})();

// ============================================================
// Send receipt (send-receipt.blade.php)
// ============================================================
(function(){
  const cfg = window.LedgerReceiptConfig;
  const card = document.getElementById('receiptCard');
  if(!cfg || !card) return;

  const RECEIPT_FILENAME = cfg.filename;

  // Translated strings the JS below needs — resolved server-side with
  // __() and handed over as JSON so this stays a plain .blade.php script
  // block rather than needing a build step. "Ledger" itself stays fixed
  // (brand name); only the surrounding words are translated.
  const RECEIPT_I18N = cfg.i18n;

  function receiptPdfOptions(){
    return {
      margin: 10,
      filename: RECEIPT_FILENAME,
      image: { type: 'jpeg', quality: 0.98 },
      html2canvas: { scale: 2, backgroundColor: '#ffffff' },
      jsPDF: { unit: 'mm', format: 'a5', orientation: 'portrait' },
    };
  }

  function setReceiptStatus(message){
    document.getElementById('receiptActionStatus').textContent = message || '';
  }

  function downloadReceiptPdf(){
    setReceiptStatus(RECEIPT_I18N.preparingPdf);
    const card = document.getElementById('receiptCard');

    html2pdf().set(receiptPdfOptions()).from(card).save().then(function(){
      setReceiptStatus('');
    }).catch(function(){
      setReceiptStatus(RECEIPT_I18N.pdfError);
    });
  }

  function shareReceipt(){
    const card = document.getElementById('receiptCard');
    const shareText = RECEIPT_I18N.sharePrefix + ': ' + cfg.reference;

    // Prefer sharing the actual PDF file when the browser supports the
    // Web Share API with files (most mobile browsers). Desktop Chrome and
    // most desktop browsers don't support file sharing this way, so this
    // falls back step by step rather than just failing silently.
    if (navigator.canShare && window.File) {
      setReceiptStatus(RECEIPT_I18N.preparingShare);

      html2pdf().set(receiptPdfOptions()).from(card).outputPdf('blob').then(function(blob){
        const file = new File([blob], RECEIPT_FILENAME, { type: 'application/pdf' });

        if (navigator.canShare({ files: [file] })) {
          return navigator.share({
            files: [file],
            title: RECEIPT_I18N.shareTitle,
            text: shareText,
          });
        }

        return shareTextFallback(shareText);
      }).then(function(){
        setReceiptStatus('');
      }).catch(function(err){
        if (err && err.name === 'AbortError') { setReceiptStatus(''); return; }
        setReceiptStatus(RECEIPT_I18N.shareError);
      });

      return;
    }

    shareTextFallback(shareText).then(function(){
      setReceiptStatus('');
    }).catch(function(err){
      if (err && err.name === 'AbortError') { setReceiptStatus(''); return; }
      setReceiptStatus(RECEIPT_I18N.shareUnsupported);
    });
  }

  function shareTextFallback(text){
    if (navigator.share) {
      return navigator.share({ title: RECEIPT_I18N.shareTitle, text: text });
    }

    return Promise.reject(new Error('share_unsupported'));
  }

  window.downloadReceiptPdf = downloadReceiptPdf;
  window.shareReceipt = shareReceipt;
})();

// ============================================================
// Top Up (topup.blade.php)
// ============================================================
(function(){
  const cfg = window.LedgerTopUpConfig;
  const form = document.getElementById('topUpForm');
  if(!cfg || !form) return;

  const CARD_TOPUP_FEE_RATE = 0.029;

  function updateFeeBreakdown(){
    const isCard = document.getElementById('segFromCard').classList.contains('active');
    const breakdown = document.getElementById('feeBreakdown');
    breakdown.classList.toggle('show', isCard);

    const amount = parseAmount(document.getElementById('amountInput').value);
    document.getElementById('amountHidden').value = amount;

    if(!isCard) return;

    const fee = amount * CARD_TOPUP_FEE_RATE;
    const total = amount + fee;
    document.getElementById('feeAmountDisplay').textContent = formatUsd(amount);
    document.getElementById('feeFeeDisplay').textContent = formatUsd(fee);
    document.getElementById('feeTotalDisplay').textContent = formatUsd(total);
  }

  function setTopUpMode(mode){
    document.getElementById('segFromBank').classList.toggle('active', mode === 'bank');
    document.getElementById('segFromCard').classList.toggle('active', mode === 'card');
    document.getElementById('topUpBankPanel').style.display = mode === 'bank' ? 'block' : 'none';
    document.getElementById('topUpCardPanel').style.display = mode === 'card' ? 'block' : 'none';
    document.getElementById('sourceTypeInput').value = mode;
    document.getElementById('linkedAccountIdInput').value = firstChipId(mode === 'bank' ? 'topUpBankPanel' : 'topUpCardPanel');
    updateFeeBreakdown();
  }

  function selectSourceChip(el, type, accountId){
    el.parentElement.querySelectorAll('.linked-chip').forEach(c => c.classList.remove('selected'));
    el.classList.add('selected');
    document.getElementById('sourceTypeInput').value = type;
    document.getElementById('linkedAccountIdInput').value = accountId;
  }

  function setAmount(el, value){
    document.querySelectorAll('.quick-amt-btn').forEach(b => b.classList.remove('active'));
    el.classList.add('active');
    document.getElementById('amountInput').value = value;
    updateFeeBreakdown();
  }

  document.addEventListener('DOMContentLoaded', function(){
    document.getElementById('amountInput').addEventListener('input', updateFeeBreakdown);
    updateFeeBreakdown();

    document.getElementById('topUpForm').addEventListener('submit', function(e){
      e.preventDefault();

      if(!document.getElementById('linkedAccountIdInput').value){
        alert(cfg.linkAccountFirstAlert);
        return;
      }

      openPinModal(this, cfg.pinError);
    });
  });

  window.setTopUpMode = setTopUpMode;
  window.selectSourceChip = selectSourceChip;
  window.setAmount = setAmount;
})();

// ============================================================
// Withdraw (withdraw.blade.php)
// ============================================================
(function(){
  const cfg = window.LedgerWithdrawConfig;
  const form = document.getElementById('withdrawForm');
  if(!cfg || !form) return;

  const CARD_WITHDRAW_FEE = 1.50;

  function updateFeeBreakdown(){
    const isCard = document.getElementById('segToCard').classList.contains('active');
    const breakdown = document.getElementById('feeBreakdown');
    breakdown.classList.toggle('show', isCard);

    const amount = parseAmount(document.getElementById('amountInput').value);
    document.getElementById('amountHidden').value = amount;

    if(!isCard) return;

    const total = Math.max(amount - CARD_WITHDRAW_FEE, 0);
    document.getElementById('feeAmountDisplay').textContent = formatUsd(amount);
    document.getElementById('feeFeeDisplay').textContent = formatUsd(CARD_WITHDRAW_FEE);
    document.getElementById('feeTotalDisplay').textContent = formatUsd(total);
  }

  function setWithdrawMode(mode){
    document.getElementById('segToBank').classList.toggle('active', mode === 'bank');
    document.getElementById('segToCard').classList.toggle('active', mode === 'card');
    document.getElementById('withdrawBankPanel').style.display = mode === 'bank' ? 'block' : 'none';
    document.getElementById('withdrawCardPanel').style.display = mode === 'card' ? 'block' : 'none';
    document.getElementById('destinationTypeInput').value = mode;
    document.getElementById('linkedAccountIdInput').value = firstChipId(mode === 'bank' ? 'withdrawBankPanel' : 'withdrawCardPanel');
    updateFeeBreakdown();
  }

  function selectDestChip(el, type, accountId){
    el.parentElement.querySelectorAll('.linked-chip').forEach(c => c.classList.remove('selected'));
    el.classList.add('selected');
    document.getElementById('destinationTypeInput').value = type;
    document.getElementById('linkedAccountIdInput').value = accountId;
  }

  function setAmount(el, value){
    document.querySelectorAll('.quick-amt-btn').forEach(b => b.classList.remove('active'));
    el.classList.add('active');
    document.getElementById('amountInput').value = value;
    updateFeeBreakdown();
  }

  document.addEventListener('DOMContentLoaded', function(){
    document.getElementById('amountInput').addEventListener('input', updateFeeBreakdown);
    updateFeeBreakdown();

    document.getElementById('withdrawForm').addEventListener('submit', function(e){
      e.preventDefault();

      if(!document.getElementById('linkedAccountIdInput').value){
        alert(cfg.linkAccountFirstAlert);
        return;
      }

      openPinModal(this, cfg.pinError);
    });
  });

  window.setWithdrawMode = setWithdrawMode;
  window.selectDestChip = selectDestChip;
  window.setAmount = setAmount;
})();

// ============================================================
// Send money (send.blade.php)
// ============================================================
(function(){
  const cfg = window.LedgerSendConfig;
  const form = document.getElementById('sendForm');
  if(!cfg || !form) return;

  const sendI18n = cfg.i18n;

  function syncAmountHidden(){
    // Keeps the hidden #amountHidden field — the one actually submitted —
    // in sync with whatever's typed in the comma-formatted display field,
    // regardless of which "Send to" tab is active. No fee is added to
    // this on either tab — Another bank doesn't charge one any more than
    // Ledger does.
    document.getElementById('amountHidden').value = parseAmount(document.getElementById('amountInput').value);
  }

  function setReceiveMode(mode){
    const panels = {ledger:'ledgerOption', bank:'bankOption', international:'internationalOption', linked:'linkedOption'};
    const buttons = {ledger:'segLedger', bank:'segBank', international:'segInternational', linked:'segLinked'};

    Object.keys(panels).forEach(key => {
      document.getElementById(panels[key]).style.display = (key === mode) ? 'block' : 'none';
      document.getElementById(buttons[key]).classList.toggle('active', key === mode);
    });
    document.getElementById('modeInput').value = mode;
    syncAmountHidden();

    // "Linked bank" is really the same real action as Withdraw -> Debit
    // card (money leaving your Ledger balance to a card you've linked), so
    // it submits straight to that same backend instead of
    // TransferController::store() — see WithdrawalController::store().
    // Every other tab (including International) posts to
    // TransferController::store().
    document.getElementById('sendForm').action = (mode === 'linked')
      ? cfg.withdrawStoreUrl
      : cfg.sendStoreUrl;

    // This is the fix for "I click Send and nothing happens": a
    // required field that sits inside a hidden (display:none) tab can't
    // be focused, and a browser that can't focus an invalid required
    // field just silently blocks the WHOLE form from submitting — no
    // error, no page change, nothing. That's exactly what was happening
    // whenever the Ledger tab's required fields were left blank while
    // "Another bank" was the active tab (or vice versa). Only the fields
    // belonging to whichever tab is actually showing are "required" now.
    const ledgerRequired = (mode === 'ledger');
    const bankRequired = (mode === 'bank');
    const intlRequired = (mode === 'international');
    document.getElementById('recipientAccountInput').required = ledgerRequired;
    document.getElementById('recipientNameInput').required = ledgerRequired;
    document.getElementById('bankNameInput').required = bankRequired;
    document.querySelector("input[name='bank_recipient_name']").required = bankRequired;
    document.querySelector("input[name='bank_account_number']").required = bankRequired;
    document.querySelector("input[name='bank_routing_number']").required = bankRequired;
    document.getElementById('intlBankNameInput').required = intlRequired;
    document.getElementById('intlRecipientNameInput').required = intlRequired;
    document.getElementById('intlCountryInput').required = intlRequired;
    document.getElementById('intlAccountInput').required = intlRequired;
    document.getElementById('intlSwiftInput').required = intlRequired;
    document.getElementById('intlCurrencyInput').required = intlRequired;

    if(intlRequired){
      scheduleIntlConversion();
    } else {
      document.getElementById('intlConvertedNote').style.display = 'none';
    }
  }

  // ---------- live currency conversion preview (International tab) ----------
  let intlConvertTimer = null;
  let intlConvertAbort = null;

  function scheduleIntlConversion(){
    if(document.getElementById('modeInput').value !== 'international') return;
    clearTimeout(intlConvertTimer);
    intlConvertTimer = setTimeout(runIntlConversion, 350);
  }

  function runIntlConversion(){
    const note = document.getElementById('intlConvertedNote');
    const currency = document.getElementById('intlCurrencyInput').value.trim().toUpperCase();
    const amount = parseAmount(document.getElementById('amountInput').value);

    if(currency.length !== 3 || amount <= 0){
      note.style.display = 'none';
      return;
    }

    if(intlConvertAbort) intlConvertAbort.abort();
    intlConvertAbort = new AbortController();

    fetch(cfg.internationalConvertUrl + '?currency=' + encodeURIComponent(currency) + '&amount=' + encodeURIComponent(amount), { signal: intlConvertAbort.signal })
      .then(res => res.ok ? res.json() : { known: false })
      .then(data => {
        if(!data.known){
          note.style.display = 'none';
          return;
        }
        document.getElementById('intlConvertedAmount').textContent =
          new Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(data.converted) + ' ' + data.currency;
        note.style.display = 'flex';
      })
      .catch(err => {
        if(err.name === 'AbortError') return;
        note.style.display = 'none';
      });
  }

  function selectLinkedChip(el, accountId, type){
    document.querySelectorAll('#linkedOption .linked-chip').forEach(c => c.classList.remove('selected'));
    el.classList.add('selected');
    document.getElementById('linkedAccountIdInput').value = accountId;
    document.getElementById('destinationTypeInput').value = type;

    // The fee differs by destination (see WithdrawalController::store()'s
    // CARD_WITHDRAWAL_FEE vs free bank payouts), so the note under the
    // chips has to switch with whichever one is actually selected.
    document.getElementById('linkedFeeNoteCard').style.display = (type === 'card') ? 'flex' : 'none';
    document.getElementById('linkedFeeNoteBank').style.display = (type === 'bank') ? 'flex' : 'none';
  }

  function setAmount(el, value){
    document.querySelectorAll('.quick-amt-btn').forEach(b => b.classList.remove('active'));
    el.classList.add('active');
    document.getElementById('amountInput').value = value;
    syncAmountHidden();
    scheduleIntlConversion();
  }

  document.addEventListener('DOMContentLoaded', function(){
    document.getElementById('amountInput').addEventListener('input', function(){
      syncAmountHidden();
      scheduleIntlConversion();
    });
    syncAmountHidden();

    // If the page reloaded after a rejected submission (e.g. "Another
    // bank" with a missing field), show whichever tab was actually being
    // used — not always Ledger — so the person sees the same tab, with
    // their own input still in it, rather than the page silently
    // resetting to a different tab than the one they were on.
    setReceiveMode(document.getElementById('modeInput').value || 'ledger');

    prefillFromScan();
  });

  // Arriving here from Scan to Pay (see scan.blade.php) puts the account
  // number it already confirmed exists in ?account= — this re-looks it up
  // (never trusts a name from the URL) and fills both fields on the Ledger
  // tab, so the only thing left for the sender to do is enter an amount and
  // hit Send.
  function prefillFromScan(){
    const params = new URLSearchParams(window.location.search);
    const account = params.get('account');
    if(!account) return;

    setReceiveMode('ledger');
    document.getElementById('recipientAccountInput').value = account;

    fetch(cfg.sendLookupUrl + '?account_number=' + encodeURIComponent(account), {
      headers: { 'Accept': 'application/json' },
    })
      .then(function(res){
        const contentType = res.headers.get('content-type') || '';
        if(!res.ok || !contentType.includes('application/json')) return { found: false };
        return res.json();
      })
      .then(function(data){
        if(data && data.found){
          document.getElementById('recipientNameInput').value = data.name;
        }
      })
      .catch(function(){});
  }

  // Shows the processing overlay the instant the form is submitted, and
  // disables both "Send" buttons (the in-form one and the fixed
  // duplicate) so a slow connection can't tempt a second click into a
  // second transfer. Nothing here fakes a delay — the overlay just covers
  // however long the real POST to TransferController::store() takes,
  // whether that ends in a redirect to the receipt or back to this page
  // with an error.
  document.getElementById('sendForm').addEventListener('submit', function(e){
    e.preventDefault();

    if(document.getElementById('modeInput').value === 'linked' && !document.getElementById('linkedAccountIdInput').value){
      alert(sendI18n.linkCardFirstAlert);
      return;
    }

    openPinModal(this, sendI18n.pinError);
  });

  function openSheet(){
    document.getElementById('bankSearch').value = '';
    resetBankSearchPanel();
    document.getElementById('sheetOverlay').classList.add('open');
    // Shows the full curated list right away — it's a short admin-managed
    // directory, not a live third-party search, so there's no need to wait
    // for someone to start typing before showing anything.
    runBankSearch('');
    setTimeout(() => document.getElementById('bankSearch').focus(), 150);
  }
  function closeSheet(e){
    if(e) e.stopPropagation();
    document.getElementById('sheetOverlay').classList.remove('open');
  }

  function resetBankSearchPanel(){
    clearBankResults();
    document.getElementById('bankSearchHint').style.display = 'none';
    document.getElementById('bankSearchLoading').style.display = 'none';
    document.getElementById('noBankResults').style.display = 'none';
  }

  function clearBankResults(){
    document.querySelectorAll('#bankOptionList .bank-option[data-source="directory"]').forEach(el => el.remove());
  }

  // ---------- live bank search (admin-managed directory; debounced, cancels stale requests) ----------
  let bankSearchTimer = null;
  let bankSearchAbort = null;

  function onBankSearchInput(value){
    clearTimeout(bankSearchTimer);
    bankSearchTimer = setTimeout(() => runBankSearch(value.trim()), 250);
  }

  function runBankSearch(query){
    if(bankSearchAbort) bankSearchAbort.abort();
    bankSearchAbort = new AbortController();

    document.getElementById('bankSearchHint').style.display = 'none';
    document.getElementById('noBankResults').style.display = 'none';
    document.getElementById('bankSearchLoading').style.display = 'block';

    fetch(cfg.banksSearchUrl + '?q=' + encodeURIComponent(query), { signal: bankSearchAbort.signal })
      .then(res => {
        if(!res.ok) throw new Error('Bank search request failed');
        return res.json();
      })
      .then(json => {
        document.getElementById('bankSearchLoading').style.display = 'none';
        const banks = json.banks || [];
        renderBankResults(banks);
        document.getElementById('noBankResults').textContent = sendI18n.noBankResults;
        document.getElementById('noBankResults').style.display = banks.length === 0 ? 'block' : 'none';
      })
      .catch(err => {
        if(err.name === 'AbortError') return;
        document.getElementById('bankSearchLoading').style.display = 'none';
        clearBankResults();
        document.getElementById('noBankResults').textContent = sendI18n.bankSearchError;
        document.getElementById('noBankResults').style.display = 'block';
      });
  }

  function renderBankResults(banks){
    clearBankResults();

    const list = document.getElementById('bankOptionList');

    banks.forEach(bank => {
      const name = bank.name || '';
      if(!name) return;

      const color = bankMarkColorFor(name);
      const initial = name.trim().charAt(0).toUpperCase() || '?';

      const el = document.createElement('div');
      el.className = 'bank-option';
      el.dataset.bank = name;
      el.dataset.source = 'directory';

      const mark = document.createElement('div');
      mark.className = 'mark';
      mark.style.background = color;
      mark.textContent = initial;

      const info = document.createElement('div');
      info.className = 'info';
      const nameEl = document.createElement('p');
      nameEl.className = 'name';
      nameEl.textContent = name;
      const metaEl = document.createElement('p');
      metaEl.className = 'meta';
      metaEl.textContent = bank.meta || sendI18n.bankFallback;
      info.append(nameEl, metaEl);

      const check = document.createElement('div');
      check.className = 'check';

      el.append(mark, info, check);
      // No pre-linked account for a bank found via search — selectBank's
      // meta stays blank so the picker just shows name + routing/account
      // number fields, same as the old "Bank of America"/"Wells Fargo"
      // entries used to.
      el.addEventListener('click', () => selectBank(el, name, '', color, initial));

      list.appendChild(el);
    });
  }

  function selectBank(el, name, meta, color, initial){
    document.querySelectorAll('.bank-option').forEach(o=>{
      o.classList.remove('selected');
      const check = o.querySelector('.check');
      if(check) check.innerHTML = '';
    });
    el.classList.add('selected');
    el.querySelector('.check').innerHTML = '<svg viewBox="0 0 24 24" fill="none"><path d="M20 6L9 17l-5-5"/></svg>';

    // Just fills in the real "Bank" text field — same field you could have
    // typed into directly. This is a shortcut, not the only way in.
    document.getElementById('bankNameInput').value = name;

    setTimeout(()=>closeSheet(), 180);
  }

  // ---------- international bank picker (admin-managed directory, type=international) ----------
  function openIntlSheet(){
    document.getElementById('intlBankSearch').value = '';
    resetIntlBankSearchPanel();
    document.getElementById('intlSheetOverlay').classList.add('open');
    // Shows the full curated list right away — it's a short admin-managed
    // directory, not a live third-party search, so there's no need to wait
    // for someone to start typing before showing anything.
    runIntlBankSearch('');
    setTimeout(() => document.getElementById('intlBankSearch').focus(), 150);
  }
  function closeIntlSheet(e){
    if(e) e.stopPropagation();
    document.getElementById('intlSheetOverlay').classList.remove('open');
  }

  function resetIntlBankSearchPanel(){
    clearIntlBankResults();
    document.getElementById('intlBankSearchHint').style.display = 'none';
    document.getElementById('intlBankSearchLoading').style.display = 'none';
    document.getElementById('noIntlBankResults').style.display = 'none';
  }

  function clearIntlBankResults(){
    document.getElementById('intlBankOptionList').innerHTML = '';
  }

  let intlBankSearchTimer = null;
  let intlBankSearchAbort = null;

  function onIntlBankSearchInput(value){
    clearTimeout(intlBankSearchTimer);
    intlBankSearchTimer = setTimeout(() => runIntlBankSearch(value.trim()), 250);
  }

  function runIntlBankSearch(query){
    if(intlBankSearchAbort) intlBankSearchAbort.abort();
    intlBankSearchAbort = new AbortController();

    document.getElementById('intlBankSearchHint').style.display = 'none';
    document.getElementById('noIntlBankResults').style.display = 'none';
    document.getElementById('intlBankSearchLoading').style.display = 'block';

    fetch(cfg.banksSearchInternationalUrl + '?q=' + encodeURIComponent(query), { signal: intlBankSearchAbort.signal })
      .then(res => {
        if(!res.ok) throw new Error('Bank search request failed');
        return res.json();
      })
      .then(json => {
        document.getElementById('intlBankSearchLoading').style.display = 'none';
        const banks = json.banks || [];
        renderIntlBankResults(banks);
        document.getElementById('noIntlBankResults').textContent = sendI18n.noBankResults;
        document.getElementById('noIntlBankResults').style.display = banks.length === 0 ? 'block' : 'none';
      })
      .catch(err => {
        if(err.name === 'AbortError') return;
        document.getElementById('intlBankSearchLoading').style.display = 'none';
        clearIntlBankResults();
        document.getElementById('noIntlBankResults').textContent = sendI18n.bankSearchError;
        document.getElementById('noIntlBankResults').style.display = 'block';
      });
  }

  function renderIntlBankResults(banks){
    clearIntlBankResults();

    const list = document.getElementById('intlBankOptionList');

    banks.forEach(bank => {
      const name = bank.name || '';
      if(!name) return;

      const color = bankMarkColorFor(name);
      const initial = name.trim().charAt(0).toUpperCase() || '?';

      const el = document.createElement('div');
      el.className = 'bank-option';

      const mark = document.createElement('div');
      mark.className = 'mark';
      mark.style.background = color;
      mark.textContent = initial;

      const info = document.createElement('div');
      info.className = 'info';
      const nameEl = document.createElement('p');
      nameEl.className = 'name';
      nameEl.textContent = name;
      const metaEl = document.createElement('p');
      metaEl.className = 'meta';
      metaEl.textContent = bank.meta || sendI18n.bankFallback;
      info.append(nameEl, metaEl);

      const check = document.createElement('div');
      check.className = 'check';

      el.append(mark, info, check);
      el.addEventListener('click', () => selectIntlBank(el, bank));

      list.appendChild(el);
    });
  }

  function selectIntlBank(el, bank){
    document.querySelectorAll('#intlBankOptionList .bank-option').forEach(o=>{
      o.classList.remove('selected');
      const check = o.querySelector('.check');
      if(check) check.innerHTML = '';
    });
    el.classList.add('selected');
    el.querySelector('.check').innerHTML = '<svg viewBox="0 0 24 24" fill="none"><path d="M20 6L9 17l-5-5"/></svg>';

    // Fills in the real fields — same ones you could type into directly —
    // so nothing about the International tab's own required fields
    // changes just because this shortcut was used.
    document.getElementById('intlBankNameInput').value = bank.name || '';
    if(bank.country) document.getElementById('intlCountryInput').value = bank.country;
    if(bank.swift_code) document.getElementById('intlSwiftInput').value = bank.swift_code;
    if(bank.currency) document.getElementById('intlCurrencyInput').value = bank.currency;

    setTimeout(()=>closeIntlSheet(), 180);
  }

  window.setReceiveMode = setReceiveMode;
  window.selectLinkedChip = selectLinkedChip;
  window.setAmount = setAmount;
  window.scheduleIntlConversion = scheduleIntlConversion;
  window.openSheet = openSheet;
  window.closeSheet = closeSheet;
  window.onBankSearchInput = onBankSearchInput;
  window.openIntlSheet = openIntlSheet;
  window.closeIntlSheet = closeIntlSheet;
  window.onIntlBankSearchInput = onIntlBankSearchInput;
})();

// ============================================================
// Support chat (support.blade.php — dashboard)
// ============================================================
(function(){
  const cfg = window.LedgerSupportChatConfig;
  const chatThreadEl = document.getElementById('chatThread');
  if(!cfg || !chatThreadEl) return;

  const csrfToken = cfg.csrfToken;
  const storeUrl = cfg.storeUrl;
  const pollUrl = cfg.pollUrl;
  const streamUrl = cfg.streamUrl;
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

  function confirmStartNew(){
    ledgerConfirm(cfg.i18n.startNewConfirm, function(){
      document.getElementById('startNewForm').submit();
    }, { confirmButtonText: cfg.i18n.startNewButton });
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
          if (t) t.textContent += cfg.i18n.notSentRetry;
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

  window.confirmStartNew = confirmStartNew;
  window.sendMessageText = sendMessageText;
  window.sendAttachment = sendAttachment;
})();

// ============================================================
// Link bank account / card (link.blade.php)
// ============================================================
(function(){
  const cfg = window.LedgerLinkConfig;
  const form = document.getElementById('linkForm');
  if(!cfg || !form) return;

  function setLinkMode(mode){
    document.getElementById('segLinkBank').classList.toggle('active', mode === 'bank');
    document.getElementById('segLinkCard').classList.toggle('active', mode === 'card');
    document.getElementById('linkBankPanel').style.display = mode === 'bank' ? 'block' : 'none';
    document.getElementById('linkCardPanel').style.display = mode === 'card' ? 'block' : 'none';
    document.getElementById('linkTypeInput').value = mode;

    // Hiding a panel with display:none does NOT stop its `required` fields
    // from being enforced on submit — the browser still tries to validate
    // them, can't focus a hidden field to show the "fill this in" bubble,
    // and just silently blocks the whole form instead (only a console
    // warning — "An invalid form control ... is not focusable" — to show
    // for it, no visible error). That's exactly what made "Link account"
    // look like it did nothing when the Card panel's required fields sat
    // there empty and hidden while filling out the Bank panel. Toggling
    // `required` in step with visibility keeps only the active panel's
    // fields required, matching what the segmented control is supposed to
    // mean, and is also called on page load below so the very first
    // submit (before anyone touches the toggle) starts out correct too.
    document.querySelectorAll('#linkBankPanel input').forEach(el => { el.required = mode === 'bank'; });
    document.querySelectorAll('#linkCardPanel input').forEach(el => { el.required = mode === 'card'; });
  }

  function openSheet(){
    document.getElementById('bankSearch').value = '';
    runBankSearch('');
    document.getElementById('sheetOverlay').classList.add('open');
    setTimeout(() => document.getElementById('bankSearch').focus(), 150);
  }
  function closeSheet(e){
    if(e) e.stopPropagation();
    document.getElementById('sheetOverlay').classList.remove('open');
  }

  // ---------- live bank search (admin-managed directory; debounced, cancels stale requests) ----------
  let bankSearchTimer = null;
  let bankSearchAbort = null;

  function onBankSearchInput(value){
    clearTimeout(bankSearchTimer);
    bankSearchTimer = setTimeout(() => runBankSearch(value.trim()), 300);
  }

  function runBankSearch(query){
    if(bankSearchAbort) bankSearchAbort.abort();
    bankSearchAbort = new AbortController();

    document.getElementById('noBankResults').style.display = 'none';
    document.getElementById('bankSearchLoading').style.display = 'block';

    fetch(cfg.banksSearchUrl + '?q=' + encodeURIComponent(query), { signal: bankSearchAbort.signal })
      .then(res => {
        if(!res.ok) throw new Error('Bank search request failed');
        return res.json();
      })
      .then(json => {
        document.getElementById('bankSearchLoading').style.display = 'none';
        const banks = json.banks || [];
        renderBankResults(banks);
        document.getElementById('noBankResults').style.display = banks.length === 0 ? 'block' : 'none';
      })
      .catch(err => {
        if(err.name === 'AbortError') return;
        document.getElementById('bankSearchLoading').style.display = 'none';
        document.getElementById('bankOptionList').innerHTML = '';
        document.getElementById('noBankResults').textContent = cfg.i18n.bankSearchError;
        document.getElementById('noBankResults').style.display = 'block';
      });
  }

  function renderBankResults(banks){
    const list = document.getElementById('bankOptionList');
    list.innerHTML = '';

    banks.forEach(bank => {
      const name = bank.name || '';
      if(!name) return;

      const color = bankMarkColorFor(name);
      const initial = name.trim().charAt(0).toUpperCase() || '?';

      const el = document.createElement('div');
      el.className = 'bank-option';
      el.dataset.bank = name;

      const mark = document.createElement('div');
      mark.className = 'mark';
      mark.style.background = color;
      mark.textContent = initial;

      const info = document.createElement('div');
      info.className = 'info';
      const nameEl = document.createElement('p');
      nameEl.className = 'name';
      nameEl.textContent = name;
      info.append(nameEl);
      if(bank.meta){
        const metaEl = document.createElement('p');
        metaEl.className = 'meta';
        metaEl.textContent = bank.meta;
        info.append(metaEl);
      }

      const check = document.createElement('div');
      check.className = 'check';

      el.append(mark, info, check);
      // Selecting a directory bank that has a routing number on file fills
      // it in too, not just the name — one less field to hunt down for a
      // bank the admin already entered fully.
      el.addEventListener('click', () => selectBank(name, bank.routing_number));

      list.appendChild(el);
    });
  }

  function selectBank(name, routingNumber){
    document.getElementById('linkBankNameInput').value = name;
    if(routingNumber){
      document.getElementById('linkRoutingNumberInput').value = routingNumber;
    }
    closeSheet();
  }

  function formatCardPreview(){
    const num = document.getElementById('cardNumberInput').value.replace(/\D/g,'').padEnd(16,'•');
    const grouped = num.match(/.{1,4}/g).join(' ');
    document.getElementById('cvNumber').textContent = grouped;

    const name = document.getElementById('cardNameInput').value.trim();
    document.getElementById('cvName').textContent = name ? name.toUpperCase() : cfg.i18n.cardholderNameDefault;

    const exp = document.getElementById('cardExpiryInput').value.trim();
    document.getElementById('cvExpiry').textContent = exp || cfg.i18n.expiryPlaceholder;
  }

  document.addEventListener('DOMContentLoaded', function(){
    // Matches the page's default appearance (Bank account tab active, Card
    // panel already display:none via its inline style) — this just makes
    // sure the Card panel's fields aren't left `required` from the raw
    // HTML on the very first load, before anyone touches the toggle.
    setLinkMode('bank');
    formatCardPreview();
    if (cfg.statusMessage) showToast(cfg.statusMessage, 2200);
  });

  window.setLinkMode = setLinkMode;
  window.openSheet = openSheet;
  window.closeSheet = closeSheet;
  window.onBankSearchInput = onBankSearchInput;
})();

// ============================================================
// Pay bills (paybills.blade.php)
// ============================================================
(function(){
  const cfg = window.LedgerPayBillsConfig;
  const overlay = document.getElementById('addBillSheetOverlay');
  if(!cfg || !overlay) return;

  // Translated strings — now sourced from the server-rendered bootstrap
  // config (window.LedgerPayBillsConfig) instead of being declared here
  // directly, since this file is a plain static asset with no Blade/@json
  // available to it.
  const I18N_PAYMENT_SCHEDULED_SUFFIX = cfg.i18n.paymentScheduledSuffix;
  const I18N_PAY_BTN = cfg.i18n.payBtn;
  const I18N_THIS_BILL_FALLBACK = cfg.i18n.thisBillFallback;
  const I18N_REMOVE_CONFIRM_TEXT = cfg.i18n.removeConfirmText;
  const I18N_REMOVE_CONFIRM_BUTTON = cfg.i18n.removeConfirmButton;
  const I18N_REMOVE_CANCEL_BUTTON = cfg.i18n.removeCancelButton;
  const I18N_ERROR_REMOVE_FAILED = cfg.i18n.errorRemoveFailed;
  const I18N_ERROR_REMOVE_CONNECTION = cfg.i18n.errorRemoveConnection;
  const I18N_REMOVE_ARIA_TEMPLATE = cfg.i18n.removeAriaTemplate;
  const I18N_REMOVE_CONFIRM_TITLE_TEMPLATE = cfg.i18n.removeConfirmTitleTemplate;
  const I18N_BILL_REMOVED_TOAST_TEMPLATE = cfg.i18n.billRemovedToastTemplate;
  const I18N_BILL_ADDED_TOAST_TEMPLATE = cfg.i18n.billAddedToastTemplate;

  function openPaySheet(category, biller, amount){
    document.getElementById('payCategory').textContent = category;
    document.getElementById('payBiller').textContent = biller;
    document.getElementById('payAmount').value = amount;
    document.getElementById('paySheetOverlay').classList.add('open');
  }
  function closePaySheet(e){
    if(e) e.stopPropagation();
    document.getElementById('paySheetOverlay').classList.remove('open');
  }
  function confirmPayment(){
    closePaySheet();
    showBillToast(document.getElementById('payCategory').textContent + ' ' + I18N_PAYMENT_SCHEDULED_SUFFIX);
  }

  function showBillToast(message){
    showToast(message, 2200);
  }

  // Same icon set as the PHP $billIcons array above, so a bill added
  // through the form gets the right icon immediately, without a reload.
  const BILL_ICONS = {
    'Rent / Mortgage': '<path d="M3 11l9-8 9 8"/><path d="M5 10v10h14V10"/>',
    'Credit Card': '<rect x="3" y="6" width="18" height="13" rx="2"/><path d="M3 10h18M7 15h4"/>',
    'Medical Insurance': '<path d="M12 21s-7-4.35-9.5-9A5.5 5.5 0 0 1 12 6a5.5 5.5 0 0 1 9.5 6c-2.5 4.65-9.5 9-9.5 9Z"/>',
    'Taxes': '<rect x="4" y="3" width="16" height="18" rx="2"/><path d="M8 8h8M8 12h8M8 16h5"/>',
    'Student Loan': '<path d="M22 10L12 5 2 10l10 5 10-5Z"/><path d="M6 12v5c0 1.5 3 3 6 3s6-1.5 6-3v-5"/>',
  };
  const DEFAULT_BILL_ICON = '<circle cx="12" cy="12" r="9"/><path d="M9 12h6M12 9v6"/>';

  function openAddBillSheet(){
    document.getElementById('addBillError').classList.remove('show');
    document.getElementById('addBillSheetOverlay').classList.add('open');
  }
  function closeAddBillSheet(e){
    if(e) e.stopPropagation();
    document.getElementById('addBillSheetOverlay').classList.remove('open');
  }

  function onAddBillCategoryChange(){
    const isOther = document.getElementById('addBillCategory').value === 'Other';
    document.getElementById('addBillCustomCategoryGroup').style.display = isOther ? 'block' : 'none';
  }

  function showAddBillError(message){
    const el = document.getElementById('addBillError');
    el.textContent = message;
    el.classList.add('show');
  }

  // Full HTML-attribute-safe escaping — needed because the Pay button below
  // puts these values inside double-quoted data-* attributes, not just
  // between tags, so a stray " in a custom bill name has to be neutralized
  // too, not just & < >.
  function escapeAttr(str){
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#39;');
  }

  function buildBillRowHTML(bill){
    const icon = BILL_ICONS[bill.category] || DEFAULT_BILL_ICON;
    const category = escapeAttr(bill.category);
    const biller = escapeAttr(bill.biller);
    const amount = escapeAttr(bill.amount);
    return (
      '<div class="bill-row is-entering" data-bill-id="' + bill.id + '">' +
        '<div class="b-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">' + icon + '</svg></div>' +
        '<div class="b-mid">' +
          '<p class="name">' + category + '</p>' +
          '<p class="meta">' + biller + ' · ' + escapeAttr(bill.due_date_label) + '</p>' +
        '</div>' +
        '<div class="b-right">' +
          '<p class="amt">$' + amount + '</p>' +
          '<button type="button" class="bill-pay-btn" data-category="' + category + '" data-biller="' + biller + '" data-amount="' + amount + '">' + I18N_PAY_BTN + '</button>' +
        '</div>' +
        '<button type="button" class="bill-remove-btn" data-bill-id="' + bill.id + '" aria-label="' + escapeAttr(I18N_REMOVE_ARIA_TEMPLATE.replace('__CATEGORY__', bill.category)) + '">' +
          '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/></svg>' +
        '</button>' +
      '</div>'
    );
  }

  // One listener on the container handles both Pay and Remove clicks, for
  // bills rendered by the server on page load AND any added later by
  // fetch() — no need to re-wire anything after inserting a new row.
  // #billRows doesn't exist at all for a savings-only account (no checking
  // account = no bills UI, see $hasChecking in the PHP above), hence the
  // null check — without it this line would throw on every page load for
  // those users.
  const billRowsEl = document.getElementById('billRows');
  if (billRowsEl) {
    billRowsEl.addEventListener('click', function(e){
      const removeBtn = e.target.closest('.bill-remove-btn');
      if (removeBtn) {
        removeBill(removeBtn);
        return;
      }
      const btn = e.target.closest('.bill-pay-btn');
      if (!btn) return;
      openPaySheet(btn.dataset.category, btn.dataset.biller, btn.dataset.amount);
    });
  }

  async function removeBill(button){
    const row = button.closest('.bill-row');
    const billId = row.dataset.billId;
    const name = row.querySelector('.b-mid .name')?.textContent || I18N_THIS_BILL_FALLBACK;

    const confirmResult = await Swal.fire({
      title: I18N_REMOVE_CONFIRM_TITLE_TEMPLATE.replace('__NAME__', name),
      text: I18N_REMOVE_CONFIRM_TEXT,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: I18N_REMOVE_CONFIRM_BUTTON,
      cancelButtonText: I18N_REMOVE_CANCEL_BUTTON,
      confirmButtonColor: '#C1503C',
      cancelButtonColor: '#2F6F62',
      reverseButtons: true,
      focusCancel: true,
    });
    if (!confirmResult.isConfirmed) return;

    button.disabled = true;

    try {
      const response = await fetch('/bills/' + billId, {
        method: 'DELETE',
        headers: {
          'Accept': 'application/json',
          'X-CSRF-TOKEN': cfg.csrfToken,
        },
      });

      if (!response.ok) {
        showBillToast(I18N_ERROR_REMOVE_FAILED);
        button.disabled = false;
        return;
      }

      row.classList.add('is-removing');
      setTimeout(() => {
        row.remove();
        if (!document.querySelector('#billRows .bill-row')) {
          document.getElementById('billCard').style.display = 'none';
          document.getElementById('billsEmptyState').style.display = 'flex';
        }
      }, 200);

      showBillToast(I18N_BILL_REMOVED_TOAST_TEMPLATE.replace('__NAME__', name));
    } catch (err) {
      showBillToast(I18N_ERROR_REMOVE_CONNECTION);
      button.disabled = false;
    }
  }

  async function submitAddBill(){
    const categorySelect = document.getElementById('addBillCategory');
    const isOther = categorySelect.value === 'Other';
    const category = (isOther ? document.getElementById('addBillCustomCategory').value : categorySelect.value).trim();
    const biller = document.getElementById('addBillBiller').value.trim();
    const amount = document.getElementById('addBillAmount').value.trim();
    const dueDate = document.getElementById('addBillDueDate').value;

    if (!category) return showAddBillError(isOther ? cfg.i18n.errorEnterBillName : cfg.i18n.errorChooseBillType);
    if (!biller) return showAddBillError(cfg.i18n.errorEnterBiller);
    if (!amount || isNaN(parseFloat(amount)) || parseFloat(amount) <= 0) return showAddBillError(cfg.i18n.errorEnterValidAmount);
    if (!dueDate) return showAddBillError(cfg.i18n.errorPickDueDate);

    const submitBtn = document.getElementById('addBillSubmitBtn');
    submitBtn.disabled = true;
    submitBtn.textContent = cfg.i18n.adding;

    try {
      const response = await fetch(cfg.billsStoreUrl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': cfg.csrfToken,
        },
        body: JSON.stringify({ category, biller, amount, due_date: dueDate }),
      });

      if (!response.ok) {
        const body = await response.json().catch(() => null);
        // 422 (validation) puts the message under body.errors; 403 (the
        // "checking account required" case) puts it under body.message.
        const firstError = body && body.errors ? Object.values(body.errors)[0]?.[0] : (body ? body.message : null);
        showAddBillError(firstError || cfg.i18n.errorAddBillFailed);
        return;
      }

      const { bill } = await response.json();

      document.getElementById('billsEmptyState').style.display = 'none';
      document.getElementById('billCard').style.display = 'block';
      document.getElementById('billRows').insertAdjacentHTML('beforeend', buildBillRowHTML(bill));

      // Reset the form for next time and close up.
      categorySelect.value = 'Rent / Mortgage';
      onAddBillCategoryChange();
      document.getElementById('addBillCustomCategory').value = '';
      document.getElementById('addBillBiller').value = '';
      document.getElementById('addBillAmount').value = '';
      document.getElementById('addBillDueDate').value = '';
      closeAddBillSheet();
      showBillToast(I18N_BILL_ADDED_TOAST_TEMPLATE.replace('__CATEGORY__', bill.category));
    } catch (err) {
      showAddBillError(cfg.i18n.errorAddBillConnection);
    } finally {
      submitBtn.disabled = false;
      submitBtn.textContent = cfg.i18n.addBillSubmit;
    }
  }

  window.openPaySheet = openPaySheet;
  window.closePaySheet = closePaySheet;
  window.confirmPayment = confirmPayment;
  window.openAddBillSheet = openAddBillSheet;
  window.closeAddBillSheet = closeAddBillSheet;
  window.onAddBillCategoryChange = onAddBillCategoryChange;
  window.submitAddBill = submitAddBill;
})();

// ============================================================
// My cards (cards.blade.php)
// ============================================================
(function(){
  const cfg = window.LedgerCardsConfig;
  if(!cfg) return;

  const csrfToken = cfg.csrfToken;
  const hasPendingCardRequest = cfg.hasPendingCardRequest;

  // Every card-scoped endpoint shares the same {card} route pattern, so
  // one URL template per action (with a placeholder token swapped for the
  // currently selected card's real id) is all that's needed — see
  // urlFor(). Laravel's route() helper just substitutes the given value
  // into the URL at generation time, so passing a placeholder string here
  // is safe even though no card with that id exists.
  const cardUrls = cfg.cardUrls;

  function urlFor(key){
    return cardUrls[key].replace('CARD_ID', currentCard);
  }

  const CARDS = cfg.cards;
  let currentCard = cfg.firstCardId;
  let revealed = false;

  function selectCard(id){
    currentCard = id;
    revealed = false;
    document.querySelectorAll('.card-scroll .card-visual').forEach(c => c.classList.remove('selected'));
    document.getElementById('card-' + id).classList.add('selected');
    document.getElementById('card-' + id).scrollIntoView({ behavior:'smooth', inline:'center', block:'nearest' });
    renderCard();
  }

  function renderCard(){
    if (currentCard === null || !CARDS[currentCard]) return;
    const card = CARDS[currentCard];

    document.getElementById('cardNumberValue').textContent = revealed ? card.number : card.masked;
    document.getElementById('cardCvvValue').textContent = revealed ? card.cvv : '•••';
    document.getElementById('cardPinValue').textContent = revealed ? (card.pin || '----') : '••••';
    document.getElementById('cardExpiryValue').textContent = card.expiry;
    document.getElementById('cardholderValue').textContent = card.cardholderName;
    document.getElementById('cardLimitValue').textContent = card.limitLabel;

    const limitInline = document.getElementById('limitStateInline');
    if (limitInline) limitInline.textContent = card.limitLabel;

    const contactlessState = document.getElementById('contactlessState');
    if (contactlessState) contactlessState.textContent = card.contactless ? cfg.i18n.stateOn : cfg.i18n.stateOff;

    const onlineState = document.getElementById('onlineState');
    if (onlineState) onlineState.textContent = card.onlinePayments ? cfg.i18n.stateOn : cfg.i18n.stateOff;

    document.getElementById('freezeToggle').checked = card.frozen;
    document.getElementById('freezeActionLabel').textContent = card.frozen ? cfg.i18n.actionUnfreeze : cfg.i18n.actionFreeze;

    renderTransactions();
  }

  function renderTransactions(){
    const list = document.getElementById('cardActivityList');
    const empty = document.getElementById('cardActivityEmpty');
    if (!list || !empty) return;

    const txs = (CARDS[currentCard] && CARDS[currentCard].transactions) || [];
    list.innerHTML = '';

    if (txs.length === 0) {
      list.style.display = 'none';
      empty.style.display = 'block';
      return;
    }

    list.style.display = '';
    empty.style.display = 'none';

    txs.forEach(function(tx){
      const row = document.createElement('div');
      row.className = 'tx-row out';
      row.innerHTML =
        '<div class="tx-bar"></div>' +
        '<div class="tx-mid"><p class="name"></p><p class="meta"></p></div>' +
        '<div class="tx-amt"></div>';
      row.querySelector('.name').textContent = tx.merchant;
      row.querySelector('.meta').textContent = tx.dateLabel + ' · ' + tx.category;
      row.querySelector('.tx-amt').textContent = tx.amountLabel;
      list.appendChild(row);
    });
  }

  function toggleReveal(){
    revealed = !revealed;
    renderCard();
  }

  function toggleFreeze(){
    document.getElementById('freezeToggle').click();
  }

  function onFreezeToggle(){
    const wanted = document.getElementById('freezeToggle').checked;
    const card = CARDS[currentCard];
    const wasFrozen = card.frozen;

    applyFreezeUi(wanted);

    fetch(urlFor('freeze'), {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
    })
      .then(function(res){ if(!res.ok) throw new Error(); return res.json(); })
      .then(function(json){
        applyFreezeUi(json.status === 'frozen');
        showToast(json.status === 'frozen' ? cfg.i18n.toastCardFrozen : cfg.i18n.toastCardUnfrozen);
      })
      .catch(function(){
        applyFreezeUi(wasFrozen);
        showToast(cfg.i18n.toastCouldNotUpdateCard);
      });
  }

  function applyFreezeUi(frozen){
    CARDS[currentCard].frozen = frozen;
    const visual = document.getElementById('card-' + currentCard);
    const badge = visual.querySelector('.card-badge');
    visual.classList.toggle('frozen', frozen);
    badge.classList.toggle('frozen', frozen);
    badge.textContent = frozen ? cfg.i18n.badgeFrozen : (CARDS[currentCard].virtual ? cfg.i18n.badgeVirtual : cfg.i18n.badgeActive);
    document.getElementById('freezeToggle').checked = frozen;
    document.getElementById('freezeActionLabel').textContent = frozen ? cfg.i18n.actionUnfreeze : cfg.i18n.actionFreeze;
  }

  function copyCardNumber(){
    const card = CARDS[currentCard];
    navigator.clipboard?.writeText(card.number.replace(/\s/g,'')).then(()=> showToast(cfg.i18n.toastCardNumberCopied))
      .catch(()=> showToast(cfg.i18n.toastUnableToCopy));
  }

  function revealPin(){
    revealed = true;
    renderCard();
    document.querySelector('.card-detail-card').scrollIntoView({ behavior:'smooth', block:'center' });
    showToast(cfg.i18n.toastPinRevealedBelow);
  }

  function changePin(){
    const card = CARDS[currentCard];
    const current = card.hasPin ? prompt(cfg.i18n.promptCurrentPin) : null;
    if (card.hasPin && current === null) return;
    if (card.hasPin && !/^\d{4}$/.test(current || '')) { showToast(cfg.i18n.toastPinMustBe4Digits); return; }

    const next = prompt(cfg.i18n.promptNewPin);
    if (next === null) return;
    if (!/^\d{4}$/.test(next)) { showToast(cfg.i18n.toastPinMustBe4Digits); return; }

    const confirmPin = prompt(cfg.i18n.promptConfirmPin);
    if (confirmPin === null) return;
    if (confirmPin !== next) { showToast(cfg.i18n.toastPinsDidNotMatch); return; }

    fetch(urlFor('pin'), {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'Content-Type': 'application/json' },
      body: JSON.stringify({ current_pin: current, pin: next, pin_confirmation: confirmPin }),
    })
      .then(function(res){ return res.json().then(function(json){ return { ok: res.ok, json: json }; }); })
      .then(function(result){
        if (!result.ok) { showToast(result.json.error || cfg.i18n.toastCouldNotUpdatePin); return; }
        card.pin = next;
        card.hasPin = true;
        if (revealed) renderCard();
        showToast(cfg.i18n.toastPinUpdated);
      })
      .catch(function(){ showToast(cfg.i18n.toastCouldNotUpdatePinRetry); });
  }

  function editLimit(){
    const card = CARDS[currentCard];
    const currentAmount = card.limitLabel === cfg.i18n.noLimitSet ? '' : card.limitLabel.replace(/[^0-9.]/g, '');
    const raw = prompt(cfg.i18n.promptSetSpendingLimit, currentAmount);
    if (raw === null) return;

    const trimmed = raw.trim();
    if (trimmed !== '' && isNaN(Number(trimmed))) { showToast(cfg.i18n.toastEnterValidAmount); return; }

    fetch(urlFor('limit'), {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'Content-Type': 'application/json' },
      body: JSON.stringify({ limit: trimmed === '' ? null : trimmed }),
    })
      .then(function(res){ if(!res.ok) throw new Error(); return res.json(); })
      .then(function(json){
        card.limitLabel = json.limit_label;
        renderCard();
        showToast(cfg.i18n.toastSpendingLimitUpdated);
      })
      .catch(function(){ showToast(cfg.i18n.toastCouldNotUpdateLimitRetry); });
  }

  function toggleContactless(){
    const card = CARDS[currentCard];
    fetch(urlFor('contactless'), {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
    })
      .then(function(res){ if(!res.ok) throw new Error(); return res.json(); })
      .then(function(json){
        card.contactless = json.enabled;
        renderCard();
        showToast(json.enabled ? cfg.i18n.toastContactlessTurnedOn : cfg.i18n.toastContactlessTurnedOff);
      })
      .catch(function(){ showToast(cfg.i18n.toastCouldNotUpdateRetry); });
  }

  function toggleOnlinePayments(){
    const card = CARDS[currentCard];
    fetch(urlFor('online'), {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
    })
      .then(function(res){ if(!res.ok) throw new Error(); return res.json(); })
      .then(function(json){
        card.onlinePayments = json.enabled;
        renderCard();
        showToast(json.enabled ? cfg.i18n.toastOnlinePaymentsTurnedOn : cfg.i18n.toastOnlinePaymentsTurnedOff);
      })
      .catch(function(){ showToast(cfg.i18n.toastCouldNotUpdateRetry); });
  }

  function reportLostOrStolen(){
    ledgerConfirm(cfg.i18n.confirmReportLostMessage, function(){
      const form = document.getElementById('reportLostForm');
      form.setAttribute('action', urlFor('reportLost'));
      form.submit();
    }, { danger: true, confirmButtonText: cfg.i18n.confirmReportLostButton });
  }

  function closeCard(){
    ledgerConfirm(cfg.i18n.confirmCloseCardMessage, function(){
      const form = document.getElementById('closeCardForm');
      form.setAttribute('action', urlFor('close'));
      form.submit();
    }, { danger: true, confirmButtonText: cfg.i18n.confirmCloseCardButton });
  }

  function replaceCard(){
    ledgerConfirm(cfg.i18n.confirmReplaceCardMessage, function(){
      const form = document.getElementById('replaceCardForm');
      form.setAttribute('action', urlFor('replace'));
      form.submit();
    }, { confirmButtonText: cfg.i18n.confirmReplaceCardButton });
  }

  // Backs the "Add a card" tile in the carousel. This never creates a
  // real card by itself — it only files a CardRequest for an admin to
  // approve (see CardController::requestCard()). Once approved, the real
  // card (with its generated number, CVV, and PIN) shows up in the
  // carousel on its own, the same as any other card.
  function requestNewCard(){
    if (hasPendingCardRequest) {
      showToast(cfg.i18n.toastRequestAlreadyPending);
      return;
    }

    // Was a native prompt() asking the user to type the literal word
    // "physical" or "virtual" — replaced with a proper SweetAlert2 radio
    // picker so this step matches every other confirmation in the app
    // instead of dropping into an unstyled browser dialog.
    Swal.fire({
      title: cfg.i18n.promptRequestCardType,
      input: 'radio',
      inputOptions: {
        physical: cfg.i18n.optionPhysicalCard,
        virtual: cfg.i18n.optionVirtualCard,
      },
      inputValue: 'physical',
      showCancelButton: true,
      confirmButtonText: cfg.i18n.confirmRequestCardButton,
      cancelButtonText: 'Cancel',
      confirmButtonColor: '#2F6F62',
      cancelButtonColor: '#8C9298',
      reverseButtons: true,
      inputValidator: function(value){
        if (!value) return cfg.i18n.toastTypePhysicalOrVirtual;
      },
    }).then(function(result){
      if (!result.isConfirmed) return;
      const type = result.value;

      ledgerConfirm(cfg.i18n.confirmRequestCardPrefix + ' ' + type + ' ' + cfg.i18n.confirmRequestCardSuffix, function(){
        document.getElementById('cardRequestTypeField').value = type;
        document.getElementById('cardRequestForm').submit();
      }, { confirmButtonText: cfg.i18n.confirmRequestCardButton });
    });
  }

  document.addEventListener('DOMContentLoaded', function(){
    renderCard();
    if (cfg.statusMessage) showToast(cfg.statusMessage);
  });

  window.selectCard = selectCard;
  window.toggleReveal = toggleReveal;
  window.toggleFreeze = toggleFreeze;
  window.onFreezeToggle = onFreezeToggle;
  window.copyCardNumber = copyCardNumber;
  window.revealPin = revealPin;
  window.changePin = changePin;
  window.editLimit = editLimit;
  window.toggleContactless = toggleContactless;
  window.toggleOnlinePayments = toggleOnlinePayments;
  window.reportLostOrStolen = reportLostOrStolen;
  window.closeCard = closeCard;
  window.replaceCard = replaceCard;
  window.requestNewCard = requestNewCard;
})();

// ============================================================
// Scan to pay (scan.blade.php)
// ============================================================
(function(){
  const cfg = window.LedgerScanConfig;
  const video = document.getElementById('scanVideo');
  if(!cfg || !video) return;

  let scanning = false;
  let activeStream = null;
  let lookupInFlight = false;

  function setScanMode(mode){
    document.getElementById('segScan').classList.toggle('active', mode === 'scan');
    document.getElementById('segMyQr').classList.toggle('active', mode === 'myqr');
    document.getElementById('scanPanel').style.display = mode === 'scan' ? 'block' : 'none';
    document.getElementById('myQrPanel').style.display = mode === 'myqr' ? 'block' : 'none';

    if(mode === 'scan'){
      startScanner();
    } else {
      stopScanner();
    }
  }

  async function startScanner(){
    if(scanning) return;
    const video = document.getElementById('scanVideo');
    const status = document.getElementById('scanStatus');
    try{
      activeStream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
      video.srcObject = activeStream;
      scanning = true;
      status.textContent = cfg.i18n.scanStatusDefault;
      requestAnimationFrame(scanTick);
    } catch(err){
      status.textContent = cfg.i18n.cameraUnavailable;
    }
  }

  function stopScanner(){
    scanning = false;
    if(activeStream){
      activeStream.getTracks().forEach(t => t.stop());
      activeStream = null;
    }
  }

  function scanTick(){
    if(!scanning) return;
    const video = document.getElementById('scanVideo');
    const canvas = document.getElementById('scanCanvas');

    if(video.readyState === video.HAVE_ENOUGH_DATA){
      canvas.width = video.videoWidth;
      canvas.height = video.videoHeight;
      const ctx = canvas.getContext('2d');
      ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
      const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);

      if(window.jsQR){
        const code = jsQR(imageData.data, imageData.width, imageData.height);
        if(code){
          onCodeFound(code.data);
          return;
        }
      }
    }
    requestAnimationFrame(scanTick);
  }

  // A Ledger QR code (see receive.blade.php / the My QR panel above) encodes
  // "ledger://pay?name=...&account=...&bank=...". Manual entry usually gets
  // just the bare account number instead, so this accepts either — if it
  // doesn't parse as that URL scheme, the whole string is treated as the
  // account number as-is.
  function extractAccountNumber(raw){
    const value = (raw || '').trim();
    try {
      const url = new URL(value);
      const account = url.searchParams.get('account');
      if(account) return account;
    } catch (e) {
      // not a URL — fall through and treat it as a bare account number
    }
    return value.replace(/\s+/g, '');
  }

  // Real lookup against the same endpoint Send Money's Ledger tab uses (see
  // TransferController::lookup) — a scanned/typed code only ever leads
  // somewhere real. Nothing is sent from here; this just finds the account
  // and hands off to Send, which still requires its own review + submit.
  function lookupAndGoToSend(accountNumber){
    if(!accountNumber || lookupInFlight) return;
    lookupInFlight = true;
    document.getElementById('scanStatus').textContent = cfg.i18n.lookingUpAccount;

    fetch(cfg.sendLookupUrl + "?account_number=" + encodeURIComponent(accountNumber), {
      headers: { 'Accept': 'application/json' },
    })
      .then(function(res){
        const contentType = res.headers.get('content-type') || '';
        if(!res.ok || !contentType.includes('application/json')) return { found: false };
        return res.json();
      })
      .then(function(data){
        lookupInFlight = false;
        if(data && data.found){
          showToast(cfg.i18n.accountFoundOpeningSend, 2400);
          document.getElementById('scanStatus').textContent = cfg.i18n.accountFoundOpeningSend;
          setTimeout(function(){
            window.location.href = cfg.sendUrl + "?account=" + encodeURIComponent(accountNumber);
          }, 500);
        } else {
          showToast(cfg.i18n.noLedgerAccountFound, 2400);
          document.getElementById('scanStatus').textContent = cfg.i18n.scanStatusDefault;
          scanning = true;
          requestAnimationFrame(scanTick);
        }
      })
      .catch(function(){
        lookupInFlight = false;
        showToast(cfg.i18n.couldNotLookUp, 2400);
        document.getElementById('scanStatus').textContent = cfg.i18n.scanStatusDefault;
        scanning = true;
        requestAnimationFrame(scanTick);
      });
  }

  function onCodeFound(data){
    scanning = false;
    stopScanner();
    lookupAndGoToSend(extractAccountNumber(data));
  }

  function submitManualCode(){
    const raw = document.getElementById('manualCodeInput').value.trim();
    if(!raw) return;
    lookupAndGoToSend(extractAccountNumber(raw));
  }

  document.addEventListener('DOMContentLoaded', function(){
    startScanner();
    if(window.QRCode){
      new QRCode(document.getElementById('qrcode'), {
        text: cfg.qrText,
        width: 148,
        height: 148,
        colorDark: '#10202F',
        colorLight: '#FFFFFF'
      });
    }
  });

  window.addEventListener('beforeunload', stopScanner);

  window.setScanMode = setScanMode;
  window.submitManualCode = submitManualCode;
})();
