@extends('layouts.apps')

@section('content')

{{-- ============ HERO ============ --}}
<section class="hero" id="hero">
  <div class="hero-blob hero-blob-1"></div>
  <div class="hero-blob hero-blob-2"></div>
  <div class="container">
    <div class="hero-grid">

      <div class="hero-copy fade-up">
        <div class="eyebrow"><span class="dot"></span> {{ __('welcome.hero_eyebrow') }}</div>
        <h1 class="h1">{{ __('welcome.hero_h1_line1') }}<br>{{ __('welcome.hero_h1_line2') }}</h1>
        <p class="lede" style="max-width:460px; margin-top:18px;">
          {{ __('welcome.hero_lede') }}
        </p>

        @auth
          {{-- Signed-in visitor: no point showing a "create account" form
               to someone who already has one — send them straight back
               into their own ledger instead. --}}
          <div class="hero-form" style="max-width:420px;">
            {{-- Admins have no personal dashboard of their own — send them
                 to the admin dashboard instead of the consumer one. --}}
            <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('dashboard') }}" class="btn btn-primary" style="width:100%; justify-content:center;">
              {{ auth()->user()->isAdmin() ? __('welcome.go_to_admin_dashboard') : __('welcome.go_to_dashboard') }}
              <svg viewBox="0 0 24 24" fill="none"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </a>
          </div>
          <p class="mono" style="font-size:12px; color:var(--text-3); margin-top:12px;">{{ __('welcome.welcome_back_note') }}</p>
        @else
          {{-- A GET to the register page, not a real signup submission —
               this just hands off the email address so the guest doesn't
               have to type it twice (see pages/auth/register.blade.php's
               old('email', request()->query('email')) fallback). Used to
               be onsubmit="return false;", which meant clicking "Open an
               account" here did nothing at all. --}}
          <form class="hero-form" method="GET" action="{{ route('register') }}">
            <input type="email" name="email" placeholder="you@email.com" required>
            <button type="submit" class="btn btn-primary">
              {{ __('welcome.open_an_account') }}
              <svg viewBox="0 0 24 24" fill="none"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
          </form>
          <p class="mono" style="font-size:12px; color:var(--text-3); margin-top:12px;">{{ __('welcome.no_minimum_note') }}</p>
        @endauth

        <div class="hero-stats">
          <div>
            <div class="h3 count-up" style="font-family:'Newsreader',serif;" data-target="128" data-suffix="k+">0k+</div>
            <p style="font-size:13px; color:var(--text-2);">{{ __('welcome.stat_ledgers_opened') }}</p>
          </div>
          <div>
            <div class="h3 count-up" style="font-family:'Newsreader',serif;" data-prefix="$" data-target="2.1" data-decimals="1" data-suffix="B">$0B</div>
            <p style="font-size:13px; color:var(--text-2);">{{ __('welcome.stat_reconciled_monthly') }}</p>
          </div>
          <div>
            <div class="h3 count-up" style="font-family:'Newsreader',serif;" data-target="4.9" data-decimals="1" data-suffix="/5">0/5</div>
            <p style="font-size:13px; color:var(--text-2);">{{ __('welcome.stat_average_rating') }}</p>
          </div>
        </div>
      </div>

      <div class="hero-visual fade-up">
        <div class="ledger-card" id="tiltCard">
          <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-top:8px;">
            <div>
              <div class="k-label">{{ __('welcome.hero_available_balance') }}</div>
              <div class="k-value">$12,480.32</div>
            </div>
            <span class="chip">&bull;&bull;&bull;&bull; 4471</span>
          </div>
          <div class="k-underline"></div>
          <svg viewBox="0 0 240 60" width="100%" height="60" style="margin-top:6px;">
            <polyline class="sparkline-path" points="0,42 30,38 60,44 90,26 120,30 150,14 180,20 210,8 240,12" fill="none" stroke="var(--wheat)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          <div style="display:flex; gap:10px; margin-top:26px;">
            <span class="chip">{{ __('welcome.hero_up_this_month') }}</span>
            <span class="chip">{{ __('welcome.hero_interest_apy') }}</span>
          </div>
        </div>

        <div class="hero-float-card">
          <div class="hero-float-row">
            <span class="hero-float-icon">
              <svg viewBox="0 0 24 24" fill="none"><path d="M7 17L17 7M17 7H9M17 7V15" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </span>
            <div>
              <div style="font-size:13.5px; font-weight:600;">{{ __('welcome.hero_float_payment_received') }}</div>
              <div class="mono" style="font-size:12px; color:var(--text-3);">from J. Alvarez &middot; +$640.00</div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

{{-- ============ TICKER ============ --}}
<div class="ticker-strip">
  <div class="ticker-track">
    <span>{{ __('welcome.ticker_no_hidden_fees') }}</span>
    <span>{{ __('welcome.ticker_realtime_sync') }}</span>
    <span>{{ __('welcome.ticker_apy_savings') }}</span>
    <span>{{ __('welcome.ticker_free_transfers') }}</span>
    <span>{{ __('welcome.ticker_bank_encryption') }}</span>
    <span>{{ __('welcome.ticker_support') }}</span>
    <span>{{ __('welcome.ticker_no_hidden_fees') }}</span>
    <span>{{ __('welcome.ticker_realtime_sync') }}</span>
    <span>{{ __('welcome.ticker_apy_savings') }}</span>
    <span>{{ __('welcome.ticker_free_transfers') }}</span>
    <span>{{ __('welcome.ticker_bank_encryption') }}</span>
    <span>{{ __('welcome.ticker_support') }}</span>
  </div>
</div>

{{-- ============ ABOUT ============ --}}
<section class="section" id="about">
  <div class="container">
    <div class="section-head">
      <div>
        <div class="eyebrow"><span class="dot"></span> {{ __('welcome.about_eyebrow') }}</div>
        <h2 class="h2">{{ __('welcome.about_h2_line1') }}<br>{{ __('welcome.about_h2_line2') }}</h2>
      </div>
      <p class="lede">{{ __('welcome.about_lede') }}</p>
    </div>

    <div class="grid-3">
      <div class="pillar fade-up" style="animation-delay:0s;">
        <div class="pillar-icon">
          <svg viewBox="0 0 24 24" fill="none"><path d="M4 6h16M4 12h16M4 18h10" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
        </div>
        <h3 class="h3">{{ __('welcome.pillar1_title') }}</h3>
        <p class="lede" style="font-size:15px;">{{ __('welcome.pillar1_desc') }}</p>
      </div>
      <div class="pillar fade-up" style="animation-delay:0.08s;">
        <div class="pillar-icon">
          <svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="8" stroke="currentColor" stroke-width="1.7"/><path d="M12 8v4l3 2" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </div>
        <h3 class="h3">{{ __('welcome.pillar2_title') }}</h3>
        <p class="lede" style="font-size:15px;">{{ __('welcome.pillar2_desc') }}</p>
      </div>
      <div class="pillar fade-up" style="animation-delay:0.16s;">
        <div class="pillar-icon">
          <svg viewBox="0 0 24 24" fill="none"><path d="M12 3l7 3v6c0 4.5-3 7.5-7 9-4-1.5-7-4.5-7-9V6l7-3Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg>
        </div>
        <h3 class="h3">{{ __('welcome.pillar3_title') }}</h3>
        <p class="lede" style="font-size:15px;">{{ __('welcome.pillar3_desc') }}</p>
      </div>
    </div>
  </div>
</section>

{{-- ============ FEATURE BANNER ============ --}}
<section class="section" style="background:var(--paper-2); border-top:1px solid var(--mist); border-bottom:1px solid var(--mist);">
  <div class="container">
    <div class="feature-grid">

      <div class="fade-up">
        <div class="eyebrow"><span class="dot"></span> {{ __('welcome.feature_eyebrow') }}</div>
        <h2 class="h2">{{ __('welcome.feature_h2_line1') }}<br>{{ __('welcome.feature_h2_line2') }}</h2>
        <p class="lede" style="margin-top:14px; max-width:420px;">
          {{ __('welcome.feature_lede') }}
        </p>
        <ul class="feature-list">
          <li>
            <svg viewBox="0 0 24 24" fill="none"><path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            {{ __('welcome.feature_li1') }}
          </li>
          <li>
            <svg viewBox="0 0 24 24" fill="none"><path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            {{ __('welcome.feature_li2') }}
          </li>
          <li>
            <svg viewBox="0 0 24 24" fill="none"><path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            {{ __('welcome.feature_li3') }}
          </li>
        </ul>
        <a href="{{ auth()->check() ? (auth()->user()->isAdmin() ? route('admin.dashboard') : route('dashboard')) : route('register') }}" class="btn btn-outline" style="margin-top:24px;">
          {{ auth()->check() ? (auth()->user()->isAdmin() ? __('welcome.go_to_admin_dashboard') : __('welcome.go_to_dashboard')) : __('welcome.see_how_spend_tracking') }}
          <svg viewBox="0 0 24 24" fill="none"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </a>
      </div>

      <div class="fade-up">
        <div class="spend-mock">
          <div class="k-label" style="color:var(--text-3);">{{ __('welcome.spending_this_month') }}</div>
          <div class="ring-wrap">
            <div class="ring" style="--pct:65;">
              <div class="ring-inner">
                <div class="mono" style="font-size:22px; font-weight:600;">65%</div>
                <div style="font-size:11px; color:var(--text-3);">{{ __('welcome.of_budget') }}</div>
              </div>
            </div>
          </div>
          <div class="spend-cats">
            <div><span class="cat-dot" style="background:var(--sage);"></span> {{ __('welcome.cat_living') }} <span class="mono" style="margin-left:auto; color:var(--text-3);">$1,120</span></div>
            <div><span class="cat-dot" style="background:var(--wheat);"></span> {{ __('welcome.cat_travel') }} <span class="mono" style="margin-left:auto; color:var(--text-3);">$340</span></div>
            <div><span class="cat-dot" style="background:var(--coral);"></span> {{ __('welcome.cat_dining') }} <span class="mono" style="margin-left:auto; color:var(--text-3);">$212</span></div>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

{{-- ============ SERVICES ============ --}}
<section class="section" id="services">
  <div class="container">
    <div class="section-head">
      <div>
        <div class="eyebrow"><span class="dot"></span> {{ __('welcome.services_eyebrow') }}</div>
        <h2 class="h2">{{ __('welcome.services_h2_line1') }}<br>{{ __('welcome.services_h2_line2') }}</h2>
      </div>
      <p class="lede">{{ __('welcome.services_lede') }}</p>
    </div>

    <div class="grid-3">
      @php
        $services = [
          ['icon' => '<path d="M7 17L17 7M17 7H9M17 7V15"/>', 'title' => __('welcome.service1_title'), 'desc' => __('welcome.service1_desc')],
          ['icon' => '<path d="M17 7L7 17M7 17H15M7 17V9"/>', 'title' => __('welcome.service2_title'), 'desc' => __('welcome.service2_desc')],
          ['icon' => '<path d="M6 3h9l3 3v15H6V3Z"/><path d="M9 9h6M9 13h6M9 17h3"/>', 'title' => __('welcome.service3_title'), 'desc' => __('welcome.service3_desc')],
          ['icon' => '<circle cx="12" cy="12" r="8"/><path d="M12 8v8M9 11h6"/>', 'title' => __('welcome.service4_title'), 'desc' => __('welcome.service4_desc')],
          ['icon' => '<rect x="3" y="6" width="18" height="13" rx="2"/><path d="M3 10h18M7 15h4"/>', 'title' => __('welcome.service5_title'), 'desc' => __('welcome.service5_desc')],
          ['icon' => '<rect x="4" y="4" width="6" height="6"/><rect x="14" y="4" width="6" height="6"/><rect x="4" y="14" width="6" height="6"/><path d="M15 15h5v5h-5z"/>', 'title' => __('welcome.service6_title'), 'desc' => __('welcome.service6_desc')],
        ];
      @endphp

      @foreach ($services as $service)
        <div class="service-card fade-up" style="animation-delay:{{ $loop->index * 0.08 }}s;">
          <div class="service-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">{!! $service['icon'] !!}</svg>
          </div>
          <h3 class="h3" style="font-size:19px;">{{ $service['title'] }}</h3>
          <p class="lede" style="font-size:14.5px; margin-top:8px;">{!! $service['desc'] !!}</p>
        </div>
      @endforeach
    </div>
  </div>
</section>

{{-- ============ BENEFITS CAROUSEL ============ --}}
<section class="section" style="background:var(--ink);">
  <div class="container">
    <div class="section-head">
      <div>
        <div class="eyebrow on-dark"><span class="dot"></span> {{ __('welcome.benefits_eyebrow') }}</div>
        <h2 class="h2 on-dark">{{ __('welcome.benefits_h2_line1') }}<br>{{ __('welcome.benefits_h2_line2') }}</h2>
      </div>
      <div class="carousel-nav">
        <button class="carousel-btn" id="benefitPrev" aria-label="Previous">
          <svg viewBox="0 0 24 24" fill="none"><path d="M15 6l-6 6 6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>
        <button class="carousel-btn" id="benefitNext" aria-label="Next">
          <svg viewBox="0 0 24 24" fill="none"><path d="M9 6l6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>
      </div>
    </div>

    <div class="carousel-viewport">
      <div class="carousel-track" id="benefitTrack">
        <div class="benefit-slide">
          <div class="k-label" style="color:var(--wheat);">01</div>
          <h3 class="h3 on-dark" style="margin-top:10px;">{{ __('welcome.benefit1_title') }}</h3>
          <p class="on-dark lede" style="font-size:14.5px;">{{ __('welcome.benefit1_desc') }}</p>
        </div>
        <div class="benefit-slide">
          <div class="k-label" style="color:var(--wheat);">02</div>
          <h3 class="h3 on-dark" style="margin-top:10px;">{{ __('welcome.benefit2_title') }}</h3>
          <p class="on-dark lede" style="font-size:14.5px;">{{ __('welcome.benefit2_desc') }}</p>
        </div>
        <div class="benefit-slide">
          <div class="k-label" style="color:var(--wheat);">03</div>
          <h3 class="h3 on-dark" style="margin-top:10px;">{{ __('welcome.benefit3_title') }}</h3>
          <p class="on-dark lede" style="font-size:14.5px;">{{ __('welcome.benefit3_desc') }}</p>
        </div>
        <div class="benefit-slide">
          <div class="k-label" style="color:var(--wheat);">04</div>
          <h3 class="h3 on-dark" style="margin-top:10px;">{{ __('welcome.benefit4_title') }}</h3>
          <p class="on-dark lede" style="font-size:14.5px;">{{ __('welcome.benefit4_desc') }}</p>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- ============ TRUST STATS ============ --}}
