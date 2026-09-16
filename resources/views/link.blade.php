@extends('layouts.app')

@section('content')
<?php
    // Real linked accounts for this user — see LinkedAccountController and
    // LinkedAccount.php. Newest first, bank and card rows mixed together,
    // same as the "Already linked" list this replaces.
    $accounts = auth()->user()->linkedAccounts()->latest()->get();
?>
<div class="send-wrap">

  <div class="page-header fade-in d1">
    <a href="<?= e(url()->previous()) ?>" class="icon-btn">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
    </a>
    <h1><?= e(__('link.title')) ?></h1>
    <a href="<?= e(route('dashboard')) ?>" class="icon-btn">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><path d="M3 11l9-8 9 8"/><path d="M5 10v10h14V10"/></svg>
    </a>
  </div>

  <?php if (session('linkError')): ?>
    <div class="send-alert fade-in d1"><?= e(session('linkError')) ?></div>
  <?php endif; ?>

  <?php if ($errors->any()): ?>
    <div class="send-alert fade-in d1">
      <ul style="margin:0; padding-left:18px;">
        <?php foreach ($errors->all() as $error): ?>
          <li><?= e($error) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <!-- ============ already linked ============ -->
  <p class="section-label fade-in d2"><?= e(__('link.already_linked')) ?></p>
  <div class="setting-list fade-in d2" style="margin-bottom:24px;">
    <?php $__ledger_forelse_1 = true; foreach ($accounts as $account): $__ledger_forelse_1 = false; ?>
      <div class="setting-item" style="cursor:default;">
        <div class="s-icon" style="background:var(--ink); opacity:1;">
          <?php if ($account->isBank()): ?>
            <svg viewBox="0 0 24 24" fill="none" stroke="var(--wheat)" stroke-width="1.7"><path d="M3 21h18M4 21V10l8-6 8 6v11M9 21v-6h6v6"/></svg>
          <?php else: ?>
            <svg viewBox="0 0 24 24" fill="none" stroke="var(--wheat)" stroke-width="1.7"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>
          <?php endif; ?>
        </div>
        <span class="label"><?= e($account->displayLabel()) ?><span class="linked-tag"><?= e($account->detailLabel()) ?></span></span>
        <form method="POST" action="<?= e(route('link-account.destroy', $account)) ?>" style="display:contents;" data-confirm="<?= e(__('link.remove_confirm', ['label' => $account->displayLabel(), 'detail' => $account->detailLabel()])) ?>" data-confirm-danger="1" data-confirm-button="<?= e(__('link.remove_button')) ?>">
          <?= csrf_field() ?>
          <?= method_field('DELETE') ?>
          <button type="submit" class="remove-btn" aria-label="<?= e(__('link.remove_aria', ['label' => $account->displayLabel()])) ?>">
            <svg viewBox="0 0 24 24" fill="none"><path d="M18 6L6 18M6 6l12 12"/></svg>
          </button>
        </form>
      </div>
    <?php endforeach; if ($__ledger_forelse_1): ?>
      <p style="padding:14px 4px; opacity:0.65;"><?= e(__('link.empty_state')) ?></p>
    <?php endif; ?>
  </div>

  <!-- ============ add new: bank account or card ============ -->
  <p class="section-label fade-in d3"><?= e(__('link.add_new')) ?></p>

  <form method="POST" action="<?= e(route('link-account.store')) ?>" id="linkForm" class="fade-in d3">
    <?= csrf_field() ?>
    <input type="hidden" name="type" id="linkTypeInput" value="bank">

    <div class="segmented" style="margin-bottom:18px;">
      <button class="seg-btn active" id="segLinkBank" onclick="setLinkMode('bank')" type="button">
        <svg viewBox="0 0 24 24" fill="none"><path d="M3 21h18M4 21V10l8-6 8 6v11M9 21v-6h6v6"/></svg>
        <?= e(__('link.bank_account_tab')) ?>
      </button>
      <button class="seg-btn" id="segLinkCard" onclick="setLinkMode('card')" type="button">
        <svg viewBox="0 0 24 24" fill="none"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>
        <?= e(__('link.card_tab')) ?>
      </button>
    </div>

    <!-- ---- link a bank account ---- -->
    <div id="linkBankPanel">
      <div class="field-group">
        <p class="label"><?= e(__('link.bank_label')) ?></p>
        <div class="bank-input-row">
          <input type="text" class="text-input" id="linkBankNameInput" name="bank_name" value="<?= e(old('bank_name')) ?>" placeholder="<?= e(__('link.bank_name_placeholder')) ?>" autocomplete="off" required>
          <button type="button" class="bank-search-btn" onclick="openSheet()" aria-label="<?= e(__('link.search_banks_aria')) ?>">
            <?php /* Missing stroke="currentColor" here (present on this same
                 icon in send.blade.php) meant the icon had neither a fill
                 nor a stroke — completely invisible even with the button
                 box itself now properly sized/bordered. */ ?>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.35-4.35"/></svg>
          </button>
        </div>
      </div>
      <div class="field-group">
        <p class="label"><?= e(__('link.account_holder_name')) ?></p>
        <input type="text" class="text-input" name="account_holder_name" value="<?= e(old('account_holder_name')) ?>" placeholder="<?= e(__('link.account_holder_name_placeholder')) ?>" autocomplete="off" required>
      </div>
      <div class="field-group">
        <p class="label"><?= e(__('link.account_number')) ?></p>
        <input type="text" class="text-input" name="account_number" value="<?= e(old('account_number')) ?>" placeholder="<?= e(__('link.account_number_placeholder')) ?>" inputmode="numeric" autocomplete="off" required>
      </div>
      <div class="field-group">
        <p class="label"><?= e(__('link.routing_number')) ?></p>
        <input type="text" class="text-input" id="linkRoutingNumberInput" name="routing_number" value="<?= e(old('routing_number')) ?>" placeholder="<?= e(__('link.routing_number_placeholder')) ?>" inputmode="numeric" maxlength="9" required>
      </div>
    </div>

    <!-- ---- link a debit/credit card ---- -->
    <div id="linkCardPanel" style="display:none;">
      <div class="card-visual" id="cardPreview">
        <div class="cv-top left-align">
          <svg width="34" height="24" viewBox="0 0 34 24" fill="none">
            <defs>
              <linearGradient id="chipGradLink" x1="0" y1="0" x2="34" y2="24" gradientUnits="userSpaceOnUse">
                <stop offset="0" stop-color="#F3D287"/>
                <stop offset="1" stop-color="#C9A24B"/>
              </linearGradient>
            </defs>
            <rect x="0.5" y="0.5" width="33" height="23" rx="4" fill="url(#chipGradLink)" stroke="#8C6A22" stroke-width="0.6"/>
            <line x1="12" y1="0.5" x2="12" y2="23.5" stroke="#8C6A22" stroke-width="0.6"/>
            <line x1="22" y1="0.5" x2="22" y2="23.5" stroke="#8C6A22" stroke-width="0.6"/>
            <line x1="0.5" y1="8" x2="33.5" y2="8" stroke="#8C6A22" stroke-width="0.6"/>
            <line x1="0.5" y1="16" x2="33.5" y2="16" stroke="#8C6A22" stroke-width="0.6"/>
            <rect x="12" y="8" width="10" height="8" fill="none" stroke="#8C6A22" stroke-width="0.6" rx="1.5"/>
          </svg>
          <span style="font-family:'Newsreader', serif; font-weight:600; font-size:15px;">Ledger</span>
        </div>
        <div>
          <div class="cv-number" id="cvNumber">•••• •••• •••• ••••</div>
          <div class="cv-bottom">
            <span class="cv-name" id="cvName"><?= e(__('link.cardholder_name_default')) ?></span>
            <span id="cvExpiry"><?= e(__('link.expiry_placeholder')) ?></span>
          </div>
        </div>
      </div>

      <div class="field-group">
        <p class="label"><?= e(__('link.card_number')) ?></p>
        <input type="text" class="text-input" id="cardNumberInput" name="card_number" value="<?= e(old('card_number')) ?>" placeholder="<?= e(__('link.card_number_placeholder')) ?>"
               inputmode="numeric" maxlength="19" oninput="formatCardPreview()" required>
      </div>
      <div class="field-group">
        <p class="label"><?= e(__('link.cardholder_name')) ?></p>
        <input type="text" class="text-input" id="cardNameInput" name="card_name" value="<?= e(old('card_name')) ?>" placeholder="<?= e(__('link.cardholder_name_placeholder')) ?>"
               autocomplete="off" oninput="formatCardPreview()" required>
      </div>
      <div style="display:flex; gap:12px;">
        <div class="field-group" style="flex:1;">
          <p class="label"><?= e(__('link.expiry')) ?></p>
          <input type="text" class="text-input" id="cardExpiryInput" name="card_expiry" value="<?= e(old('card_expiry')) ?>" placeholder="<?= e(__('link.expiry_placeholder')) ?>" maxlength="5" oninput="formatCardPreview()" required>
        </div>
        <div class="field-group" style="flex:1;">
          <p class="label"><?= e(__('link.cvv')) ?></p>
          <input type="text" class="text-input" name="cvv" placeholder="<?= e(__('link.cvv_placeholder')) ?>" inputmode="numeric" maxlength="4" required>
        </div>
      </div>
    </div>

    <div class="pay-btn-wrap">
      <button type="submit" class="pay-btn"><?= e(__('link.link_account_button')) ?></button>
    </div>
  </form>

