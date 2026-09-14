@extends('layouts.admin')

@section('title', 'Guest messages')

@section('content')
  <div class="admin-header">
    <p class="eyebrow"><a href="{{ route('admin.support') }}">← Support inbox</a></p>
    <h1>Guest messages</h1>
    <p>
      {{ $conversations->total() }} conversation{{ $conversations->total() === 1 ? '' : 's' }} from the
      chat widget on the marketing page. Nobody here has an account yet.
    </p>
  </div>

  <div class="admin-stat-row">
    <div class="admin-stat">
      <div class="admin-stat-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg></div>
      <p class="label">Conversations</p>
      <p class="value">{{ $conversations->total() }}</p>
    </div>
    <div class="admin-stat is-dark">
      <div class="admin-stat-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg></div>
      <p class="label">Open now</p>
      <p class="value">{{ $openCount }}</p>
    </div>
  </div>

  <div class="admin-card">
    <div class="admin-tabs" id="guestStatusTabs" style="margin-bottom:14px;">
      <button type="button" class="admin-tab active" data-filter="all" onclick="setGuestFilter(this, 'all')">All</button>
      <button type="button" class="admin-tab" data-filter="open" onclick="setGuestFilter(this, 'open')">Open</button>
      <button type="button" class="admin-tab" data-filter="closed" onclick="setGuestFilter(this, 'closed')">Ended</button>
    </div>
    <label class="admin-search">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.35-4.35"/></svg>
      <input type="text" id="guestSearchInput" placeholder="Search by name or email" oninput="applyGuestFilters()">
    </label>
  </div>

  <div class="admin-card" style="padding:0;">
    @if($conversations->isEmpty())
      <p class="admin-empty">No guest messages yet. They'll show up here as soon as a visitor uses the chat icon on the marketing page.</p>
    @else
      <div id="guestRows">
        @foreach($conversations as $conversation)
          @php($last = $conversation->messages->first())
          <div
            class="support-row"
            data-filter-status="{{ $conversation->status }}"
            data-search="{{ strtolower($conversation->guest_name.' '.$conversation->guest_email) }}"
            onclick="window.location='{{ route('admin.support.guests.show', $conversation) }}'"
          >
            <div class="admin-avatar">{{ $conversation->initials() }}</div>
            <div class="support-row-mid">
              <p class="support-row-name">
                {{ $conversation->guest_name }}
                <span class="admin-pill {{ $conversation->isOpen() ? 'status-active' : 'status-frozen' }}" style="margin-left:6px;">{{ $conversation->statusLabel() }}</span>
                @if($conversation->unread_count > 0)
                  <span class="admin-badge">{{ $conversation->unread_count }} new</span>
                @endif
              </p>
              <p class="support-row-email">{{ $conversation->guest_email }}</p>
              <p class="support-row-preview">
                @if($last)
                  @if($last->isFromAdmin())
                    <span class="tag">You</span> {{ $last->preview(70) }}
                  @else
                    {{ $last->preview(70) }}
                  @endif
                @else
                  -
                @endif
              </p>
            </div>
            <div class="support-row-time">{{ optional($last)->created_at?->diffForHumans() }}</div>
            <div class="support-row-chevron">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>
            </div>
          </div>
        @endforeach
      </div>
      <p id="guestNoResults" style="display:none;" class="admin-empty">No conversations match your search.</p>
      <div style="padding:0 20px 16px;">
        @include('partials.admin-pagination', ['paginator' => $conversations])
      </div>
    @endif
  </div>

  <style>
    /* Same row styling as admin/support.blade.php's .support-row — kept as
       its own local copy rather than a shared partial, matching how
       admin-kyc.blade.php and admin-support.blade.php already each keep
       their own local copy of .admin-select instead of sharing one. */
    .support-row{
      display:flex; align-items:center; gap:14px; padding:14px 20px; cursor:pointer;
      border-bottom:1px solid var(--admin-border);
    }
    #guestRows .support-row:last-child{ border-bottom:none; }
    .support-row:hover{ background:rgba(47,111,98,0.045); }
    .support-row-mid{ flex:1; min-width:0; }
    .support-row-name{ margin:0 0 2px; font-weight:600; font-size:13.5px; }
    .support-row-email{ margin:0 0 4px; font-size:12px; color:var(--admin-text-2); }
    .support-row-preview{
      margin:0; font-size:12.5px; color:var(--admin-text-2);
      overflow:hidden; text-overflow:ellipsis; white-space:nowrap; max-width:460px;
    }
    .support-row-preview .tag{ font-weight:700; color:var(--admin-text); }
    .support-row-time{ font-size:12px; color:var(--admin-text-2); white-space:nowrap; flex-shrink:0; }
    .support-row-chevron{ color:var(--admin-text-2); flex-shrink:0; display:flex; }
    .support-row-chevron svg{ width:16px; height:16px; }
    @media (max-width:640px){
      .support-row-preview{ max-width:220px; }
      .support-row-time{ display:none; }
    }
  </style>
@endsection

@section('scripts')
<script>
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
</script>
@endsection