<section class="section">
  <div class="container">
    <div class="grid-3">
      @php
        $stats = [
          ['pct' => 99.9, 'color' => 'var(--sage)', 'title' => __('welcome.stat1_title'), 'desc' => __('welcome.stat1_desc')],
          ['pct' => 94, 'color' => 'var(--wheat)', 'title' => __('welcome.stat2_title'), 'desc' => __('welcome.stat2_desc')],
          ['pct' => 87, 'color' => 'var(--coral)', 'title' => __('welcome.stat3_title'), 'desc' => __('welcome.stat3_desc')],
        ];
      @endphp
      @foreach ($stats as $stat)
        <div class="stat-card fade-up" style="animation-delay:{{ $loop->index * 0.1 }}s;">
          <div class="stat-ring" data-target="{{ $stat['pct'] }}" data-suffix="%">
            <svg viewBox="0 0 120 120">
              <circle class="stat-ring-bg" cx="60" cy="60" r="52"/>
              <circle class="stat-ring-fg" cx="60" cy="60" r="52" style="stroke:{{ $stat['color'] }};"/>
            </svg>
            <div class="stat-ring-value">0%</div>
          </div>
          <h3 class="h3" style="font-size:18px; margin-top:16px;">{{ $stat['title'] }}</h3>
          <p class="lede" style="font-size:14px;">{!! $stat['desc'] !!}</p>
        </div>
      @endforeach
    </div>
  </div>
