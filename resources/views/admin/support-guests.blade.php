@extends('layouts.admin')

@section('title', 'Guest messages')

@section('content')
  <div class="admin-header">
    <p class="eyebrow"><a href="<?= e(route('admin.support')) ?>">← Support inbox</a></p>
    <h1>Guest messages</h1>
    <p>
      <?= e($conversations->total()) ?> conversation<?= e($conversations->total() === 1 ? '' : 's') ?> from the
      chat widget on the marketing page. Nobody here has an account yet.
    </p>
  </div>

  <div class="admin-stat-row">
    <div class="admin-stat">
      <div class="admin-stat-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg></div>
      <p class="label">Conversations</p>
      <p class="value"><?= e($conversations->total()) ?></p>
    </div>
    <div class="admin-stat is-dark">
      <div class="admin-stat-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg></div>
      <p class="label">Open now</p>
      <p class="value"><?= e($openCount) ?></p>
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
    <?php if ($conversations->isEmpty()): ?>
      <p class="admin-empty">No guest messages yet. They'll show up here as soon as a visitor uses the chat icon on the marketing page.</p>
    <?php else: ?>
      <div id="guestRows">
        <?php foreach ($conversations as $conversation): ?>
          <?php $last = $conversation->messages->first(); ?>
          <div
            class="support-row"
            data-filter-status="<?= e($conversation->status) ?>"
            data-search="<?= e(strtolower($conversation->guest_name.' '.$conversation->guest_email)) ?>"
            onclick="window.location='<?= e(route('admin.support.guests.show', $conversation)) ?>'"
          >
            <div class="admin-avatar"><?= e($conversation->initials()) ?></div>
            <div class="support-row-mid">
              <p class="support-row-name">
                <?= e($conversation->guest_name) ?>
                <span class="admin-pill <?= e($conversation->isOpen() ? 'status-active' : 'status-frozen') ?>" style="margin-left:6px;"><?= e($conversation->statusLabel()) ?></span>
                <?php if ($conversation->unread_count > 0): ?>
                  <span class="admin-badge"><?= e($conversation->unread_count) ?> new</span>
                <?php endif; ?>
              </p>
              <p class="support-row-email"><?= e($conversation->guest_email) ?></p>
              <p class="support-row-preview">
                <?php if ($last): ?>
                  <?php if ($last->isFromAdmin()): ?>
                    <span class="tag">You</span> <?= e($last->preview(70)) ?>
                  <?php else: ?>
                    <?= e($last->preview(70)) ?>
                  <?php endif; ?>
                <?php else: ?>
                  -
                <?php endif; ?>
              </p>
            </div>
            <div class="support-row-time"><?= e(optional($last)->created_at?->diffForHumans()) ?></div>
            <div class="support-row-chevron">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
      <p id="guestNoResults" style="display:none;" class="admin-empty">No conversations match your search.</p>
      <div style="padding:0 20px 16px;">
        @include('partials.admin-pagination', ['paginator' => $conversations])
      </div>
    <?php endif; ?>
  </div>
@endsection
