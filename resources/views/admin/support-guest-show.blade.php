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
        <input type="text" id="guestChatInput" placeholder="Reply as Ledger Support…" oninput="pingGuestTyping()" onkeydown="if(event.key==='Enter'){ event.preventDefault(); sendGuestReply(); }">
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
@endsection

@section('scripts')
<script>
  // Small Blade-rendered bootstrap for this one page — the CSRF token,
  // route URLs and this guest's initials can't live in the static
  // admin.js file, so they're handed off here as data instead. The rest
  // of the guest-chat logic (polling, sending, rendering bubbles) lives
  // in admin_asset/js/admin.js, which reads this object when it loads
  // right after this block.
  window.LedgerGuestChat = {
    csrfToken: '{{ csrf_token() }}',
    storeUrl: '{{ route('admin.support.guests.messages.store', $conversation) }}',
    pollUrl: '{{ route('admin.support.guests.messages.poll', $conversation) }}',
    typingUrl: '{{ route('admin.support.guests.typing', $conversation) }}',
    guestInitials: '{{ $conversation->initials() }}',
  };
</script>
@endsection