</section>

{{-- ============ CALCULATORS ============ --}}
<section class="section" id="calculators" style="background:var(--paper-2); border-top:1px solid var(--mist); border-bottom:1px solid var(--mist);">
  <div class="container">
    <div class="section-head">
      <div>
        <div class="eyebrow"><span class="dot"></span> {{ __('welcome.calc_eyebrow') }}</div>
        <h2 class="h2">{{ __('welcome.calc_h2_line1') }}<br>{{ __('welcome.calc_h2_line2') }}</h2>
      </div>
      <p class="lede">{{ __('welcome.calc_lede') }}</p>
    </div>

    <div class="calc-tabs">
      <button class="calc-tab active" data-tab="savings">{{ __('welcome.calc_tab_savings') }}</button>
      <button class="calc-tab" data-tab="budget">{{ __('welcome.calc_tab_budget') }}</button>
      <button class="calc-tab" data-tab="loan">{{ __('welcome.calc_tab_loan') }}</button>
    </div>

    <div class="calc-panel active" id="calc-savings">
      <div class="calc-grid">
        <div class="calc-inputs">
          <label>{{ __('welcome.calc_starting_balance') }} <span class="mono calc-out" id="sv-start-out">$1,000</span></label>
          <input type="range" id="sv-start" min="0" max="20000" step="100" value="1000">

          <label>{{ __('welcome.calc_monthly_deposit') }} <span class="mono calc-out" id="sv-deposit-out">$200</span></label>
          <input type="range" id="sv-deposit" min="0" max="2000" step="10" value="200">

          <label>{{ __('welcome.calc_years') }} <span class="mono calc-out" id="sv-years-out">5</span></label>
          <input type="range" id="sv-years" min="1" max="30" step="1" value="5">

          <label>{{ __('welcome.calc_annual_rate') }} <span class="mono calc-out" id="sv-rate-out">3.8%</span></label>
          <input type="range" id="sv-rate" min="0" max="8" step="0.1" value="3.8">
        </div>
        <div class="ledger-card calc-result">
          <div class="k-label">{{ __('welcome.calc_balance_after') }} <span id="sv-years-label">5</span> {{ __('welcome.calc_years_lc') }}</div>
          <div class="k-value" id="sv-total">$0</div>
          <div class="k-underline"></div>
          <div class="calc-breakdown">
            <div><span>{{ __('welcome.calc_youll_deposit') }}</span><span class="mono" id="sv-contributed">$0</span></div>
            <div><span>{{ __('welcome.calc_interest_earned') }}</span><span class="mono" id="sv-growth">$0</span></div>
          </div>
          <div class="calc-bars" id="sv-bars"></div>
        </div>
      </div>
    </div>

    <div class="calc-panel" id="calc-budget">
      <div class="calc-grid">
        <div class="calc-inputs">
          <label>{{ __('welcome.calc_monthly_income') }} <span class="mono calc-out" id="bg-income-out">$5,000</span></label>
          <input type="range" id="bg-income" min="1000" max="20000" step="100" value="5000">

          <label>{{ __('welcome.cat_living') }} <span class="mono calc-out" id="bg-living-out">45%</span></label>
          <input type="range" id="bg-living" min="0" max="100" step="1" value="45">

          <label>{{ __('welcome.cat_travel') }} <span class="mono calc-out" id="bg-travel-out">10%</span></label>
          <input type="range" id="bg-travel" min="0" max="100" step="1" value="10">

          <label>{{ __('welcome.cat_dining') }} <span class="mono calc-out" id="bg-dining-out">15%</span></label>
          <input type="range" id="bg-dining" min="0" max="100" step="1" value="15">
        </div>
        <div class="ledger-card calc-result">
          <div class="k-label">{{ __('welcome.calc_left_to_save') }}</div>
          <div class="k-value" id="bg-leftover">$0</div>
          <div class="k-underline"></div>
          <div class="calc-breakdown">
            <div><span class="cat-dot" style="background:var(--sage);"></span><span>{{ __('welcome.cat_living') }}</span><span class="mono" id="bg-living-amt" style="margin-left:auto;">$0</span></div>
            <div><span class="cat-dot" style="background:var(--wheat);"></span><span>{{ __('welcome.cat_travel') }}</span><span class="mono" id="bg-travel-amt" style="margin-left:auto;">$0</span></div>
            <div><span class="cat-dot" style="background:var(--coral);"></span><span>{{ __('welcome.cat_dining') }}</span><span class="mono" id="bg-dining-amt" style="margin-left:auto;">$0</span></div>
          </div>
        </div>
      </div>
    </div>

    <div class="calc-panel" id="calc-loan">
      <div class="calc-grid">
        <div class="calc-inputs">
          <label>{{ __('welcome.calc_loan_amount') }} <span class="mono calc-out" id="ln-amount-out">$20,000</span></label>
          <input type="range" id="ln-amount" min="1000" max="100000" step="500" value="20000">

          <label>{{ __('welcome.calc_interest_rate_apr') }} <span class="mono calc-out" id="ln-rate-out">6.5%</span></label>
          <input type="range" id="ln-rate" min="0.5" max="20" step="0.1" value="6.5">

          <label>{{ __('welcome.calc_term') }} <span class="mono calc-out" id="ln-term-out">5 years</span></label>
          <input type="range" id="ln-term" min="1" max="30" step="1" value="5">
        </div>
        <div class="ledger-card calc-result">
          <div class="k-label">{{ __('welcome.calc_estimated_monthly_payment') }}</div>
          <div class="k-value" id="ln-payment">$0</div>
          <div class="k-underline"></div>
          <div class="calc-breakdown">
            <div><span>{{ __('welcome.calc_total_paid') }}</span><span class="mono" id="ln-total">$0</span></div>
            <div><span>{{ __('welcome.calc_total_interest') }}</span><span class="mono" id="ln-interest">$0</span></div>
          </div>
        </div>
      </div>
    </div>

  </div>
