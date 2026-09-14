@extends('layouts.app')

@section('content')
<div class="receipt-wrap">
  <div class="page-header fade-in d1">
    <a href="{{ route('dashboard') }}" class="icon-btn">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6L6 18M6 6l12 12"/></svg>
    </a>
    <h1>{{ __('receipt.page_title') }}</h1>
    <span style="width:40px; display:inline-block;"></span>
  </div>

  <div class="receipt-card fade-in d2" id="receiptCard">
    {{-- Proper name of the credit union itself — deliberately not run
         through __(), same as everywhere else this appears in the app. --}}
    <p class="receipt-letterhead">Ledger Federal Credit Union</p>

    @php $receiptIcon = $icon ?? 'check'; @endphp
    <div class="receipt-status">
      <div class="receipt-check receipt-check-{{ $receiptIcon }}" id="receiptCheck">
        @if($receiptIcon === 'clock')
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg>
        @elseif($receiptIcon === 'x')
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6L6 18M6 6l12 12"/></svg>
        @else
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
        @endif
      </div>
      <p class="receipt-title">{{ $title ?? __('receipt.title_payment_sent') }}</p>
      <p class="receipt-amount">{{ $amountSign ?? '-' }}${{ number_format($amount, 2) }}</p>
      {{-- Controllers pass subVerb as a plain 'from'/'to'/'by' identifier,
           not already-translated text — mapped to a translated word here so
           there's one place to keep in sync rather than three call sites. --}}
      @php
        $receiptSubVerbs = [
          'from' => __('receipt.subverb_from'),
          'to' => __('receipt.subverb_to'),
          'by' => __('receipt.subverb_by'),
        ];
      @endphp
      <p class="receipt-sub">{{ $receiptSubVerbs[$subVerb ?? 'to'] ?? __('receipt.subverb_to') }} {{ $recipientName }}</p>
    </div>

    <div class="receipt-details">
      @foreach($rows as $row)
        <div class="receipt-row"><span>{{ $row['label'] }}</span><span>{{ $row['value'] }}</span></div>
      @endforeach
    </div>
  </div>

  @if($banner)
    <p class="receipt-banner fade-in d2">{{ $banner }}</p>
  @endif

  <div class="receipt-actions fade-in d3">
    <button type="button" class="receipt-action-btn" onclick="downloadReceiptPdf()">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v12m0 0l-4-4m4 4l4-4M4 21h16"/></svg>
      {{ __('receipt.download_pdf') }}
    </button>
    <button type="button" class="receipt-action-btn" onclick="shareReceipt()">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><path d="M8.6 13.5l6.8 4M15.4 6.5l-6.8 4"/></svg>
      {{ __('receipt.share') }}
    </button>
  </div>
  <p class="receipt-action-status" id="receiptActionStatus"></p>

  <div class="pay-btn-wrap">
    <a href="{{ route('dashboard') }}" class="pay-btn">{{ __('receipt.done') }}</a>
  </div>
  {{-- Hidden when this receipt is opened from History (?from=history) —
       "Send another" only makes sense right after actually sending money,
       not while browsing back through old transactions. See
       TransferController::receipt()/externalReceipt() for where
       $hideSendAnother comes from. --}}
  @unless($hideSendAnother ?? false)
    <div class="pay-btn-wrap" style="margin-top:10px;">
      <a href="{{ route('send') }}" class="pay-btn" style="background:transparent; color:var(--ink); border:1px solid var(--ink);">{{ __('receipt.send_another') }}</a>
    </div>
  @endunless
</div>

<style>
  .receipt-wrap{ max-width:480px; margin:0 auto; padding:0 4px 40px; }
  .receipt-card{
    background:#fff; border:1px solid rgba(0,0,0,0.08); border-radius:18px; padding:28px 22px; margin:18px 0;
    box-shadow:0 1px 3px rgba(0,0,0,0.04);
  }
  .receipt-status{ text-align:center; margin-bottom:22px; }
  .receipt-check{
    width:56px; height:56px; border-radius:50%; background:rgba(30,110,80,0.12); color:var(--ink);
    display:flex; align-items:center; justify-content:center; margin:0 auto 14px;
  }
  .receipt-check svg{ width:26px; height:26px; }
  /* Pending/cancelled/failed receipts (a scheduled transfer viewed from
     History) swap the green success check for one of these instead — see
     TransferController::receipt()/externalReceipt() for when each is
     used. Declared after the base .receipt-check rule above so they win
     on equal specificity. */
  .receipt-check-clock{ background:rgba(201,162,75,0.18); color:#9A6B10; }
  .receipt-check-x{ background:rgba(193,80,60,0.12); color:#C1503C; }
  .receipt-title{ margin:0 0 6px; font-size:12.5px; font-weight:700; color:var(--text-3); text-transform:uppercase; letter-spacing:.06em; }
  .receipt-amount{ margin:0 0 4px; font-size:32px; font-weight:800; color:var(--ink); }
  .receipt-sub{ margin:0; font-size:14px; color:var(--text-3); }
  .receipt-details{ border-top:1px solid rgba(0,0,0,0.08); padding-top:16px; }
  .receipt-row{ display:flex; justify-content:space-between; gap:14px; padding:8px 0; font-size:13.5px; }
  .receipt-row span:first-child{ color:var(--text-3); }
  .receipt-row span:last-child{ font-weight:600; text-align:right; }

  .receipt-letterhead{
    margin:0 0 18px; text-align:center; font-size:11px; font-weight:700; text-transform:uppercase;
    letter-spacing:.08em; color:var(--text-3);
  }

  .receipt-banner{
    background:#eef4fb; color:#2b5a86; border:1px solid #cfe0f2; border-radius:12px;
    padding:11px 14px; font-size:12.5px; font-weight:500; line-height:1.45; margin:0 0 18px;
  }

  /* Pops the checkmark in on page load — this page loads right after the
     "Processing your transfer…" overlay on send.blade.php, so the reveal
     here is what makes that feel like it lands on a result rather than
     just being a different page. */
  .receipt-check{ animation:receiptCheckIn .45s cubic-bezier(.34,1.56,.64,1) both; animation-delay:.1s; }
  @keyframes receiptCheckIn{
    0%{ transform:scale(0); opacity:0; }
    60%{ transform:scale(1.12); opacity:1; }
    100%{ transform:scale(1); opacity:1; }
  }
  @media (prefers-reduced-motion:reduce){ .receipt-check{ animation:none; } }

  .receipt-actions{ display:flex; gap:10px; margin:6px 0 14px; }
  .receipt-action-btn{
    flex:1; display:flex; align-items:center; justify-content:center; gap:7px; border:1px solid rgba(0,0,0,0.12);
    background:#fff; border-radius:12px; padding:11px 10px; font-size:13px; font-weight:600; font-family:inherit;
    color:var(--ink); cursor:pointer;
  }
  .receipt-action-btn svg{ width:16px; height:16px; }
  .receipt-action-btn:hover{ background:rgba(30,110,80,0.06); }
  .receipt-action-btn[disabled]{ opacity:0.6; cursor:default; }
  .receipt-action-status{ text-align:center; font-size:12px; color:var(--text-3); min-height:16px; margin:0 0 8px; }

  /* html2pdf renders #receiptCard by cloning it off-screen — this keeps
     that clone from ever flashing visible if a browser is slow to move it. */
  .html2pdf__overlay{ opacity:0 !important; }
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
  const RECEIPT_FILENAME = 'ledger-receipt-{{ $reference }}.pdf';

  // Translated strings the JS below needs — resolved server-side with
  // __() and handed over as JSON so this stays a plain .blade.php script
  // block rather than needing a build step. "Ledger" itself stays fixed
  // (brand name); only the surrounding words are translated.
  const RECEIPT_I18N = {
    sharePrefix: @json(__('receipt.share_text_prefix')),
    shareTitle: @json(__('receipt.share_title')),
    preparingPdf: @json(__('receipt.js_preparing_pdf')),
    pdfError: @json(__('receipt.js_pdf_error')),
    preparingShare: @json(__('receipt.js_preparing_share')),
    shareError: @json(__('receipt.js_share_error')),
    shareUnsupported: @json(__('receipt.js_share_unsupported')),
  };

  function receiptPdfOptions(){
    return {
      margin: 10,
      filename: RECEIPT_FILENAME,
      image: { type: 'jpeg', quality: 0.98 },
      html2canvas: { scale: 2, backgroundColor: '#ffffff' },
      jsPDF: { unit: 'mm', format: 'a5', orientation: 'portrait' },
    };
  }

  function setReceiptStatus(message){
    document.getElementById('receiptActionStatus').textContent = message || '';
  }

  function downloadReceiptPdf(){
    setReceiptStatus(RECEIPT_I18N.preparingPdf);
    const card = document.getElementById('receiptCard');

    html2pdf().set(receiptPdfOptions()).from(card).save().then(function(){
      setReceiptStatus('');
    }).catch(function(){
      setReceiptStatus(RECEIPT_I18N.pdfError);
    });
  }

  function shareReceipt(){
    const card = document.getElementById('receiptCard');
    const shareText = RECEIPT_I18N.sharePrefix + ': {{ $reference }}';

    // Prefer sharing the actual PDF file when the browser supports the
    // Web Share API with files (most mobile browsers). Desktop Chrome and
    // most desktop browsers don't support file sharing this way, so this
    // falls back step by step rather than just failing silently.
    if (navigator.canShare && window.File) {
      setReceiptStatus(RECEIPT_I18N.preparingShare);

      html2pdf().set(receiptPdfOptions()).from(card).outputPdf('blob').then(function(blob){
        const file = new File([blob], RECEIPT_FILENAME, { type: 'application/pdf' });

        if (navigator.canShare({ files: [file] })) {
          return navigator.share({
            files: [file],
            title: RECEIPT_I18N.shareTitle,
            text: shareText,
          });
        }

        return shareTextFallback(shareText);
      }).then(function(){
        setReceiptStatus('');
      }).catch(function(err){
        if (err && err.name === 'AbortError') { setReceiptStatus(''); return; }
        setReceiptStatus(RECEIPT_I18N.shareError);
      });

      return;
    }

    shareTextFallback(shareText).then(function(){
      setReceiptStatus('');
    }).catch(function(err){
      if (err && err.name === 'AbortError') { setReceiptStatus(''); return; }
      setReceiptStatus(RECEIPT_I18N.shareUnsupported);
    });
  }

  function shareTextFallback(text){
    if (navigator.share) {
      return navigator.share({ title: RECEIPT_I18N.shareTitle, text: text });
    }

    return Promise.reject(new Error('share_unsupported'));
  }
</script>
@endsection