</div>

<!-- ============ bank picker sheet ============ -->
<div class="sheet-overlay" id="sheetOverlay" onclick="closeSheet(event)">
  <div class="sheet" onclick="event.stopPropagation()">
    <div class="sheet-handle"></div>
    <div class="sheet-head">
      <h4><?= e(__('link.select_a_bank')) ?></h4>
      <div class="icon-btn" onclick="closeSheet()">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8"><path d="M18 6L6 18M6 6l12 12"/></svg>
      </div>
    </div>
    <div class="sheet-search">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.35-4.35"/></svg>
      <input type="text" id="bankSearch" placeholder="<?= e(__('link.bank_search_placeholder')) ?>" oninput="onBankSearchInput(this.value)">
    </div>
    <div id="bankOptionList">
      <?php /* Populated live from the admin-managed bank directory (the same
           one Send Money's "Another bank" tab searches, and the one admins
           manage from Admin > Banks) — see openSheet()/onBankSearchInput()/
           renderBankResults() below. This used to be nine hardcoded banks
           with no connection to that directory at all, so a bank an admin
           added never showed up here, and searching for anything outside
           that fixed list of nine just came up empty with no way forward. */ ?>
    </div>
    <p id="bankSearchLoading" style="display:none; text-align:center; font-size:13px; color:var(--text-3); padding:18px 0;">
      <?= e(__('link.bank_search_loading')) ?>
    </p>
    <p id="noBankResults" style="display:none; text-align:center; font-size:13px; color:var(--text-3); padding:18px 0;">
      <?= e(__('link.no_bank_results')) ?>
    </p>
  </div>
