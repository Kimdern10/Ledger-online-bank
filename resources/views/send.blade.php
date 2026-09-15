@extends('layouts.app')

@section('content')
@php
    // Every linked account — bank or card — see LinkedAccountController.
    // The "Linked bank" tab submits to WithdrawalController::store(), which
    // already handles both destination types generically (see its own fee
    // logic), so there's no reason to hide the bank half of what's linked.
    $linkedCards = auth()->user()->linkedAccounts()->latest()->get();
@endphp
<div class="send-wrap">

  <div class="page-header fade-in d1">
      <a href="{{ url()->previous() }}" class="icon-btn">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
    </a>
    <h1>{{ __('send.title') }}</h1>
    <a href="{{ route('dashboard') }}" class="icon-btn">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><path d="M3 11l9-8 9 8"/><path d="M5 10v10h14V10"/></svg>
    </a>
  </div>

  {{-- Confirms a transfer was scheduled (not sent — see 'sendSuccess' in
       TransferController::scheduleLedgerTransfer()/scheduleExternalTransfer())
       or that a scheduled one was cancelled. --}}
  @if(session('sendSuccess'))
    <div class="send-alert send-alert-success fade-in d1">{{ session('sendSuccess') }}</div>
  @endif

  {{-- Business-logic failures (no matching account, insufficient funds, an
       unsupported "send to" mode) land here — TransferController::store()
       flashes 'sendError' rather than treating these as field validation. --}}
  @if(session('sendError'))
    <div class="send-alert fade-in d1">{{ session('sendError') }}</div>
  @endif

  {{-- The "Linked bank" tab actually submits to WithdrawalController::store()
       (see setReceiveMode()'s form.action swap below) — since sending to
       your own linked card is really the same real action as Withdraw ->
       Debit card, this reuses that same backend instead of a second copy of
       it. Its failures (no card selected, none linked yet) flash here. --}}
  @if(session('withdrawError'))
    <div class="send-alert fade-in d1">{{ session('withdrawError') }}</div>
  @endif

  {{-- No transaction PIN created yet — see RequiresTransactionPin. A plain
       flag rather than text, so this can include a real link. --}}
  @if(session('pinRequired'))
    <div class="send-alert fade-in d1">
      {{ __('send.pin_required_prefix') }}
      <a href="{{ route('setting.transaction-pin') }}" style="text-decoration:underline; color:inherit;">{{ __('send.pin_required_link') }}</a>.
    </div>
  @endif

  {{-- Actual field validation (missing recipient, bad amount) --}}
  @if($errors->any())
    <div class="send-alert fade-in d1">
      <ul style="margin:0; padding-left:18px;">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form method="POST" action="{{ route('send.store') }}" id="sendForm">
    @csrf
    <input type="hidden" name="mode" id="modeInput" value="{{ old('mode', 'ledger') }}">
    <input type="hidden" name="when" id="whenInput" value="{{ old('when', 'now') }}">
    <input type="hidden" name="amount" id="amountHidden" value="0">
    {{-- Only read when the form submits to WithdrawalController::store()
         (mode = linked) — see setReceiveMode() and selectLinkedChip(). --}}
    <input type="hidden" name="destination_type" id="destinationTypeInput" value="{{ old('destination_type', optional($linkedCards->first())->type ?? 'card') }}">
    <input type="hidden" name="linked_account_id" id="linkedAccountIdInput" value="{{ old('linked_account_id', optional($linkedCards->first())->id) }}">

    <!-- ============ From: fixed to the Ledger account, no picker ============ -->
    <p class="section-label">{{ __('send.from') }}</p>
    <div class="from-row fade-in d2">
      <div class="bank-mark">L</div>
      <div class="bank-info">
        <p class="name">Ledger Federal Credit Union</p>
        <p class="meta">{{ __('send.checking_label') }} •••• 4471</p>
      </div>
      <div class="bank-balance">
        <p class="amt">${{ number_format((float) auth()->user()->balance, 2) }}</p>
        <p class="tag">{{ __('send.available') }}</p>
      </div>
    </div>

    <!-- ============ Send to: Ledger | Another bank | Linked bank ============ -->
    <p class="section-label">{{ __('send.send_to') }}</p>
    <div class="fade-in d2">
      <div class="segmented four">
        <button class="seg-btn active" id="segLedger" onclick="setReceiveMode('ledger')" type="button">
          <svg viewBox="0 0 24 24" fill="none"><rect x="4" y="4" width="7" height="7"/><rect x="13" y="4" width="7" height="7"/><rect x="4" y="13" width="7" height="7"/><path d="M14 15h5M16.5 12.5v5"/></svg>
          {{ __('send.tab_ledger') }}
        </button>
        <button class="seg-btn" id="segBank" onclick="setReceiveMode('bank')" type="button">
          <svg viewBox="0 0 24 24" fill="none"><path d="M3 21h18M4 21V10l8-6 8 6v11M9 21v-6h6v6"/></svg>
          {{ __('send.tab_another_bank') }}
        </button>
        <button class="seg-btn" id="segInternational" onclick="setReceiveMode('international')" type="button">
          <svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a14 14 0 0 1 0 18a14 14 0 0 1 0-18Z"/></svg>
          {{ __('send.tab_international') }}
        </button>
        <button class="seg-btn" id="segLinked" onclick="setReceiveMode('linked')" type="button">
          <svg viewBox="0 0 24 24" fill="none"><path d="M10 13a5 5 0 0 0 7.5.5l2-2a5 5 0 0 0-7-7l-1.5 1.5"/><path d="M14 11a5 5 0 0 0-7.5-.5l-2 2a5 5 0 0 0 7 7l1.5-1.5"/></svg>
          {{ __('send.tab_linked_bank') }}
        </button>
      </div>

      <!-- ============ 1) Ledger: fully manual — account number and name are both just typed in ============ -->
      <div id="ledgerOption">
        <div class="field-group">
          <p class="label">{{ __('send.account_number') }}</p>
          <input type="text" class="text-input" id="recipientAccountInput" name="recipient_account_number" value="{{ old('recipient_account_number') }}" placeholder="{{ __('send.recipient_ledger_account_placeholder') }}" inputmode="numeric" autocomplete="off" required>
        </div>
        <div class="field-group">
          <p class="label">{{ __('send.recipient_name') }}</p>
          <input type="text" class="text-input" id="recipientNameInput" name="recipient_name" value="{{ old('recipient_name') }}" placeholder="{{ __('send.recipient_name_placeholder') }}" autocomplete="off" required>
        </div>
        <p class="receive-note instant">
          <svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
          {{ __('send.instant_ledger_note') }}
        </p>
      </div>

      <!-- ============ 2) Another bank: everything here is a plain field the sender types themselves —
           "Select a bank" is just a shortcut that fills the Bank field in for you; it was never
           required to submit, but it used to be the ONLY way to fill bank_name in, so someone who
           skipped it (typed the other three fields and hit Send) had the form silently fail
           server-side validation. Now Bank is a real text input like the rest, so nothing can be
           "filled in" without actually being submitted. ============ -->
      <div id="bankOption" style="display:none;">
        <div class="field-group">
          <p class="label">{{ __('send.bank_label') }}</p>
          <div class="bank-input-row">
            <input type="text" class="text-input" id="bankNameInput" name="bank_name" value="{{ old('bank_name') }}" placeholder="{{ __('send.recipient_bank_name_placeholder') }}" autocomplete="off" required>
            <button type="button" class="bank-search-btn" onclick="openSheet()" aria-label="{{ __('send.select_bank') }}">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.35-4.35"/></svg>
            </button>
          </div>
        </div>

        <div class="field-group">
          <p class="label">{{ __('send.recipient_name') }}</p>
          <input type="text" class="text-input" name="bank_recipient_name" value="{{ old('bank_recipient_name') }}" placeholder="{{ __('send.recipient_placeholder') }}" autocomplete="off">
        </div>
        <div class="field-group">
          <p class="label">{{ __('send.account_number') }}</p>
          <input type="text" class="text-input" name="bank_account_number" value="{{ old('bank_account_number') }}" placeholder="{{ __('send.recipient_account_number_placeholder') }}" inputmode="numeric" autocomplete="off">
        </div>
        <div class="field-group">
          <p class="label">{{ __('send.routing_number') }}</p>
          <input type="text" class="text-input" name="bank_routing_number" value="{{ old('bank_routing_number') }}" placeholder="{{ __('send.recipient_routing_number_placeholder') }}" inputmode="numeric">
        </div>
      </div>

      <!-- ============ 3) International bank: same idea as Another bank, plus country/SWIFT/currency —
           "Select a bank" pulls from the same admin-managed directory, filtered to international entries
           only (see BankDirectoryController::searchInternational()). ============ -->
      <div id="internationalOption" style="display:none;">
        <div class="field-group">
          <p class="label">{{ __('send.intl_bank_name_label') }}</p>
          <div class="bank-input-row">
            <input type="text" class="text-input" id="intlBankNameInput" name="intl_bank_name" value="{{ old('intl_bank_name') }}" placeholder="{{ __('send.intl_bank_name_placeholder') }}" autocomplete="off">
            <button type="button" class="bank-search-btn" onclick="openIntlSheet()" aria-label="{{ __('send.select_bank') }}">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.35-4.35"/></svg>
            </button>
          </div>
        </div>
        <div class="field-group">
          <p class="label">{{ __('send.recipient_name') }}</p>
          <input type="text" class="text-input" id="intlRecipientNameInput" name="intl_recipient_name" value="{{ old('intl_recipient_name') }}" placeholder="{{ __('send.recipient_placeholder') }}" autocomplete="off">
        </div>
        <div class="field-group">
          <p class="label">{{ __('send.intl_country_label') }}</p>
          <input type="text" class="text-input" id="intlCountryInput" name="intl_country" value="{{ old('intl_country') }}" placeholder="{{ __('send.intl_country_placeholder') }}" autocomplete="off">
        </div>
        <div class="field-group">
          <p class="label">{{ __('send.intl_account_label') }}</p>
          <input type="text" class="text-input" id="intlAccountInput" name="intl_account_number" value="{{ old('intl_account_number') }}" placeholder="{{ __('send.intl_account_placeholder') }}" autocomplete="off">
        </div>
        <div class="field-group">
          <p class="label">{{ __('send.intl_swift_label') }}</p>
          <input type="text" class="text-input" id="intlSwiftInput" name="intl_swift_code" value="{{ old('intl_swift_code') }}" placeholder="{{ __('send.intl_swift_placeholder') }}" autocomplete="off" style="text-transform:uppercase;">
        </div>
        <div class="field-group">
          <p class="label">{{ __('send.intl_currency_label') }}</p>
          <input type="text" class="text-input" id="intlCurrencyInput" name="intl_currency" value="{{ old('intl_currency', 'USD') }}" placeholder="{{ __('send.intl_currency_placeholder') }}" maxlength="3" autocomplete="off" style="text-transform:uppercase;" oninput="scheduleIntlConversion()">
        </div>
        {{-- Live preview only — see TransferController::convertPreview().
             The rate actually locked in on the transfer (and shown on its
             receipt) is looked up fresh, again, at the moment it's sent or
             scheduled, not carried over from here. --}}
        <p class="receive-note" id="intlConvertedNote" style="display:none;">
          <svg viewBox="0 0 24 24" fill="none"><path d="M17 1l4 4-4 4"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/><path d="M7 23l-4-4 4-4"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/></svg>
          {{ __('send.intl_converted_prefix') }} <strong id="intlConvertedAmount"></strong>
        </p>
        <p class="receive-note">
          <svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></svg>
          {{ __('send.intl_transfer_note') }}
        </p>
      </div>

      <!-- ============ 4) Linked bank: send to any of your own linked bank accounts or cards ============ -->
      <div id="linkedOption" style="display:none;">
        <p class="from-label">{{ __('send.choose_linked_card') }}</p>
        @if($linkedCards->isEmpty())
          <p style="padding:10px 4px; opacity:0.65; font-size:13px;">
            {{ __('send.no_linked_card') }} <a href="{{ route('link-account') }}" style="color:inherit; text-decoration:underline;">{{ __('send.link_one_first') }}</a>.
          </p>
        @else
          @php $selectedCardId = (int) old('linked_account_id', $linkedCards->first()->id); @endphp
          @php $selectedType = old('destination_type', $linkedCards->first()->type); @endphp
          <div class="linked-chips">
            @foreach($linkedCards as $card)
              <div class="linked-chip {{ $card->id === $selectedCardId ? 'selected' : '' }}" data-account-id="{{ $card->id }}" onclick="selectLinkedChip(this, {{ $card->id }}, '{{ $card->type }}')">
                <span class="chip-mark" style="background:var(--ink);">{{ substr($card->displayLabel(), 0, 1) }}</span> {{ $card->displayLabel() }} {{ $card->detailLabel() }}
              </div>
            @endforeach
          </div>
        @endif

        <p class="receive-note instant" id="linkedFeeNoteCard" style="{{ $linkedCards->isNotEmpty() && $selectedType !== 'card' ? 'display:none;' : '' }}">
          <svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
          {{ __('send.instant_linked_note_card') }}
        </p>
        <p class="receive-note instant" id="linkedFeeNoteBank" style="{{ $linkedCards->isEmpty() || $selectedType === 'card' ? 'display:none;' : '' }}">
          <svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
          {{ __('send.instant_linked_note_bank') }}
        </p>
      </div>
    </div>

    <!-- ============ Amount ============ -->
    <p class="section-label">{{ __('send.amount') }}</p>
    <div class="amount-card fade-in d3">
      <div class="amount-field">
        <span class="cur">$</span>
        <input type="text" id="amountInput" inputmode="decimal" value="" placeholder="0.00" aria-label="{{ __('send.amount_input_aria') }}">
      </div>
      <div class="amount-underline"></div>
      <div class="quick-amounts">
        <button type="button" class="quick-amt-btn" onclick="setAmount(this,'100')">$100</button>
        <button type="button" class="quick-amt-btn" onclick="setAmount(this,'250')">$250</button>
        <button type="button" class="quick-amt-btn" onclick="setAmount(this,'500')">$500</button>
        <button type="button" class="quick-amt-btn" onclick="setAmount(this,'1,000')">$1,000</button>
      </div>
    </div>

    <!-- ============ Category / note ============ -->
    <div class="fade-in d3">
      <div class="field-group">
        <p class="label">{{ __('send.category') }}</p>
        <div class="select-wrap">
          <select name="category">
            <option value="">{{ __('send.select_category') }}</option>
            <option value="Family & friends">{{ __('send.cat_family_friends') }}</option>
            <option value="Rent & bills">{{ __('send.cat_rent_bills') }}</option>
            <option value="Business">{{ __('send.cat_business') }}</option>
            <option value="Gift">{{ __('send.cat_gift') }}</option>
          </select>
          <div class="chev">
            <svg viewBox="0 0 24 24" fill="none"><path d="M6 9l6 6 6-6"/></svg>
          </div>
        </div>
      </div>

      <div class="field-group">
        <p class="label">{{ __('send.note_label') }} <span>{{ __('send.optional') }}</span></p>
        <div class="textarea-wrap">
          <textarea name="note" placeholder="{{ __('send.note_placeholder') }}"></textarea>
          <div class="clip">
            <svg viewBox="0 0 24 24" fill="none"><path d="M21.44 11.05l-9.19 9.19a5 5 0 0 1-7.07-7.07l9.19-9.19a3.5 3.5 0 0 1 4.95 4.95l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg>
          </div>
        </div>
      </div>

      <div class="pay-btn-wrap has-fixed-duplicate">
        <button type="submit" class="pay-btn">{{ __('send.send_btn') }}</button>
      </div>
    </div>
  </form>

