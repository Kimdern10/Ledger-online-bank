<header class="site-header">
  <div class="container">
    <div class="header-row">

      <a href="{!! route('home') !!}" class="brand">
        <span class="mark">L</span>
        <span class="word">Ledger</span>
      </a>

      <nav class="main-nav" id="mainNav">
        <a href="{!! route('home') !!}" data-section="hero" class="{!! request()->routeIs('home') ? 'active' : '' !!}">{{ __('welcome.nav_home') }}</a>
        <a href="{!! route('home') !!}#about" data-section="about">{{ __('welcome.nav_about') }}</a>
        <a href="{!! route('home') !!}#services" data-section="services">{{ __('welcome.nav_services') }}</a>
        <a href="{!! route('home') !!}#calculators" data-section="calculators">{{ __('welcome.nav_calculators') }}</a>
        <a href="{!! route('home') !!}#faq" data-section="faq">{{ __('welcome.nav_faq') }}</a>
        <a href="{!! route('home') !!}#contact" data-section="contact">{{ __('welcome.nav_contact') }}</a>

        {{-- Language selector — usable by guests and signed-in visitors alike.
             Posts to LanguageSettingController::updateGuest(), which just
             remembers the choice in the session (see routes/web.php). --}}
        <form method="POST" action="{!! route('language.guest.update') !!}" class="nav-lang-form">
          @csrf
          <label class="nav-lang-select-wrap">
            <span class="sr-only">{{ __('welcome.language_label') }}</span>
            <select name="language" class="nav-lang-select" onchange="this.form.submit()">
              @foreach(\App\Support\Locale::LANGUAGES as $code => $label)
                <option value="{{ $code }}" @selected(app()->getLocale() === $code)>{{ $label }}</option>
              @endforeach
            </select>
            <svg class="nav-lang-chev" viewBox="0 0 24 24" fill="none"><path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </label>
          <noscript><button type="submit" class="btn btn-outline btn-block">{{ __('welcome.language_label') }}</button></noscript>
        </form>

        <div class="nav-mobile-actions">
          @auth
            <a href="{!! route('dashboard') !!}" class="btn btn-primary btn-block">
              {{ __('welcome.go_to_dashboard') }}
              <svg viewBox="0 0 24 24" fill="none"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </a>
          @else
            <a href="{!! route('login') !!}" class="btn btn-outline btn-block">{{ __('welcome.log_in') }}</a>
            <a href="{!! route('register') !!}" class="btn btn-primary btn-block">
              {{ __('welcome.open_an_account') }}
              <svg viewBox="0 0 24 24" fill="none"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </a>
          @endauth
        </div>
      </nav>

      <div class="header-cta">
        @auth
          <a href="{!! route('dashboard') !!}" class="btn btn-primary">
            {{ __('welcome.go_to_dashboard') }}
            <svg viewBox="0 0 24 24" fill="none"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </a>
        @else
          <a href="{!! route('login') !!}" class="btn btn-outline">{{ __('welcome.log_in') }}</a>
          <a href="{!! route('register') !!}" class="btn btn-primary">
            {{ __('welcome.open_an_account') }}
            <svg viewBox="0 0 24 24" fill="none"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </a>
        @endauth
        <button class="menu-toggle" id="menuToggle" aria-label="Open menu" aria-expanded="false">
          <svg viewBox="0 0 24 24" fill="none"><path d="M4 7h16M4 12h16M4 17h16" stroke-linecap="round"/></svg>
        </button>
      </div>

    </div>
  </div>
</header>

<div class="nav-backdrop" id="navBackdrop"></div>
