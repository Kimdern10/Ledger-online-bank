@extends('layouts.app')

@section('content')
<?php
    // Every linked account — bank or card — see LinkedAccountController.
    // The "Linked bank" tab submits to WithdrawalController::store(), which
    // already handles both destination types generically (see its own fee
    // logic), so there's no reason to hide the bank half of what's linked.
    $linkedCards = auth()->user()->linkedAccounts()->latest()->get();
?>
<div class="send-wrap">

  <div class="page-header fade-in d1">
      <a href="<?= e(url()->previous()) ?>" class="icon-btn">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
    </a>
    <h1><?= e(__('send.title')) ?></h1>
    <a href="<?= e(route('dashboard')) ?>" class="icon-btn">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><path d="M3 11l9-8 9 8"/><path d="M5 10v10h14V10"/></svg>
    </a>
  </div>

  <?php /* Confirms a transfer was scheduled (not sent — see 'sendSuccess' in
       TransferController::scheduleLedgerTransfer()/scheduleExternalTransfer())
       or that a scheduled one was cancelled. */ ?>
  <?php if (session('sendSuccess')): ?>
    <div class="send-alert send-alert-success fade-in d1"><?= e(session('sendSuccess')) ?></div>
  <?php endif; ?>

  <?php /* Business-logic failures (no matching account, insufficient funds, an
       unsupported "send to" mode) land here — TransferController::store()
       flashes 'sendError' rather than treating these as field validation. */ ?>
  <?php if (session('sendError')): ?>
    <div class="send-alert fade-in d1"><?= e(session('sendError')) ?></div>
  <?php endif; ?>

  <?php /* The "Linked bank" tab actually submits to WithdrawalController::store()
       (see setReceiveMode()'s form.action swap below) — since sending to
       your own linked card is really the same real action as Withdraw ->
       Debit card, this reuses that same backend instead of a second copy of
       it. Its failures (no card selected, none linked yet) flash here. */ ?>
  <?php if (session('withdrawError')): ?>
    <div class="send-alert fade-in d1"><?= e(session('withdrawError')) ?></div>
  <?php endif; ?>

  <?php /* No transaction PIN created yet — see RequiresTransactionPin. A plain
       flag rather than text, so this can include a real link. */ ?>
  <?php if (session('pinRequired')): ?>
    <div class="send-alert fade-in d1">
      <?= e(__('send.pin_required_prefix')) ?>
      <a href="<?= e(route('setting.transaction-pin')) ?>" style="text-decoration:underline; color:inherit;"><?= e(__('send.pin_required_link')) ?></a>.
    </div>
  <?php endif; ?>

  <?php /* Actual field validation (missing recipient, bad amount) */ ?>
  <?php if ($errors->any()): ?>
    <div class="send-alert fade-in d1">
      <ul style="margin:0; padding-left:18px;">
        <?php foreach ($errors->all() as $error): ?>
          <li><?= e($error) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <form method="POST" action="<?= e(route('send.store')) ?>" id="sendForm">
    <?= csrf_field() ?>
    <input type="hidden" name="mode" id="modeInput" value="<?= e(old('mode', 'ledger')) ?>">
    <input type="hidden" name="when" id="whenInput" value="<?= e(old('when', 'now')) ?>">
    <input type="hidden" name="amount" id="amountHidden" value="0">
    <?php /* Only read when the form submits to WithdrawalController::store()
         (mode = linked) — see setReceiveMode() and selectLinkedChip(). */ ?>
    <input type="hidden" name="destination_type" id="destinationTypeInput" value="<?= e(old('destination_type', optional($linkedCards->first())->type ?? 'card')) ?>">
    <input type="hidden" name="linked_account_id" id="linkedAccountIdInput" value="<?= e(old('linked_account_id', optional($linkedCards->first())->id)) ?>">

    <!-- ============ From: fixed to the Ledger account, no picker ============ -->
    <p class="section-label"><?= e(__('send.from')) ?></p>
    <div class="from-row fade-in d2">
      <div class="bank-mark">L</div>
      <div class="bank-info">
        <p class="name">Ledger Federal Credit Union</p>
        <p class="meta"><?= e(__('send.checking_label')) ?> •••• 4471</p>
      </div>
      <div class="bank-balance">
        <p class="amt">$<?= e(number_format((float) auth()->user()->balance, 2)) ?></p>
        <p class="tag"><?= e(__('send.available')) ?></p>
      </div>
    </div>

    <!-- ============ Send to: Ledger | Another bank | Linked bank ============ -->
    <p class="section-label"><?= e(__('send.send_to')) ?></p>
    <div class="fade-in d2">
      <div class="segmented four">
        <button class="seg-btn active" id="segLedger" onclick="setReceiveMode('ledger')" type="button">
          <svg viewBox="0 0 24 24" fill="none"><rect x="4" y="4" width="7" height="7"/><rect x="13" y="4" width="7" height="7"/><rect x="4" y="13" width="7" height="7"/><path d="M14 15h5M16.5 12.5v5"/></svg>
          <?= e(__('send.tab_ledger')) ?>
        </button>
        <button class="seg-btn" id="segBank" onclick="setReceiveMode('bank')" type="button">
          <svg viewBox="0 0 24 24" fill="none"><path d="M3 21h18M4 21V10l8-6 8 6v11M9 21v-6h6v6"/></svg>
          <?= e(__('send.tab_another_bank')) ?>
        </button>
        <button class="seg-btn" id="segInternational" onclick="setReceiveMode('international')" type="button">
          <svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a14 14 0 0 1 0 18a14 14 0 0 1 0-18Z"/></svg>
          <?= e(__('send.tab_international')) ?>
        </button>
        <button class="seg-btn" id="segLinked" onclick="setReceiveMode('linked')" type="button">
          <svg viewBox="0 0 24 24" fill="none"><path d="M10 13a5 5 0 0 0 7.5.5l2-2a5 5 0 0 0-7-7l-1.5 1.5"/><path d="M14 11a5 5 0 0 0-7.5-.5l-2 2a5 5 0 0 0 7 7l1.5-1.5"/></svg>
          <?= e(__('send.tab_linked_bank')) ?>
        </button>
      </div>

      <!-- ============ 1) Ledger: fully manual — account number and name are both just typed in ============ -->
      <div id="ledgerOption">
        <div class="field-group">
          <p class="label"><?= e(__('send.account_number')) ?></p>
          <input type="text" class="text-input" id="recipientAccountInput" name="recipient_account_number" value="<?= e(old('recipient_account_number')) ?>" placeholder="<?= e(__('send.recipient_ledger_account_placeholder')) ?>" inputmode="numeric" autocomplete="off" required>
        </div>
        <div class="field-group">
          <p class="label"><?= e(__('send.recipient_name')) ?></p>
          <input type="text" class="text-input" id="recipientNameInput" name="recipient_name" value="<?= e(old('recipient_name')) ?>" placeholder="<?= e(__('send.recipient_name_placeholder')) ?>" autocomplete="off" required>
        </div>
        <p class="receive-note instant">
          <svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
          <?= e(__('send.instant_ledger_note')) ?>
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
          <p class="label"><?= e(__('send.bank_label')) ?></p>
          <div class="bank-input-row">
            <input type="text" class="text-input" id="bankNameInput" name="bank_name" value="<?= e(old('bank_name')) ?>" placeholder="<?= e(__('send.recipient_bank_name_placeholder')) ?>" autocomplete="off" required>
            <button type="button" class="bank-search-btn" onclick="openSheet()" aria-label="<?= e(__('send.select_bank')) ?>">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.35-4.35"/></svg>
            </button>
          </div>
        </div>

        <div class="field-group">
          <p class="label"><?= e(__('send.recipient_name')) ?></p>
          <input type="text" class="text-input" name="bank_recipient_name" value="<?= e(old('bank_recipient_name')) ?>" placeholder="<?= e(__('send.recipient_placeholder')) ?>" autocomplete="off">
        </div>
        <div class="field-group">
          <p class="label"><?= e(__('send.account_number')) ?></p>
          <input type="text" class="text-input" name="bank_account_number" value="<?= e(old('bank_account_number')) ?>" placeholder="<?= e(__('send.recipient_account_number_placeholder')) ?>" inputmode="numeric" autocomplete="off">
        </div>
        <div class="field-group">
          <p class="label"><?= e(__('send.routing_number')) ?></p>
          <input type="text" class="text-input" name="bank_routing_number" value="<?= e(old('bank_routing_number')) ?>" placeholder="<?= e(__('send.recipient_routing_number_placeholder')) ?>" inputmode="numeric">
        </div>
      </div>

      <!-- ============ 3) International bank: same idea as Another bank, plus country/SWIFT/currency —
           "Select a bank" pulls from the same admin-managed directory, filtered to international entries
           only (see BankDirectoryController::searchInternational()). ============ -->
      <div id="internationalOption" style="display:none;">
        <div class="field-group">
          <p class="label"><?= e(__('send.intl_bank_name_label')) ?></p>
          <div class="bank-input-row">
            <input type="text" class="text-input" id="intlBankNameInput" name="intl_bank_name" value="<?= e(old('intl_bank_name')) ?>" placeholder="<?= e(__('send.intl_bank_name_placeholder')) ?>" autocomplete="off">
            <button type="button" class="bank-search-btn" onclick="openIntlSheet()" aria-label="<?= e(__('send.select_bank')) ?>">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.35-4.35"/></svg>
            </button>
          </div>
        </div>
        <div class="field-group">
          <p class="label"><?= e(__('send.recipient_name')) ?></p>
          <input type="text" class="text-input" id="intlRecipientNameInput" name="intl_recipient_name" value="<?= e(old('intl_recipient_name')) ?>" placeholder="<?= e(__('send.recipient_placeholder')) ?>" autocomplete="off">
        </div>
        <div class="field-group">
          <p class="label"><?= e(__('send.intl_country_label')) ?></p>
          <input type="text" class="text-input" id="intlCountryInput" name="intl_country" value="<?= e(old('intl_country')) ?>" placeholder="<?= e(__('send.intl_country_placeholder')) ?>" autocomplete="off">
        </div>
        <div class="field-group">
          <p class="label"><?= e(__('send.intl_account_label')) ?></p>
          <input type="text" class="text-input" id="intlAccountInput" name="intl_account_number" value="<?= e(old('intl_account_number')) ?>" placeholder="<?= e(__('send.intl_account_placeholder')) ?>" autocomplete="off">
        </div>
        <div class="field-group">
          <p class="label"><?= e(__('send.intl_swift_label')) ?></p>
          <input type="text" class="text-input" id="intlSwiftInput" name="intl_swift_code" value="<?= e(old('intl_swift_code')) ?>" placeholder="<?= e(__('send.intl_swift_placeholder')) ?>" autocomplete="off" style="text-transform:uppercase;">
        </div>
        <div class="field-group">
          <p class="label"><?= e(__('send.intl_currency_label')) ?></p>
          <input type="text" class="text-input" id="intlCurrencyInput" name="intl_currency" value="<?= e(old('intl_currency', 'USD')) ?>" placeholder="<?= e(__('send.intl_currency_placeholder')) ?>" maxlength="3" autocomplete="off" style="text-transform:uppercase;" oninput="scheduleIntlConversion()">
        </div>
        <?php /* Live preview only — see TransferController::convertPreview().
             The rate actually locked in on the transfer (and shown on its
             receipt) is looked up fresh, again, at the moment it's sent or
             scheduled, not carried over from here. */ ?>
        <p class="receive-note" id="intlConvertedNote" style="display:none;">
          <svg viewBox="0 0 24 24" fill="none"><path d="M17 1l4 4-4 4"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/><path d="M7 23l-4-4 4-4"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/></svg>
          <?= e(__('send.intl_converted_prefix')) ?> <strong id="intlConvertedAmount"></strong>
        </p>
        <p class="receive-note">
          <svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></svg>
          <?= e(__('send.intl_transfer_note')) ?>
        </p>
      </div>

      <!-- ============ 4) Linked bank: send to any of your own linked bank accounts or cards ============ -->
      <div id="linkedOption" style="display:none;">
        <p class="from-label"><?= e(__('send.choose_linked_card')) ?></p>
        <?php if ($linkedCards->isEmpty()): ?>
          <p style="padding:10px 4px; opacity:0.65; font-size:13px;">
            <?= e(__('send.no_linked_card')) ?> <a href="<?= e(route('link-account')) ?>" style="color:inherit; text-decoration:underline;"><?= e(__('send.link_one_first')) ?></a>.
          </p>
        <?php else: ?>
          <?php $selectedCardId = (int) old('linked_account_id', $linkedCards->first()->id); ?>
          <?php $selectedType = old('destination_type', $linkedCards->first()->type); ?>
          <div class="linked-chips">
            <?php foreach ($linkedCards as $card): ?>
              <div class="linked-chip <?= e($card->id === $selectedCardId ? 'selected' : '') ?>" data-account-id="<?= e($card->id) ?>" onclick="selectLinkedChip(this, <?= e($card->id) ?>, '<?= e($card->type) ?>')">
                <span class="chip-mark" style="background:var(--ink);"><?= e(substr($card->displayLabel(), 0, 1)) ?></span> <?= e($card->displayLabel()) ?> <?= e($card->detailLabel()) ?>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <p class="receive-note instant" id="linkedFeeNoteCard" style="<?= e($linkedCards->isNotEmpty() && $selectedType !== 'card' ? 'display:none;' : '') ?>">
          <svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
          <?= e(__('send.instant_linked_note_card')) ?>
        </p>
        <p class="receive-note instant" id="linkedFeeNoteBank" style="<?= e($linkedCards->isEmpty() || $selectedType === 'card' ? 'display:none;' : '') ?>">
          <svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
          <?= e(__('send.instant_linked_note_bank')) ?>
        </p>
      </div>
    </div>

    <!-- ============ Amount ============ -->
    <p class="section-label"><?= e(__('send.amount')) ?></p>
    <div class="amount-card fade-in d3">
      <div class="amount-field">
        <span class="cur">$</span>
        <input type="text" id="amountInput" inputmode="decimal" value="" placeholder="0.00" aria-label="<?= e(__('send.amount_input_aria')) ?>">
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
        <p class="label"><?= e(__('send.category')) ?></p>
        <div class="select-wrap">
          <select name="category">
            <option value=""><?= e(__('send.select_category')) ?></option>
            <option value="Family & friends"><?= e(__('send.cat_family_friends')) ?></option>
            <option value="Rent & bills"><?= e(__('send.cat_rent_bills')) ?></option>
            <option value="Business"><?= e(__('send.cat_business')) ?></option>
            <option value="Gift"><?= e(__('send.cat_gift')) ?></option>
          </select>
          <div class="chev">
            <svg viewBox="0 0 24 24" fill="none"><path d="M6 9l6 6 6-6"/></svg>
          </div>
        </div>
      </div>

      <div class="field-group">
        <p class="label"><?= e(__('send.note_label')) ?> <span><?= e(__('send.optional')) ?></span></p>
        <div class="textarea-wrap">
          <textarea name="note" placeholder="<?= e(__('send.note_placeholder')) ?>"></textarea>
          <div class="clip">
            <svg viewBox="0 0 24 24" fill="none"><path d="M21.44 11.05l-9.19 9.19a5 5 0 0 1-7.07-7.07l9.19-9.19a3.5 3.5 0 0 1 4.95 4.95l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg>
          </div>
        </div>
      </div>

      <div class="pay-btn-wrap has-fixed-duplicate">
        <button type="submit" class="pay-btn"><?= e(__('send.send_btn')) ?></button>
      </div>
    </div>
  </form>

