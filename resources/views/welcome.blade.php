@extends('layouts.apps')

@section('content')

<?php /* ============ HERO ============ */ ?>
<section class="hero" id="hero">
  <div class="hero-blob hero-blob-1"></div>
  <div class="hero-blob hero-blob-2"></div>
  <div class="container">
    <div class="hero-grid">

      <div class="hero-copy fade-up">
        <div class="eyebrow"><span class="dot"></span> <?= e(__('welcome.hero_eyebrow')) ?></div>
        <h1 class="h1"><?= e(__('welcome.hero_h1_line1')) ?><br><?= e(__('welcome.hero_h1_line2')) ?></h1>
        <p class="lede" style="max-width:460px; margin-top:18px;">
          <?= e(__('welcome.hero_lede')) ?>
        </p>

        <?php if (auth()->check()): ?>
          <?php /* Signed-in visitor: no point showing a "create account" form
               to someone who already has one — send them straight back
               into their own ledger instead. */ ?>
          <div class="hero-form" style="max-width:420px;">
            <?php /* Admins have no personal dashboard of their own — send them
                 to the admin dashboard instead of the consumer one. */ ?>
            <a href="<?= e(auth()->user()->isAdmin() ? route('admin.dashboard') : route('dashboard')) ?>" class="btn btn-primary" style="width:100%; justify-content:center;">
              <?= e(auth()->user()->isAdmin() ? __('welcome.go_to_admin_dashboard') : __('welcome.go_to_dashboard')) ?>
              <svg viewBox="0 0 24 24" fill="none"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </a>
          </div>
          <p class="mono" style="font-size:12px; color:var(--text-3); margin-top:12px;"><?= e(__('welcome.welcome_back_note')) ?></p>
        <?php else: ?>
          <?php /* A GET to the register page, not a real signup submission —
               this just hands off the email address so the guest doesn't
               have to type it twice (see pages/auth/register.blade.php's
               old('email', request()->query('email')) fallback). Used to
               be onsubmit="return false;", which meant clicking "Open an
               account" here did nothing at all. */ ?>
          <form class="hero-form" method="GET" action="<?= e(route('register')) ?>">
            <input type="email" name="email" placeholder="you@email.com" required>
            <button type="submit" class="btn btn-primary">
              <?= e(__('welcome.open_an_account')) ?>
              <svg viewBox="0 0 24 24" fill="none"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
          </form>
          <p class="mono" style="font-size:12px; color:var(--text-3); margin-top:12px;"><?= e(__('welcome.no_minimum_note')) ?></p>
        <?php endif; ?>

        <div class="hero-stats">
          <div>
            <div class="h3 count-up" style="font-family:'Newsreader',serif;" data-target="128" data-suffix="k+">0k+</div>
            <p style="font-size:13px; color:var(--text-2);"><?= e(__('welcome.stat_ledgers_opened')) ?></p>
          </div>
          <div>
            <div class="h3 count-up" style="font-family:'Newsreader',serif;" data-prefix="$" data-target="2.1" data-decimals="1" data-suffix="B">$0B</div>
            <p style="font-size:13px; color:var(--text-2);"><?= e(__('welcome.stat_reconciled_monthly')) ?></p>
          </div>
          <div>
            <div class="h3 count-up" style="font-family:'Newsreader',serif;" data-target="4.9" data-decimals="1" data-suffix="/5">0/5</div>
            <p style="font-size:13px; color:var(--text-2);"><?= e(__('welcome.stat_average_rating')) ?></p>
          </div>
        </div>
      </div>

      <div class="hero-visual fade-up">
        <div class="ledger-card" id="tiltCard">
          <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-top:8px;">
            <div>
              <div class="k-label"><?= e(__('welcome.hero_available_balance')) ?></div>
              <div class="k-value">$12,480.32</div>
            </div>
            <span class="chip">&bull;&bull;&bull;&bull; 4471</span>
          </div>
          <div class="k-underline"></div>
          <svg viewBox="0 0 240 60" width="100%" height="60" style="margin-top:6px;">
            <polyline class="sparkline-path" points="0,42 30,38 60,44 90,26 120,30 150,14 180,20 210,8 240,12" fill="none" stroke="var(--wheat)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          <div style="display:flex; gap:10px; margin-top:26px;">
            <span class="chip"><?= e(__('welcome.hero_up_this_month')) ?></span>
            <span class="chip"><?= e(__('welcome.hero_interest_apy')) ?></span>
          </div>
        </div>

        <div class="hero-float-card">
          <div class="hero-float-row">
            <span class="hero-float-icon">
              <svg viewBox="0 0 24 24" fill="none"><path d="M7 17L17 7M17 7H9M17 7V15" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </span>
            <div>
              <div style="font-size:13.5px; font-weight:600;"><?= e(__('welcome.hero_float_payment_received')) ?></div>
              <div class="mono" style="font-size:12px; color:var(--text-3);">from J. Alvarez &middot; +$640.00</div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<?php /* ============ TICKER ============ */ ?>
