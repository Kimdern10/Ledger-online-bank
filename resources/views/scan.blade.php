@extends('layouts.app')

@section('content')
<?php
    $user = auth()->user();
    $bankName = 'Ledger Federal Credit Union';
    // Same "checking first if they have both" rule receive.blade.php uses
    // for its QR code, so the two pages point at the same real account.
    $primaryAccountNumber = $user->checking_account_number ?: $user->savings_account_number;
    $accountLabel = $user->checking_account_number ? 'Checking' : ($user->savings_account_number ? 'Savings' : 'Account');
?>
<div class="send-wrap">

  <div class="page-header fade-in d1">
    <a href="{{ url()->previous() }}" class="icon-btn">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
    </a>
    <h1>{{ __('scan.title') }}</h1>
    <a href="{{ route('dashboard') }}" class="icon-btn">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><path d="M3 11l9-8 9 8"/><path d="M5 10v10h14V10"/></svg>
    </a>
  </div>

  <div class="segmented fade-in d2" style="margin-bottom:20px;">
    <button class="seg-btn active" id="segScan" onclick="setScanMode('scan')" type="button">
      <svg viewBox="0 0 24 24" fill="none"><rect x="4" y="4" width="7" height="7"/><rect x="13" y="4" width="7" height="7"/><rect x="4" y="13" width="7" height="7"/><path d="M14 15h5M16.5 12.5v5"/></svg>
      {{ __('scan.scan_to_pay') }}
    </button>
    <button class="seg-btn" id="segMyQr" onclick="setScanMode('myqr')" type="button">
      <svg viewBox="0 0 24 24" fill="none"><path d="M12 5v14M5 12l7 7 7-7"/></svg>
      {{ __('scan.my_qr_code') }}
    </button>
  </div>

  <!-- ============ scan panel ============ -->
  <div id="scanPanel" class="fade-in d3">
    <div class="scan-viewport">
      <video id="scanVideo" playsinline autoplay muted></video>
      <div class="scan-frame">
        <div class="scan-corner tl"></div>
        <div class="scan-corner tr"></div>
        <div class="scan-corner bl"></div>
        <div class="scan-corner br"></div>
      </div>
    </div>
    <p class="scan-status" id="scanStatus">{{ __('scan.scan_status_default') }}</p>
    <canvas id="scanCanvas" style="display:none;"></canvas>

    <div class="scan-fallback">
      <input type="text" class="text-input" id="manualCodeInput" placeholder="{{ __('scan.manual_code_placeholder') }}">
      <button type="button" class="pay-btn" onclick="submitManualCode()">{{ __('scan.go_button') }}</button>
    </div>
  </div>

  <!-- ============ my QR panel ============ -->
  <div id="myQrPanel" class="fade-in d3" style="display:none;">
    <div class="send-card">
      <p class="ledger-label" style="text-align:center; margin-bottom:14px;">{{ __('scan.show_this_to_get_paid') }}</p>
      <div class="qr-box">
        <div id="qrcode"></div>
      </div>
      <p class="qr-caption">{{ $user->name }} · {{ $bankName }} · {{ $accountLabel }} •••• {{ substr((string) $primaryAccountNumber, -4) }}</p>
    </div>
  </div>

</div>

<div class="ledger-toast" id="ledgerToast">
  <svg viewBox="0 0 24 24" fill="none"><path d="M20 6L9 17l-5-5"/></svg>
  <span id="toastText">{{ __('scan.toast_code_recognized') }}</span>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jsqr/1.4.0/jsQR.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
  window.LedgerScanConfig = {
    sendLookupUrl: "{{ route('send.lookup') }}",
    sendUrl: "{{ route('send') }}",
    qrText: 'ledger://pay?name={{ urlencode($user->name) }}&account={{ $primaryAccountNumber }}&bank={{ urlencode($bankName) }}',
    i18n: {
      scanStatusDefault: @json(__('scan.scan_status_default')),
      cameraUnavailable: @json(__('scan.camera_unavailable')),
      lookingUpAccount: @json(__('scan.looking_up_account')),
      accountFoundOpeningSend: @json(__('scan.account_found_opening_send')),
      noLedgerAccountFound: @json(__('scan.no_ledger_account_found')),
      couldNotLookUp: @json(__('scan.could_not_look_up')),
    },
  };
</script>
@endsection