</div>

<div class="fixed-bottom-btn">
  <button type="submit" form="sendForm" class="pay-btn"><?= e(__('send.send_btn')) ?></button>
</div>

<!-- ============ Processing overlay ============ -->
<?php /* Shown the moment the form is submitted and left up until the browser
     actually navigates away — it isn't a fake timed animation, it's
     covering the real time the server takes to look up the recipient,
     check the balance, and move the money (see TransferController::store()).
     If something goes wrong (bad recipient, insufficient funds), the
     redirect back to this same page removes it automatically since it's
     a fresh page load. */ ?>
<div class="processing-overlay" id="processingOverlay">
  <div class="processing-spinner"></div>
  <p class="processing-text"><?= e(__('send.processing')) ?></p>
  <p class="processing-sub"><?= e(__('send.processing_sub')) ?></p>
</div>

<?php /* ============ transaction PIN confirmation ============
     Pops up right before the real submit, for every mode this form can
     end up posting to (Ledger/Another bank -> TransferController::store(),
     Linked bank -> WithdrawalController::store()). See openPinModal() /
     confirmPin() below and RequiresTransactionPin server-side. */ ?>
<div class="sheet-overlay" id="pinModalOverlay" onclick="closePinModal(event)">
  <div class="sheet" onclick="event.stopPropagation()" style="max-width:340px; margin:0 auto;">
    <div class="sheet-handle"></div>
    <div class="sheet-head">
      <h4><?= e(__('send.pin_modal_title')) ?></h4>
      <div class="icon-btn" onclick="closePinModal()">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8"><path d="M18 6L6 18M6 6l12 12"/></svg>
      </div>
    </div>
    <div style="padding:4px 20px 22px;">
      <p style="margin:0 0 14px; font-size:13px; color:var(--text-3);"><?= e(__('send.pin_modal_body')) ?></p>
      <input type="password" class="text-input" id="pinModalInput" inputmode="numeric" maxlength="4" placeholder="••••" style="text-align:center; font-size:22px; letter-spacing:10px;" autocomplete="off">
      <p id="pinModalError" style="display:none; color:#B4121B; font-size:12.5px; margin:8px 0 0;"></p>
      <button type="button" class="pay-btn" style="margin-top:16px; width:100%;" onclick="confirmPin()"><?= e(__('send.pin_confirm')) ?></button>
    </div>
  </div>
