@extends('layouts.admin')

@section('title', 'Support')

@section('content')
  <div class="admin-header">
    <p class="eyebrow">Customer support</p>
    <h1>Support inbox</h1>
    <p>{{ $conversations->total() }} conversation{{ $conversations->total() === 1 ? '' : 's' }}, most recently active first.</p>
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
    <div class="admin-stat {{ $needsReplyCount > 0 ? 'is-danger' : '' }}">
      <div class="admin-stat-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 9v4M12 17h.01M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0Z"/></svg></div>
      <p class="label">Needs a reply</p>
      <p class="value">{{ $needsReplyCount }}</p>
    </div>
  </div>

  <div class="admin-card">
    <div class="admin-tabs" id="supportStatusTabs" style="margin-bottom:14px;">
      <button type="button" class="admin-tab active" data-filter="all" onclick="setSupportFilter(this, 'all')">All</button>
      <button type="button" class="admin-tab" data-filter="open" onclick="setSupportFilter(this, 'open')">Open</button>
      <button type="button" class="admin-tab" data-filter="needs" onclick="setSupportFilter(this, 'needs')">Needs a reply</button>
      <button type="button" class="admin-tab" data-filter="urgent" onclick="setSupportFilter(this, 'urgent')">Urgent</button>
      <button type="button" class="admin-tab" data-filter="closed" onclick="setSupportFilter(this, 'closed')">Ended</button>
    </div>
    <div style="display:flex; gap:10px; flex-wrap:wrap;">
      <label class="admin-search" style="flex:1; min-width:200px;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.35-4.35"/></svg>
        <input type="text" id="supportSearchInput" placeholder="Search by name or email" oninput="applySupportFilters()">
      </label>
      <select id="supportCategorySelect" class="admin-select" onchange="applySupportFilters()">
        <option value="">All categories</option>
        <option value="card issue">Card issue</option>
        <option value="fraud or dispute">Fraud or dispute</option>
        <option value="payment problem">Payment problem</option>
        <option value="account access">Account access</option>
        <option value="account status">Account status</option>
        <option value="balance or statement">Balance or statement</option>
        <option value="bill pay">Bill pay</option>
        <option value="something else">Something else</option>
        <option value="general">General</option>
      </select>
    </div>
  </div>

  <div class="admin-card" style="padding:0;">
    @if($conversations->isEmpty())
      <p class="admin-empty">No support conversations yet. They'll show up here as soon as a customer sends their first message.</p>
    @else
      <div id="supportRows">
        @foreach($conversations as $conversation)
          @php($last = $conversation->messages->first())
          @php($needsReply = $conversation->isOpen() && $conversation->real_admin_reply_count === 0)
          <div
            class="support-row"
            data-filter-status="{{ $conversation->status }}"
            data-filter-needs="{{ $needsReply ? '1' : '0' }}"
            data-filter-priority="{{ $conversation->priority }}"
            data-filter-category="{{ $conversation->category ?? 'general' }}"
            data-search="{{ strtolower($conversation->user->name.' '.$conversation->user->email) }}"
            onclick="window.location='{{ route('admin.support.show', $conversation->user) }}'"
          >
            <div class="admin-avatar">
              @if($conversation->user->avatar)
                <img src="{{ $conversation->user->avatar }}" alt="{{ $conversation->user->name }}" style="width:100%; height:100%; border-radius:inherit; object-fit:cover;">
              @else
                {{ $conversation->user->initials() }}
              @endif
            </div>
            <div class="support-row-mid">
              <p class="support-row-name">
                {{ $conversation->user->name }}
                <span class="admin-pill {{ $conversation->isOpen() ? 'status-active' : 'status-frozen' }}" style="margin-left:6px;">{{ $conversation->statusLabel() }}</span>
                @if($conversation->isUrgent())
                  <span class="admin-pill status-disabled">Urgent</span>
                @endif
                @if($conversation->unread_count > 0)
                  <span class="admin-badge">{{ $conversation->unread_count }} new</span>
                @endif
                @if($needsReply)
                  <span class="admin-badge" style="background:rgba(214,150,40,0.16); color:#9A6B10;">Needs a reply</span>
                @endif
                <span class="admin-badge">{{ $conversation->categoryLabel() }}</span>
              </p>
              <p class="support-row-email">{{ $conversation->user->email }}</p>
              <p class="support-row-preview">
                @if($last)
                  @if($last->is_bot)
                    <span class="tag">Bot</span> {{ $last->preview(70) }}
                  @elseif($last->isFromAdmin())
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
      <p id="supportNoResults" style="display:none;" class="admin-empty">No conversations match your search.</p>
      <div style="padding:0 20px 16px;">
        @include('partials.admin-pagination', ['paginator' => $conversations])
      </div>
    @endif
  </div>

  <style>
    .support-row{
      display:flex; align-items:center; gap:14px; padding:14px 20px; cursor:pointer;
      border-bottom:1px solid var(--admin-border);
    }
    #supportRows .support-row:last-child{ border-bottom:none; }
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
    .admin-select{
      border:1px solid var(--admin-border); border-radius:8px; padding:6px 10px; font-size:12.5px;
      font-family:inherit; background:#fff; color:var(--admin-text);
    }
  </style>
@endsection

@section('scripts')
<script>
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
</script>
@endsection