<div class="ticker-strip">
  <div class="ticker-track">
    <span><?= e(__('welcome.ticker_no_hidden_fees')) ?></span>
    <span><?= e(__('welcome.ticker_realtime_sync')) ?></span>
    <span><?= e(__('welcome.ticker_apy_savings')) ?></span>
    <span><?= e(__('welcome.ticker_free_transfers')) ?></span>
    <span><?= e(__('welcome.ticker_bank_encryption')) ?></span>
    <span><?= e(__('welcome.ticker_support')) ?></span>
    <span><?= e(__('welcome.ticker_no_hidden_fees')) ?></span>
    <span><?= e(__('welcome.ticker_realtime_sync')) ?></span>
    <span><?= e(__('welcome.ticker_apy_savings')) ?></span>
    <span><?= e(__('welcome.ticker_free_transfers')) ?></span>
    <span><?= e(__('welcome.ticker_bank_encryption')) ?></span>
    <span><?= e(__('welcome.ticker_support')) ?></span>
  </div>
</div>

<?php /* ============ ABOUT ============ */ ?>
<section class="section" id="about">
  <div class="container">
    <div class="section-head">
      <div>
        <div class="eyebrow"><span class="dot"></span> <?= e(__('welcome.about_eyebrow')) ?></div>
        <h2 class="h2"><?= e(__('welcome.about_h2_line1')) ?><br><?= e(__('welcome.about_h2_line2')) ?></h2>
      </div>
      <p class="lede"><?= e(__('welcome.about_lede')) ?></p>
    </div>

    <div class="grid-3">
      <div class="pillar fade-up" style="animation-delay:0s;">
        <div class="pillar-icon">
          <svg viewBox="0 0 24 24" fill="none"><path d="M4 6h16M4 12h16M4 18h10" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
        </div>
        <h3 class="h3"><?= e(__('welcome.pillar1_title')) ?></h3>
        <p class="lede" style="font-size:15px;"><?= e(__('welcome.pillar1_desc')) ?></p>
      </div>
      <div class="pillar fade-up" style="animation-delay:0.08s;">
        <div class="pillar-icon">
          <svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="8" stroke="currentColor" stroke-width="1.7"/><path d="M12 8v4l3 2" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </div>
        <h3 class="h3"><?= e(__('welcome.pillar2_title')) ?></h3>
        <p class="lede" style="font-size:15px;"><?= e(__('welcome.pillar2_desc')) ?></p>
      </div>
      <div class="pillar fade-up" style="animation-delay:0.16s;">
        <div class="pillar-icon">
          <svg viewBox="0 0 24 24" fill="none"><path d="M12 3l7 3v6c0 4.5-3 7.5-7 9-4-1.5-7-4.5-7-9V6l7-3Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg>
        </div>
        <h3 class="h3"><?= e(__('welcome.pillar3_title')) ?></h3>
        <p class="lede" style="font-size:15px;"><?= e(__('welcome.pillar3_desc')) ?></p>
      </div>
    </div>
  </div>