</div>

<!-- ============ bank directory sheet (for "Another bank") ============ -->
<div class="sheet-overlay" id="sheetOverlay" onclick="closeSheet(event)">
  <div class="sheet" onclick="event.stopPropagation()">
    <div class="sheet-handle"></div>
    <div class="sheet-head">
      <h4><?= e(__('send.select_bank')) ?></h4>
      <div class="icon-btn" onclick="closeSheet()">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8"><path d="M18 6L6 18M6 6l12 12"/></svg>
      </div>
    </div>

    <div class="sheet-search">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.35-4.35"/></svg>
      <input type="text" id="bankSearch" placeholder="<?= e(__('send.bank_search_placeholder')) ?>" oninput="onBankSearchInput(this.value)">
    </div>

    <div id="bankOptionList">
      <?php /* Populated live from the admin-managed bank directory as you type
           — see onBankSearchInput()/renderBankResults() below. */ ?>
    </div>

    <p id="bankSearchHint" style="text-align:center; font-size:13px; color:var(--text-3); padding:18px 0;">
      <?= e(__('send.bank_search_hint')) ?>
    </p>
    <p id="bankSearchLoading" style="display:none; text-align:center; font-size:13px; color:var(--text-3); padding:18px 0;">
      <?= e(__('send.bank_search_loading')) ?>
    </p>
    <p id="noBankResults" style="display:none; text-align:center; font-size:13px; color:var(--text-3); padding:18px 0;">
      <?= e(__('send.no_bank_results')) ?>
    </p>
  </div>
