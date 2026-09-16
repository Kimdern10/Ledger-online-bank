@extends('layouts.app')

@section('content')
<div class="receipt-wrap">
  <div class="page-header fade-in d1">
    <a href="<?= e(route('dashboard')) ?>" class="icon-btn">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6L6 18M6 6l12 12"/></svg>
    </a>
    <h1><?= e(__('receipt.page_title')) ?></h1>
    <span style="width:40px; display:inline-block;"></span>
  </div>

  <div class="receipt-card fade-in d2" id="receiptCard">
    <?php /* Proper name of the credit union itself — deliberately not run
         through __(), same as everywhere else this appears in the app. */ ?>
    <p class="receipt-letterhead">Ledger Federal Credit Union</p>

    <?php $receiptIcon = $icon ?? 'check'; ?>
    <div class="receipt-status">
      <div class="receipt-check receipt-check-<?= e($receiptIcon) ?>" id="receiptCheck">
        <?php if ($receiptIcon === 'clock'): ?>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg>
        <?php elseif ($receiptIcon === 'x'): ?>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6L6 18M6 6l12 12"/></svg>
        <?php else: ?>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
        <?php endif; ?>
      </div>
      <p class="receipt-title"><?= e($title ?? __('receipt.title_payment_sent')) ?></p>
      <p class="receipt-amount"><?= e($amountSign ?? '-') ?>$<?= e(number_format($amount, 2)) ?></p>
      <?php /* Controllers pass subVerb as a plain 'from'/'to'/'by' identifier,
           not already-translated text — mapped to a translated word here so
           there's one place to keep in sync rather than three call sites. */ ?>
      <?php
        $receiptSubVerbs = [
          'from' => __('receipt.subverb_from'),
          'to' => __('receipt.subverb_to'),
          'by' => __('receipt.subverb_by'),
        ];
      ?>
      <p class="receipt-sub"><?= e($receiptSubVerbs[$subVerb ?? 'to'] ?? __('receipt.subverb_to')) ?> <?= e($recipientName) ?></p>
    </div>

    <div class="receipt-details">
      <?php foreach ($rows as $row): ?>
        <div class="receipt-row"><span><?= e($row['label']) ?></span><span><?= e($row['value']) ?></span></div>
      <?php endforeach; ?>
    </div>
  </div>

  <?php if ($banner): ?>
    <p class="receipt-banner fade-in d2"><?= e($banner) ?></p>
  <?php endif; ?>

  <div class="receipt-actions fade-in d3">
    <button type="button" class="receipt-action-btn" onclick="downloadReceiptPdf()">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v12m0 0l-4-4m4 4l4-4M4 21h16"/></svg>
      <?= e(__('receipt.download_pdf')) ?>
    </button>
    <button type="button" class="receipt-action-btn" onclick="shareReceipt()">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><path d="M8.6 13.5l6.8 4M15.4 6.5l-6.8 4"/></svg>
      <?= e(__('receipt.share')) ?>
    </button>
  </div>
  <p class="receipt-action-status" id="receiptActionStatus"></p>

  <div class="pay-btn-wrap">
    <a href="<?= e(route('dashboard')) ?>" class="pay-btn"><?= e(__('receipt.done')) ?></a>
  </div>
  <?php /* Hidden when this receipt is opened from History (?from=history) —
       "Send another" only makes sense right after actually sending money,
       not while browsing back through old transactions. See
       TransferController::receipt()/externalReceipt() for where
       $hideSendAnother comes from. */ ?>
  <?php if (!($hideSendAnother ?? false)): ?>
    <div class="pay-btn-wrap" style="margin-top:10px;">
      <a href="<?= e(route('send')) ?>" class="pay-btn" style="background:transparent; color:var(--ink); border:1px solid var(--ink);"><?= e(__('receipt.send_another')) ?></a>
    </div>
  <?php endif; ?>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
  window.LedgerReceiptConfig = {
    filename: 'ledger-receipt-<?= e($reference) ?>.pdf',
    reference: '<?= e($reference) ?>',
    i18n: {
      sharePrefix: <?= json_encode(__('receipt.share_text_prefix')) ?>,
      shareTitle: <?= json_encode(__('receipt.share_title')) ?>,
      preparingPdf: <?= json_encode(__('receipt.js_preparing_pdf')) ?>,
      pdfError: <?= json_encode(__('receipt.js_pdf_error')) ?>,
      preparingShare: <?= json_encode(__('receipt.js_preparing_share')) ?>,
      shareError: <?= json_encode(__('receipt.js_share_error')) ?>,
      shareUnsupported: <?= json_encode(__('receipt.js_share_unsupported')) ?>,
    },
  };
</script>
@endsection
