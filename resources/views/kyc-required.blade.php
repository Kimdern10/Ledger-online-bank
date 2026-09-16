@extends('layouts.app')

@section('content')
<div class="send-wrap">

  <div class="page-header fade-in d1">
    <a href="<?= e(route('dashboard')) ?>" class="icon-btn">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
    </a>
    <h1><?= e(__('send.title')) ?></h1>
    <span style="width:38px; display:inline-block;"></span>
  </div>

  <div class="card-detail-card fade-in d2" style="text-align:center; padding:36px 24px;">
    <div style="width:56px; height:56px; border-radius:50%; background:rgba(47,111,98,0.12); display:flex; align-items:center; justify-content:center; margin:0 auto 18px;">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" style="width:26px; height:26px;">
        <rect x="3" y="8" width="18" height="13" rx="2"/><path d="M8 8V6a4 4 0 0 1 8 0v2"/>
      </svg>
    </div>

    <?php if ($status === 'pending'): ?>
      <p style="margin:0 0 8px; font-size:16.5px; font-weight:700;"><?= e(__('verify.kyc_gate_pending_title')) ?></p>
      <p style="margin:0 auto 22px; max-width:380px; font-size:13.5px; opacity:0.75; line-height:1.55;">
        <?= e(__('verify.kyc_gate_pending_body')) ?>
      </p>
      <div style="display:flex; gap:10px; justify-content:center; flex-wrap:wrap;">
        <a href="<?= e(route('dashboard')) ?>" class="btn btn-outline"><?= e(__('verify.back_to_dashboard')) ?></a>
        <a href="<?= e(route('support')) ?>" class="btn btn-primary"><?= e(__('verify.message_support')) ?></a>
      </div>
    <?php elseif ($status === 'rejected'): ?>
      <p style="margin:0 0 8px; font-size:16.5px; font-weight:700;"><?= e(__('verify.kyc_gate_rejected_title')) ?></p>
      <p style="margin:0 auto 22px; max-width:380px; font-size:13.5px; opacity:0.75; line-height:1.55;">
        <?= e(__('verify.kyc_gate_rejected_body')) ?>
      </p>
      <div style="display:flex; gap:10px; justify-content:center; flex-wrap:wrap;">
        <a href="<?= e(route('kyc.create')) ?>" class="btn btn-primary"><?= e(__('verify.verify_identity')) ?></a>
        <a href="<?= e(route('dashboard')) ?>" class="btn btn-outline"><?= e(__('verify.back_to_dashboard')) ?></a>
      </div>
    <?php else: ?>
      <p style="margin:0 0 8px; font-size:16.5px; font-weight:700;"><?= e(__('verify.kyc_gate_not_started_title')) ?></p>
      <p style="margin:0 auto 22px; max-width:380px; font-size:13.5px; opacity:0.75; line-height:1.55;">
        <?= e(__('verify.kyc_gate_not_started_body')) ?>
      </p>
      <div style="display:flex; gap:10px; justify-content:center; flex-wrap:wrap;">
        <a href="<?= e(route('kyc.create')) ?>" class="btn btn-primary"><?= e(__('verify.verify_identity')) ?></a>
        <a href="<?= e(route('dashboard')) ?>" class="btn btn-outline"><?= e(__('verify.back_to_dashboard')) ?></a>
      </div>
    <?php endif; ?>
  </div>

</div>
@endsection