</div>

<!-- ============ international bank directory sheet (for "International bank") ============ -->
<div class="sheet-overlay" id="intlSheetOverlay" onclick="closeIntlSheet(event)">
  <div class="sheet" onclick="event.stopPropagation()">
    <div class="sheet-handle"></div>
    <div class="sheet-head">
      <h4><?= e(__('send.select_bank')) ?></h4>
      <div class="icon-btn" onclick="closeIntlSheet()">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8"><path d="M18 6L6 18M6 6l12 12"/></svg>
      </div>
    </div>

    <div class="sheet-search">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.35-4.35"/></svg>
      <input type="text" id="intlBankSearch" placeholder="<?= e(__('send.intl_bank_search_placeholder')) ?>" oninput="onIntlBankSearchInput(this.value)">
    </div>

    <div id="intlBankOptionList"></div>

    <p id="intlBankSearchHint" style="text-align:center; font-size:13px; color:var(--text-3); padding:18px 0;">
      <?= e(__('send.intl_bank_search_hint')) ?>
    </p>
    <p id="intlBankSearchLoading" style="display:none; text-align:center; font-size:13px; color:var(--text-3); padding:18px 0;">
      <?= e(__('send.bank_search_loading')) ?>
    </p>
    <p id="noIntlBankResults" style="display:none; text-align:center; font-size:13px; color:var(--text-3); padding:18px 0;">
      <?= e(__('send.no_bank_results')) ?>
    </p>
  </div>