</section>

<?php /* ============ FEATURE BANNER ============ */ ?>
<section class="section" style="background:var(--paper-2); border-top:1px solid var(--mist); border-bottom:1px solid var(--mist);">
  <div class="container">
    <div class="feature-grid">

      <div class="fade-up">
        <div class="eyebrow"><span class="dot"></span> <?= e(__('welcome.feature_eyebrow')) ?></div>
        <h2 class="h2"><?= e(__('welcome.feature_h2_line1')) ?><br><?= e(__('welcome.feature_h2_line2')) ?></h2>
        <p class="lede" style="margin-top:14px; max-width:420px;">
          <?= e(__('welcome.feature_lede')) ?>
        </p>
        <ul class="feature-list">
          <li>
            <svg viewBox="0 0 24 24" fill="none"><path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <?= e(__('welcome.feature_li1')) ?>
          </li>
          <li>
            <svg viewBox="0 0 24 24" fill="none"><path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <?= e(__('welcome.feature_li2')) ?>
          </li>
          <li>
            <svg viewBox="0 0 24 24" fill="none"><path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <?= e(__('welcome.feature_li3')) ?>
          </li>
        </ul>
        <a href="<?= e(auth()->check() ? (auth()->user()->isAdmin() ? route('admin.dashboard') : route('dashboard')) : route('register')) ?>" class="btn btn-outline" style="margin-top:24px;">
          <?= e(auth()->check() ? (auth()->user()->isAdmin() ? __('welcome.go_to_admin_dashboard') : __('welcome.go_to_dashboard')) : __('welcome.see_how_spend_tracking')) ?>
          <svg viewBox="0 0 24 24" fill="none"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </a>
      </div>

      <div class="fade-up">
        <div class="spend-mock">
          <div class="k-label" style="color:var(--text-3);"><?= e(__('welcome.spending_this_month')) ?></div>
          <div class="ring-wrap">
            <div class="ring" style="--pct:65;">
              <div class="ring-inner">
                <div class="mono" style="font-size:22px; font-weight:600;">65%</div>
                <div style="font-size:11px; color:var(--text-3);"><?= e(__('welcome.of_budget')) ?></div>
              </div>
            </div>
          </div>
          <div class="spend-cats">
            <div><span class="cat-dot" style="background:var(--sage);"></span> <?= e(__('welcome.cat_living')) ?> <span class="mono" style="margin-left:auto; color:var(--text-3);">$1,120</span></div>
            <div><span class="cat-dot" style="background:var(--wheat);"></span> <?= e(__('welcome.cat_travel')) ?> <span class="mono" style="margin-left:auto; color:var(--text-3);">$340</span></div>
            <div><span class="cat-dot" style="background:var(--coral);"></span> <?= e(__('welcome.cat_dining')) ?> <span class="mono" style="margin-left:auto; color:var(--text-3);">$212</span></div>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<?php /* ============ SERVICES ============ */ ?>
