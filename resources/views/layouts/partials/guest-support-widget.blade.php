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

<script>
  window.LedgerGuestSupportConfig = {
    csrfToken: '{{ csrf_token() }}',
    routes: {
      init: '{{ route('support.guest.init') }}',
      start: '{{ route('support.guest.start') }}',
      send: '{{ route('support.guest.messages.store') }}',
      poll: '{{ route('support.guest.messages.poll') }}',
      end: '{{ route('support.guest.end') }}'
    },
  };
</script>
