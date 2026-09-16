@extends('layouts.app')

@section('content')
<?php
    $user = auth()->user();
    $bills = $user->bills()->orderBy('due_date')->get();

    // Same "which account do we actually have" logic as the dashboard and
    // receive-money page: prefer checking, fall back to savings, so this
    // never shows a made-up account that doesn't belong to this user.
    $primaryAccountType = null;
    $primaryAccountNumber = null;
    if (! empty($user->checking_account_number)) {
        $primaryAccountType = __('paybills.account_type_checking');
        $primaryAccountNumber = $user->checking_account_number;
    } elseif (! empty($user->savings_account_number)) {
        $primaryAccountType = __('paybills.account_type_savings');
        $primaryAccountNumber = $user->savings_account_number;
    }
    $primaryAccountLast4 = $primaryAccountNumber ? substr($primaryAccountNumber, -4) : '----';
    $userBalance = (float) ($user->balance ?? 0);

    // Bills can only be added to / paid from a checking account — someone
    // with a savings-only account sees a "checking required" notice below
    // instead of the add/pay UI. BillController::store() enforces this
    // server-side too, so it can't be bypassed by calling the endpoint
    // directly.
    $hasChecking = ! empty($user->checking_account_number);

    // Category -> the SVG paths already used for each known bill type, so
    // a bill someone adds themselves gets the same look as the originals.
    // Anything that doesn't match (i.e. a custom "Other" name) falls back
    // to $defaultBillIcon instead of breaking.
    $billIcons = [
        'Rent / Mortgage' => '<path d="M3 11l9-8 9 8"/><path d="M5 10v10h14V10"/>',
        'Credit Card' => '<rect x="3" y="6" width="18" height="13" rx="2"/><path d="M3 10h18M7 15h4"/>',
        'Medical Insurance' => '<path d="M12 21s-7-4.35-9.5-9A5.5 5.5 0 0 1 12 6a5.5 5.5 0 0 1 9.5 6c-2.5 4.65-9.5 9-9.5 9Z"/>',
        'Taxes' => '<rect x="4" y="3" width="16" height="18" rx="2"/><path d="M8 8h8M8 12h8M8 16h5"/>',
        'Student Loan' => '<path d="M22 10L12 5 2 10l10 5 10-5Z"/><path d="M6 12v5c0 1.5 3 3 6 3s6-1.5 6-3v-5"/>',
    ];
    $defaultBillIcon = '<circle cx="12" cy="12" r="9"/><path d="M9 12h6M12 9v6"/>';