<section class="section" id="services">
  <div class="container">
    <div class="section-head">
      <div>
        <div class="eyebrow"><span class="dot"></span> <?= e(__('welcome.services_eyebrow')) ?></div>
        <h2 class="h2"><?= e(__('welcome.services_h2_line1')) ?><br><?= e(__('welcome.services_h2_line2')) ?></h2>
      </div>
      <p class="lede"><?= e(__('welcome.services_lede')) ?></p>
    </div>

    <div class="grid-3">
      <?php
        $services = [
          ['icon' => '<path d="M7 17L17 7M17 7H9M17 7V15"/>', 'title' => __('welcome.service1_title'), 'desc' => __('welcome.service1_desc')],
          ['icon' => '<path d="M17 7L7 17M7 17H15M7 17V9"/>', 'title' => __('welcome.service2_title'), 'desc' => __('welcome.service2_desc')],
          ['icon' => '<path d="M6 3h9l3 3v15H6V3Z"/><path d="M9 9h6M9 13h6M9 17h3"/>', 'title' => __('welcome.service3_title'), 'desc' => __('welcome.service3_desc')],
          ['icon' => '<circle cx="12" cy="12" r="8"/><path d="M12 8v8M9 11h6"/>', 'title' => __('welcome.service4_title'), 'desc' => __('welcome.service4_desc')],
          ['icon' => '<rect x="3" y="6" width="18" height="13" rx="2"/><path d="M3 10h18M7 15h4"/>', 'title' => __('welcome.service5_title'), 'desc' => __('welcome.service5_desc')],
          ['icon' => '<rect x="4" y="4" width="6" height="6"/><rect x="14" y="4" width="6" height="6"/><rect x="4" y="14" width="6" height="6"/><path d="M15 15h5v5h-5z"/>', 'title' => __('welcome.service6_title'), 'desc' => __('welcome.service6_desc')],
        ];
      ?>

      <?php foreach ($services as $__servicesIndex => $service): ?>
        <div class="service-card fade-up" style="animation-delay:<?= e($__servicesIndex * 0.08) ?>s;">
          <div class="service-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><?= $service['icon'] ?></svg>
          </div>
          <h3 class="h3" style="font-size:19px;"><?= e($service['title']) ?></h3>
          <p class="lede" style="font-size:14.5px; margin-top:8px;"><?= $service['desc'] ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php /* ============ BENEFITS CAROUSEL ============ */ ?>
<section class="section" style="background:var(--ink);">
  <div class="container">
    <div class="section-head">
      <div>
        <div class="eyebrow on-dark"><span class="dot"></span> <?= e(__('welcome.benefits_eyebrow')) ?></div>
        <h2 class="h2 on-dark"><?= e(__('welcome.benefits_h2_line1')) ?><br><?= e(__('welcome.benefits_h2_line2')) ?></h2>
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
          <h3 class="h3 on-dark" style="margin-top:10px;"><?= e(__('welcome.benefit1_title')) ?></h3>
          <p class="on-dark lede" style="font-size:14.5px;"><?= e(__('welcome.benefit1_desc')) ?></p>
        </div>
        <div class="benefit-slide">
          <div class="k-label" style="color:var(--wheat);">02</div>
          <h3 class="h3 on-dark" style="margin-top:10px;"><?= e(__('welcome.benefit2_title')) ?></h3>
          <p class="on-dark lede" style="font-size:14.5px;"><?= e(__('welcome.benefit2_desc')) ?></p>
        </div>
        <div class="benefit-slide">
          <div class="k-label" style="color:var(--wheat);">03</div>
          <h3 class="h3 on-dark" style="margin-top:10px;"><?= e(__('welcome.benefit3_title')) ?></h3>
          <p class="on-dark lede" style="font-size:14.5px;"><?= e(__('welcome.benefit3_desc')) ?></p>
        </div>
        <div class="benefit-slide">
          <div class="k-label" style="color:var(--wheat);">04</div>
          <h3 class="h3 on-dark" style="margin-top:10px;"><?= e(__('welcome.benefit4_title')) ?></h3>
          <p class="on-dark lede" style="font-size:14.5px;"><?= e(__('welcome.benefit4_desc')) ?></p>
        </div>
      </div>
    </div>
  </div>
</section>

