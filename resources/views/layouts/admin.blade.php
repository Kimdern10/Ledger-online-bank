<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Admin') · Ledger Admin</title>
  <style>
    :root{
      --admin-bg:#F3F5F4; --admin-card:#FFFFFF; --admin-text:#10202F; --admin-text-2:#5C6B72;
      --admin-accent:#2F6F62; --admin-accent-2:#C1503C; --admin-border:rgba(16,32,47,0.10);
      --admin-dark:#123B30; --admin-dark-2:#1B4E40;
    }
    *{ box-sizing:border-box; }
    body{
      margin:0; font-family:'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
      background:var(--admin-bg); color:var(--admin-text); font-size:14px;
    }
    a{ color:inherit; text-decoration:none; }
    .admin-shell{ display:flex; min-height:100vh; }

    /* ---------- Sidebar ---------- */
    .admin-sidebar{
      width:230px; flex-shrink:0; background:var(--admin-card); border-right:1px solid var(--admin-border);
      padding:20px 14px; display:flex; flex-direction:column; gap:2px;
    }
    .admin-brand{
      font-weight:700; font-size:16.5px; margin:0 6px 20px; color:var(--admin-dark);
      display:flex; align-items:center; gap:8px;
    }
    .admin-brand-mark{
      width:26px; height:26px; border-radius:8px; background:var(--admin-dark); color:#fff;
      display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:700; flex-shrink:0;
    }
    .admin-nav-section{
      font-size:10.5px; font-weight:700; letter-spacing:.06em; text-transform:uppercase;
      color:var(--admin-text-2); margin:14px 10px 6px; opacity:0.75;
    }
    .admin-nav-section:first-of-type{ margin-top:2px; }
    .admin-nav-link{
      padding:9px 10px; border-radius:9px; font-size:13.5px; font-weight:500; color:var(--admin-text-2);
      display:block;
    }
    .admin-nav-link:hover{ background:rgba(47,111,98,0.08); color:var(--admin-text); }
    .admin-nav-link.active{ background:var(--admin-dark); color:#fff; font-weight:600; }
    .admin-sidebar-footer{ margin-top:auto; padding-top:10px; border-top:1px solid var(--admin-border); }
    .admin-logout-btn{
      width:100%; text-align:left; border:none; background:none; padding:9px 10px; border-radius:8px;
      font-size:13.5px; font-weight:500; color:var(--admin-text-2); cursor:pointer; font-family:inherit;
    }
    .admin-logout-btn:hover{ background:rgba(193,80,60,0.1); color:var(--admin-accent-2); }

    /* ---------- Top bar (page title + admin identity) ---------- */
    .admin-topbar{
      display:flex; align-items:center; justify-content:space-between; gap:12px;
      padding:16px 28px; background:var(--admin-card); border-bottom:1px solid var(--admin-border);
    }
    .admin-topbar-left{ display:flex; align-items:center; gap:12px; }
    .admin-topbar-title{ font-size:15.5px; font-weight:600; margin:0; }
    .admin-whoami{ display:flex; align-items:center; gap:10px; }
    .admin-avatar{
      width:34px; height:34px; border-radius:50%; background:rgba(47,111,98,0.14); color:var(--admin-accent);
      display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:700; flex-shrink:0;
    }
    .admin-whoami-text{ line-height:1.25; }
    .admin-whoami-text p.name{ margin:0; font-size:13px; font-weight:600; }
    .admin-whoami-text p.role{ margin:0; font-size:11.5px; color:var(--admin-text-2); }

    /* ---------- Main content ---------- */
    .admin-main-wrap{ flex:1; display:flex; flex-direction:column; min-width:0; }
    .admin-main{ flex:1; padding:26px 28px; max-width:1120px; width:100%; }
    .admin-header{ margin-bottom:20px; }
    .admin-header .eyebrow{
      font-size:11px; font-weight:700; letter-spacing:.06em; text-transform:uppercase;
      color:var(--admin-text-2); margin:0 0 6px; opacity:0.75;
    }
    .admin-header h1{ font-size:22px; margin:0; }
    .admin-header p{ margin:6px 0 0; font-size:13px; color:var(--admin-text-2); }
    .admin-status{
      background:rgba(47,111,98,0.1); color:var(--admin-accent); border:1px solid rgba(47,111,98,0.25);
      padding:9px 14px; border-radius:10px; font-size:13px; margin-bottom:18px;
    }
    .admin-card{
      background:var(--admin-card); border:1px solid var(--admin-border); border-radius:14px; padding:18px 20px;
      margin-bottom:18px;
    }

    /* ---------- Stat tiles ---------- */
    .admin-stat-row{ display:flex; gap:14px; flex-wrap:wrap; margin-bottom:22px; }
    .admin-stat{
      flex:1; min-width:150px; border-radius:14px; padding:16px 18px; border:1px solid var(--admin-border);
      background:var(--admin-card);
    }
    .admin-stat.is-dark{ background:var(--admin-dark); border-color:var(--admin-dark); color:#fff; }
    .admin-stat.is-danger{ background:var(--admin-accent-2); border-color:var(--admin-accent-2); color:#fff; }
    .admin-stat-icon{
      width:32px; height:32px; border-radius:9px; display:flex; align-items:center; justify-content:center;
      background:rgba(255,255,255,0.14); margin-bottom:10px;
    }
    .admin-stat:not(.is-dark):not(.is-danger) .admin-stat-icon{ background:rgba(47,111,98,0.1); color:var(--admin-accent); }
    .admin-stat-icon svg{ width:16px; height:16px; }
    .admin-stat p.label{ margin:0 0 4px; font-size:12.5px; opacity:0.85; }
    .admin-stat:not(.is-dark):not(.is-danger) p.label{ color:var(--admin-text-2); opacity:1; }
    .admin-stat p.value{ margin:0; font-size:22px; font-weight:700; }

    /* ---------- Tables ---------- */
    table.admin-table{ width:100%; border-collapse:collapse; font-size:13.5px; }
    table.admin-table th{
      text-align:left; padding:10px 12px; font-size:12px; text-transform:uppercase; letter-spacing:.03em;
      color:var(--admin-text-2); border-bottom:1px solid var(--admin-border); white-space:nowrap;
    }
    table.admin-table td{ padding:11px 12px; border-bottom:1px solid var(--admin-border); vertical-align:middle; }
    table.admin-table tr:last-child td{ border-bottom:none; }
    .admin-badge{
      display:inline-block; padding:2px 9px; margin:1px 3px 1px 0; border-radius:20px; font-size:11.5px; font-weight:600;
      background:rgba(47,111,98,0.1); color:var(--admin-accent); white-space:nowrap;
    }
    .admin-pill{
      display:inline-flex; align-items:center; gap:5px; padding:3px 11px; border-radius:20px;
      font-size:12px; font-weight:600; white-space:nowrap;
    }
    .admin-pill::before{ content:''; width:6px; height:6px; border-radius:50%; background:currentColor; }
    .admin-pill.status-active{ background:rgba(47,111,98,0.12); color:var(--admin-accent); }
    .admin-pill.status-frozen{ background:rgba(90,150,220,0.14); color:#3568A8; }
    .admin-pill.status-suspended{ background:rgba(214,150,40,0.16); color:#9A6B10; }
    .admin-pill.status-disabled{ background:rgba(193,80,60,0.12); color:var(--admin-accent-2); }

    .admin-btn{
      border:none; border-radius:8px; padding:7px 13px; font-size:12.5px; font-weight:600; cursor:pointer;
      font-family:inherit;
    }
    .admin-btn-primary{ background:var(--admin-accent); color:#fff; }
    .admin-btn-dark{ background:var(--admin-dark); color:#fff; }
    .admin-btn-outline{ background:#fff; color:var(--admin-text); border:1px solid var(--admin-border); }
    .admin-btn-danger{ background:rgba(193,80,60,0.1); color:var(--admin-accent-2); }
    .admin-btn-primary:hover, .admin-btn-dark:hover{ opacity:0.9; }
    .admin-btn-outline:hover{ background:var(--admin-bg); }
    .admin-btn-danger:hover{ background:rgba(193,80,60,0.18); }
    .admin-btn[disabled]{ opacity:0.5; cursor:default; }

    .admin-field-error{ color:var(--admin-accent-2); font-size:11.5px; margin:4px 0 0; }
    .admin-empty{ padding:26px; text-align:center; color:var(--admin-text-2); font-size:13.5px; }
    .admin-search{
      display:flex; align-items:center; gap:8px; border:1px solid var(--admin-border); border-radius:11px;
      padding:10px 14px; background:var(--admin-card); color:var(--admin-text-2);
    }
    .admin-search svg{ width:16px; height:16px; flex-shrink:0; }
    .admin-search input{
      border:none; outline:none; font-size:13.5px; font-family:inherit; flex:1; background:none; color:var(--admin-text);
    }
    /* ---------- Pagination ---------- */
    .admin-pagination{
      display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap;
      padding:14px 4px 4px;
    }
    .admin-pagination-summary{ font-size:12.5px; color:var(--admin-text-2); }
    .admin-pagination-links{ display:flex; align-items:center; gap:4px; flex-wrap:wrap; }
    .admin-page-link{
      display:inline-flex; align-items:center; justify-content:center; min-width:30px; height:30px;
      padding:0 8px; border-radius:8px; font-size:12.5px; font-weight:600; color:var(--admin-text-2);
      border:1px solid var(--admin-border); background:#fff;
    }
    .admin-page-link:hover{ background:var(--admin-bg); color:var(--admin-text); }
    .admin-page-link.active{ background:var(--admin-dark); color:#fff; border-color:var(--admin-dark); }
    .admin-page-link.disabled{ opacity:0.4; pointer-events:none; }
    .admin-page-ellipsis{ padding:0 4px; color:var(--admin-text-2); font-size:12.5px; }

    .admin-tabs{ display:flex; gap:4px; flex-wrap:wrap; }
    .admin-tab{
      border:none; background:none; padding:8px 14px; border-radius:9px; font-size:13px; font-weight:600;
      color:var(--admin-text-2); cursor:pointer; font-family:inherit;
    }
    .admin-tab:hover{ background:rgba(16,32,47,0.05); }
    .admin-tab.active{ background:var(--admin-dark); color:#fff; }

    /* ---------- Mobile drawer ---------- */
    .admin-mobile-bar{ display:none; }
    .admin-sidebar-overlay{
      display:none; position:fixed; inset:0; background:rgba(16,32,47,0.4);
      z-index:39; opacity:0; transition:opacity .2s ease;
    }
    @media (max-width:720px){
      .admin-mobile-bar{
        display:flex; align-items:center; gap:12px; position:sticky; top:0; z-index:20;
        background:var(--admin-card); border-bottom:1px solid var(--admin-border);
        padding:12px 16px;
      }
      .admin-menu-btn{
        border:none; background:none; padding:6px; margin:-6px; border-radius:8px; cursor:pointer;
        display:flex; align-items:center; justify-content:center; color:var(--admin-text);
      }
      .admin-menu-btn:hover{ background:rgba(16,32,47,0.06); }
      .admin-menu-btn svg{ width:22px; height:22px; }
      .admin-mobile-bar .admin-brand{ margin:0; }
      .admin-topbar{ display:none; }

      .admin-shell{ display:block; }
      .admin-sidebar{
        position:fixed; top:0; left:0; height:100vh; width:240px; z-index:40;
        transform:translateX(-100%); transition:transform .22s ease;
        box-shadow:2px 0 16px rgba(16,32,47,0.12);
      }
      .admin-sidebar.open{ transform:translateX(0); }
      .admin-sidebar-overlay.open{ display:block; opacity:1; }
      .admin-main{ padding:18px; max-width:100%; }
    }
    @media (prefers-reduced-motion:reduce){
      .admin-sidebar{ transition:none; }
    }
  </style>
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
      <a href="{{ route('admin.dashboard') }}" class="admin-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Dashboard</a>

      <p class="admin-nav-section">User management</p>
      <a href="{{ route('admin.users') }}" class="admin-nav-link {{ request()->routeIs('admin.users') || request()->routeIs('admin.users.show') ? 'active' : '' }}">Users</a>
      <a href="{{ route('admin.trash') }}" class="admin-nav-link {{ request()->routeIs('admin.trash') ? 'active' : '' }}">Trash</a>

      <p class="admin-nav-section">Financial</p>
      <a href="{{ route('admin.bills') }}" class="admin-nav-link {{ request()->routeIs('admin.bills') ? 'active' : '' }}">Bills</a>
      <a href="{{ route('admin.banks') }}" class="admin-nav-link {{ request()->routeIs('admin.banks*') ? 'active' : '' }}">Banks</a>

      <p class="admin-nav-section">Cards</p>
      @php
        // Same "compute right here in the shared sidebar" house style as
        // the support unread count above, so the pending-requests badge
        // shows up no matter which admin page is open.
        $adminPendingCardRequestCount = \App\Models\CardRequest::where('status', 'pending')->count();
      @endphp
      <a href="{{ route('admin.card-requests') }}" class="admin-nav-link {{ request()->routeIs('admin.card-requests') ? 'active' : '' }}">
        Card requests
        @if($adminPendingCardRequestCount > 0)
          <span class="admin-badge" style="margin-left:6px;">{{ $adminPendingCardRequestCount }}</span>
        @endif
      </a>
      <a href="{{ route('admin.cards.reported') }}" class="admin-nav-link {{ request()->routeIs('admin.cards.reported') ? 'active' : '' }}">Reported cards</a>

      <p class="admin-nav-section">Identity</p>
      @php
        // Same "compute right here in the shared sidebar" house style as
        // the card-requests and support unread counts above.
        $adminPendingKycCount = \App\Models\KycVerification::where('status', 'pending')->count();
      @endphp
      <a href="{{ route('admin.kyc') }}" class="admin-nav-link {{ request()->routeIs('admin.kyc') ? 'active' : '' }}">
        Identity verification
        @if($adminPendingKycCount > 0)
          <span class="admin-badge" style="margin-left:6px;">{{ $adminPendingKycCount }}</span>
        @endif
      </a>
      @php
        // Same "compute right here in the shared sidebar" house style as
        // $adminPendingKycCount just above.
        $adminPendingAddressCount = \App\Models\AddressVerification::where('status', 'pending')->count();
      @endphp
      <a href="{{ route('admin.address') }}" class="admin-nav-link {{ request()->routeIs('admin.address') ? 'active' : '' }}">
        Address verification
        @if($adminPendingAddressCount > 0)
          <span class="admin-badge" style="margin-left:6px;">{{ $adminPendingAddressCount }}</span>
        @endif
      </a>

      <p class="admin-nav-section">Customer support</p>
      @php
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
      @endphp
      <a href="{{ route('admin.support') }}" class="admin-nav-link {{ request()->routeIs('admin.support') || request()->routeIs('admin.support.show') ? 'active' : '' }}">
        Support
        @if($adminUnreadSupportCount > 0)
          <span class="admin-badge" style="margin-left:6px;">{{ $adminUnreadSupportCount }}</span>
        @endif
      </a>
      @php
        // Same "compute right here in the shared sidebar" house style as
        // every other unread/pending count above. Guest messages have no
        // user_id at all (see guest_support_messages' migration), so
        // "unread by an admin" here is just "nobody's replied to it yet" —
        // sender_id null means the guest sent it.
        $adminUnreadGuestSupportCount = \App\Models\GuestSupportMessage::whereNull('read_at')
          ->whereNull('sender_id')
          ->count();
      @endphp
      <a href="{{ route('admin.support.guests') }}" class="admin-nav-link {{ request()->routeIs('admin.support.guests') || request()->routeIs('admin.support.guests.show') ? 'active' : '' }}">
        Guest messages
        @if($adminUnreadGuestSupportCount > 0)
          <span class="admin-badge" style="margin-left:6px;">{{ $adminUnreadGuestSupportCount }}</span>
        @endif
      </a>

      <div class="admin-sidebar-footer">
        <form method="POST" action="{{ route('logout') }}">
          @csrf
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
          <div class="admin-avatar">{{ auth()->user()->initials() }}</div>
          <div class="admin-whoami-text">
            <p class="name">{{ auth()->user()->name }}</p>
            <p class="role">Administrator</p>
          </div>
        </div>
      </div>

      <main class="admin-main">
        @if(session('status'))
          <div class="admin-status">{{ session('status') }}</div>
        @endif
        @yield('content')
      </main>
    </div>
  </div>

  <script>
    // Only matters on mobile (the sidebar has no .open/closed state at all
    // on laptop widths — CSS never moves it there), so this is harmless
    // dead weight above 720px rather than something that needs its own
    // guard.
    function openAdminSidebar(){
      document.getElementById('adminSidebar').classList.add('open');
      document.getElementById('adminSidebarOverlay').classList.add('open');
      document.getElementById('adminMenuBtn').setAttribute('aria-expanded', 'true');
    }
    function closeAdminSidebar(){
      document.getElementById('adminSidebar').classList.remove('open');
      document.getElementById('adminSidebarOverlay').classList.remove('open');
      document.getElementById('adminMenuBtn').setAttribute('aria-expanded', 'false');
    }
    function toggleAdminSidebar(){
      document.getElementById('adminSidebar').classList.contains('open') ? closeAdminSidebar() : openAdminSidebar();
    }
    // Closing on nav-link tap means you never end up back on a page with
    // the drawer still hanging open over it.
    document.querySelectorAll('.admin-sidebar .admin-nav-link').forEach(function(link){
      link.addEventListener('click', closeAdminSidebar);
    });
    document.addEventListener('keydown', function(e){
      if (e.key === 'Escape') closeAdminSidebar();
    });
  </script>

  @include('partials.sweetalert')

  {{-- Individual admin pages push their own <script> blocks in here via
       @section('scripts') ... @endsection, rather than inlining them at
       the bottom of every page's markup. --}}
  @yield('scripts')
</body>
</html>
