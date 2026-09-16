@extends('layouts.app')

@section('content')
<div class="send-wrap">

  <div class="page-header fade-in d1">
    <a href="<?= e(route('setting')) ?>" class="icon-btn">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
    </a>
    <h1><?= e(__('setting.password_change')) ?></h1>
    <a href="<?= e(route('dashboard')) ?>" class="icon-btn">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><path d="M3 11l9-8 9 8"/><path d="M5 10v10h14V10"/></svg>
    </a>
  </div>

  <?php if (session('status')): ?>
    <div class="card-detail-card fade-in d1" style="margin-bottom:18px;">
      <p style="margin:0; font-size:13.5px;"><?= e(session('status')) ?></p>
    </div>
  <?php endif; ?>

  <?php if (session('passwordError')): ?>
    <div class="send-alert fade-in d1"><?= e(session('passwordError')) ?></div>
  <?php endif; ?>

  <?php if ($errors->any()): ?>
    <div class="send-alert fade-in d1">
      <ul style="margin:0; padding-left:18px;">
        <?php foreach ($errors->all() as $error): ?>
          <li><?= e($error) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <div class="send-card fade-in d2">
    <p style="margin:0 0 18px; font-size:13px; color:var(--text-3); line-height:1.5;">
      <?= e(__('account.password_intro')) ?>
    </p>

    <form method="POST" action="<?= e(route('setting.password.update')) ?>">
      <?= csrf_field() ?>

      <div class="field-group">
        <p class="label"><?= e(__('account.current_password')) ?></p>
        <input type="password" class="text-input" name="current_password" autocomplete="current-password" required>
      </div>

      <div class="field-group">
        <p class="label"><?= e(__('account.new_password')) ?></p>
        <input type="password" class="text-input" name="password" autocomplete="new-password" minlength="8" required>
      </div>

      <div class="field-group">
        <p class="label"><?= e(__('account.confirm_new_password')) ?></p>
        <input type="password" class="text-input" name="password_confirmation" autocomplete="new-password" minlength="8" required>
      </div>

      <div class="pay-btn-wrap" style="margin-top:8px;">
        <button type="submit" class="pay-btn"><?= e(__('account.update_password')) ?></button>
      </div>
    </form>
  </div>

</div>
@endsection