?>
<div class="send-wrap">

  <div class="page-header fade-in d1">
    <a href="{{ url()->previous() }}" class="icon-btn">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
    </a>
    <h1>{{ __('paybills.page_title') }}</h1>
    <a href="{{ route('dashboard') }}" class="icon-btn">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><path d="M3 11l9-8 9 8"/><path d="M5 10v10h14V10"/></svg>
    </a>
  </div>

  <p class="section-label">{{ __('paybills.from_label') }}</p>
  <div class="from-row fade-in d2">
    <div class="bank-mark">L</div>
    <div class="bank-info">
      <p class="name">Ledger Federal Credit Union</p>
      <p class="meta">{{ $primaryAccountType ?? __('paybills.account_fallback') }} •••• {{ $primaryAccountLast4 }}</p>
    </div>
    <div class="bank-balance">
      <p class="amt">${{ number_format($userBalance, 2) }}</p>
      <p class="tag">{{ __('paybills.available') }}</p>
    </div>
  </div>

  @if($hasChecking)
    <div class="bills-section-head fade-in d3">
      <p class="section-label" style="margin:0;">{{ __('paybills.upcoming_bills') }}</p>
      <button type="button" class="add-bill-link" onclick="openAddBillSheet()">{{ __('paybills.add_bill_link') }}</button>
    </div>

    {{-- New users (and anyone who's paid off/removed everything) see this
         instead of a list of bills that were never really theirs. --}}
    <div class="bills-empty-state fade-in d3" id="billsEmptyState" @if($bills->isNotEmpty()) style="display:none;" @endif>
      <div class="bills-empty-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">{!! $defaultBillIcon !!}</svg>
      </div>
      <p class="bills-empty-title">{{ __('paybills.no_bills_yet') }}</p>
      <p class="bills-empty-sub">{{ __('paybills.no_bills_sub') }}</p>
      <button type="button" class="bill-pay-btn" onclick="openAddBillSheet()">{{ __('paybills.add_bill_link') }}</button>
    </div>

    <div class="bill-card fade-in d3" id="billCard" @if($bills->isEmpty()) style="display:none;" @endif>
      <div id="billRows">
        @foreach($bills as $bill)
          <div class="bill-row" data-bill-id="{{ $bill->id }}">
            <div class="b-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">{!! $billIcons[$bill->category] ?? $defaultBillIcon !!}</svg>
            </div>
            <div class="b-mid">
              <p class="name">{{ $bill->category }}</p>
              <p class="meta">{{ $bill->biller }} · {{ __('paybills.due_prefix') }} {{ $bill->due_date->format('M j') }}</p>
            </div>
            <div class="b-right">
              <p class="amt">${{ number_format($bill->amount, 2) }}</p>
              <button type="button" class="bill-pay-btn" data-category="{{ $bill->category }}" data-biller="{{ $bill->biller }}" data-amount="{{ number_format($bill->amount, 2) }}">{{ __('paybills.pay_btn') }}</button>
            </div>
            <button type="button" class="bill-remove-btn" data-bill-id="{{ $bill->id }}" aria-label="{{ __('paybills.remove_bill_aria', ['category' => $bill->category]) }}">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/></svg>
            </button>
          </div>
        @endforeach
      </div>
    </div>
  @else
    {{-- Savings-only accounts land here — no add/pay UI at all, since
         BillController::store() would reject it anyway. --}}
    <p class="section-label">{{ __('paybills.upcoming_bills') }}</p>
    <div class="bills-empty-state fade-in d3">
      <div class="bills-empty-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
      </div>
      <p class="bills-empty-title">{{ __('paybills.checking_required_title') }}</p>
      <p class="bills-empty-sub">{{ __('paybills.checking_required_sub') }}</p>
    </div>
  @endif
</div>

<!-- ============ add-bill sheet ============ -->
<div class="sheet-overlay" id="addBillSheetOverlay" onclick="closeAddBillSheet(event)">
  <div class="sheet" onclick="event.stopPropagation()">
    <div class="sheet-handle"></div>
    <div class="sheet-head">
      <h4>{{ __('paybills.add_bill_heading') }}</h4>
      <div class="icon-btn" onclick="closeAddBillSheet()">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8"><path d="M18 6L6 18M6 6l12 12"/></svg>
      </div>
    </div>

    <div class="field-group">
      <p class="label">{{ __('paybills.bill_type_label') }}</p>
      <div class="select-wrap">
        <select id="addBillCategory" class="text-input" onchange="onAddBillCategoryChange()">
          <option value="Rent / Mortgage">{{ __('paybills.bill_type_rent_mortgage') }}</option>
          <option value="Credit Card">{{ __('paybills.bill_type_credit_card') }}</option>
          <option value="Medical Insurance">{{ __('paybills.bill_type_medical_insurance') }}</option>
          <option value="Taxes">{{ __('paybills.bill_type_taxes') }}</option>
          <option value="Student Loan">{{ __('paybills.bill_type_student_loan') }}</option>
          <option value="Other">{{ __('paybills.bill_type_other') }}</option>
        </select>
      </div>
    </div>

    <div class="field-group" id="addBillCustomCategoryGroup" style="display:none;">
      <p class="label">{{ __('paybills.bill_name_label') }}</p>
      <input type="text" id="addBillCustomCategory" class="text-input" placeholder="{{ __('paybills.bill_name_placeholder') }}" maxlength="100">
    </div>

    <div class="field-group">
      <p class="label">{{ __('paybills.biller_label') }}</p>
      <input type="text" id="addBillBiller" class="text-input" placeholder="{{ __('paybills.biller_placeholder') }}" maxlength="150">
    </div>

    <div class="field-group">
      <p class="label">{{ __('paybills.amount_label') }}</p>
      <div class="select-wrap" style="padding:0;">
        <input type="text" id="addBillAmount" class="text-input" inputmode="decimal" placeholder="0.00">
      </div>
    </div>

    <div class="field-group">
      <p class="label">{{ __('paybills.due_date_label') }}</p>
      <input type="date" id="addBillDueDate" class="text-input">
    </div>

    <p class="add-bill-error" id="addBillError"></p>

    <div class="pay-btn-wrap">
      <button type="button" class="pay-btn" id="addBillSubmitBtn" onclick="submitAddBill()">{{ __('paybills.add_bill_submit') }}</button>
    </div>
  </div>
</div>

<!-- ============ pay-bill confirmation sheet ============ -->
<div class="sheet-overlay" id="paySheetOverlay" onclick="closePaySheet(event)">
  <div class="sheet" onclick="event.stopPropagation()">
    <div class="sheet-handle"></div>
    <div class="sheet-head">
      <h4 id="payCategory">{{ __('paybills.pay_bill_heading') }}</h4>
      <div class="icon-btn" onclick="closePaySheet()">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8"><path d="M18 6L6 18M6 6l12 12"/></svg>
      </div>
    </div>

    <p class="section-label" id="payBiller" style="margin-bottom:16px;">{{ __('paybills.biller_label') }}</p>

    <div class="field-group">
      <p class="label">{{ __('paybills.amount_label') }}</p>
      <div class="select-wrap" style="padding:0;">
        <input type="text" id="payAmount" class="text-input" inputmode="decimal" value="">
      </div>
    </div>

    <p class="from-label">{{ __('paybills.pay_from') }}</p>
    <div class="from-row" style="margin-bottom:6px;">
      <div class="bank-mark">L</div>
      <div class="bank-info">
        <p class="name">Ledger Federal Credit Union</p>
        <p class="meta">{{ $primaryAccountType ?? __('paybills.account_fallback') }} •••• {{ $primaryAccountLast4 }}</p>
      </div>
    </div>

    <div class="pay-btn-wrap">
      <button type="button" class="pay-btn" onclick="confirmPayment()">{{ __('paybills.confirm_payment') }}</button>
    </div>
  </div>
</div>

<div class="ledger-toast" id="ledgerToast">
  <svg viewBox="0 0 24 24" fill="none"><path d="M20 6L9 17l-5-5"/></svg>
  <span id="toastText">{{ __('paybills.payment_scheduled_toast') }}</span>
</div>

<!-- Powers the "Remove bill?" confirmation dialog below, in place of the
     browser's plain confirm() popup. -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
  window.LedgerPayBillsConfig = {
    csrfToken: '{{ csrf_token() }}',
    billsStoreUrl: "{{ route('bills.store') }}",
    i18n: {
      paymentScheduledSuffix: @json(__('paybills.payment_scheduled_suffix')),
      payBtn: @json(__('paybills.pay_btn')),
      thisBillFallback: @json(__('paybills.this_bill_fallback')),
      removeConfirmText: @json(__('paybills.remove_confirm_text')),
      removeConfirmButton: @json(__('paybills.remove_confirm_button')),
      removeCancelButton: @json(__('paybills.remove_cancel_button')),
      errorRemoveFailed: @json(__('paybills.error_remove_bill_failed')),
      errorRemoveConnection: @json(__('paybills.error_remove_bill_connection')),
      removeAriaTemplate: @json(__('paybills.remove_bill_aria', ['category' => '__CATEGORY__'])),
      removeConfirmTitleTemplate: @json(__('paybills.remove_confirm_title', ['name' => '__NAME__'])),
      billRemovedToastTemplate: @json(__('paybills.bill_removed_toast', ['name' => '__NAME__'])),
      billAddedToastTemplate: @json(__('paybills.bill_added_toast', ['category' => '__CATEGORY__'])),
      errorEnterBillName: @json(__('paybills.error_enter_bill_name')),
      errorChooseBillType: @json(__('paybills.error_choose_bill_type')),
      errorEnterBiller: @json(__('paybills.error_enter_biller')),
      errorEnterValidAmount: @json(__('paybills.error_enter_valid_amount')),
      errorPickDueDate: @json(__('paybills.error_pick_due_date')),
      adding: @json(__('paybills.adding')),
      errorAddBillFailed: @json(__('paybills.error_add_bill_failed')),
      errorAddBillConnection: @json(__('paybills.error_add_bill_connection')),
      addBillSubmit: @json(__('paybills.add_bill_submit')),
    },
  };
</script>


@endsection
