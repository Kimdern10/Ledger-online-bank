@extends('layouts.app')

@section('content')
@php
    $user = auth()->user();
    $primaryAccountNumber = $user->checking_account_number ?: $user->savings_account_number;
    $accountLabel = $user->checking_account_number ? 'Checking' : ($user->savings_account_number ? 'Savings' : 'Account');

    // Real linked accounts — see LinkedAccountController. Top Up only ever
    // offers accounts the user has actually linked on the Link Account page.
    $bankAccounts = $user->linkedAccounts()->where('type', 'bank')->latest()->get();
    $cardAccounts = $user->linkedAccounts()->where('type', 'card')->latest()->get();
@endphp
<div class="send-wrap">

  <div class="page-header fade-in d1">
    <a href="{{ url()->previous() }}" class="icon-btn">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
    </a>
    <h1>{{ __('topup.title') }}</h1>
    <a href="{{ route('dashboard') }}" class="icon-btn">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><path d="M3 11l9-8 9 8"/><path d="M5 10v10h14V10"/></svg>
    </a>
  </div>

  @if(session('topUpError'))
    <div class="send-alert fade-in d1">{{ session('topUpError') }}</div>
  @endif
  {{-- No transaction PIN created yet — see RequiresTransactionPin. --}}
  @if(session('pinRequired'))
    <div class="send-alert fade-in d1">
      {{ __('topup.pin_required_prefix') }}
      <a href="{{ route('setting.transaction-pin') }}" style="text-decoration:underline; color:inherit;">{{ __('topup.pin_required_link') }}</a>.
    </div>
  @endif
  @if($errors->any())
    <div class="send-alert fade-in d1">
      <ul style="margin:0; padding-left:18px;">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <p class="section-label">{{ __('topup.to') }}</p>
  <div class="from-row fade-in d2">
    <div class="bank-mark">L</div>
    <div class="bank-info">
      <p class="name">Ledger Federal Credit Union</p>
      <p class="meta">{{ $accountLabel }} •••• {{ substr((string) $primaryAccountNumber, -4) }}</p>
    </div>
    <div class="bank-balance">
      <p class="amt">${{ number_format((float) $user->balance, 2) }}</p>
      <p class="tag">{{ __('topup.current_balance') }}</p>
    </div>
  </div>

  <form method="POST" action="{{ route('top-up.store') }}" id="topUpForm">
    @csrf
    <input type="hidden" name="source_type" id="sourceTypeInput" value="bank">
    <input type="hidden" name="linked_account_id" id="linkedAccountIdInput" value="{{ optional($bankAccounts->first())->id }}">
    <input type="hidden" name="amount" id="amountHidden" value="0">

    <p class="section-label">{{ __('topup.top_up_from') }}</p>
    <div class="fade-in d2">
      <div class="segmented" style="margin-bottom:14px;">
        <button class="seg-btn active" id="segFromBank" onclick="setTopUpMode('bank')" type="button">
          <svg viewBox="0 0 24 24" fill="none"><path d="M3 21h18M4 21V10l8-6 8 6v11M9 21v-6h6v6"/></svg>
          {{ __('topup.linked_bank') }}
        </button>
        <button class="seg-btn" id="segFromCard" onclick="setTopUpMode('card')" type="button">
          <svg viewBox="0 0 24 24" fill="none"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>
          {{ __('topup.debit_credit_card') }}
        </button>
      </div>

      <div id="topUpBankPanel">
        @if($bankAccounts->isEmpty())
          <p style="padding:10px 4px; opacity:0.65; font-size:13px;">
            {{ __('topup.no_linked_bank') }} <a href="{{ route('link-account') }}" style="color:inherit; text-decoration:underline;">{{ __('topup.link_one_first') }}</a>.
          </p>
        @else
          <div class="linked-chips">
            @foreach($bankAccounts as $index => $account)
              <div class="linked-chip {{ $index === 0 ? 'selected' : '' }}" data-account-id="{{ $account->id }}" onclick="selectSourceChip(this, 'bank', {{ $account->id }})">
                <span class="chip-mark" style="background:var(--ink);">{{ substr($account->displayLabel(), 0, 1) }}</span> {{ $account->displayLabel() }} {{ $account->detailLabel() }}
              </div>
            @endforeach
          </div>
          <p class="receive-note instant">
            <svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
            {{ __('topup.bank_note') }}
          </p>
        @endif
      </div>

      <div id="topUpCardPanel" style="display:none;">
        @if($cardAccounts->isEmpty())
          <p style="padding:10px 4px; opacity:0.65; font-size:13px;">
            {{ __('topup.no_linked_card') }} <a href="{{ route('link-account') }}" style="color:inherit; text-decoration:underline;">{{ __('topup.link_one_first') }}</a>.
          </p>
        @else
          <div class="linked-chips">
            @foreach($cardAccounts as $index => $account)
              <div class="linked-chip {{ $index === 0 ? 'selected' : '' }}" data-account-id="{{ $account->id }}" onclick="selectSourceChip(this, 'card', {{ $account->id }})">
                <span class="chip-mark" style="background:var(--ink);">{{ substr($account->displayLabel(), 0, 1) }}</span> {{ $account->displayLabel() }} {{ $account->detailLabel() }}
              </div>
            @endforeach
          </div>
          <p class="receive-note">
            <svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
            {{ __('topup.card_note') }}
          </p>
        @endif
      </div>
    </div>

    <p class="section-label">{{ __('topup.amount') }}</p>
    <div class="amount-card fade-in d3">
      <div class="amount-field">
        <span class="cur">$</span>
        <input type="text" id="amountInput" inputmode="decimal" value="100" aria-label="{{ __('topup.amount_aria_label') }}">
      </div>
      <div class="amount-underline"></div>
      <div class="quick-amounts">
        <button type="button" class="quick-amt-btn active" onclick="setAmount(this,'100')">$100</button>
        <button type="button" class="quick-amt-btn" onclick="setAmount(this,'250')">$250</button>
        <button type="button" class="quick-amt-btn" onclick="setAmount(this,'500')">$500</button>
        <button type="button" class="quick-amt-btn" onclick="setAmount(this,'1,000')">$1,000</button>
      </div>

      <div class="fee-breakdown" id="feeBreakdown">
        <div class="fee-row">
          <span>{{ __('topup.fee_amount') }}</span>
          <span id="feeAmountDisplay">$100.00</span>
        </div>
        <div class="fee-row">
          <span>{{ __('topup.fee_card_processing') }}</span>
          <span id="feeFeeDisplay">$2.90</span>
        </div>
        <div class="fee-row total">
          <span>{{ __('topup.fee_total_charged') }}</span>
          <span id="feeTotalDisplay">$102.90</span>
        </div>
      </div>
    </div>

    <div class="pay-btn-wrap fade-in d3">
      <button type="submit" class="pay-btn" id="topUpSubmitBtn" {{ $bankAccounts->isEmpty() && $cardAccounts->isEmpty() ? 'disabled' : '' }}>{{ __('topup.add_money_btn') }}</button>
    </div>
  </form>

