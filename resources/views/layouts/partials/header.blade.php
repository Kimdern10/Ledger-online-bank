<nav class="sidenav">
    <div class="brand">
        <div class="mark">L</div>
        <span>Ledger</span>
    </div>

    <a href="{!! route('home') !!}" class="nav-item {!! request()->routeIs('home') ? 'active' : '' !!}">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.6"><path d="M3 11l9-8 9 8"/><path d="M5 10v10h14V10"/></svg>
        <span class="label">{{ __('dashboard.nav_home') }}</span>
    </a>
    <a href="{!! route('card') !!}" class="nav-item {!! request()->routeIs('card') ? 'active' : '' !!}">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.6"><rect x="3" y="6" width="18" height="13" rx="2"/><path d="M3 10h18"/></svg>
        <span class="label">{{ __('dashboard.nav_cards') }}</span>
    </a>
    <a href="{!! route('scan') !!}" class="nav-item {!! request()->routeIs('scan') ? 'active' : '' !!}">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.6"><rect x="4" y="4" width="7" height="7"/><rect x="13" y="4" width="7" height="7"/><rect x="4" y="13" width="7" height="7"/><path d="M14 15h5M16.5 12.5v5"/></svg>
        <span class="label">{{ __('dashboard.nav_pay_scan') }}</span>
    </a>
    <a href="{!! route('setting') !!}" class="nav-item {!! request()->routeIs('setting') ? 'active' : '' !!}">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.6"><circle cx="12" cy="8" r="4"/><path d="M4 21v-1a8 8 0 0 1 16 0v1"/></svg>
        <span class="label">{{ __('dashboard.nav_profile') }}</span>
    </a>

    <div class="nav-fab-row">
        <a href="#" class="nav-item cta {!! request()->routeIs('send') ? 'active' : '' !!}">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="1.6"><path d="M12 19V5M5 12l7-7 7 7"/></svg>
            <span class="label">{{ __('dashboard.nav_send_money') }}</span>
        </a>
    </div>
</nav>
