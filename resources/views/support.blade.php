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
      @if(!($isHistoryView))
        <form method="POST" action="{{ route('support.new') }}">
          @csrf
          <button type="submit" class="quick-amt-btn active">{{ __('support.start_new') }}</button>
        </form>
      @endif
    </div>
  @endif

  {{-- Backs the header's "start new" icon button above — a plain form
       submit (with a confirm() prompt first) rather than fetch, since a
       full page reload is exactly what should happen afterward anyway. --}}
  <form id="startNewForm" method="POST" action="{{ route('support.new') }}" style="display:none;">@csrf</form>

</div>

<script>
  window.LedgerSupportChatConfig = {
    csrfToken: '{{ csrf_token() }}',
    storeUrl: '{{ route('support.messages.store') }}',
    pollUrl: '{{ route('support.messages.poll') }}',
    streamUrl: '{{ route('support.stream') }}',
    i18n: {
      startNewConfirm: @json(__('support.start_new_confirm')),
      startNewButton: @json(__('support.start_new_button')),
      notSentRetry: @json(__('support.not_sent_retry')),
    },
  };
</script>
@endsection