</section>

{{-- ============ TESTIMONIALS ============ --}}
<section class="section">
  <div class="container">
    <div class="section-head">
      <div>
        <div class="eyebrow"><span class="dot"></span> {{ __('welcome.testimonials_eyebrow') }}</div>
        <h2 class="h2">{{ __('welcome.testimonials_h2_line1') }}<br>{{ __('welcome.testimonials_h2_line2') }}</h2>
      </div>
    </div>

    <div class="testimonial-grid">
      @php
        $col1 = [
          ['name' => 'Maria Ostrowski', 'role' => __('welcome.testimonial1_role'), 'quote' => __('welcome.testimonial1_quote')],
          ['name' => 'Devon Marsh', 'role' => __('welcome.testimonial2_role'), 'quote' => __('welcome.testimonial2_quote')],
          ['name' => 'Priya Chandran', 'role' => __('welcome.testimonial3_role'), 'quote' => __('welcome.testimonial3_quote')],
        ];
        $col2 = [
          ['name' => 'Tomás Reyes', 'role' => __('welcome.testimonial4_role'), 'quote' => __('welcome.testimonial4_quote')],
          ['name' => 'Aisha Bello', 'role' => __('welcome.testimonial5_role'), 'quote' => __('welcome.testimonial5_quote')],
          ['name' => 'Sam Whitfield', 'role' => __('welcome.testimonial6_role'), 'quote' => __('welcome.testimonial6_quote')],
        ];
      @endphp

      <div class="testimonial-col">
        <div class="testimonial-track">
          @foreach (array_merge($col1, $col1) as $t)
            <div class="testimonial-card">
              <p class="lede" style="font-size:14.5px;">&ldquo;{!! $t['quote'] !!}&rdquo;</p>
              <div class="testimonial-who">
                <span class="testimonial-avatar">{{ collect(explode(' ', $t['name']))->map(fn($p) => $p[0])->implode('') }}</span>
                <div>
                  <div style="font-weight:600; font-size:14px;">{{ $t['name'] }}</div>
                  <div style="font-size:12.5px; color:var(--text-3);">{{ $t['role'] }}</div>
                </div>
              </div>
            </div>
          @endforeach
        </div>
      </div>

      <div class="testimonial-col">
        <div class="testimonial-track testimonial-track-reverse">
          @foreach (array_merge($col2, $col2) as $t)
            <div class="testimonial-card">
              <p class="lede" style="font-size:14.5px;">&ldquo;{!! $t['quote'] !!}&rdquo;</p>
              <div class="testimonial-who">
                <span class="testimonial-avatar">{{ collect(explode(' ', $t['name']))->map(fn($p) => $p[0])->implode('') }}</span>
                <div>
                  <div style="font-weight:600; font-size:14px;">{{ $t['name'] }}</div>
                  <div style="font-size:12.5px; color:var(--text-3);">{{ $t['role'] }}</div>
                </div>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>
</section>

{{-- ============ FAQ ============ --}}
<section class="section" id="faq" style="background:var(--paper-2); border-top:1px solid var(--mist); border-bottom:1px solid var(--mist);">
  <div class="container" style="max-width:820px;">
    <div class="section-head" style="margin-bottom:32px;">
      <div>
        <div class="eyebrow"><span class="dot"></span> {{ __('welcome.faq_eyebrow') }}</div>
        <h2 class="h2">{{ __('welcome.faq_h2_line1') }}<br>{{ __('welcome.faq_h2_line2') }}</h2>
      </div>
    </div>

    <div class="faq-list">
      @php
        $faqs = [
          ['q' => __('welcome.faq1_q'), 'a' => __('welcome.faq1_a')],
          ['q' => __('welcome.faq2_q'), 'a' => __('welcome.faq2_a')],
          ['q' => __('welcome.faq3_q'), 'a' => __('welcome.faq3_a')],
          ['q' => __('welcome.faq4_q'), 'a' => __('welcome.faq4_a')],
          ['q' => __('welcome.faq5_q'), 'a' => __('welcome.faq5_a')],
        ];
      @endphp

      @foreach ($faqs as $i => $faq)
        <div class="faq-item {{ $i === 0 ? 'open' : '' }}">
          <button class="faq-question" type="button">
            {{ $faq['q'] }}
            <svg viewBox="0 0 24 24" fill="none"><path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </button>
          <div class="faq-answer">
            <p class="lede" style="font-size:14.5px; padding-bottom:20px;">{{ $faq['a'] }}</p>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</section>

{{-- ============ INTEGRATIONS / LINK ACCOUNTS ============ --}}
<section class="section">
  <div class="container">
    <div class="ledger-card integrations-banner">
      <div class="integrations-copy">
        <div class="eyebrow on-dark"><span class="dot"></span> {{ __('welcome.integrations_eyebrow') }}</div>
        <h2 class="h2 on-dark">{{ __('welcome.integrations_h2_line1') }}<br>{{ __('welcome.integrations_h2_line2') }}</h2>
        <p class="on-dark lede" style="font-size:15px; margin-top:12px; max-width:440px;">
          {{ __('welcome.integrations_lede') }}
        </p>
        <a href="{{ auth()->check() ? route('link-account') : route('register') }}" class="btn btn-wheat" style="margin-top:22px;">
          {{ __('welcome.link_an_account') }}
          <svg viewBox="0 0 24 24" fill="none"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </a>
      </div>
      <div class="integrations-chips">
        <span class="chip">Chase</span>
        <span class="chip">Bank of America</span>
        <span class="chip">Wells Fargo</span>
        <span class="chip">Citi</span>
        <span class="chip">Capital One</span>
        <span class="chip">Ally</span>
        <span class="chip">US Bank</span>
        <span class="chip">Discover</span>
        <span class="chip">American Express</span>
        <span class="chip">{{ __('welcome.plus_more_banks') }}</span>
      </div>
    </div>
  </div>