</div>

<div class="fixed-bottom-btn">
  <button type="submit" form="sendForm" class="pay-btn">{{ __('send.send_btn') }}</button>
</div>

<!-- ============ Processing overlay ============ -->
{{-- Shown the moment the form is submitted and left up until the browser
     actually navigates away — it isn't a fake timed animation, it's
     covering the real time the server takes to look up the recipient,
     check the balance, and move the money (see TransferController::store()).
     If something goes wrong (bad recipient, insufficient funds), the
     redirect back to this same page removes it automatically since it's
     a fresh page load. --}}
<div class="processing-overlay" id="processingOverlay">
  <div class="processing-spinner"></div>
  <p class="processing-text">{{ __('send.processing') }}</p>
  <p class="processing-sub">{{ __('send.processing_sub') }}</p>
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

{{-- ============ transaction PIN confirmation ============
     Pops up right before the real submit, for every mode this form can
     end up posting to (Ledger/Another bank -> TransferController::store(),
     Linked bank -> WithdrawalController::store()). See openPinModal() /
     confirmPin() below and RequiresTransactionPin server-side. --}}
<div class="sheet-overlay" id="pinModalOverlay" onclick="closePinModal(event)">
  <div class="sheet" onclick="event.stopPropagation()" style="max-width:340px; margin:0 auto;">
    <div class="sheet-handle"></div>
    <div class="sheet-head">
      <h4>{{ __('send.pin_modal_title') }}</h4>
      <div class="icon-btn" onclick="closePinModal()">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8"><path d="M18 6L6 18M6 6l12 12"/></svg>
      </div>
    </div>
    <div style="padding:4px 20px 22px;">
      <p style="margin:0 0 14px; font-size:13px; color:var(--text-3);">{{ __('send.pin_modal_body') }}</p>
      <input type="password" class="text-input" id="pinModalInput" inputmode="numeric" maxlength="4" placeholder="••••" style="text-align:center; font-size:22px; letter-spacing:10px;" autocomplete="off">
      <p id="pinModalError" style="display:none; color:#B4121B; font-size:12.5px; margin:8px 0 0;"></p>
      <button type="button" class="pay-btn" style="margin-top:16px; width:100%;" onclick="confirmPin()">{{ __('send.pin_confirm') }}</button>
    </div>
  </div>
