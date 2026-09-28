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

  <div class="summary-row fade-in d2">
    <div class="summary-stat in">
      <p class="ss-label">{{ __('history.money_in') }}</p>
      <p class="ss-value">${{ number_format($moneyIn, 2) }}</p>
    </div>
    <div class="summary-stat out">
      <p class="ss-label">{{ __('history.money_out') }}</p>
      <p class="ss-value">${{ number_format($moneyOut, 2) }}</p>
    </div>
  </div>

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
                {{-- Only a still-'scheduled' row gets one of these — see
                     HistoryController. --}}
                @if($item['cancel_route'])
                  <form method="POST" action="{{ $item['cancel_route'] }}" class="cancel-scheduled-form" onclick="event.stopPropagation()">
                    @csrf
                    <button type="submit" class="cancel-scheduled-btn">{{ __('history.cancel') }}</button>
                  </form>
                @endif
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

  @include('partials.pagination', ['paginator' => $transactions])

</div>

@endsection
