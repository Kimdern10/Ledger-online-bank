@extends('layouts.app')

@section('content')
<div class="send-wrap">

  <div class="page-header fade-in d1">
    <a href="<?= e(route('setting')) ?>" class="icon-btn">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
    </a>
    <h1><?= e(__('terms.page_title')) ?></h1>
    <a href="<?= e(route('dashboard')) ?>" class="icon-btn">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><path d="M3 11l9-8 9 8"/><path d="M5 10v10h14V10"/></svg>
    </a>
  </div>

  <div class="send-card fade-in d2" style="font-size:13.5px; line-height:1.7; color:var(--text-2);">
    <p style="margin:0 0 14px; font-size:12px; color:var(--text-3);"><?= e(__('terms.last_updated', ['date' => now()->translatedFormat('F Y')])) ?></p>

    <?php for ($i = 1; $i <= 17; $i++): ?>
      <p style="margin:0 0 14px;"><strong><?= e(__("terms.s{$i}_title")) ?></strong> <?= e(__("terms.s{$i}_body")) ?></p>
    <?php endfor; ?>

    <p style="margin:0;"><strong><?= e(__('terms.s18_title')) ?></strong> <?= __('terms.s18_body', ['support_link' => '<a href="' . route('support') . '" style="color:#C9A24B; font-weight:600;">' . __('terms.support_link_text') . '</a>']) ?></p>
  </div>

</div>
@endsection