<?php /* ============ TRUST STATS ============ */ ?>
<section class="section">
  <div class="container">
    <div class="grid-3">
      <?php
        $stats = [
          ['pct' => 99.9, 'color' => 'var(--sage)', 'title' => __('welcome.stat1_title'), 'desc' => __('welcome.stat1_desc')],
          ['pct' => 94, 'color' => 'var(--wheat)', 'title' => __('welcome.stat2_title'), 'desc' => __('welcome.stat2_desc')],
          ['pct' => 87, 'color' => 'var(--coral)', 'title' => __('welcome.stat3_title'), 'desc' => __('welcome.stat3_desc')],
        ];
      ?>
      <?php foreach ($stats as $__statsIndex => $stat): ?>
        <div class="stat-card fade-up" style="animation-delay:<?= e($__statsIndex * 0.1) ?>s;">
          <div class="stat-ring" data-target="<?= e($stat['pct']) ?>" data-suffix="%">
            <svg viewBox="0 0 120 120">
              <circle class="stat-ring-bg" cx="60" cy="60" r="52"/>
              <circle class="stat-ring-fg" cx="60" cy="60" r="52" style="stroke:<?= e($stat['color']) ?>;"/>
            </svg>
            <div class="stat-ring-value">0%</div>
          </div>
          <h3 class="h3" style="font-size:18px; margin-top:16px;"><?= e($stat['title']) ?></h3>
          <p class="lede" style="font-size:14px;"><?= $stat['desc'] ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php /* ============ CALCULATORS ============ */ ?>
<section class="section" id="calculators" style="background:var(--paper-2); border-top:1px solid var(--mist); border-bottom:1px solid var(--mist);">
  <div class="container">
    <div class="section-head">
      <div>
        <div class="eyebrow"><span class="dot"></span> <?= e(__('welcome.calc_eyebrow')) ?></div>
        <h2 class="h2"><?= e(__('welcome.calc_h2_line1')) ?><br><?= e(__('welcome.calc_h2_line2')) ?></h2>
      </div>
      <p class="lede"><?= e(__('welcome.calc_lede')) ?></p>
    </div>

    <div class="calc-tabs">
      <button class="calc-tab active" data-tab="savings"><?= e(__('welcome.calc_tab_savings')) ?></button>
      <button class="calc-tab" data-tab="budget"><?= e(__('welcome.calc_tab_budget')) ?></button>
      <button class="calc-tab" data-tab="loan"><?= e(__('welcome.calc_tab_loan')) ?></button>
    </div>

    <div class="calc-panel active" id="calc-savings">
      <div class="calc-grid">
        <div class="calc-inputs">
          <label><?= e(__('welcome.calc_starting_balance')) ?> <span class="mono calc-out" id="sv-start-out">$1,000</span></label>
          <input type="range" id="sv-start" min="0" max="20000" step="100" value="1000">

          <label><?= e(__('welcome.calc_monthly_deposit')) ?> <span class="mono calc-out" id="sv-deposit-out">$200</span></label>
          <input type="range" id="sv-deposit" min="0" max="2000" step="10" value="200">

          <label><?= e(__('welcome.calc_years')) ?> <span class="mono calc-out" id="sv-years-out">5</span></label>
          <input type="range" id="sv-years" min="1" max="30" step="1" value="5">

          <label><?= e(__('welcome.calc_annual_rate')) ?> <span class="mono calc-out" id="sv-rate-out">3.8%</span></label>
          <input type="range" id="sv-rate" min="0" max="8" step="0.1" value="3.8">
        </div>
        <div class="ledger-card calc-result">
          <div class="k-label"><?= e(__('welcome.calc_balance_after')) ?> <span id="sv-years-label">5</span> <?= e(__('welcome.calc_years_lc')) ?></div>
          <div class="k-value" id="sv-total">$0</div>
          <div class="k-underline"></div>
          <div class="calc-breakdown">
            <div><span><?= e(__('welcome.calc_youll_deposit')) ?></span><span class="mono" id="sv-contributed">$0</span></div>
            <div><span><?= e(__('welcome.calc_interest_earned')) ?></span><span class="mono" id="sv-growth">$0</span></div>
          </div>
          <div class="calc-bars" id="sv-bars"></div>
        </div>
      </div>
    </div>

    <div class="calc-panel" id="calc-budget">
      <div class="calc-grid">
        <div class="calc-inputs">
          <label><?= e(__('welcome.calc_monthly_income')) ?> <span class="mono calc-out" id="bg-income-out">$5,000</span></label>
          <input type="range" id="bg-income" min="1000" max="20000" step="100" value="5000">

          <label><?= e(__('welcome.cat_living')) ?> <span class="mono calc-out" id="bg-living-out">45%</span></label>
          <input type="range" id="bg-living" min="0" max="100" step="1" value="45">

          <label><?= e(__('welcome.cat_travel')) ?> <span class="mono calc-out" id="bg-travel-out">10%</span></label>
          <input type="range" id="bg-travel" min="0" max="100" step="1" value="10">

          <label><?= e(__('welcome.cat_dining')) ?> <span class="mono calc-out" id="bg-dining-out">15%</span></label>
          <input type="range" id="bg-dining" min="0" max="100" step="1" value="15">
        </div>
        <div class="ledger-card calc-result">
          <div class="k-label"><?= e(__('welcome.calc_left_to_save')) ?></div>
          <div class="k-value" id="bg-leftover">$0</div>
          <div class="k-underline"></div>
          <div class="calc-breakdown">
            <div><span class="cat-dot" style="background:var(--sage);"></span><span><?= e(__('welcome.cat_living')) ?></span><span class="mono" id="bg-living-amt" style="margin-left:auto;">$0</span></div>
            <div><span class="cat-dot" style="background:var(--wheat);"></span><span><?= e(__('welcome.cat_travel')) ?></span><span class="mono" id="bg-travel-amt" style="margin-left:auto;">$0</span></div>
            <div><span class="cat-dot" style="background:var(--coral);"></span><span><?= e(__('welcome.cat_dining')) ?></span><span class="mono" id="bg-dining-amt" style="margin-left:auto;">$0</span></div>
          </div>
        </div>
      </div>
    </div>

    <div class="calc-panel" id="calc-loan">
      <div class="calc-grid">
        <div class="calc-inputs">
          <label><?= e(__('welcome.calc_loan_amount')) ?> <span class="mono calc-out" id="ln-amount-out">$20,000</span></label>
          <input type="range" id="ln-amount" min="1000" max="100000" step="500" value="20000">

          <label><?= e(__('welcome.calc_interest_rate_apr')) ?> <span class="mono calc-out" id="ln-rate-out">6.5%</span></label>
          <input type="range" id="ln-rate" min="0.5" max="20" step="0.1" value="6.5">

          <label><?= e(__('welcome.calc_term')) ?> <span class="mono calc-out" id="ln-term-out">5 years</span></label>
          <input type="range" id="ln-term" min="1" max="30" step="1" value="5">
        </div>
        <div class="ledger-card calc-result">
          <div class="k-label"><?= e(__('welcome.calc_estimated_monthly_payment')) ?></div>
          <div class="k-value" id="ln-payment">$0</div>
          <div class="k-underline"></div>
          <div class="calc-breakdown">
            <div><span><?= e(__('welcome.calc_total_paid')) ?></span><span class="mono" id="ln-total">$0</span></div>
            <div><span><?= e(__('welcome.calc_total_interest')) ?></span><span class="mono" id="ln-interest">$0</span></div>
          </div>
        </div>
      </div>
    </div>

  </div>
