@extends('layouts.app')

@section('content')
<div class="send-wrap">

  <div class="page-header fade-in d1">
    <a href="<?= e(route('setting')) ?>" class="icon-btn">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
    </a>
    <h1><?= e(__('setting.app_info')) ?></h1>
    <a href="<?= e(route('dashboard')) ?>" class="icon-btn">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><path d="M3 11l9-8 9 8"/><path d="M5 10v10h14V10"/></svg>
    </a>
  </div>

  <div class="profile-card fade-in d2" style="margin-bottom:18px;">
    <div class="profile-avatar" style="background:#10202F; color:#C9A24B;">L</div>
    <h3 style="margin:10px 0 2px;">Ledger</h3>
    <p class="profile-email"><?= e(__('appinfo.version', ['number' => '1.0.0'])) ?></p>
  </div>

  <div class="fade-in d3">
    <p class="setting-group-label"><?= e(__('appinfo.details')) ?></p>
    <div class="setting-list">
      <div class="setting-item" style="cursor:default;">
        <span class="label"><?= e(__('appinfo.app_version')) ?></span>
        <span style="font-size:13px; color:var(--text-3);">1.0.0</span>
      </div>
      <div class="setting-item" style="cursor:default;">
        <span class="label"><?= e(__('appinfo.laravel')) ?></span>
        <span style="font-size:13px; color:var(--text-3);"><?= e(app()->version()) ?></span>
      </div>
      <div class="setting-item" style="cursor:default;">
        <span class="label"><?= e(__('appinfo.php')) ?></span>
        <span style="font-size:13px; color:var(--text-3);"><?= e(PHP_VERSION) ?></span>
      </div>
    </div>

    <p class="setting-group-label"><?= e(__('appinfo.more')) ?></p>
    <div class="setting-list">
      <a href="<?= e(route('setting.terms')) ?>" class="setting-item">
        <div class="s-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><path d="M14 3v4a1 1 0 0 0 1 1h4"/><path d="M17 21H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h7l5 5v11a2 2 0 0 1-2 2Z"/></svg>
        </div>
        <span class="label"><?= e(__('setting.terms')) ?></span>
        <svg class="s-chev" viewBox="0 0 24 24" fill="none"><path d="M9 18l6-6-6-6"/></svg>
      </a>
      <a href="<?= e(route('support')) ?>" class="setting-item">
        <div class="s-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
        </div>
        <span class="label"><?= e(__('setting.support_ticket')) ?></span>
        <svg class="s-chev" viewBox="0 0 24 24" fill="none"><path d="M9 18l6-6-6-6"/></svg>
      </a>
    </div>
  </div>

</div>
@endsection