</div>

<!-- ============ bank directory sheet (for "Another bank") ============ -->
<div class="sheet-overlay" id="sheetOverlay" onclick="closeSheet(event)">
  <div class="sheet" onclick="event.stopPropagation()">
    <div class="sheet-handle"></div>
    <div class="sheet-head">
      <h4>{{ __('send.select_bank') }}</h4>
      <div class="icon-btn" onclick="closeSheet()">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8"><path d="M18 6L6 18M6 6l12 12"/></svg>
      </div>
    </div>

    <div class="sheet-search">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.35-4.35"/></svg>
      <input type="text" id="bankSearch" placeholder="{{ __('send.bank_search_placeholder') }}" oninput="onBankSearchInput(this.value)">
    </div>

    <div id="bankOptionList">
      {{-- Populated live from the admin-managed bank directory as you type
           — see onBankSearchInput()/renderBankResults() below. --}}
    </div>

    <p id="bankSearchHint" style="text-align:center; font-size:13px; color:var(--text-3); padding:18px 0;">
      {{ __('send.bank_search_hint') }}
    </p>
    <p id="bankSearchLoading" style="display:none; text-align:center; font-size:13px; color:var(--text-3); padding:18px 0;">
      {{ __('send.bank_search_loading') }}
    </p>
    <p id="noBankResults" style="display:none; text-align:center; font-size:13px; color:var(--text-3); padding:18px 0;">
      {{ __('send.no_bank_results') }}
    </p>
  </div>
