@extends('layouts.admin')

@section('title', 'Users')

@section('content')
  <div class="admin-header">
    <p class="eyebrow">User management</p>
    <h1>Users</h1>
    <p><?= e($users->total()) ?> registered accounts.</p>
  </div>

  <div class="admin-card">
    <div class="admin-tabs" id="userStatusTabs" style="margin-bottom:14px;">
      <button type="button" class="admin-tab active" data-status="all" onclick="setUserStatusFilter(this, 'all')">All</button>
      <button type="button" class="admin-tab" data-status="active" onclick="setUserStatusFilter(this, 'active')">Active</button>
      <button type="button" class="admin-tab" data-status="frozen" onclick="setUserStatusFilter(this, 'frozen')">Frozen</button>
      <button type="button" class="admin-tab" data-status="suspended" onclick="setUserStatusFilter(this, 'suspended')">Suspended</button>
      <button type="button" class="admin-tab" data-status="disabled" onclick="setUserStatusFilter(this, 'disabled')">Disabled</button>
    </div>
    <label class="admin-search">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.35-4.35"/></svg>
      <input type="text" id="userSearchInput" placeholder="Search by name or email" oninput="applyUserFilters()">
    </label>
  </div>

  <div class="admin-card" style="padding:0; overflow-x:auto;">
    <?php if ($users->isEmpty()): ?>
      <p class="admin-empty">No users yet.</p>
    <?php else: ?>
      <table class="admin-table" id="usersTable">
        <thead>
          <tr>
            <th>User</th>
            <th>Available balance</th>
            <th>Joined</th>
            <th>Status</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($users as $u): ?>
            <tr class="user-row" data-status="<?= e($u->account_status) ?>" data-search="<?= e(strtolower($u->name.' '.$u->email)) ?>" onclick="window.location='<?= e(route('admin.users.show', $u)) ?>'" style="cursor:pointer;">
              <td>
                <div style="display:flex; align-items:center; gap:11px;">
                  <div class="admin-avatar"><?= e($u->initials()) ?></div>
                  <div>
                    <p style="margin:0; font-weight:600;"><?= e($u->name) ?></p>
                    <p style="margin:0; font-size:12.5px; color:var(--admin-text-2);"><?= e($u->email) ?></p>
                  </div>
                </div>
              </td>
              <td>$<?= e(number_format((float) $u->balance, 2)) ?></td>
              <td><?= e($u->created_at->format('M j, Y')) ?></td>
              <td><span class="admin-pill status-<?= e($u->account_status) ?>"><?= e($u->accountStatusLabel()) ?></span></td>
              <td style="text-align:right; color:var(--admin-text-2);">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:16px; height:16px;"><path d="M9 18l6-6-6-6"/></svg>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <p id="userNoResults" style="display:none;" class="admin-empty">No users match your search.</p>
      <div style="padding:0 20px 16px;">
        @include('partials.admin-pagination', ['paginator' => $users])
      </div>
    <?php endif; ?>
  </div>
@endsection