</section>

<?php /* ============ TESTIMONIALS ============ */ ?>
<section class="section">
  <div class="container">
    <div class="section-head">
      <div>
        <div class="eyebrow"><span class="dot"></span> <?= e(__('welcome.testimonials_eyebrow')) ?></div>
        <h2 class="h2"><?= e(__('welcome.testimonials_h2_line1')) ?><br><?= e(__('welcome.testimonials_h2_line2')) ?></h2>
      </div>
    </div>

    <div class="testimonial-grid">
      <?php
        $col1 = [
          ['name' => 'Maria Ostrowski', 'role' => __('welcome.testimonial1_role'), 'quote' => __('welcome.testimonial1_quote')],
          ['name' => 'Devon Marsh', 'role' => __('welcome.testimonial2_role'), 'quote' => __('welcome.testimonial2_quote')],
          ['name' => 'Priya Chandran', 'role' => __('welcome.testimonial3_role'), 'quote' => __('welcome.testimonial3_quote')],
        ];
        $col2 = [
          ['name' => 'Tomás Reyes', 'role' => __('welcome.testimonial4_role'), 'quote' => __('welcome.testimonial4_quote')],
          ['name' => 'Claudia Smith', 'role' => __('welcome.testimonial5_role'), 'quote' => __('welcome.testimonial5_quote')],
          ['name' => 'Sam Whitfield', 'role' => __('welcome.testimonial6_role'), 'quote' => __('welcome.testimonial6_quote')],
        ];
      ?>

      <div class="testimonial-col">
        <div class="testimonial-track">
          <?php foreach (array_merge($col1, $col1) as $t): ?>
            <div class="testimonial-card">
              <p class="lede" style="font-size:14.5px;">&ldquo;<?= $t['quote'] ?>&rdquo;</p>
              <div class="testimonial-who">
                <?php
                    // Splitting on a single space breaks the moment a name has a
                    // leading/trailing/double space (explode() then yields an
                    // empty-string part, and PHP 8 throws "Uninitialized string
                    // offset 0" on ''[0]) -- filter() drops those empty parts
                    // first so a stray space in the data can never crash this.
                ?>
                <span class="testimonial-avatar"><?= e(collect(explode(' ', $t['name']))->filter()->map(fn($p) => $p[0])->implode('')) ?></span>
                <div>
                  <div style="font-weight:600; font-size:14px;"><?= e($t['name']) ?></div>
                  <div style="font-size:12.5px; color:var(--text-3);"><?= e($t['role']) ?></div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="testimonial-col">
        <div class="testimonial-track testimonial-track-reverse">
          <?php foreach (array_merge($col2, $col2) as $t): ?>
            <div class="testimonial-card">
              <p class="lede" style="font-size:14.5px;">&ldquo;<?= $t['quote'] ?>&rdquo;</p>
              <div class="testimonial-who">
                <span class="testimonial-avatar"><?= e(collect(explode(' ', $t['name']))->filter()->map(fn($p) => $p[0])->implode('')) ?></span>
                <div>
                  <div style="font-weight:600; font-size:14px;"><?= e($t['name']) ?></div>
                  <div style="font-size:12.5px; color:var(--text-3);"><?= e($t['role']) ?></div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</section>