</div>

<!-- ============ international bank directory sheet (for "International bank") ============ -->
<div class="sheet-overlay" id="intlSheetOverlay" onclick="closeIntlSheet(event)">
  <div class="sheet" onclick="event.stopPropagation()">
    <div class="sheet-handle"></div>
    <div class="sheet-head">
      <h4>{{ __('send.select_bank') }}</h4>
      <div class="icon-btn" onclick="closeIntlSheet()">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8"><path d="M18 6L6 18M6 6l12 12"/></svg>
      </div>
    </div>

    <div class="sheet-search">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.35-4.35"/></svg>
      <input type="text" id="intlBankSearch" placeholder="{{ __('send.intl_bank_search_placeholder') }}" oninput="onIntlBankSearchInput(this.value)">
    </div>

    <div id="intlBankOptionList"></div>

    <p id="intlBankSearchHint" style="text-align:center; font-size:13px; color:var(--text-3); padding:18px 0;">
      {{ __('send.intl_bank_search_hint') }}
    </p>
    <p id="intlBankSearchLoading" style="display:none; text-align:center; font-size:13px; color:var(--text-3); padding:18px 0;">
      {{ __('send.bank_search_loading') }}
    </p>
    <p id="noIntlBankResults" style="display:none; text-align:center; font-size:13px; color:var(--text-3); padding:18px 0;">
      {{ __('send.no_bank_results') }}
    </p>
  </div>
