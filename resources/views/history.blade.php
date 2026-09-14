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

  {{-- Every group and row below comes straight from HistoryController —
       every real transaction type this user has (transfers, withdrawals,
       top-ups, admin balance adjustments, and card purchases), grouped by
       real dates. Rows with a receipt page (transfers, withdrawals,
       top-ups) link to it, exactly like Send Money's own receipt. Admin
       adjustments and card purchases have no receipt of their own — those
       render as plain, non-clickable rows instead. --}}
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
            {{-- A scheduled/cancelled/failed transfer has a real receipt
                 now (view_url — see HistoryController) but still can't be a
                 plain <a> the way the branch above is: a still-'scheduled'
                 row needs its Cancel form to sit inside it, and a <form>
                 nested in an <a> is invalid HTML. So this row navigates via
                 its own onclick instead, and the Cancel button's existing
                 onclick="event.stopPropagation()" keeps a click on IT from
                 also triggering the row's. A row with neither (card
                 purchases, which have no receipt at all) just isn't
                 clickable, same as before. --}}
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

<style>
  /* .tx-row used to be a plain div — now it's a clickable link to that
     transaction's receipt, so this strips the default anchor styling
     (blue text, underline) the rest of .tx-row's own CSS doesn't already
     override. */
  .tx-row{ text-decoration:none; color:inherit; cursor:pointer; }
  .tx-row.not-clickable{ cursor:default; }
  .no-tx-empty{ text-align:center; font-size:13.5px; color:var(--text-3); padding:40px 20px; line-height:1.5; }

  .tx-amt{ display:flex; flex-direction:column; align-items:flex-end; gap:4px; }
  .cancel-scheduled-form{ margin:0; }
  .cancel-scheduled-btn{
    font-size:11.5px; font-weight:600; color:#B4121B; background:#fdecea;
    border:1px solid #f5c6c2; border-radius:8px; padding:3px 9px; cursor:pointer;
  }
  .cancel-scheduled-btn:hover{ background:#fbdedb; }
</style>

<script>
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
</script>
@endsection