<?php /* ============ FAQ ============ */ ?>
<section class="section" id="faq" style="background:var(--paper-2); border-top:1px solid var(--mist); border-bottom:1px solid var(--mist);">
  <div class="container" style="max-width:820px;">
    <div class="section-head" style="margin-bottom:32px;">
      <div>
        <div class="eyebrow"><span class="dot"></span> <?= e(__('welcome.faq_eyebrow')) ?></div>
        <h2 class="h2"><?= e(__('welcome.faq_h2_line1')) ?><br><?= e(__('welcome.faq_h2_line2')) ?></h2>
      </div>
    </div>

    <div class="faq-list">
      <?php
        $faqs = [
          ['q' => __('welcome.faq1_q'), 'a' => __('welcome.faq1_a')],
          ['q' => __('welcome.faq2_q'), 'a' => __('welcome.faq2_a')],
          ['q' => __('welcome.faq3_q'), 'a' => __('welcome.faq3_a')],
          ['q' => __('welcome.faq4_q'), 'a' => __('welcome.faq4_a')],
          ['q' => __('welcome.faq5_q'), 'a' => __('welcome.faq5_a')],
        ];
      ?>

      <?php foreach ($faqs as $i => $faq): ?>
        <div class="faq-item <?= e($i === 0 ? 'open' : '') ?>">
          <button class="faq-question" type="button">
            <?= e($faq['q']) ?>
            <svg viewBox="0 0 24 24" fill="none"><path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </button>
          <div class="faq-answer">
            <p class="lede" style="font-size:14.5px; padding-bottom:20px;"><?= e($faq['a']) ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php /* ============ INTEGRATIONS / LINK ACCOUNTS ============ */ ?>
