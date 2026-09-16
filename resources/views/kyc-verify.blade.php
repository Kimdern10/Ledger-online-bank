@extends('layouts.app')

@section('content')
<div class="send-wrap">

  <div class="page-header fade-in d1">
    <a href="<?= e(route('dashboard')) ?>" class="icon-btn">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
    </a>
    <h1><?= e(__('verify.verify_identity')) ?></h1>
    <span style="width:38px; display:inline-block;"></span>
  </div>

  <?php if (session('status')): ?>
    <div class="card-detail-card fade-in d1" style="margin-bottom:18px;">
      <p style="margin:0; font-size:13.5px;"><?= e(session('status')) ?></p>
    </div>
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

  <?php if ($kyc && $kyc->isApproved()): ?>
    <?php /* ================= Already approved ================= */ ?>
    <div class="card-detail-card fade-in d2" style="text-align:center; padding:36px 24px;">
      <div style="width:56px; height:56px; border-radius:50%; background:rgba(47,111,98,0.12); display:flex; align-items:center; justify-content:center; margin:0 auto 18px;">
        <svg viewBox="0 0 24 24" fill="none" stroke="#2F6F62" stroke-width="2" style="width:26px; height:26px;"><path d="M5 12l4 4 10-10"/></svg>
      </div>
      <p style="margin:0 0 8px; font-size:16.5px; font-weight:700;"><?= e(__('verify.kyc_approved_title')) ?></p>
      <p style="margin:0 auto 22px; max-width:380px; font-size:13.5px; opacity:0.75; line-height:1.55;">
        <?php if ($kyc->reviewed_at): ?>
          <?= e(__('verify.kyc_approved_body_dated', ['date' => $kyc->reviewed_at->translatedFormat('M j, Y')])) ?>
        <?php else: ?>
          <?= e(__('verify.kyc_approved_body')) ?>
        <?php endif; ?>
      </p>
      <a href="<?= e(route('dashboard')) ?>" class="btn btn-primary"><?= e(__('verify.back_to_dashboard')) ?></a>
    </div>

  <?php elseif ($kyc && $kyc->isPending()): ?>
    <?php /* ================= Awaiting admin review ================= */ ?>
    <div class="card-detail-card fade-in d2" style="text-align:center; padding:36px 24px;">
      <div style="width:56px; height:56px; border-radius:50%; background:rgba(193,150,40,0.14); display:flex; align-items:center; justify-content:center; margin:0 auto 18px;">
        <svg viewBox="0 0 24 24" fill="none" stroke="#9A6B10" stroke-width="1.6" style="width:26px; height:26px;"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg>
      </div>
      <p style="margin:0 0 8px; font-size:16.5px; font-weight:700;"><?= e(__('verify.kyc_pending_title')) ?></p>
      <p style="margin:0 auto 22px; max-width:380px; font-size:13.5px; opacity:0.75; line-height:1.55;">
        <?= e(__('verify.kyc_pending_body', ['type' => $kyc->documentTypeLabel(), 'time' => $kyc->created_at->diffForHumans()])) ?>
      </p>
      <a href="<?= e(route('dashboard')) ?>" class="btn btn-primary"><?= e(__('verify.back_to_dashboard')) ?></a>
    </div>

  <?php else: ?>
    <?php /* ================= Form: first submission, or resubmitting after a rejection ================= */ ?>
    <?php if ($kyc && $kyc->isRejected()): ?>
      <div class="send-alert fade-in d2">
        <p style="margin:0;"><strong><?= e(__('verify.last_submission_rejected')) ?></strong></p>
        <?php if ($kyc->rejection_reason): ?>
          <p style="margin:6px 0 0;"><?= e($kyc->rejection_reason) ?></p>
        <?php endif; ?>
        <p style="margin:6px 0 0;"><?= e(__('verify.kyc_resubmit_notice')) ?></p>
      </div>
    <?php endif; ?>

    <div class="send-card fade-in d2">
      <p style="margin:0 0 4px; font-size:15px; font-weight:700;"><?= e(__('verify.kyc_form_title')) ?></p>
      <p style="margin:0 0 20px; font-size:13px; color:var(--text-3); line-height:1.5;">
        <?= e(__('verify.kyc_form_body')) ?>
      </p>

      <form method="POST" action="<?= e(route('kyc.store')) ?>" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <div class="field-group">
          <p class="label"><?= e(__('verify.id_type_label')) ?></p>
          <div class="select-wrap">
            <select name="id_document_type" class="text-input" required>
              <option value=""><?= e(__('verify.select_id_type')) ?></option>
              <?php foreach ($documentTypes as $value => $label): ?>
                <option value="<?= e($value) ?>" <?= e(old('id_document_type', $kyc->id_document_type ?? '') === $value ? 'selected' : '') ?>><?= e($label) ?></option>
              <?php endforeach; ?>
            </select>
            <div class="chev">
              <svg viewBox="0 0 24 24" fill="none"><path d="M6 9l6 6 6-6"/></svg>
            </div>
          </div>
        </div>

        <div class="field-group">
          <p class="label"><?= e(__('verify.id_photo_label')) ?></p>
          <input type="file" name="id_document" class="text-input" accept="image/*" required>
        </div>

        <div class="field-group">
          <p class="label"><?= e(__('verify.selfie_label')) ?> <span style="font-weight:400; color:var(--text-3);"><?= e(__('verify.selfie_hint')) ?></span></p>
          <input type="file" name="selfie" class="text-input" accept="image/*" required>
        </div>

        <div class="pay-btn-wrap" style="margin-top:8px;">
          <button type="submit" class="pay-btn"><?= e(__('verify.submit_for_review')) ?></button>
        </div>
      </form>
    </div>
  <?php endif; ?>

</div>
@endsection