</div>

<style>
  .send-alert{
    background:#fdecea; color:#b3261e; border:1px solid #f5c6c2; border-radius:12px;
    padding:12px 14px; font-size:13.5px; font-weight:600; margin:0 0 14px;
  }
  .send-alert ul{ font-weight:500; }
  .send-alert-success{
    background:#eaf7ee; color:#1f7a3d; border-color:#c7ebd2;
  }
  .schedule-note{
    background:#eef4fb; color:#2b5a86; border:1px solid #cfe0f2; border-radius:10px;
    padding:10px 12px; font-size:12.5px; font-weight:500; line-height:1.45; margin-top:10px;
  }
  .verify-note{
    background:#eef4fb; color:#2b5a86; border:1px solid #cfe0f2; border-radius:10px;
    padding:10px 12px; font-size:12px; font-weight:500; line-height:1.45; margin-top:10px;
  }
  .pay-btn[disabled]{ opacity:0.6; cursor:default; pointer-events:none; }

  .bank-input-row{ display:flex; align-items:center; gap:8px; }
  .bank-input-row .text-input{ flex:1; }
  .bank-search-btn{
    flex:0 0 auto; width:44px; height:44px; border-radius:10px; border:1px solid rgba(0,0,0,0.12);
    background:#fff; display:flex; align-items:center; justify-content:center; cursor:pointer; color:var(--ink);
  }
  .bank-search-btn svg{ width:18px; height:18px; }
  .bank-search-btn:hover{ background:rgba(30,110,80,0.06); }
</style>

@php
    // Every JS-side user-facing string on this page (alerts, dynamically
    // rendered bank-search text) is pre-translated server-side here, since
    // plain JS has no access to __() — see send.php lang files for the
    // source text in each language. Built as a plain array first, then
    // handed to @json() below — @json() with __() calls nested directly
    // inside its own argument list doesn't compile cleanly.
    $sendI18n = [
        'linkCardFirstAlert' => __('send.link_card_first_alert'),
        'pinError' => __('send.pin_error'),
        'noBankResults' => __('send.no_bank_results'),
        'bankSearchError' => __('send.bank_search_error'),
        'creditUnion' => __('send.credit_union'),
        'bankFallback' => __('send.bank_fallback'),
    ];