</div>

<?php
    // Every JS-side user-facing string on this page (alerts, dynamically
    // rendered bank-search text) is pre-translated server-side here, since
    // plain JS has no access to __() — see send.php lang files for the
    // source text in each language. Built as a plain array first, then
    // handed to json_encode() below — json_encode() with __() calls nested
    // directly inside its own argument list doesn't compile cleanly.
    $sendI18n = [
        'linkCardFirstAlert' => __('send.link_card_first_alert'),
        'pinError' => __('send.pin_error'),
        'noBankResults' => __('send.no_bank_results'),
        'bankSearchError' => __('send.bank_search_error'),
        'creditUnion' => __('send.credit_union'),
        'bankFallback' => __('send.bank_fallback'),
    ];
?>
<script>
  window.LedgerSendConfig = {
    i18n: <?= json_encode($sendI18n) ?>,
    withdrawStoreUrl: "<?= e(route('withdraw.store')) ?>",
    sendStoreUrl: "<?= e(route('send.store')) ?>",
    internationalConvertUrl: "<?= e(route('send.international.convert')) ?>",
    sendLookupUrl: "<?= e(route('send.lookup')) ?>",
    banksSearchUrl: "<?= e(route('banks.search')) ?>",
    banksSearchInternationalUrl: "<?= e(route('banks.search.international')) ?>",
  };
</script>
@endsection
