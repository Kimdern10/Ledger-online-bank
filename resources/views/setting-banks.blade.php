@extends('layouts.app')

@section('content')
<div class="send-wrap">

  <div class="page-header fade-in d1">
    <a href="<?= e(route('setting')) ?>" class="icon-btn">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
    </a>
    <h1>Banks</h1>
    <a href="<?= e(route('dashboard')) ?>" class="icon-btn">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><path d="M3 11l9-8 9 8"/><path d="M5 10v10h14V10"/></svg>
    </a>
  </div>

  <div class="send-card fade-in d2" style="margin-bottom:14px;">
    <p style="margin:0; font-size:13px; color:var(--text-3); line-height:1.5;">
      These are the banks available when you send money to another bank or send internationally. Don't see the one you need? Reach out to support.
    </p>
  </div>

  <div class="send-card fade-in d2" style="padding:10px 0; margin-bottom:10px;">
    <div style="display:flex; gap:8px; padding:0 18px 12px; flex-wrap:wrap;">
      <a href="<?= e(route('setting.banks')) ?>" class="app-page-link <?= e(! $type ? 'active' : '') ?>" style="text-decoration:none;">All</a>
      <a href="<?= e(route('setting.banks', ['type' => 'external'])) ?>" class="app-page-link <?= e($type === 'external' ? 'active' : '') ?>" style="text-decoration:none;">Domestic</a>
      <a href="<?= e(route('setting.banks', ['type' => 'international'])) ?>" class="app-page-link <?= e($type === 'international' ? 'active' : '') ?>" style="text-decoration:none;">International</a>
    </div>

    <?php if ($banks->isEmpty()): ?>
      <p style="margin:0; padding:24px 18px; text-align:center; font-size:13px; color:var(--text-3);"><?= e(__('setting.no_banks_listed')) ?></p>
    <?php else: ?>
      <?php foreach ($banks as $bank): ?>
        <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; padding:12px 18px; border-top:1px solid var(--mist);">
          <div>
            <p style="margin:0; font-weight:700; font-size:13.5px;"><?= e($bank->name) ?></p>
            <p style="margin:3px 0 0; font-size:12px; color:var(--text-3);">
              <?= e($bank->isInternational() ? 'International' : 'Domestic') ?>
              <?php if ($bank->country): ?> &middot; <?= e($bank->country) ?> <?php endif; ?>
              <?php if ($bank->currency): ?> &middot; <?= e($bank->currency) ?> <?php endif; ?>
            </p>
          </div>
          <p style="margin:0; font-size:11.5px; color:var(--text-3); text-align:right;">
            <?php if ($bank->isInternational() && $bank->swift_code): ?>
              SWIFT <?= e($bank->swift_code) ?>
            <?php elseif ($bank->routing_number): ?>
              Routing <?= e($bank->routing_number) ?>
            <?php endif; ?>
          </p>
        </div>
      <?php endforeach; ?>

      <div style="padding:0 18px;">
        @include('partials.pagination', ['paginator' => $banks])
      </div>
    <?php endif; ?>
  </div>

</div>
@endsection
