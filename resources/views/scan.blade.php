@extends('layouts.app')

@section('content')
@php
    $user = auth()->user();
    $bankName = 'Ledger Federal Credit Union';
    // Same "checking first if they have both" rule receive.blade.php uses
    // for its QR code, so the two pages point at the same real account.
    $primaryAccountNumber = $user->checking_account_number ?: $user->savings_account_number;
    $accountLabel = $user->checking_account_number ? 'Checking' : ($user->savings_account_number ? 'Savings' : 'Account');
@endphp
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
  let scanning = false;
  let activeStream = null;
  let lookupInFlight = false;

  function setScanMode(mode){
    document.getElementById('segScan').classList.toggle('active', mode === 'scan');
    document.getElementById('segMyQr').classList.toggle('active', mode === 'myqr');
    document.getElementById('scanPanel').style.display = mode === 'scan' ? 'block' : 'none';
    document.getElementById('myQrPanel').style.display = mode === 'myqr' ? 'block' : 'none';

    if(mode === 'scan'){
      startScanner();
    } else {
      stopScanner();
    }
  }

  async function startScanner(){
    if(scanning) return;
    const video = document.getElementById('scanVideo');
    const status = document.getElementById('scanStatus');
    try{
      activeStream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
      video.srcObject = activeStream;
      scanning = true;
      status.textContent = @json(__('scan.scan_status_default'));
      requestAnimationFrame(scanTick);
    } catch(err){
      status.textContent = @json(__('scan.camera_unavailable'));
    }
  }

  function stopScanner(){
    scanning = false;
    if(activeStream){
      activeStream.getTracks().forEach(t => t.stop());
      activeStream = null;
    }
  }

  function scanTick(){
    if(!scanning) return;
    const video = document.getElementById('scanVideo');
    const canvas = document.getElementById('scanCanvas');

    if(video.readyState === video.HAVE_ENOUGH_DATA){
      canvas.width = video.videoWidth;
      canvas.height = video.videoHeight;
      const ctx = canvas.getContext('2d');
      ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
      const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);

      if(window.jsQR){
        const code = jsQR(imageData.data, imageData.width, imageData.height);
        if(code){
          onCodeFound(code.data);
          return;
        }
      }
    }
    requestAnimationFrame(scanTick);
  }

  // A Ledger QR code (see receive.blade.php / the My QR panel above) encodes
  // "ledger://pay?name=...&account=...&bank=...". Manual entry usually gets
  // just the bare account number instead, so this accepts either — if it
  // doesn't parse as that URL scheme, the whole string is treated as the
  // account number as-is.
  function extractAccountNumber(raw){
    const value = (raw || '').trim();
    try {
      const url = new URL(value);
      const account = url.searchParams.get('account');
      if(account) return account;
    } catch (e) {
      // not a URL — fall through and treat it as a bare account number
    }
    return value.replace(/\s+/g, '');
  }

  // Real lookup against the same endpoint Send Money's Ledger tab uses (see
  // TransferController::lookup) — a scanned/typed code only ever leads
  // somewhere real. Nothing is sent from here; this just finds the account
  // and hands off to Send, which still requires its own review + submit.
  function lookupAndGoToSend(accountNumber){
    if(!accountNumber || lookupInFlight) return;
    lookupInFlight = true;
    document.getElementById('scanStatus').textContent = @json(__('scan.looking_up_account'));

    fetch("{{ route('send.lookup') }}?account_number=" + encodeURIComponent(accountNumber), {
      headers: { 'Accept': 'application/json' },
    })
      .then(function(res){
        const contentType = res.headers.get('content-type') || '';
        if(!res.ok || !contentType.includes('application/json')) return { found: false };
        return res.json();
      })
      .then(function(data){
        lookupInFlight = false;
        if(data && data.found){
          showToast(@json(__('scan.account_found_opening_send')));
          document.getElementById('scanStatus').textContent = @json(__('scan.account_found_opening_send'));
          setTimeout(function(){
            window.location.href = "{{ route('send') }}?account=" + encodeURIComponent(accountNumber);
          }, 500);
        } else {
          showToast(@json(__('scan.no_ledger_account_found')));
          document.getElementById('scanStatus').textContent = @json(__('scan.scan_status_default'));
          scanning = true;
          requestAnimationFrame(scanTick);
        }
      })
      .catch(function(){
        lookupInFlight = false;
        showToast(@json(__('scan.could_not_look_up')));
        document.getElementById('scanStatus').textContent = @json(__('scan.scan_status_default'));
        scanning = true;
        requestAnimationFrame(scanTick);
      });
  }

  function onCodeFound(data){
    scanning = false;
    stopScanner();
    lookupAndGoToSend(extractAccountNumber(data));
  }

  function submitManualCode(){
    const raw = document.getElementById('manualCodeInput').value.trim();
    if(!raw) return;
    lookupAndGoToSend(extractAccountNumber(raw));
  }

  function showToast(msg){
    const toast = document.getElementById("ledgerToast");
    document.getElementById('toastText').textContent = msg;
    toast.classList.add('show');
    clearTimeout(window._toastTimer);
    window._toastTimer = setTimeout(()=> toast.classList.remove('show'), 2400);
  }

  document.addEventListener('DOMContentLoaded', function(){
    startScanner();
    if(window.QRCode){
      new QRCode(document.getElementById('qrcode'), {
        text: 'ledger://pay?name={{ urlencode($user->name) }}&account={{ $primaryAccountNumber }}&bank={{ urlencode($bankName) }}',
        width: 148,
        height: 148,
        colorDark: '#10202F',
        colorLight: '#FFFFFF'
      });
    }
  });

  window.addEventListener('beforeunload', stopScanner);
</script>
@endsection
