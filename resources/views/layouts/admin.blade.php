<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Admin') · Ledger Admin</title>
  <link rel="stylesheet" href="<?= asset('admin_asset/css/admin.css') ?>">
</head>
<body>
  <div class="admin-mobile-bar">
    <button type="button" class="admin-menu-btn" id="adminMenuBtn" aria-label="Open menu" aria-expanded="false" aria-controls="adminSidebar" onclick="toggleAdminSidebar()">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
    </button>
    <p class="admin-brand"><span class="admin-brand-mark">L</span> Ledger Admin</p>
  </div>

  <div class="admin-sidebar-overlay" id="adminSidebarOverlay" onclick="closeAdminSidebar()"></div>

  <div class="admin-shell">
    <aside class="admin-sidebar" id="adminSidebar">
      <p class="admin-brand"><span class="admin-brand-mark">L</span> Ledger Admin</p>

      <p class="admin-nav-section">Overview</p>
      <a href="<?= route('admin.dashboard') ?>" class="admin-nav-link <?= request()->routeIs('admin.dashboard') ? 'active' : '' ?>">Dashboard</a>

      <p class="admin-nav-section">User management</p>
      <a href="<?= route('admin.users') ?>" class="admin-nav-link <?= (request()->routeIs('admin.users') || request()->routeIs('admin.users.show')) ? 'active' : '' ?>">Users</a>
      <a href="<?= route('admin.trash') ?>" class="admin-nav-link <?= request()->routeIs('admin.trash') ? 'active' : '' ?>">Trash</a>

      <p class="admin-nav-section">Financial</p>
      <a href="<?= route('admin.bills') ?>" class="admin-nav-link <?= request()->routeIs('admin.bills') ? 'active' : '' ?>">Bills</a>
      <a href="<?= route('admin.banks') ?>" class="admin-nav-link <?= request()->routeIs('admin.banks*') ? 'active' : '' ?>">Banks</a>

      <p class="admin-nav-section">Cards</p>
      <?php
        // Same "compute right here in the shared sidebar" house style as
        // the support unread count above, so the pending-requests badge
        // shows up no matter which admin page is open.
        $adminPendingCardRequestCount = \App\Models\CardRequest::where('status', 'pending')->count();
      ?>
      <a href="<?= route('admin.card-requests') ?>" class="admin-nav-link <?= request()->routeIs('admin.card-requests') ? 'active' : '' ?>">
        Card requests
        <?php if ($adminPendingCardRequestCount > 0): ?>
          <span class="admin-badge" style="margin-left:6px;"><?= e($adminPendingCardRequestCount) ?></span>
        <?php endif; ?>
      </a>
      <a href="<?= route('admin.cards.reported') ?>" class="admin-nav-link <?= request()->routeIs('admin.cards.reported') ? 'active' : '' ?>">Reported cards</a>

      <p class="admin-nav-section">Identity</p>
      <?php
        // Same "compute right here in the shared sidebar" house style as
        // the card-requests and support unread counts above.
        $adminPendingKycCount = \App\Models\KycVerification::where('status', 'pending')->count();
      ?>
      <a href="<?= route('admin.kyc') ?>" class="admin-nav-link <?= request()->routeIs('admin.kyc') ? 'active' : '' ?>">
        Identity verification
        <?php if ($adminPendingKycCount > 0): ?>
          <span class="admin-badge" style="margin-left:6px;"><?= e($adminPendingKycCount) ?></span>
        <?php endif; ?>
      </a>
      <?php
        // Same "compute right here in the shared sidebar" house style as
        // $adminPendingKycCount just above.
        $adminPendingAddressCount = \App\Models\AddressVerification::where('status', 'pending')->count();
      ?>
      <a href="<?= route('admin.address') ?>" class="admin-nav-link <?= request()->routeIs('admin.address') ? 'active' : '' ?>">
        Address verification
        <?php if ($adminPendingAddressCount > 0): ?>
          <span class="admin-badge" style="margin-left:6px;"><?= e($adminPendingAddressCount) ?></span>
        <?php endif; ?>
      </a>

      <p class="admin-nav-section">Customer support</p>
      <?php
        // Computed directly here (same house style as dashboard.blade.php
        // pulling numbers straight off auth()->user() in an inline PHP
        // block) since the sidebar is shared across every admin page — this way
        // the unread count shows up no matter which page an admin is on,
        // without needing every single admin controller method to
        // remember to pass it in. "Unread by an admin" here means the
        // MESSAGE'S OWN sender_id equals its own user_id — i.e. it was
        // written by the customer, not a fellow admin replying — see
        // AdminSupportController for the same rule applied elsewhere.
        $adminUnreadSupportCount = \App\Models\SupportMessage::whereNull('read_at')
          ->whereColumn('sender_id', 'user_id')
          ->count();
      ?>
      <a href="<?= route('admin.support') ?>" class="admin-nav-link <?= (request()->routeIs('admin.support') || request()->routeIs('admin.support.show')) ? 'active' : '' ?>">
        Support
        <?php if ($adminUnreadSupportCount > 0): ?>
          <span class="admin-badge" style="margin-left:6px;"><?= e($adminUnreadSupportCount) ?></span>
        <?php endif; ?>
      </a>
      <?php
        // Same "compute right here in the shared sidebar" house style as
        // every other unread/pending count above. Guest messages have no
        // user_id at all (see guest_support_messages' migration), so
        // "unread by an admin" here is just "nobody's replied to it yet" —
        // sender_id null means the guest sent it.
        $adminUnreadGuestSupportCount = \App\Models\GuestSupportMessage::whereNull('read_at')
          ->whereNull('sender_id')
          ->count();
      ?>
      <a href="<?= route('admin.support.guests') ?>" class="admin-nav-link <?= (request()->routeIs('admin.support.guests') || request()->routeIs('admin.support.guests.show')) ? 'active' : '' ?>">
        Guest messages
        <?php if ($adminUnreadGuestSupportCount > 0): ?>
          <span class="admin-badge" style="margin-left:6px;"><?= e($adminUnreadGuestSupportCount) ?></span>
        <?php endif; ?>
      </a>

      <div class="admin-sidebar-footer">
        <form method="POST" action="<?= route('logout') ?>">
          <?= csrf_field() ?>
          <button type="submit" class="admin-logout-btn">Log out</button>
        </form>
      </div>
    </aside>

    <div class="admin-main-wrap">
      <div class="admin-topbar">
        <div class="admin-topbar-left">
          <p class="admin-topbar-title">@yield('title', 'Admin')</p>
        </div>
        <div class="admin-whoami">
          <div class="admin-avatar"><?= e(auth()->user()->initials()) ?></div>
          <div class="admin-whoami-text">
            <p class="name"><?= e(auth()->user()->name) ?></p>
            <p class="role">Administrator</p>
          </div>
        </div>
      </div>

      <main class="admin-main">
        <?php if (session('status')): ?>
          <div class="admin-status"><?= e(session('status')) ?></div>
        <?php endif; ?>
        @yield('content')
      </main>
    </div>
  </div>

  @include('partials.sweetalert')

  <?php /* Individual admin pages push their own small <script> blocks in here
       via @section('scripts') ... @endsection. Today that's only pages
       that need a tiny Blade-rendered bootstrap (CSRF token, route URLs,
       the other person's initials) — see admin/support-show.blade.php and
       admin/support-guest-show.blade.php — and it has to run BEFORE
       admin.js below, since admin.js reads window.LedgerGuestChat /
       window.LedgerAdminChat the moment it loads. */ ?>
  @yield('scripts')

  <?php /* Shared JS for every admin page (sidebar toggle, plus each individual
       page's own logic guarded on its own elements existing) — see the
       file's own header comment for why it's safe to load everywhere. */ ?>
  <script src="<?= asset('admin_asset/js/admin.js') ?>"></script>
</body>
</html>
