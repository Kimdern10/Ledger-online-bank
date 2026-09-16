<nav class="bottom-nav">
    <a href="{!! route('home') !!}" class="nav-item {!! request()->routeIs('home') ? 'active' : '' !!}">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.6"><path d="M3 11l9-8 9 8"/><path d="M5 10v10h14V10"/></svg>
        <span class="label">{{ __('dashboard.nav_home') }}</span>
    </a>
    <a href="{!! route('card') !!}" class="nav-item {!! request()->routeIs('card') ? 'active' : '' !!}">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.6"><rect x="3" y="6" width="18" height="13" rx="2"/><path d="M3 10h18"/></svg>
        <span class="label">{{ __('dashboard.nav_cards') }}</span>
    </a>
    <a href="{!! route('scan') !!}" class="mobile-fab">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8"><rect x="4" y="4" width="7" height="7"/><rect x="13" y="4" width="7" height="7"/><rect x="4" y="13" width="7" height="7"/><path d="M14 15h5M16.5 12.5v5"/></svg>
    </a>
    <a href="{!! route('setting') !!}" class="nav-item {!! request()->routeIs('profile.edit') ? 'active' : '' !!}">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.6"><circle cx="12" cy="8" r="4"/><path d="M4 21v-1a8 8 0 0 1 16 0v1"/></svg>
        <span class="label">{{ __('dashboard.nav_profile') }}</span>
    </a>
</nav>