</section>

{{-- ============ BLOG ============ --}}
<section class="section" id="blog">
  <div class="container">
    <div class="section-head">
      <div>
        <div class="eyebrow"><span class="dot"></span> {{ __('welcome.blog_eyebrow') }}</div>
        <h2 class="h2">{{ __('welcome.blog_h2_line1') }}<br>{{ __('welcome.blog_h2_line2') }}</h2>
      </div>
      <a href="#" class="btn btn-outline">
        {{ __('welcome.read_all_posts') }}
        <svg viewBox="0 0 24 24" fill="none"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/></svg>
      </a>
    </div>

    <div class="grid-3">
      <a href="#" class="blog-card fade-up" style="animation-delay:0s;">
        <div class="blog-card-head" style="background:var(--sage-light);">
          <svg viewBox="0 0 24 24" fill="none" stroke="var(--sage)" stroke-width="1.6"><circle cx="12" cy="12" r="8"/><path d="M12 8v4l3 2"/></svg>
        </div>
        <div class="blog-card-body">
          <div class="mono" style="font-size:12px; color:var(--text-3);">{{ __('welcome.blog1_meta') }}</div>
          <h3 class="h3" style="font-size:19px; margin-top:8px;">{{ __('welcome.blog1_title') }}</h3>
          <p class="lede" style="font-size:14px; margin-top:8px;">{{ __('welcome.blog1_desc') }}</p>
        </div>
      </a>
      <a href="#" class="blog-card fade-up" style="animation-delay:0.08s;">
        <div class="blog-card-head" style="background:var(--wheat-light);">
          <svg viewBox="0 0 24 24" fill="none" stroke="#a9822f" stroke-width="1.6"><path d="M12 3v18M5 8l7-5 7 5M5 16l7 5 7-5"/></svg>
        </div>
        <div class="blog-card-body">
          <div class="mono" style="font-size:12px; color:var(--text-3);">{{ __('welcome.blog2_meta') }}</div>
          <h3 class="h3" style="font-size:19px; margin-top:8px;">{{ __('welcome.blog2_title') }}</h3>
          <p class="lede" style="font-size:14px; margin-top:8px;">{{ __('welcome.blog2_desc') }}</p>
        </div>
      </a>
      <a href="#" class="blog-card fade-up" style="animation-delay:0.16s;">
        <div class="blog-card-head" style="background:var(--coral-light);">
          <svg viewBox="0 0 24 24" fill="none" stroke="var(--coral)" stroke-width="1.6"><path d="M12 3l7 3v6c0 4.5-3 7.5-7 9-4-1.5-7-4.5-7-9V6l7-3Z"/></svg>
        </div>
        <div class="blog-card-body">
          <div class="mono" style="font-size:12px; color:var(--text-3);">{{ __('welcome.blog3_meta') }}</div>
          <h3 class="h3" style="font-size:19px; margin-top:8px;">{{ __('welcome.blog3_title') }}</h3>
          <p class="lede" style="font-size:14px; margin-top:8px;">{{ __('welcome.blog3_desc') }}</p>
        </div>
      </a>
    </div>
  </div>
</section>

@endsection

