@extends('layouts.app')

@section('content')
@php
    $user = auth()->user();

    // Which account number(s) to show is driven by what was picked on
    // step 5 of onboarding ("Account Type": checking / savings / both) —
    // see onboarding.blade.php and Profile::booted(). Falls back to
    // showing both only if account_type somehow isn't set.
    $accountType = strtolower($user->profile->account_type ?? 'both');
    $showChecking = in_array($accountType, ['checking', 'both'], true);
    $showSavings = in_array($accountType, ['savings', 'both'], true);

    $checkingAccountNumber = $user->checking_account_number ?? '';
    $savingsAccountNumber = $user->savings_account_number ?? '';
    // groups the raw digits for readability, e.g. "1234 5678 90"
    $formattedCheckingAccountNumber = trim(chunk_split($checkingAccountNumber, 4, ' '));
    $formattedSavingsAccountNumber = trim(chunk_split($savingsAccountNumber, 4, ' '));
    $bankName = 'Ledger Federal Credit Union';
    // Whichever one the user actually has is what the QR code points to —
    // checking first if they have both.
    $primaryAccountNumber = $checkingAccountNumber ?: $savingsAccountNumber;
@endphp
<div class="send-wrap">

  <div class="page-header fade-in d1">
    <a href="{{ url()->previous() }}" class="icon-btn">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
    </a>
    <h1>{{ __('receive.title') }}</h1>
    <a href="{{ route('dashboard') }}" class="icon-btn">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><path d="M3 11l9-8 9 8"/><path d="M5 10v10h14V10"/></svg>
    </a>
  </div>

  <div class="send-card fade-in d2">

    <div class="acct-detail name">
      <p class="ad-label">{{ __('receive.account_name') }}</p>
      <div class="ad-row">
        <span class="ad-value" id="valName">{{ $user->name }}</span>
        <button type="button" class="copy-icon-btn" onclick="copyField('valName', receiveI18n.accountName)">
          <svg viewBox="0 0 24 24" fill="none"><rect x="9" y="9" width="11" height="11" rx="2"/><path d="M5 15V5a2 2 0 0 1 2-2h10"/></svg>
        </button>
      </div>
    </div>

    <div class="acct-divider"></div>

    @if($showChecking)
    <div class="acct-detail">
      <p class="ad-label">{{ __('receive.checking_account_number') }}</p>
      <div class="ad-row">
        <span class="ad-value" id="valChecking">{{ $formattedCheckingAccountNumber }}</span>
        <button type="button" class="copy-icon-btn" onclick="copyField('valChecking', receiveI18n.checkingAccountNumber)">
          <svg viewBox="0 0 24 24" fill="none"><rect x="9" y="9" width="11" height="11" rx="2"/><path d="M5 15V5a2 2 0 0 1 2-2h10"/></svg>
        </button>
      </div>
    </div>
    @endif

    @if($showSavings)
    <div class="acct-detail">
      <p class="ad-label">{{ __('receive.savings_account_number') }}</p>
      <div class="ad-row">
        <span class="ad-value" id="valSavings">{{ $formattedSavingsAccountNumber }}</span>
        <button type="button" class="copy-icon-btn" onclick="copyField('valSavings', receiveI18n.savingsAccountNumber)">
          <svg viewBox="0 0 24 24" fill="none"><rect x="9" y="9" width="11" height="11" rx="2"/><path d="M5 15V5a2 2 0 0 1 2-2h10"/></svg>
        </button>
      </div>
    </div>
    @endif

    <div class="acct-detail">
      <p class="ad-label">{{ __('receive.bank_name') }}</p>
      <div class="ad-row">
        <span class="ad-value" id="valBank">{{ $bankName }}</span>
        <button type="button" class="copy-icon-btn" onclick="copyField('valBank', receiveI18n.bankName)">
          <svg viewBox="0 0 24 24" fill="none"><rect x="9" y="9" width="11" height="11" rx="2"/><path d="M5 15V5a2 2 0 0 1 2-2h10"/></svg>
        </button>
      </div>
    </div>

    <div class="qr-box">
      <div id="qrcode"></div>
    </div>
    <p class="qr-caption">{{ __('receive.qr_caption', ['name' => $user->name]) }}</p>
  </div>

  <div class="btn-row fade-in d3">
    <button type="button" class="btn-outline" onclick="copyAllDetails()">
      <svg viewBox="0 0 24 24" fill="none"><rect x="9" y="9" width="11" height="11" rx="2"/><path d="M5 15V5a2 2 0 0 1 2-2h10"/></svg>
      {{ __('receive.copy_details') }}
    </button>
    <button type="button" class="pay-btn" onclick="shareDetails()">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" style="margin-right:6px;"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><path d="M8.6 13.5l6.8 4M15.4 6.5l-6.8 4"/></svg>
      {{ __('receive.share') }}
    </button>
  </div>