</div>

<div class="processing-overlay" id="processingOverlay">
  <div class="processing-spinner"></div>
  <p class="processing-text">{{ __('topup.processing') }}</p>
  <p class="processing-sub">{{ __('topup.processing_sub') }}</p>
</div>

{{-- ============ transaction PIN confirmation ============
     See send.blade.php's copy of this same modal for the full explanation. --}}
<div class="sheet-overlay" id="pinModalOverlay" onclick="closePinModal(event)">
  <div class="sheet" onclick="event.stopPropagation()" style="max-width:340px; margin:0 auto;">
    <div class="sheet-handle"></div>
    <div class="sheet-head">
      <h4>{{ __('topup.pin_modal_title') }}</h4>
      <div class="icon-btn" onclick="closePinModal()">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8"><path d="M18 6L6 18M6 6l12 12"/></svg>
      </div>
    </div>
    <div style="padding:4px 20px 22px;">
      <p style="margin:0 0 14px; font-size:13px; color:var(--text-3);">{{ __('topup.pin_modal_body') }}</p>
      <input type="password" class="text-input" id="pinModalInput" inputmode="numeric" maxlength="4" placeholder="••••" style="text-align:center; font-size:22px; letter-spacing:10px;" autocomplete="off">
      <p id="pinModalError" style="display:none; color:#B4121B; font-size:12.5px; margin:8px 0 0;"></p>
      <button type="button" class="pay-btn" style="margin-top:16px; width:100%;" onclick="confirmPin()">{{ __('topup.pin_confirm') }}</button>
    </div>
  </div>
</div>

<style>
  .processing-overlay{
    display:none; position:fixed; inset:0; background:rgba(255,255,255,0.97); z-index:200;
    flex-direction:column; align-items:center; justify-content:center; text-align:center; padding:20px;
  }
  .processing-overlay.show{ display:flex; }
  .processing-spinner{
    width:46px; height:46px; border-radius:50%; border:4px solid rgba(30,110,80,0.15);
    border-top-color:var(--ink); animation:processingSpin .8s linear infinite; margin-bottom:18px;
  }
  .processing-text{ margin:0 0 4px; font-size:15px; font-weight:700; color:var(--ink); }
  .processing-sub{ margin:0; font-size:12.5px; color:var(--text-3); }
  @keyframes processingSpin{ to{ transform:rotate(360deg); } }
  @media (prefers-reduced-motion:reduce){ .processing-spinner{ animation:none; border-top-color:rgba(30,110,80,0.15); } }
</style>

<script>
  const CARD_TOPUP_FEE_RATE = 0.029;

  function parseAmount(raw){
    const n = parseFloat(String(raw).replace(/[^0-9.]/g, ''));
    return isNaN(n) ? 0 : n;
  }
  function formatUsd(n){
    return '$' + n.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2});
  }

  function updateFeeBreakdown(){
    const isCard = document.getElementById('segFromCard').classList.contains('active');
    const breakdown = document.getElementById('feeBreakdown');
    breakdown.classList.toggle('show', isCard);

    const amount = parseAmount(document.getElementById('amountInput').value);
    document.getElementById('amountHidden').value = amount;

    if(!isCard) return;

    const fee = amount * CARD_TOPUP_FEE_RATE;
    const total = amount + fee;
    document.getElementById('feeAmountDisplay').textContent = formatUsd(amount);
    document.getElementById('feeFeeDisplay').textContent = formatUsd(fee);
    document.getElementById('feeTotalDisplay').textContent = formatUsd(total);
  }

  function firstChipId(panelId){
    const chip = document.querySelector('#' + panelId + ' .linked-chip');
    return chip ? chip.dataset.accountId : '';
  }

  function setTopUpMode(mode){
    document.getElementById('segFromBank').classList.toggle('active', mode === 'bank');
    document.getElementById('segFromCard').classList.toggle('active', mode === 'card');
    document.getElementById('topUpBankPanel').style.display = mode === 'bank' ? 'block' : 'none';
    document.getElementById('topUpCardPanel').style.display = mode === 'card' ? 'block' : 'none';
    document.getElementById('sourceTypeInput').value = mode;
    document.getElementById('linkedAccountIdInput').value = firstChipId(mode === 'bank' ? 'topUpBankPanel' : 'topUpCardPanel');
    updateFeeBreakdown();
  }

  function selectSourceChip(el, type, accountId){
    el.parentElement.querySelectorAll('.linked-chip').forEach(c => c.classList.remove('selected'));
    el.classList.add('selected');
    document.getElementById('sourceTypeInput').value = type;
    document.getElementById('linkedAccountIdInput').value = accountId;
  }

  function setAmount(el, value){
    document.querySelectorAll('.quick-amt-btn').forEach(b => b.classList.remove('active'));
    el.classList.add('active');
    document.getElementById('amountInput').value = value;
    updateFeeBreakdown();
  }

  document.addEventListener('DOMContentLoaded', function(){
    document.getElementById('amountInput').addEventListener('input', updateFeeBreakdown);
    updateFeeBreakdown();

    document.getElementById('topUpForm').addEventListener('submit', function(e){
      e.preventDefault();

      if(!document.getElementById('linkedAccountIdInput').value){
        alert(@json(__('topup.link_account_first_alert')));
        return;
      }

      openPinModal(this);
    });
  });

  // ---------- transaction PIN modal (see send.blade.php for the full explanation) ----------
  let pinModalTargetForm = null;

  function openPinModal(form){
    pinModalTargetForm = form;
    document.getElementById('pinModalInput').value = '';
    document.getElementById('pinModalError').style.display = 'none';
    document.getElementById('pinModalOverlay').classList.add('open');
    setTimeout(function(){ document.getElementById('pinModalInput').focus(); }, 150);
  }

  function closePinModal(e){
    if(e) e.stopPropagation();
    document.getElementById('pinModalOverlay').classList.remove('open');
    pinModalTargetForm = null;
  }

  function confirmPin(){
    const pin = document.getElementById('pinModalInput').value.trim();
    if(!/^\d{4}$/.test(pin)){
      const err = document.getElementById('pinModalError');
      err.textContent = @json(__('topup.pin_error'));
      err.style.display = 'block';
      return;
    }

    const form = pinModalTargetForm;
    if(!form) return;

    let pinField = form.querySelector('input[name="transaction_pin"]');
    if(!pinField){
      pinField = document.createElement('input');
      pinField.type = 'hidden';
      pinField.name = 'transaction_pin';
      form.appendChild(pinField);
    }
    pinField.value = pin;

    document.getElementById('pinModalOverlay').classList.remove('open');
    document.getElementById('processingOverlay').classList.add('show');
    document.querySelectorAll('#topUpForm button[type="submit"]').forEach(function(btn){
      btn.disabled = true;
    });
    form.submit();
  }
</script>
@endsection