@push('styles')
<style>
  /* ---------- Hero ---------- */
  .hero{padding:64px 0 40px; position:relative; overflow:hidden;}
  @media (max-width:899px){ .hero{padding:36px 0 24px;} }
  .hero-grid{display:grid; grid-template-columns:1.05fr 0.95fr; gap:56px; align-items:center; position:relative; z-index:1;}
  @media (max-width:960px){ .hero-grid{grid-template-columns:1fr; gap:44px;} }

  .hero-blob{
    position:absolute; border-radius:50%; filter:blur(70px); opacity:.35; pointer-events:none; z-index:0;
    animation:blobFloat 14s ease-in-out infinite;
  }
  .hero-blob-1{width:380px; height:380px; background:var(--sage); top:-140px; right:-60px;}
  .hero-blob-2{width:300px; height:300px; background:var(--wheat); bottom:-120px; left:-80px; animation-delay:-6s;}
  @keyframes blobFloat{
    0%, 100%{transform:translate(0,0) scale(1);}
    50%{transform:translate(-24px, 26px) scale(1.08);}
  }
  @media (prefers-reduced-motion:reduce){ .hero-blob{animation:none;} }

  #tiltCard{transition:transform .15s ease-out; transform-style:preserve-3d; will-change:transform;}
  .sparkline-path{stroke-dasharray:400; stroke-dashoffset:400; animation:drawLine 1.6s ease-out .3s forwards;}
  @keyframes drawLine{ to{stroke-dashoffset:0;} }
  @media (prefers-reduced-motion:reduce){ .sparkline-path{animation:none; stroke-dashoffset:0;} }

  .hero-form{display:flex; gap:10px; margin-top:26px; max-width:420px;}
  .hero-form input{
    flex:1; padding:13px 18px; border-radius:100px; border:1px solid var(--mist);
    background:var(--paper-2); font-family:'Inter',sans-serif; font-size:14px; color:var(--text-1);
  }
  .hero-form input:focus{outline:none; border-color:var(--sage);}
  @media (max-width:480px){ .hero-form{flex-direction:column;} }

  .hero-stats{display:flex; gap:34px; margin-top:38px;}
  @media (max-width:480px){ .hero-stats{gap:22px; flex-wrap:wrap;} }

  .hero-visual{position:relative;}
  .hero-float-card{
    background:var(--paper-2); border:1px solid var(--mist); border-radius:16px;
    padding:16px 18px; box-shadow:0 18px 40px rgba(16,32,47,0.12);
    max-width:280px; margin:-30px 0 0 auto; position:relative; z-index:2;
  }
  @media (max-width:600px){ .hero-float-card{margin:-24px auto 0;} }
  .hero-float-row{display:flex; align-items:center; gap:12px;}
  .hero-float-icon{
    width:36px; height:36px; border-radius:50%; background:var(--sage-light); color:var(--sage);
    display:flex; align-items:center; justify-content:center; flex-shrink:0;
  }
  .hero-float-icon svg{width:16px; height:16px;}

  /* ---------- About pillars ---------- */
  .pillar{
    padding:28px; border:1px solid var(--mist); border-radius:18px; background:var(--paper-2);
    transition:transform .2s ease, box-shadow .2s ease, border-color .2s ease;
  }
  .pillar:hover{transform:translateY(-4px); box-shadow:0 16px 32px rgba(16,32,47,0.08); border-color:var(--sage);}
  .pillar:hover .pillar-icon{background:var(--sage); color:var(--paper);}
  .pillar-icon{transition:background .2s ease, color .2s ease;}
  .pillar-icon{
    width:44px; height:44px; border-radius:12px; background:var(--sage-light); color:var(--sage);
    display:flex; align-items:center; justify-content:center; margin-bottom:18px;
  }
  .pillar-icon svg{width:20px; height:20px;}
  .pillar h3{margin-bottom:4px;}

  /* ---------- Feature banner ---------- */
  .feature-grid{display:grid; grid-template-columns:1fr 1fr; gap:56px; align-items:center;}
  @media (max-width:900px){ .feature-grid{grid-template-columns:1fr;} }
  .feature-list{margin-top:20px; display:flex; flex-direction:column; gap:12px;}
  .feature-list li{display:flex; align-items:center; gap:10px; font-size:14.5px; color:var(--text-2);}
  .feature-list svg{width:16px; height:16px; color:var(--sage); flex-shrink:0;}

  .spend-mock{
    background:var(--paper); border:1px solid var(--mist); border-radius:22px; padding:32px;
    max-width:340px; margin:0 auto;
  }
  .ring-wrap{display:flex; justify-content:center; margin:22px 0;}
  .ring{
    width:150px; height:150px; border-radius:50%; position:relative;
    background:conic-gradient(var(--sage) calc(var(--pct) * 1%), var(--mist) 0);
    display:flex; align-items:center; justify-content:center;
  }
  .ring-inner{
    width:112px; height:112px; border-radius:50%; background:var(--paper);
    display:flex; flex-direction:column; align-items:center; justify-content:center;
  }
  .spend-cats{display:flex; flex-direction:column; gap:10px;}
  .spend-cats div{display:flex; align-items:center; gap:8px; font-size:13.5px; color:var(--text-2);}
  .cat-dot{width:8px; height:8px; border-radius:50%; flex-shrink:0;}

  /* ---------- Services ---------- */
  .service-card{padding:30px; border:1px solid var(--mist); border-radius:18px; background:var(--paper-2); transition:border-color .15s ease, transform .15s ease;}
  .service-card:hover{border-color:var(--sage); transform:translateY(-3px);}
  .service-icon{
    width:44px; height:44px; border-radius:12px; background:var(--wheat-light); color:#a9822f;
    display:flex; align-items:center; justify-content:center; margin-bottom:18px;
  }
  .service-icon svg{width:20px; height:20px;}

  /* ---------- Benefits carousel ---------- */
  .carousel-nav{display:flex; gap:8px;}
  .carousel-btn{
    width:40px; height:40px; border-radius:50%; border:1px solid rgba(255,255,255,0.2);
    background:transparent; color:var(--paper); display:flex; align-items:center; justify-content:center;
    cursor:pointer; transition:background .15s ease;
  }
  .carousel-btn:hover{background:rgba(255,255,255,0.08);}
  .carousel-btn svg{width:16px; height:16px;}
  .carousel-viewport{overflow:hidden;}
  .carousel-track{display:flex; gap:24px; transition:transform .4s ease;}
  .benefit-slide{
    flex:0 0 calc(25% - 18px); min-width:240px;
    background:var(--ink-2); border:1px solid rgba(255,255,255,0.1); border-radius:18px; padding:26px;
  }
  @media (max-width:900px){ .benefit-slide{flex:0 0 calc(50% - 12px);} }
  @media (max-width:600px){ .benefit-slide{flex:0 0 85%;} }

  /* ---------- Trust stats ---------- */
  .stat-card{text-align:center; padding:8px;}
  .stat-ring{width:120px; height:120px; margin:0 auto; position:relative;}
  .stat-ring svg{width:100%; height:100%; transform:rotate(-90deg);}
  .stat-ring-bg{fill:none; stroke:var(--mist); stroke-width:8;}
  .stat-ring-fg{
    fill:none; stroke-width:8; stroke-linecap:round;
    stroke-dasharray:327; stroke-dashoffset:327; transition:stroke-dashoffset 1.2s ease;
  }
  .stat-ring-value{
    position:absolute; inset:0; display:flex; align-items:center; justify-content:center;
    font-family:'Newsreader',serif; font-size:24px; font-weight:600;
  }

  /* ---------- Calculators ---------- */
  .calc-tabs{display:flex; gap:8px; margin-bottom:36px; flex-wrap:wrap;}
  .calc-tab{
    padding:11px 20px; border-radius:100px; border:1px solid var(--mist); background:var(--paper);
    font-size:14px; font-weight:600; color:var(--text-2); cursor:pointer; transition:all .15s ease;
  }
  .calc-tab.active{background:var(--ink); color:var(--paper); border-color:var(--ink);}
  .calc-panel{display:none;}
  .calc-panel.active{display:block; animation:calcFadeIn .35s ease;}
  @keyframes calcFadeIn{from{opacity:0; transform:translateY(8px);} to{opacity:1; transform:translateY(0);}}
  @media (prefers-reduced-motion:reduce){ .calc-panel.active{animation:none;} }
  .calc-grid{display:grid; grid-template-columns:1fr 1fr; gap:48px; align-items:center;}
  @media (max-width:860px){ .calc-grid{grid-template-columns:1fr; gap:32px;} }
  .calc-inputs{display:flex; flex-direction:column; gap:18px;}
  .calc-inputs label{display:flex; justify-content:space-between; font-size:13.5px; font-weight:600; color:var(--text-2);}
  .calc-out{color:var(--sage); font-weight:600;}
  .calc-inputs input[type=range]{
    width:100%; accent-color:var(--sage); height:4px; cursor:pointer;
  }
  .calc-breakdown{display:flex; flex-direction:column; gap:10px; margin-top:6px; font-size:14px;}
  .calc-breakdown div{display:flex; align-items:center; gap:8px; color:#B9C8D2;}
  .calc-breakdown span:first-of-type{color:#B9C8D2;}
  .calc-breakdown .mono{margin-left:auto; color:var(--paper);}
  .calc-bars{display:flex; align-items:flex-end; gap:6px; height:70px; margin-top:24px;}
  .calc-bars div{flex:1; background:var(--wheat); border-radius:3px 3px 0 0; min-height:3px;}

  /* ---------- Testimonials ---------- */
  .testimonial-grid{display:grid; grid-template-columns:1fr 1fr; gap:24px; max-height:560px; overflow:hidden; position:relative;}
  @media (max-width:768px){ .testimonial-grid{grid-template-columns:1fr; max-height:420px;} }
  .testimonial-grid::before, .testimonial-grid::after{
    content:''; position:absolute; left:0; right:0; height:60px; z-index:2; pointer-events:none;
  }
  .testimonial-grid::before{top:0; background:linear-gradient(var(--paper), transparent);}
  .testimonial-grid::after{bottom:0; background:linear-gradient(transparent, var(--paper));}
  .testimonial-col{overflow:hidden;}
  .testimonial-track{display:flex; flex-direction:column; gap:20px; animation:scrollUp 34s linear infinite;}
  .testimonial-track-reverse{animation-name:scrollDown;}
  .testimonial-col:hover .testimonial-track{animation-play-state:paused;}
  @keyframes scrollUp{ from{transform:translateY(0);} to{transform:translateY(-50%);} }
  @keyframes scrollDown{ from{transform:translateY(-50%);} to{transform:translateY(0);} }
  @media (prefers-reduced-motion:reduce){ .testimonial-track{animation:none;} }
  .testimonial-card{
    background:var(--paper-2); border:1px solid var(--mist); border-radius:16px; padding:22px;
    transition:transform .2s ease, box-shadow .2s ease, border-color .2s ease;
  }
  .testimonial-card:hover{transform:translateY(-3px); box-shadow:0 14px 30px rgba(16,32,47,0.08); border-color:var(--sage);}
  .testimonial-who{display:flex; align-items:center; gap:10px; margin-top:16px;}
  .testimonial-avatar{
    width:34px; height:34px; border-radius:50%; background:var(--ink); color:var(--wheat);
    font-family:'Newsreader',serif; font-weight:600; font-size:13px;
    display:flex; align-items:center; justify-content:center; flex-shrink:0;
  }

  /* ---------- FAQ ---------- */
  .faq-item{border-bottom:1px solid var(--mist);}
  .faq-question{
    width:100%; display:flex; align-items:center; justify-content:space-between; gap:16px;
    background:none; border:none; padding:20px 0; text-align:left; cursor:pointer;
    font-family:'Newsreader',serif; font-size:17px; color:var(--text-1);
  }
  .faq-question svg{width:16px; height:16px; flex-shrink:0; transition:transform .2s ease; color:var(--text-3);}
  .faq-item.open .faq-question svg{transform:rotate(180deg);}
  /* max-height is set inline by the FAQ accordion script below, measured
     from the answer's actual rendered height — not a fixed pixel value.
     A hardcoded cap here (the old rule was max-height:200px) silently
     clips any answer whose translated text needs more room than English
     did, so it must stay computed rather than a fixed number. */
  .faq-answer{max-height:0; overflow:hidden; transition:max-height .25s ease;}

  /* ---------- Integrations ---------- */
  .integrations-banner{display:flex; flex-wrap:wrap; gap:40px; justify-content:space-between; align-items:center; padding:48px;}
  .integrations-chips{display:flex; flex-wrap:wrap; gap:10px; max-width:420px;}

  /* ---------- Blog ---------- */
  .blog-card{
    display:block; border:1px solid var(--mist); border-radius:18px; overflow:hidden;
    background:var(--paper-2); text-decoration:none; transition:transform .15s ease, border-color .15s ease;
  }
  .blog-card:hover{transform:translateY(-3px); border-color:var(--sage);}
  .blog-card-head{height:120px; display:flex; align-items:center; justify-content:center;}
  .blog-card-head svg{width:36px; height:36px;}
  .blog-card-body{padding:22px;}
</style>
@endpush

@push('scripts')
<script>
  // ---------- Fade-up on scroll ----------
  (function(){
    var items = document.querySelectorAll('.fade-up');
    if(!('IntersectionObserver' in window) || !items.length){ return; }
    var io = new IntersectionObserver(function(entries){
      entries.forEach(function(entry){
        if(entry.isIntersecting){
          entry.target.style.animationPlayState = 'running';
          io.unobserve(entry.target);
        }
      });
    }, {threshold:0.15});
    items.forEach(function(el){
      el.style.animationPlayState = 'paused';
      io.observe(el);
    });
  })();

  // ---------- Hero card 3D tilt ----------
  (function(){
    var card = document.getElementById('tiltCard');
    if(!card || window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
    if(window.matchMedia('(hover: none)').matches) return; // skip on touch devices

    var bounds;
    card.addEventListener('mouseenter', function(){
      bounds = card.getBoundingClientRect();
    });
    card.addEventListener('mousemove', function(e){
      if(!bounds) bounds = card.getBoundingClientRect();
      var px = (e.clientX - bounds.left) / bounds.width;
      var py = (e.clientY - bounds.top) / bounds.height;
      var rotateY = (px - 0.5) * 14;
      var rotateX = (0.5 - py) * 14;
      card.style.transform = 'perspective(800px) rotateX(' + rotateX + 'deg) rotateY(' + rotateY + 'deg) scale3d(1.02,1.02,1.02)';
    });
    card.addEventListener('mouseleave', function(){
      card.style.transform = 'perspective(800px) rotateX(0deg) rotateY(0deg) scale3d(1,1,1)';
    });
  })();

  // ---------- Hero stat count-up ----------
  (function(){
    var items = document.querySelectorAll('.count-up');
    if(!items.length) return;

    function animate(el){
      var target = parseFloat(el.getAttribute('data-target'));
      var decimals = parseInt(el.getAttribute('data-decimals') || '0', 10);
      var prefix = el.getAttribute('data-prefix') || '';
      var suffix = el.getAttribute('data-suffix') || '';
      var start = null, duration = 1300;

      function step(ts){
        if(!start) start = ts;
        var progress = Math.min((ts - start) / duration, 1);
        var eased = 1 - Math.pow(1 - progress, 3);
        var val = (target * eased).toFixed(decimals);
        el.textContent = prefix + val + suffix;
        if(progress < 1) requestAnimationFrame(step);
      }
      requestAnimationFrame(step);
    }

    if('IntersectionObserver' in window){
      var io2 = new IntersectionObserver(function(entries){
        entries.forEach(function(entry){
          if(entry.isIntersecting){
            animate(entry.target);
            io2.unobserve(entry.target);
          }
        });
      }, {threshold:0.4});
      items.forEach(function(el){ io2.observe(el); });
    } else {
      items.forEach(animate);
    }
  })();

  // ---------- Benefits carousel ----------
  (function(){
    var track = document.getElementById('benefitTrack');
    var prev = document.getElementById('benefitPrev');
    var next = document.getElementById('benefitNext');
    if(!track || !prev || !next) return;
    var index = 0;

    function slidesPerView(){
      var w = window.innerWidth;
      if(w <= 600) return 1;
      if(w <= 900) return 2;
      return 4;
    }

    function update(){
      var total = track.children.length;
      var perView = slidesPerView();
      var max = Math.max(0, total - perView);
      if(index > max) index = max;
      if(index < 0) index = 0;
      var pct = (100 / total) * index;
      track.style.transform = 'translateX(-' + pct + '%)';
    }

    next.addEventListener('click', function(){
      var total = track.children.length;
      var perView = slidesPerView();
      if(index < total - perView) index++;
      update();
    });
    prev.addEventListener('click', function(){
      if(index > 0) index--;
      update();
    });
    window.addEventListener('resize', update);
    update();
  })();

  // ---------- Trust stat rings ----------
  (function(){
    var rings = document.querySelectorAll('.stat-ring');
    if(!rings.length) return;
    var CIRC = 2 * Math.PI * 52;

    function animate(ring){
      var target = parseFloat(ring.getAttribute('data-target'));
      var suffix = ring.getAttribute('data-suffix') || '';
      var fg = ring.querySelector('.stat-ring-fg');
      var label = ring.querySelector('.stat-ring-value');
      var offset = CIRC - (target / 100) * CIRC;
      fg.style.strokeDasharray = CIRC;
      fg.style.strokeDashoffset = CIRC;
      requestAnimationFrame(function(){ fg.style.strokeDashoffset = offset; });

      var start = null, duration = 1200;
      function step(ts){
        if(!start) start = ts;
        var progress = Math.min((ts - start) / duration, 1);
        var val = (target * progress).toFixed(target % 1 !== 0 ? 1 : 0);
        label.textContent = val + suffix;
        if(progress < 1) requestAnimationFrame(step);
      }
      requestAnimationFrame(step);
    }

    if('IntersectionObserver' in window){
      var io = new IntersectionObserver(function(entries){
        entries.forEach(function(entry){
          if(entry.isIntersecting){
            animate(entry.target);
            io.unobserve(entry.target);
          }
        });
      }, {threshold:0.4});
      rings.forEach(function(r){ io.observe(r); });
    } else {
      rings.forEach(animate);
    }
  })();

  // ---------- Calculator tabs ----------
  (function(){
    var tabs = document.querySelectorAll('.calc-tab');
    var panels = document.querySelectorAll('.calc-panel');
    tabs.forEach(function(tab){
      tab.addEventListener('click', function(){
        tabs.forEach(function(t){ t.classList.remove('active'); });
        panels.forEach(function(p){ p.classList.remove('active'); });
        tab.classList.add('active');
        document.getElementById('calc-' + tab.getAttribute('data-tab')).classList.add('active');
      });
    });
  })();

  function fmt(n){
    return '$' + Math.round(n).toLocaleString('en-US');
  }

  // ---------- Savings growth calculator ----------
  (function(){
    var start = document.getElementById('sv-start');
    var deposit = document.getElementById('sv-deposit');
    var years = document.getElementById('sv-years');
    var rate = document.getElementById('sv-rate');
    if(!start) return;

    function calc(){
      var s = parseFloat(start.value);
      var d = parseFloat(deposit.value);
      var y = parseInt(years.value, 10);
      var r = parseFloat(rate.value) / 100;
      var months = y * 12;
      var monthlyRate = r / 12;

      document.getElementById('sv-start-out').textContent = fmt(s);
      document.getElementById('sv-deposit-out').textContent = fmt(d);
      document.getElementById('sv-years-out').textContent = y;
      document.getElementById('sv-years-label').textContent = y;
      document.getElementById('sv-rate-out').textContent = rate.value + '%';

      var balance = s;
      var bars = [];
      for(var m = 1; m <= months; m++){
        balance = balance * (1 + monthlyRate) + d;
        if(m % 12 === 0) bars.push(balance);
      }
      if(bars.length === 0) bars.push(balance);

      var contributed = s + d * months;
      var growth = balance - contributed;

      document.getElementById('sv-total').textContent = fmt(balance);
      document.getElementById('sv-contributed').textContent = fmt(contributed);
      document.getElementById('sv-growth').textContent = fmt(growth);

      var barsEl = document.getElementById('sv-bars');
      var maxBar = Math.max.apply(null, bars);
      barsEl.innerHTML = '';
      bars.forEach(function(v){
        var bar = document.createElement('div');
        bar.style.height = Math.max(4, (v / maxBar) * 100) + '%';
        barsEl.appendChild(bar);
      });
    }
    [start, deposit, years, rate].forEach(function(el){ el.addEventListener('input', calc); });
    calc();
  })();

  // ---------- Monthly budget calculator ----------
  (function(){
    var income = document.getElementById('bg-income');
    var living = document.getElementById('bg-living');
    var travel = document.getElementById('bg-travel');
    var dining = document.getElementById('bg-dining');
    if(!income) return;

    function calc(){
      var inc = parseFloat(income.value);
      var l = parseFloat(living.value);
      var t = parseFloat(travel.value);
      var d = parseFloat(dining.value);

      document.getElementById('bg-income-out').textContent = fmt(inc);
      document.getElementById('bg-living-out').textContent = l + '%';
      document.getElementById('bg-travel-out').textContent = t + '%';
      document.getElementById('bg-dining-out').textContent = d + '%';

      var livingAmt = inc * (l / 100);
      var travelAmt = inc * (t / 100);
      var diningAmt = inc * (d / 100);
      var leftover = inc - livingAmt - travelAmt - diningAmt;

      document.getElementById('bg-living-amt').textContent = fmt(livingAmt);
      document.getElementById('bg-travel-amt').textContent = fmt(travelAmt);
      document.getElementById('bg-dining-amt').textContent = fmt(diningAmt);
      document.getElementById('bg-leftover').textContent = fmt(Math.max(0, leftover));
    }
    [income, living, travel, dining].forEach(function(el){ el.addEventListener('input', calc); });
    calc();
  })();

  // ---------- Loan payoff calculator ----------
  (function(){
    var amount = document.getElementById('ln-amount');
    var rate = document.getElementById('ln-rate');
    var term = document.getElementById('ln-term');
    if(!amount) return;

    function calc(){
      var p = parseFloat(amount.value);
      var r = parseFloat(rate.value) / 100 / 12;
      var n = parseInt(term.value, 10) * 12;

      document.getElementById('ln-amount-out').textContent = fmt(p);
      document.getElementById('ln-rate-out').textContent = rate.value + '%';
      document.getElementById('ln-term-out').textContent = term.value + (term.value == 1 ? ' year' : ' years');

      var payment = r === 0 ? p / n : (p * r) / (1 - Math.pow(1 + r, -n));
      var total = payment * n;
      var interest = total - p;

      document.getElementById('ln-payment').textContent = fmt(payment);
      document.getElementById('ln-total').textContent = fmt(total);
      document.getElementById('ln-interest').textContent = fmt(interest);
    }
    [amount, rate, term].forEach(function(el){ el.addEventListener('input', calc); });
    calc();
  })();

  // ---------- FAQ accordion ----------
  // max-height is measured from each answer's real rendered height (via
  // scrollHeight) instead of a fixed pixel cap, so a longer translation
  // never gets its bottom silently clipped by .faq-answer's overflow:hidden.
  (function(){
    var items = document.querySelectorAll('.faq-item');

    function openItem(item){
      var answer = item.querySelector('.faq-answer');
      item.classList.add('open');
      answer.style.maxHeight = answer.scrollHeight + 'px';
    }

    function closeItem(item){
      item.classList.remove('open');
      item.querySelector('.faq-answer').style.maxHeight = '';
    }

    items.forEach(function(item){
      var btn = item.querySelector('.faq-question');

      // The first FAQ starts already open (server-rendered with the
      // "open" class) — measure it on load too, not just on click.
      if(item.classList.contains('open')) openItem(item);

      btn.addEventListener('click', function(){
        var wasOpen = item.classList.contains('open');
        items.forEach(closeItem);
        if(!wasOpen) openItem(item);
      });
    });

    // Longer translated text can reflow onto a different number of lines
    // when the viewport is resized — recheck the open answer's height so
    // it's never left clipped (or oversized) after a resize.
    window.addEventListener('resize', function(){
      var openEl = document.querySelector('.faq-item.open');
      if(openEl) openItem(openEl);
    });
  })();
</script>
@endpush