</div>

<div class="ledger-toast" id="ledgerToast">
  <svg viewBox="0 0 24 24" fill="none"><path d="M20 6L9 17l-5-5"/></svg>
  <span id="toastText">{{ __('receive.copied') }}</span>
</div>

@php
    // Same reasoning as send.blade.php's $sendI18n — every JS-side
    // user-facing string here is pre-translated server-side, built as a
    // plain array first, then handed to @json() below.
    $receiveI18n = [
        'accountName' => __('receive.account_name'),
        'checkingAccountNumber' => __('receive.checking_account_number'),
        'savingsAccountNumber' => __('receive.savings_account_number'),
        'bankName' => __('receive.bank_name'),
        'fieldCopiedTemplate' => __('receive.field_copied', ['label' => '__LABEL__']),
        'unableToCopy' => __('receive.unable_to_copy'),
        'accountDetailsCopied' => __('receive.account_details_copied'),
        'detailsCopiedToShare' => __('receive.details_copied_to_share'),
        'sendMoneyToTemplate' => __('receive.send_money_to', ['name' => '__NAME__']),
        'myAccountDetails' => __('receive.my_account_details'),
    ];
@endphp
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
  const receiveI18n = @json($receiveI18n);

  document.addEventListener('DOMContentLoaded', function(){
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

  function showToast(msg){
    const toast = document.getElementById("ledgerToast");
    document.getElementById('toastText').textContent = msg;
    toast.classList.add('show');
    clearTimeout(window._toastTimer);
    window._toastTimer = setTimeout(()=> toast.classList.remove('show'), 2000);
  }

  function copyField(id, label){
    const text = document.getElementById(id).textContent.trim();
    navigator.clipboard?.writeText(text).then(()=> showToast(receiveI18n.fieldCopiedTemplate.replace('__LABEL__', label)))
      .catch(()=> showToast(receiveI18n.unableToCopy));
  }

  // Only pulls in a Checking / Savings line if that row actually exists on
  // the page — since receive.blade.php now only renders the row(s)
  // matching the user's chosen account type, this stays correct no matter
  // which one (or both) that turns out to be.
  function buildDetailsText(firstLine){
    const lines = [firstLine];
    const checking = document.getElementById('valChecking');
    const savings = document.getElementById('valSavings');
    if(checking) lines.push(receiveI18n.checkingAccountNumber + ': ' + checking.textContent.trim());
    if(savings) lines.push(receiveI18n.savingsAccountNumber + ': ' + savings.textContent.trim());
    lines.push(receiveI18n.bankName + ': ' + document.getElementById('valBank').textContent.trim());
    return lines.join('\n');
  }

  function copyAllDetails(){
    const text = buildDetailsText(receiveI18n.accountName + ': ' + document.getElementById('valName').textContent.trim());
    navigator.clipboard?.writeText(text).then(()=> showToast(receiveI18n.accountDetailsCopied))
      .catch(()=> showToast(receiveI18n.unableToCopy));
  }

  function shareDetails(){
    const text = buildDetailsText(receiveI18n.sendMoneyToTemplate.replace('__NAME__', document.getElementById('valName').textContent.trim()));

    if(navigator.share){
      navigator.share({ title: receiveI18n.myAccountDetails, text: text }).catch(()=>{});
    } else {
      navigator.clipboard?.writeText(text).then(()=> showToast(receiveI18n.detailsCopiedToShare));
    }
  }
</script>
@endsection
