@extends('layouts.app')

@section('content')
<div class="send-wrap">

  <div class="page-header fade-in d1">
    <a href="<?= e(route('setting')) ?>" class="icon-btn">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
    </a>
    <h1><?= e(__('setting.delete_account')) ?></h1>
    <a href="<?= e(route('dashboard')) ?>" class="icon-btn">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><path d="M3 11l9-8 9 8"/><path d="M5 10v10h14V10"/></svg>
    </a>
  </div>

  <?php if (session('deleteError')): ?>
    <div class="send-alert fade-in d1"><?= e(session('deleteError')) ?></div>
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
    <p style="margin:0 0 10px; font-size:13.5px; font-weight:700; color:#C1503C;"><?= e(__('account.delete_warning_title')) ?></p>
    <p style="margin:0 0 18px; font-size:13px; color:var(--text-3); line-height:1.6;">
      <?= __('account.delete_intro', ['support_link' => '<a href="' . route('support') . '" style="color:#C9A24B; font-weight:600;">' . __('account.support_link_text') . '</a>']) ?>
    </p>

    <div class="wallet-meta" style="margin-bottom:18px;">
      <div class="wallet-meta-row"><span><?= e(__('account.current_balance')) ?></span><span>$<?= e(number_format((float) auth()->user()->balance, 2)) ?></span></div>
    </div>

    <form method="POST" action="<?= e(route('setting.delete-account.destroy')) ?>" data-confirm="<?= e(__('account.delete_confirm_dialog')) ?>" data-confirm-danger="1" data-confirm-button="<?= e(__('account.delete_my_account')) ?>">
      <?= csrf_field() ?>

      <div class="field-group">
        <p class="label"><?= e(__('account.confirm_password_field')) ?></p>
        <input type="password" class="text-input" name="password" autocomplete="current-password" required>
      </div>

      <div class="pay-btn-wrap" style="margin-top:8px;">
        <button type="submit" class="pay-btn" style="background:#C1503C;" <?= e((float) auth()->user()->balance > 0 ? 'disabled' : '') ?>><?= e(__('account.delete_my_account')) ?></button>
      </div>

      <?php if ((float) auth()->user()->balance > 0): ?>
        <p style="margin:10px 0 0; font-size:12px; color:#C1503C; text-align:center;"><?= e(__('account.withdraw_first')) ?></p>
      <?php endif; ?>
    </form>
  </div>

</div>
@endsection