</div>

<div class="ledger-toast" id="ledgerToast">
  <svg viewBox="0 0 24 24" fill="none"><path d="M20 6L9 17l-5-5"/></svg>
  <span id="toastText"><?= e(__('link.toast_linked')) ?></span>
</div>

<?php /* .bank-input-row / .bank-search-btn style the "Bank" field's search
     button (the magnifying glass that opens the picker sheet below). This
     page reuses those same class names from Send Money's "Another bank"
     picker, but the rules themselves only ever lived in send.blade.php's
     own <style> block — never copied here, so on this page the button sat
     in the DOM unstyled: no size, no border, no icon sizing, just an
     almost-invisible sliver next to the Bank field. Same rules as
     send.blade.php, so the button looks and behaves identically here.

     .send-alert / .send-alert-success are the same story, one level up:
     this page's own linkError/validation-error banners near the top (and
     the "already linked" duplicate-account message) use that class, but
     it was never styled here either — so a rejected submission (a
     required field, a duplicate account number/card) WAS actually
     failing and coming back with a real message, it just rendered as
     plain unstyled text that was easy to miss, which is exactly what
     "click Link account and nothing happens" looks like from outside. */ ?>
<script>
  window.LedgerLinkConfig = {
    banksSearchUrl: "<?= e(route('banks.search')) ?>",
    statusMessage: <?php if (session('status')): ?> <?= json_encode(session('status')) ?> <?php else: ?> null <?php endif; ?>,
    i18n: {
      bankSearchError: <?= json_encode(__('link.bank_search_error')) ?>,
      cardholderNameDefault: <?= json_encode(__('link.cardholder_name_default')) ?>,
      expiryPlaceholder: <?= json_encode(__('link.expiry_placeholder')) ?>,
    },
  };
</script>
@endsection