<section class="section">
  <div class="container">
    <div class="ledger-card integrations-banner">
      <div class="integrations-copy">
        <div class="eyebrow on-dark"><span class="dot"></span> <?= e(__('welcome.integrations_eyebrow')) ?></div>
        <h2 class="h2 on-dark"><?= e(__('welcome.integrations_h2_line1')) ?><br><?= e(__('welcome.integrations_h2_line2')) ?></h2>
        <p class="on-dark lede" style="font-size:15px; margin-top:12px; max-width:440px;">
          <?= e(__('welcome.integrations_lede')) ?>
        </p>
        <a href="<?= e(auth()->check() ? route('link-account') : route('register')) ?>" class="btn btn-wheat" style="margin-top:22px;">
          <?= e(__('welcome.link_an_account')) ?>
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
        <span class="chip"><?= e(__('welcome.plus_more_banks')) ?></span>
      </div>
    </div>
  </div>
</section>

<?php /* ============ BLOG ============ */ ?>
<section class="section" id="blog">
  <div class="container">
    <div class="section-head">
      <div>
        <div class="eyebrow"><span class="dot"></span> <?= e(__('welcome.blog_eyebrow')) ?></div>
        <h2 class="h2"><?= e(__('welcome.blog_h2_line1')) ?><br><?= e(__('welcome.blog_h2_line2')) ?></h2>
      </div>
      <a href="#" class="btn btn-outline">
        <?= e(__('welcome.read_all_posts')) ?>
        <svg viewBox="0 0 24 24" fill="none"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/></svg>
      </a>
    </div>

    <div class="grid-3">
      <a href="#" class="blog-card fade-up" style="animation-delay:0s;">
        <div class="blog-card-head" style="background:var(--sage-light);">
          <svg viewBox="0 0 24 24" fill="none" stroke="var(--sage)" stroke-width="1.6"><circle cx="12" cy="12" r="8"/><path d="M12 8v4l3 2"/></svg>
        </div>
        <div class="blog-card-body">
          <div class="mono" style="font-size:12px; color:var(--text-3);"><?= e(__('welcome.blog1_meta')) ?></div>
          <h3 class="h3" style="font-size:19px; margin-top:8px;"><?= e(__('welcome.blog1_title')) ?></h3>
          <p class="lede" style="font-size:14px; margin-top:8px;"><?= e(__('welcome.blog1_desc')) ?></p>
        </div>
      </a>
      <a href="#" class="blog-card fade-up" style="animation-delay:0.08s;">
        <div class="blog-card-head" style="background:var(--wheat-light);">
          <svg viewBox="0 0 24 24" fill="none" stroke="#a9822f" stroke-width="1.6"><path d="M12 3v18M5 8l7-5 7 5M5 16l7 5 7-5"/></svg>
        </div>
        <div class="blog-card-body">
          <div class="mono" style="font-size:12px; color:var(--text-3);"><?= e(__('welcome.blog2_meta')) ?></div>
          <h3 class="h3" style="font-size:19px; margin-top:8px;"><?= e(__('welcome.blog2_title')) ?></h3>
          <p class="lede" style="font-size:14px; margin-top:8px;"><?= e(__('welcome.blog2_desc')) ?></p>
        </div>
      </a>
      <a href="#" class="blog-card fade-up" style="animation-delay:0.16s;">
        <div class="blog-card-head" style="background:var(--coral-light);">
          <svg viewBox="0 0 24 24" fill="none" stroke="var(--coral)" stroke-width="1.6"><path d="M12 3l7 3v6c0 4.5-3 7.5-7 9-4-1.5-7-4.5-7-9V6l7-3Z"/></svg>
        </div>
        <div class="blog-card-body">
          <div class="mono" style="font-size:12px; color:var(--text-3);"><?= e(__('welcome.blog3_meta')) ?></div>
          <h3 class="h3" style="font-size:19px; margin-top:8px;"><?= e(__('welcome.blog3_title')) ?></h3>
          <p class="lede" style="font-size:14px; margin-top:8px;"><?= e(__('welcome.blog3_desc')) ?></p>
        </div>
      </a>
    </div>
  </div>
</section>

@endsection
