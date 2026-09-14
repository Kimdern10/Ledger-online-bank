<header class="site-header">
  <div class="container">
    <div class="header-row">

      <a href="{{ route('home') }}" class="brand">
        <span class="mark">L</span>
        <span class="word">Ledger</span>
      </a>

      <nav class="main-nav" id="mainNav">
        <a href="{{ route('home') }}" data-section="hero" class="{{ request()->routeIs('home') ? 'active' : '' }}">{{ __('welcome.nav_home') }}</a>
        <a href="{{ route('home') }}#about" data-section="about">{{ __('welcome.nav_about') }}</a>
        <a href="{{ route('home') }}#services" data-section="services">{{ __('welcome.nav_services') }}</a>
        <a href="{{ route('home') }}#calculators" data-section="calculators">{{ __('welcome.nav_calculators') }}</a>
        <a href="{{ route('home') }}#faq" data-section="faq">{{ __('welcome.nav_faq') }}</a>
        <a href="{{ route('home') }}#contact" data-section="contact">{{ __('welcome.nav_contact') }}</a>

        {{-- Language selector — usable by guests and signed-in visitors alike.
             Posts to LanguageSettingController::updateGuest(), which just
             remembers the choice in the session (see routes/web.php). --}}
        <form method="POST" action="{{ route('language.guest.update') }}" class="nav-lang-form">
          @csrf
          <label class="nav-lang-select-wrap">
            <span class="sr-only">{{ __('welcome.language_label') }}</span>
            <select name="language" class="nav-lang-select" onchange="this.form.submit()">
              @foreach(\App\Http\Controllers\LanguageSettingController::LANGUAGES as $code => $label)
                <option value="{{ $code }}" {{ app()->getLocale() === $code ? 'selected' : '' }}>{{ $label }}</option>
              @endforeach
            </select>
            <svg class="nav-lang-chev" viewBox="0 0 24 24" fill="none"><path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </label>
          <noscript><button type="submit" class="btn btn-outline btn-block">{{ __('welcome.language_label') }}</button></noscript>
        </form>

        <div class="nav-mobile-actions">
          @auth
            <a href="{{ route('dashboard') }}" class="btn btn-primary btn-block">
              {{ __('welcome.go_to_dashboard') }}
              <svg viewBox="0 0 24 24" fill="none"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </a>
          @else
            <a href="{{ route('login') }}" class="btn btn-outline btn-block">{{ __('welcome.log_in') }}</a>
            <a href="{{ route('register') }}" class="btn btn-primary btn-block">
              {{ __('welcome.open_an_account') }}
              <svg viewBox="0 0 24 24" fill="none"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </a>
          @endauth
        </div>
      </nav>

      <div class="header-cta">
        @auth
          <a href="{{ route('dashboard') }}" class="btn btn-primary">
            {{ __('welcome.go_to_dashboard') }}
            <svg viewBox="0 0 24 24" fill="none"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </a>
        @else
          <a href="{{ route('login') }}" class="btn btn-outline">{{ __('welcome.log_in') }}</a>
          <a href="{{ route('register') }}" class="btn btn-primary">
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

<script>
  (function(){
    var toggle = document.getElementById('menuToggle');
    var nav = document.getElementById('mainNav');
    var backdrop = document.getElementById('navBackdrop');
    if(!toggle || !nav) return;

    var iconMenu = '<svg viewBox="0 0 24 24" fill="none"><path d="M4 7h16M4 12h16M4 17h16" stroke-linecap="round"/></svg>';
    var iconClose = '<svg viewBox="0 0 24 24" fill="none"><path d="M6 6l12 12M18 6L6 18" stroke-linecap="round"/></svg>';

    function openNav(){
      nav.classList.add('open');
      if(backdrop) backdrop.classList.add('open');
      toggle.setAttribute('aria-expanded', 'true');
      toggle.innerHTML = iconClose;
      document.body.style.overflow = 'hidden';
      // Lets the floating support-chat bubble (.gs-launcher, see
      // guest-support-widget.blade.php) move out from behind the open
      // drawer instead of floating awkwardly over it — see the
      // body.nav-open rule next to .gs-launcher's own CSS.
      document.body.classList.add('nav-open');
    }

    function closeNav(){
      nav.classList.remove('open');
      if(backdrop) backdrop.classList.remove('open');
      toggle.setAttribute('aria-expanded', 'false');
      toggle.innerHTML = iconMenu;
      document.body.style.overflow = '';
      document.body.classList.remove('nav-open');
    }

    toggle.addEventListener('click', function(){
      if(nav.classList.contains('open')){
        closeNav();
      } else {
        openNav();
      }
    });

    if(backdrop){
      backdrop.addEventListener('click', closeNav);
    }

    nav.querySelectorAll('a').forEach(function(link){
      link.addEventListener('click', closeNav);
    });

    document.addEventListener('keydown', function(e){
      if(e.key === 'Escape') closeNav();
    });

    // if the viewport is resized past the mobile breakpoint while the drawer
    // is open (e.g. rotating a tablet), reset state so it isn't stuck "open"
    // once it's back to being an inline desktop nav.
    window.addEventListener('resize', function(){
      if(window.innerWidth > 960 && nav.classList.contains('open')){
        closeNav();
      }
    });
  })();

  // ---------- Scrollspy: highlight the nav link for the section in view ----------
  // Deferred to DOMContentLoaded: this script sits in the header, above the
  // yielded page content in the layout, so the section elements it looks for
  // (#about, #services, etc.) don't exist in the DOM yet at parse time.
  document.addEventListener('DOMContentLoaded', function(){
    var links = document.querySelectorAll('.main-nav a[data-section]');
    if(!links.length || !('IntersectionObserver' in window)) return;

    var sections = [];
    links.forEach(function(link){
      var id = link.getAttribute('data-section');
      var el = document.getElementById(id);
      if(el) sections.push({id: id, el: el});
    });
    if(!sections.length) return;

    var io = new IntersectionObserver(function(entries){
      entries.forEach(function(entry){
        if(!entry.isIntersecting) return;
        var id = entry.target.id;
        links.forEach(function(link){
          link.classList.toggle('active', link.getAttribute('data-section') === id);
        });
      });
    }, {rootMargin:'-45% 0px -45% 0px', threshold:0});

    sections.forEach(function(s){ io.observe(s.el); });
  });
</script>
