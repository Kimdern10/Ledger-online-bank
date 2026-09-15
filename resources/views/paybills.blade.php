@extends('layouts.app')

@section('content')
@php
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
@endphp
<style>
  /* Empty state shown to anyone with no bills yet, and the fade+slide-in
     used both for it and for a bill row a fetch() call just added. */
  .bills-section-head{ display:flex; align-items:center; justify-content:space-between; margin-bottom:2px; }
  .add-bill-link{
    border:none; background:none; padding:4px 2px; cursor:pointer;
    font-family:'Inter', sans-serif; font-size:13.5px; font-weight:600; color:#2F6F62;
  }
  .add-bill-link:hover{ text-decoration:underline; }
  .bills-empty-state{
    display:flex; flex-direction:column; align-items:center; text-align:center;
    gap:6px; padding:34px 20px; border:1px dashed rgba(16,32,47,0.16); border-radius:16px;
  }
  html.dark .bills-empty-state{ border-color:rgba(246,244,238,0.16); }
  .bills-empty-icon{
    width:44px; height:44px; border-radius:50%; display:flex; align-items:center; justify-content:center;
    background:rgba(47,111,98,0.1); color:#2F6F62; margin-bottom:4px;
  }
  .bills-empty-icon svg{ width:22px; height:22px; }
  .bills-empty-title{ font-weight:600; font-size:14.5px; margin:0; }
  .bills-empty-sub{ font-size:13px; color:var(--ledger-text-2, #5C6B72); margin:0 0 8px; max-width:280px; }
  .bill-row.is-entering{ animation: billRowIn .32s cubic-bezier(.16,.84,.44,1) both; }
  @keyframes billRowIn{
    from{ opacity:0; transform:translateY(-6px); }
    to{ opacity:1; transform:translateY(0); }
  }
  @media (prefers-reduced-motion:reduce){
    .bill-row.is-entering{ animation:none; }
  }
  .add-bill-error{ font-size:12.5px; color:#C1503C; margin:2px 2px 0; display:none; }
  .add-bill-error.show{ display:block; }

  /* Remove-bill button: a normal flex child placed after the amount/Pay
     button, so it gets its own space in the row instead of sitting on top
     of the amount text (which is what happened when this was absolutely
     positioned in the corner). Faint by default, more visible on hover so
     it doesn't compete with Pay for attention. */
  .bill-remove-btn{
    flex-shrink:0;
    width:26px; height:26px; margin-left:6px; border-radius:50%; border:none; background:transparent;
    display:flex; align-items:center; justify-content:center; cursor:pointer;
    color:var(--ledger-text-2, #5C6B72); opacity:0.5; transition:opacity .15s, background-color .15s, color .15s;
  }
  .bill-remove-btn:hover, .bill-remove-btn:focus-visible{ opacity:1; background:rgba(193,80,60,0.12); color:#C1503C; }
  .bill-remove-btn svg{ width:14px; height:14px; }
  .bill-remove-btn:disabled{ opacity:0.3; cursor:default; }
  .bill-row.is-removing{ animation:billRowOut .2s ease both; }
  @keyframes billRowOut{
    to{ opacity:0; transform:translateY(-6px) scale(0.98); }
  }
  @media (prefers-reduced-motion:reduce){
    .bill-row.is-removing{ animation:none; opacity:0; }
  }
</style>
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
  // Translated strings shared by this script and the one below — declared
  // once here so both can reference them (top-level const/let in one
  // classic <script> tag is visible to later <script> tags on the page).
  const I18N_PAYMENT_SCHEDULED_SUFFIX = @json(__('paybills.payment_scheduled_suffix'));
  const I18N_PAY_BTN = @json(__('paybills.pay_btn'));
  const I18N_THIS_BILL_FALLBACK = @json(__('paybills.this_bill_fallback'));
  const I18N_REMOVE_CONFIRM_TEXT = @json(__('paybills.remove_confirm_text'));
  const I18N_REMOVE_CONFIRM_BUTTON = @json(__('paybills.remove_confirm_button'));
  const I18N_REMOVE_CANCEL_BUTTON = @json(__('paybills.remove_cancel_button'));
  const I18N_ERROR_REMOVE_FAILED = @json(__('paybills.error_remove_bill_failed'));
  const I18N_ERROR_REMOVE_CONNECTION = @json(__('paybills.error_remove_bill_connection'));
  // Templates carry a literal placeholder token that JS swaps out at
  // runtime with .replace() — same pattern as dashboard.acct_aria, since
  // the real value (a bill name/category) is only known client-side.
  const I18N_REMOVE_ARIA_TEMPLATE = @json(__('paybills.remove_bill_aria', ['category' => '__CATEGORY__']));
  const I18N_REMOVE_CONFIRM_TITLE_TEMPLATE = @json(__('paybills.remove_confirm_title', ['name' => '__NAME__']));
  const I18N_BILL_REMOVED_TOAST_TEMPLATE = @json(__('paybills.bill_removed_toast', ['name' => '__NAME__']));
  const I18N_BILL_ADDED_TOAST_TEMPLATE = @json(__('paybills.bill_added_toast', ['category' => '__CATEGORY__']));

  function openPaySheet(category, biller, amount){
    document.getElementById('payCategory').textContent = category;
    document.getElementById('payBiller').textContent = biller;
    document.getElementById('payAmount').value = amount;
    document.getElementById('paySheetOverlay').classList.add('open');
  }
  function closePaySheet(e){
    if(e) e.stopPropagation();
    document.getElementById('paySheetOverlay').classList.remove('open');
  }
  function confirmPayment(){
    closePaySheet();
    showBillToast(document.getElementById('payCategory').textContent + ' ' + I18N_PAYMENT_SCHEDULED_SUFFIX);
  }

  function showBillToast(message){
    const toast = document.getElementById("ledgerToast");
    document.getElementById('toastText').textContent = message;
    toast.classList.add('show');
    clearTimeout(window._toastTimer);
    window._toastTimer = setTimeout(()=> toast.classList.remove('show'), 2200);
  }
</script>

<script>
  // Same icon set as the PHP $billIcons array above, so a bill added
  // through the form gets the right icon immediately, without a reload.
  const BILL_ICONS = {
    'Rent / Mortgage': '<path d="M3 11l9-8 9 8"/><path d="M5 10v10h14V10"/>',
    'Credit Card': '<rect x="3" y="6" width="18" height="13" rx="2"/><path d="M3 10h18M7 15h4"/>',
    'Medical Insurance': '<path d="M12 21s-7-4.35-9.5-9A5.5 5.5 0 0 1 12 6a5.5 5.5 0 0 1 9.5 6c-2.5 4.65-9.5 9-9.5 9Z"/>',
    'Taxes': '<rect x="4" y="3" width="16" height="18" rx="2"/><path d="M8 8h8M8 12h8M8 16h5"/>',
    'Student Loan': '<path d="M22 10L12 5 2 10l10 5 10-5Z"/><path d="M6 12v5c0 1.5 3 3 6 3s6-1.5 6-3v-5"/>',
  };
  const DEFAULT_BILL_ICON = '<circle cx="12" cy="12" r="9"/><path d="M9 12h6M12 9v6"/>';

  function openAddBillSheet(){
    document.getElementById('addBillError').classList.remove('show');
    document.getElementById('addBillSheetOverlay').classList.add('open');
  }
  function closeAddBillSheet(e){
    if(e) e.stopPropagation();
    document.getElementById('addBillSheetOverlay').classList.remove('open');
  }

  function onAddBillCategoryChange(){
    const isOther = document.getElementById('addBillCategory').value === 'Other';
    document.getElementById('addBillCustomCategoryGroup').style.display = isOther ? 'block' : 'none';
  }

  function showAddBillError(message){
    const el = document.getElementById('addBillError');
    el.textContent = message;
    el.classList.add('show');
  }

  // Full HTML-attribute-safe escaping — needed because the Pay button below
  // puts these values inside double-quoted data-* attributes, not just
  // between tags, so a stray " in a custom bill name has to be neutralized
  // too, not just & < >.
  function escapeAttr(str){
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#39;');
  }

  function buildBillRowHTML(bill){
    const icon = BILL_ICONS[bill.category] || DEFAULT_BILL_ICON;
    const category = escapeAttr(bill.category);
    const biller = escapeAttr(bill.biller);
    const amount = escapeAttr(bill.amount);
    return (
      '<div class="bill-row is-entering" data-bill-id="' + bill.id + '">' +
        '<div class="b-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">' + icon + '</svg></div>' +
        '<div class="b-mid">' +
          '<p class="name">' + category + '</p>' +
          '<p class="meta">' + biller + ' · ' + escapeAttr(bill.due_date_label) + '</p>' +
        '</div>' +
        '<div class="b-right">' +
          '<p class="amt">$' + amount + '</p>' +
          '<button type="button" class="bill-pay-btn" data-category="' + category + '" data-biller="' + biller + '" data-amount="' + amount + '">' + I18N_PAY_BTN + '</button>' +
        '</div>' +
        '<button type="button" class="bill-remove-btn" data-bill-id="' + bill.id + '" aria-label="' + escapeAttr(I18N_REMOVE_ARIA_TEMPLATE.replace('__CATEGORY__', bill.category)) + '">' +
          '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/></svg>' +
        '</button>' +
      '</div>'
    );
  }

  // One listener on the container handles both Pay and Remove clicks, for
  // bills rendered by the server on page load AND any added later by
  // fetch() — no need to re-wire anything after inserting a new row.
  // #billRows doesn't exist at all for a savings-only account (no checking
  // account = no bills UI, see $hasChecking in the PHP above), hence the
  // null check — without it this line would throw on every page load for
  // those users.
  const billRowsEl = document.getElementById('billRows');
  if (billRowsEl) {
    billRowsEl.addEventListener('click', function(e){
      const removeBtn = e.target.closest('.bill-remove-btn');
      if (removeBtn) {
        removeBill(removeBtn);
        return;
      }
      const btn = e.target.closest('.bill-pay-btn');
      if (!btn) return;
      openPaySheet(btn.dataset.category, btn.dataset.biller, btn.dataset.amount);
    });
  }

  async function removeBill(button){
    const row = button.closest('.bill-row');
    const billId = row.dataset.billId;
    const name = row.querySelector('.b-mid .name')?.textContent || I18N_THIS_BILL_FALLBACK;

    const confirmResult = await Swal.fire({
      title: I18N_REMOVE_CONFIRM_TITLE_TEMPLATE.replace('__NAME__', name),
      text: I18N_REMOVE_CONFIRM_TEXT,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: I18N_REMOVE_CONFIRM_BUTTON,
      cancelButtonText: I18N_REMOVE_CANCEL_BUTTON,
      confirmButtonColor: '#C1503C',
      cancelButtonColor: '#2F6F62',
      reverseButtons: true,
      focusCancel: true,
    });
    if (!confirmResult.isConfirmed) return;

    button.disabled = true;

    try {
      const response = await fetch('/bills/' + billId, {
        method: 'DELETE',
        headers: {
          'Accept': 'application/json',
          'X-CSRF-TOKEN': "{{ csrf_token() }}",
        },
      });

      if (!response.ok) {
        showBillToast(I18N_ERROR_REMOVE_FAILED);
        button.disabled = false;
        return;
      }

      row.classList.add('is-removing');
      setTimeout(() => {
        row.remove();
        if (!document.querySelector('#billRows .bill-row')) {
          document.getElementById('billCard').style.display = 'none';
          document.getElementById('billsEmptyState').style.display = 'flex';
        }
      }, 200);

      showBillToast(I18N_BILL_REMOVED_TOAST_TEMPLATE.replace('__NAME__', name));
    } catch (err) {
      showBillToast(I18N_ERROR_REMOVE_CONNECTION);
      button.disabled = false;
    }
  }

  async function submitAddBill(){
    const categorySelect = document.getElementById('addBillCategory');
    const isOther = categorySelect.value === 'Other';
    const category = (isOther ? document.getElementById('addBillCustomCategory').value : categorySelect.value).trim();
    const biller = document.getElementById('addBillBiller').value.trim();
    const amount = document.getElementById('addBillAmount').value.trim();
    const dueDate = document.getElementById('addBillDueDate').value;

    if (!category) return showAddBillError(isOther ? @json(__('paybills.error_enter_bill_name')) : @json(__('paybills.error_choose_bill_type')));
    if (!biller) return showAddBillError(@json(__('paybills.error_enter_biller')));
    if (!amount || isNaN(parseFloat(amount)) || parseFloat(amount) <= 0) return showAddBillError(@json(__('paybills.error_enter_valid_amount')));
    if (!dueDate) return showAddBillError(@json(__('paybills.error_pick_due_date')));

    const submitBtn = document.getElementById('addBillSubmitBtn');
    submitBtn.disabled = true;
    submitBtn.textContent = @json(__('paybills.adding'));

    try {
      const response = await fetch("{{ route('bills.store') }}", {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': "{{ csrf_token() }}",
        },
        body: JSON.stringify({ category, biller, amount, due_date: dueDate }),
      });

      if (!response.ok) {
        const body = await response.json().catch(() => null);
        // 422 (validation) puts the message under body.errors; 403 (the
        // "checking account required" case) puts it under body.message.
        const firstError = body && body.errors ? Object.values(body.errors)[0]?.[0] : (body ? body.message : null);
        showAddBillError(firstError || @json(__('paybills.error_add_bill_failed')));
        return;
      }

      const { bill } = await response.json();

      document.getElementById('billsEmptyState').style.display = 'none';
      document.getElementById('billCard').style.display = 'block';
      document.getElementById('billRows').insertAdjacentHTML('beforeend', buildBillRowHTML(bill));

      // Reset the form for next time and close up.
      categorySelect.value = 'Rent / Mortgage';
      onAddBillCategoryChange();
      document.getElementById('addBillCustomCategory').value = '';
      document.getElementById('addBillBiller').value = '';
      document.getElementById('addBillAmount').value = '';
      document.getElementById('addBillDueDate').value = '';
      closeAddBillSheet();
      showBillToast(I18N_BILL_ADDED_TOAST_TEMPLATE.replace('__CATEGORY__', bill.category));
    } catch (err) {
      showAddBillError(@json(__('paybills.error_add_bill_connection')));
    } finally {
      submitBtn.disabled = false;
      submitBtn.textContent = @json(__('paybills.add_bill_submit'));
    }
  }
</script>
@endsection
