@extends('layouts.app')

@section('content')
<div class="send-wrap">

  <div class="page-header fade-in d1">
    <a href="{{ url()->previous() }}" class="icon-btn">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
    </a>
    <h1>{{ __('history.title') }}</h1>
    <a href="{{ route('dashboard') }}" class="icon-btn">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><path d="M3 11l9-8 9 8"/><path d="M5 10v10h14V10"/></svg>
    </a>
  </div>

  {{-- Opens the "Email a statement" panel at the bottom of this page. --}}
  <button type="button" class="pay-btn fade-in d2" style="margin-bottom:16px; gap:8px;" onclick="openStatementSheet()">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:18px; height:18px;"><path d="M6 3h9l3 3v15H6z"/><path d="M9 9h6M9 13h6M9 17h3"/></svg>
    {{ __('history.statement_button') }}
  </button>

  <div class="sheet-search fade-in d2">
    <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.35-4.35"/></svg>
    <input type="text" id="historySearch" placeholder="{{ __('history.search_placeholder') }}" oninput="applyFilters()">
  </div>

  <div class="filter-row fade-in d2">
    <button type="button" class="quick-amt-btn active" onclick="setFilter(this,'all')">{{ __('history.filter_all') }}</button>
    <button type="button" class="quick-amt-btn" onclick="setFilter(this,'sent')">{{ __('history.filter_sent') }}</button>
    <button type="button" class="quick-amt-btn" onclick="setFilter(this,'received')">{{ __('history.filter_received') }}</button>
  </div>


  @forelse($groups as $groupLabel => $items)
    <div class="date-group fade-in d3">
      <p class="date-group-label">{{ $groupLabel }}</p>
      <div class="tx-card">
        @foreach($items as $item)
          @if($item['url'])
            <a
              href="{{ $item['url'] }}"
              class="tx-row {{ $item['type'] === 'received' ? 'in' : 'out' }}"
              data-type="{{ $item['type'] }}"
              data-name="{{ strtolower($item['name']) }}"
            >
              <div class="tx-bar"></div>
              <div class="tx-mid">
                <p class="name">{{ $item['name'] }}</p>
                <p class="meta">{{ $item['meta'] }}</p>
              </div>
              <div class="tx-amt">{{ $item['amount_display'] }}</div>
            </a>
          @else
       
            <div
              class="tx-row not-clickable {{ $item['type'] === 'received' ? 'in' : 'out' }}"
              data-type="{{ $item['type'] }}"
              data-name="{{ strtolower($item['name']) }}"
              @if($item['view_url'] ?? null)
                onclick="window.location='{{ $item['view_url'] }}'"
                style="cursor:pointer;"
              @endif
            >
              <div class="tx-bar"></div>
              <div class="tx-mid">
                <p class="name">{{ $item['name'] }}</p>
                <p class="meta">{{ $item['meta'] }}</p>
              </div>
              <div class="tx-amt">
                {{ $item['amount_display'] }}
              </div>
            </div>
          @endif
        @endforeach
      </div>
    </div>
  @empty
    <p class="no-tx-empty fade-in d3">
      {{ __('history.no_transactions_yet') }}
    </p>
  @endforelse

  <p id="noResults" style="display:none; text-align:center; font-size:13px; color:var(--text-3); padding:30px 0;">
    {{ __('history.no_matches') }}
  </p>

  {{-- Only the newest transactions are listed (HistoryController::LATEST). --}}
  @if($hasOlder)
    <p style="text-align:center; font-size:12.5px; color:var(--text-3); padding:6px 12px 24px; margin:0;">
      {{ __('history.showing_latest', ['count' => $latestCount]) }}
    </p>
  @endif

</div>

{{-- "Email a statement" — HistoryController::emailStatement(). The email is
     shown for reference only: it's disabled, and the server always sends to
     the account's own email whatever the form says. --}}
@php
  $statementOpened = auth()->user()->created_at->toDateString();
  $statementToday = now()->toDateString();
  $statementFrom = max($statementOpened, now()->startOfMonth()->toDateString());
  $statementHasErrors = $errors->has('from') || $errors->has('to');
@endphp
<div class="sheet-overlay {{ $statementHasErrors ? 'open' : '' }}" id="statementSheet" onclick="closeStatementSheet(event)">
  <div class="sheet" onclick="event.stopPropagation()">
    <div class="sheet-handle"></div>
    <div class="sheet-head">
      <h4>{{ __('history.statement_title') }}</h4>
      <div class="icon-btn" onclick="closeStatementSheet()">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8"><path d="M18 6L6 18M6 6l12 12"/></svg>
      </div>
    </div>

    <p style="font-size:13.5px; color:var(--text-2); line-height:1.5; margin:0 0 16px;">{{ __('history.statement_intro') }}</p>

    @if($statementHasErrors)
      <div class="send-alert" style="margin-bottom:14px;">{{ $errors->first('from') ?: $errors->first('to') }}</div>
    @endif

    <form method="POST" action="{{ route('history.statement.email') }}" onsubmit="this.querySelector('button[type=submit]').disabled = true; this.querySelector('button[type=submit]').textContent = @js(__('history.statement_sending'));">
      @csrf
      <div style="display:flex; gap:10px; flex-wrap:wrap;">
        <div class="field-group" style="flex:1; min-width:140px;">
          <p class="label">{{ __('history.statement_from') }}</p>
          <input type="date" name="from" class="text-input" value="{{ old('from', $statementFrom) }}" min="{{ $statementOpened }}" max="{{ $statementToday }}" required>
        </div>
        <div class="field-group" style="flex:1; min-width:140px;">
          <p class="label">{{ __('history.statement_to') }}</p>
          <input type="date" name="to" class="text-input" value="{{ old('to', $statementToday) }}" min="{{ $statementOpened }}" max="{{ $statementToday }}" required>
        </div>
      </div>
      <div class="field-group">
        <p class="label">{{ __('history.statement_email') }}</p>
        <input type="email" class="text-input" value="{{ auth()->user()->email }}" disabled readonly aria-readonly="true" style="opacity:.75; cursor:not-allowed;">
        <p style="font-size:12px; color:var(--text-3); margin:6px 0 0;">{{ __('history.statement_email_note') }}</p>
      </div>

      <button type="submit" class="pay-btn">{{ __('history.statement_send') }}</button>
    </form>
  </div>
</div>

<script>
  function openStatementSheet() {
    document.getElementById('statementSheet').classList.add('open');
  }
  function closeStatementSheet(e) {
    if (e && e.target !== e.currentTarget) return;
    document.getElementById('statementSheet').classList.remove('open');
  }
  // The To date can't be before the From date.
  (function () {
    var from = document.querySelector('#statementSheet input[name="from"]');
    var to = document.querySelector('#statementSheet input[name="to"]');
    if (!from || !to) return;
    function sync() {
      to.min = from.value || from.min;
      if (to.value && from.value && to.value < from.value) to.value = from.value;
    }
    from.addEventListener('change', sync);
    sync();
  })();
</script>

@if(session('statementSent'))
<script>
  document.addEventListener('DOMContentLoaded', function () {
    if (window.ledgerToast) window.ledgerToast(@json(session('statementSent')), 'success');
  });
</script>
@endif

@endsection