@endphp
<script>
  const sendI18n = @json($sendI18n);

  function parseAmount(raw){
    const n = parseFloat(String(raw).replace(/[^0-9.]/g, ''));
    return isNaN(n) ? 0 : n;
  }

  function syncAmountHidden(){
    // Keeps the hidden #amountHidden field — the one actually submitted —
    // in sync with whatever's typed in the comma-formatted display field,
    // regardless of which "Send to" tab is active. No fee is added to
    // this on either tab — Another bank doesn't charge one any more than
    // Ledger does.
    document.getElementById('amountHidden').value = parseAmount(document.getElementById('amountInput').value);
  }

  function setReceiveMode(mode){
    const panels = {ledger:'ledgerOption', bank:'bankOption', international:'internationalOption', linked:'linkedOption'};
    const buttons = {ledger:'segLedger', bank:'segBank', international:'segInternational', linked:'segLinked'};

    Object.keys(panels).forEach(key => {
      document.getElementById(panels[key]).style.display = (key === mode) ? 'block' : 'none';
      document.getElementById(buttons[key]).classList.toggle('active', key === mode);
    });
    document.getElementById('modeInput').value = mode;
    syncAmountHidden();

    // "Linked bank" is really the same real action as Withdraw -> Debit
    // card (money leaving your Ledger balance to a card you've linked), so
    // it submits straight to that same backend instead of
    // TransferController::store() — see WithdrawalController::store().
    // Every other tab (including International) posts to
    // TransferController::store().
    document.getElementById('sendForm').action = (mode === 'linked')
      ? "{{ route('withdraw.store') }}"
      : "{{ route('send.store') }}";

    // This is the fix for "I click Send and nothing happens": a
    // required field that sits inside a hidden (display:none) tab can't
    // be focused, and a browser that can't focus an invalid required
    // field just silently blocks the WHOLE form from submitting — no
    // error, no page change, nothing. That's exactly what was happening
    // whenever the Ledger tab's required fields were left blank while
    // "Another bank" was the active tab (or vice versa). Only the fields
    // belonging to whichever tab is actually showing are "required" now.
    const ledgerRequired = (mode === 'ledger');
    const bankRequired = (mode === 'bank');
    const intlRequired = (mode === 'international');
    document.getElementById('recipientAccountInput').required = ledgerRequired;
    document.getElementById('recipientNameInput').required = ledgerRequired;
    document.getElementById('bankNameInput').required = bankRequired;
    document.querySelector("input[name='bank_recipient_name']").required = bankRequired;
    document.querySelector("input[name='bank_account_number']").required = bankRequired;
    document.querySelector("input[name='bank_routing_number']").required = bankRequired;
    document.getElementById('intlBankNameInput').required = intlRequired;
    document.getElementById('intlRecipientNameInput').required = intlRequired;
    document.getElementById('intlCountryInput').required = intlRequired;
    document.getElementById('intlAccountInput').required = intlRequired;
    document.getElementById('intlSwiftInput').required = intlRequired;
    document.getElementById('intlCurrencyInput').required = intlRequired;

    if(intlRequired){
      scheduleIntlConversion();
    } else {
      document.getElementById('intlConvertedNote').style.display = 'none';
    }
  }

  // ---------- live currency conversion preview (International tab) ----------
  let intlConvertTimer = null;
  let intlConvertAbort = null;

  function scheduleIntlConversion(){
    if(document.getElementById('modeInput').value !== 'international') return;
    clearTimeout(intlConvertTimer);
    intlConvertTimer = setTimeout(runIntlConversion, 350);
  }

  function runIntlConversion(){
    const note = document.getElementById('intlConvertedNote');
    const currency = document.getElementById('intlCurrencyInput').value.trim().toUpperCase();
    const amount = parseAmount(document.getElementById('amountInput').value);

    if(currency.length !== 3 || amount <= 0){
      note.style.display = 'none';
      return;
    }

    if(intlConvertAbort) intlConvertAbort.abort();
    intlConvertAbort = new AbortController();

    fetch('{{ route('send.international.convert') }}?currency=' + encodeURIComponent(currency) + '&amount=' + encodeURIComponent(amount), { signal: intlConvertAbort.signal })
      .then(res => res.ok ? res.json() : { known: false })
      .then(data => {
        if(!data.known){
          note.style.display = 'none';
          return;
        }
        document.getElementById('intlConvertedAmount').textContent =
          new Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(data.converted) + ' ' + data.currency;
        note.style.display = 'flex';
      })
      .catch(err => {
        if(err.name === 'AbortError') return;
        note.style.display = 'none';
      });
  }

  function selectLinkedChip(el, accountId, type){
    document.querySelectorAll('#linkedOption .linked-chip').forEach(c => c.classList.remove('selected'));
    el.classList.add('selected');
    document.getElementById('linkedAccountIdInput').value = accountId;
    document.getElementById('destinationTypeInput').value = type;

    // The fee differs by destination (see WithdrawalController::store()'s
    // CARD_WITHDRAWAL_FEE vs free bank payouts), so the note under the
    // chips has to switch with whichever one is actually selected.
    document.getElementById('linkedFeeNoteCard').style.display = (type === 'card') ? 'flex' : 'none';
    document.getElementById('linkedFeeNoteBank').style.display = (type === 'bank') ? 'flex' : 'none';
  }

  function setAmount(el, value){
    document.querySelectorAll('.quick-amt-btn').forEach(b => b.classList.remove('active'));
    el.classList.add('active');
    document.getElementById('amountInput').value = value;
    syncAmountHidden();
    scheduleIntlConversion();
  }

  document.addEventListener('DOMContentLoaded', function(){
    document.getElementById('amountInput').addEventListener('input', function(){
      syncAmountHidden();
      scheduleIntlConversion();
    });
    syncAmountHidden();

    // If the page reloaded after a rejected submission (e.g. "Another
    // bank" with a missing field), show whichever tab was actually being
    // used — not always Ledger — so the person sees the same tab, with
    // their own input still in it, rather than the page silently
    // resetting to a different tab than the one they were on.
    setReceiveMode(document.getElementById('modeInput').value || 'ledger');

    prefillFromScan();
  });

  // Arriving here from Scan to Pay (see scan.blade.php) puts the account
  // number it already confirmed exists in ?account= — this re-looks it up
  // (never trusts a name from the URL) and fills both fields on the Ledger
  // tab, so the only thing left for the sender to do is enter an amount and
  // hit Send.
  function prefillFromScan(){
    const params = new URLSearchParams(window.location.search);
    const account = params.get('account');
    if(!account) return;

    setReceiveMode('ledger');
    document.getElementById('recipientAccountInput').value = account;

    fetch('{{ route('send.lookup') }}?account_number=' + encodeURIComponent(account), {
      headers: { 'Accept': 'application/json' },
    })
      .then(function(res){
        const contentType = res.headers.get('content-type') || '';
        if(!res.ok || !contentType.includes('application/json')) return { found: false };
        return res.json();
      })
      .then(function(data){
        if(data && data.found){
          document.getElementById('recipientNameInput').value = data.name;
        }
      })
      .catch(function(){});
  }

  // Shows the processing overlay the instant the form is submitted, and
  // disables both "Send" buttons (the in-form one and the fixed
  // duplicate) so a slow connection can't tempt a second click into a
  // second transfer. Nothing here fakes a delay — the overlay just covers
  // however long the real POST to TransferController::store() takes,
  // whether that ends in a redirect to the receipt or back to this page
  // with an error.
  document.getElementById('sendForm').addEventListener('submit', function(e){
    e.preventDefault();

    if(document.getElementById('modeInput').value === 'linked' && !document.getElementById('linkedAccountIdInput').value){
      alert(sendI18n.linkCardFirstAlert);
      return;
    }

    openPinModal(this);
  });

  // ---------- transaction PIN modal ----------
  // Shared shape across send/withdraw/top-up.blade.php: openPinModal(form)
  // shows it, confirmPin() writes the entered PIN into a transaction_pin
  // hidden field on that form and calls form.submit() directly — which,
  // unlike a normal submit button click, does NOT re-fire this 'submit'
  // listener, so there's no risk of looping back into the modal.
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
      err.textContent = sendI18n.pinError;
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
    document.querySelectorAll('#sendForm button[type="submit"], button[form="sendForm"]').forEach(function(btn){
      btn.disabled = true;
    });
    form.submit();
  }

  function openSheet(){
    document.getElementById('bankSearch').value = '';
    resetBankSearchPanel();
    document.getElementById('sheetOverlay').classList.add('open');
    // Shows the full curated list right away — it's a short admin-managed
    // directory, not a live third-party search, so there's no need to wait
    // for someone to start typing before showing anything.
    runBankSearch('');
    setTimeout(() => document.getElementById('bankSearch').focus(), 150);
  }
  function closeSheet(e){
    if(e) e.stopPropagation();
    document.getElementById('sheetOverlay').classList.remove('open');
  }

  function resetBankSearchPanel(){
    clearBankResults();
    document.getElementById('bankSearchHint').style.display = 'none';
    document.getElementById('bankSearchLoading').style.display = 'none';
    document.getElementById('noBankResults').style.display = 'none';
  }

  function clearBankResults(){
    document.querySelectorAll('#bankOptionList .bank-option[data-source="directory"]').forEach(el => el.remove());
  }

  // ---------- live bank search (admin-managed directory; debounced, cancels stale requests) ----------
  let bankSearchTimer = null;
  let bankSearchAbort = null;

  function onBankSearchInput(value){
    clearTimeout(bankSearchTimer);
    bankSearchTimer = setTimeout(() => runBankSearch(value.trim()), 250);
  }

  function runBankSearch(query){
    if(bankSearchAbort) bankSearchAbort.abort();
    bankSearchAbort = new AbortController();

    document.getElementById('bankSearchHint').style.display = 'none';
    document.getElementById('noBankResults').style.display = 'none';
    document.getElementById('bankSearchLoading').style.display = 'block';

    fetch('{{ route('banks.search') }}?q=' + encodeURIComponent(query), { signal: bankSearchAbort.signal })
      .then(res => {
        if(!res.ok) throw new Error('Bank search request failed');
        return res.json();
      })
      .then(json => {
        document.getElementById('bankSearchLoading').style.display = 'none';
        const banks = json.banks || [];
        renderBankResults(banks);
        document.getElementById('noBankResults').textContent = sendI18n.noBankResults;
        document.getElementById('noBankResults').style.display = banks.length === 0 ? 'block' : 'none';
      })
      .catch(err => {
        if(err.name === 'AbortError') return;
        document.getElementById('bankSearchLoading').style.display = 'none';
        clearBankResults();
        document.getElementById('noBankResults').textContent = sendI18n.bankSearchError;
        document.getElementById('noBankResults').style.display = 'block';
      });
  }

  // A handful of stable, readable colors for the bank "mark" avatar — which
  // one a bank gets is just a deterministic hash of its name, so the same
  // bank always gets the same color across searches.
  const BANK_MARK_COLORS = [
    '#0A3161', '#0F4C97', '#B4121B', '#C8102E', '#0C2074', '#652D92',
    '#004977', '#00587C', '#5A2A82', '#7C11E0', '#FF6000', '#00274D',
    '#00A0DF', '#003057', '#DA291C', '#00693C', '#00854A',
  ];

  function bankMarkColorFor(name){
    let hash = 0;
    for(let i = 0; i < name.length; i++){
      hash = (hash * 31 + name.charCodeAt(i)) >>> 0;
    }
    return BANK_MARK_COLORS[hash % BANK_MARK_COLORS.length];
  }

  function renderBankResults(banks){
    clearBankResults();

    const list = document.getElementById('bankOptionList');

    banks.forEach(bank => {
      const name = bank.name || '';
      if(!name) return;

      const color = bankMarkColorFor(name);
      const initial = name.trim().charAt(0).toUpperCase() || '?';

      const el = document.createElement('div');
      el.className = 'bank-option';
      el.dataset.bank = name;
      el.dataset.source = 'directory';

      const mark = document.createElement('div');
      mark.className = 'mark';
      mark.style.background = color;
      mark.textContent = initial;

      const info = document.createElement('div');
      info.className = 'info';
      const nameEl = document.createElement('p');
      nameEl.className = 'name';
      nameEl.textContent = name;
      const metaEl = document.createElement('p');
      metaEl.className = 'meta';
      metaEl.textContent = bank.meta || sendI18n.bankFallback;
      info.append(nameEl, metaEl);

      const check = document.createElement('div');
      check.className = 'check';

      el.append(mark, info, check);
      // No pre-linked account for a bank found via search — selectBank's
      // meta stays blank so the picker just shows name + routing/account
      // number fields, same as the old "Bank of America"/"Wells Fargo"
      // entries used to.
      el.addEventListener('click', () => selectBank(el, name, '', color, initial));

      list.appendChild(el);
    });
  }

  function selectBank(el, name, meta, color, initial){
    document.querySelectorAll('.bank-option').forEach(o=>{
      o.classList.remove('selected');
      const check = o.querySelector('.check');
      if(check) check.innerHTML = '';
    });
    el.classList.add('selected');
    el.querySelector('.check').innerHTML = '<svg viewBox="0 0 24 24" fill="none"><path d="M20 6L9 17l-5-5"/></svg>';

    // Just fills in the real "Bank" text field — same field you could have
    // typed into directly. This is a shortcut, not the only way in.
    document.getElementById('bankNameInput').value = name;

    setTimeout(()=>closeSheet(), 180);
  }

  // ---------- international bank picker (admin-managed directory, type=international) ----------
  function openIntlSheet(){
    document.getElementById('intlBankSearch').value = '';
    resetIntlBankSearchPanel();
    document.getElementById('intlSheetOverlay').classList.add('open');
    // Shows the full curated list right away — it's a short admin-managed
    // directory, not a live third-party search, so there's no need to wait
    // for someone to start typing before showing anything.
    runIntlBankSearch('');
    setTimeout(() => document.getElementById('intlBankSearch').focus(), 150);
  }
  function closeIntlSheet(e){
    if(e) e.stopPropagation();
    document.getElementById('intlSheetOverlay').classList.remove('open');
  }

  function resetIntlBankSearchPanel(){
    clearIntlBankResults();
    document.getElementById('intlBankSearchHint').style.display = 'none';
    document.getElementById('intlBankSearchLoading').style.display = 'none';
    document.getElementById('noIntlBankResults').style.display = 'none';
  }

  function clearIntlBankResults(){
    document.getElementById('intlBankOptionList').innerHTML = '';
  }

  let intlBankSearchTimer = null;
  let intlBankSearchAbort = null;

  function onIntlBankSearchInput(value){
    clearTimeout(intlBankSearchTimer);
    intlBankSearchTimer = setTimeout(() => runIntlBankSearch(value.trim()), 250);
  }

  function runIntlBankSearch(query){
    if(intlBankSearchAbort) intlBankSearchAbort.abort();
    intlBankSearchAbort = new AbortController();

    document.getElementById('intlBankSearchHint').style.display = 'none';
    document.getElementById('noIntlBankResults').style.display = 'none';
    document.getElementById('intlBankSearchLoading').style.display = 'block';

    fetch('{{ route('banks.search.international') }}?q=' + encodeURIComponent(query), { signal: intlBankSearchAbort.signal })
      .then(res => {
        if(!res.ok) throw new Error('Bank search request failed');
        return res.json();
      })
      .then(json => {
        document.getElementById('intlBankSearchLoading').style.display = 'none';
        const banks = json.banks || [];
        renderIntlBankResults(banks);
        document.getElementById('noIntlBankResults').textContent = sendI18n.noBankResults;
        document.getElementById('noIntlBankResults').style.display = banks.length === 0 ? 'block' : 'none';
      })
      .catch(err => {
        if(err.name === 'AbortError') return;
        document.getElementById('intlBankSearchLoading').style.display = 'none';
        clearIntlBankResults();
        document.getElementById('noIntlBankResults').textContent = sendI18n.bankSearchError;
        document.getElementById('noIntlBankResults').style.display = 'block';
      });
  }

  function renderIntlBankResults(banks){
    clearIntlBankResults();

    const list = document.getElementById('intlBankOptionList');

    banks.forEach(bank => {
      const name = bank.name || '';
      if(!name) return;

      const color = bankMarkColorFor(name);
      const initial = name.trim().charAt(0).toUpperCase() || '?';

      const el = document.createElement('div');
      el.className = 'bank-option';

      const mark = document.createElement('div');
      mark.className = 'mark';
      mark.style.background = color;
      mark.textContent = initial;

      const info = document.createElement('div');
      info.className = 'info';
      const nameEl = document.createElement('p');
      nameEl.className = 'name';
      nameEl.textContent = name;
      const metaEl = document.createElement('p');
      metaEl.className = 'meta';
      metaEl.textContent = bank.meta || sendI18n.bankFallback;
      info.append(nameEl, metaEl);

      const check = document.createElement('div');
      check.className = 'check';

      el.append(mark, info, check);
      el.addEventListener('click', () => selectIntlBank(el, bank));

      list.appendChild(el);
    });
  }

  function selectIntlBank(el, bank){
    document.querySelectorAll('#intlBankOptionList .bank-option').forEach(o=>{
      o.classList.remove('selected');
      const check = o.querySelector('.check');
      if(check) check.innerHTML = '';
    });
    el.classList.add('selected');
    el.querySelector('.check').innerHTML = '<svg viewBox="0 0 24 24" fill="none"><path d="M20 6L9 17l-5-5"/></svg>';

    // Fills in the real fields — same ones you could type into directly —
    // so nothing about the International tab's own required fields
    // changes just because this shortcut was used.
    document.getElementById('intlBankNameInput').value = bank.name || '';
    if(bank.country) document.getElementById('intlCountryInput').value = bank.country;
    if(bank.swift_code) document.getElementById('intlSwiftInput').value = bank.swift_code;
    if(bank.currency) document.getElementById('intlCurrencyInput').value = bank.currency;

    setTimeout(()=>closeIntlSheet(), 180);
  }
</script>
@endsection
