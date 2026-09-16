@extends('layouts.app')

@section('content')
<?php
    $user = auth()->user();
    $primaryAccountNumber = $user->checking_account_number ?: $user->savings_account_number;
    $accountLabel = $user->checking_account_number ? 'Checking' : ($user->savings_account_number ? 'Savings' : 'Account');

    // Real linked accounts — see LinkedAccountController. Top Up only ever
    // offers accounts the user has actually linked on the Link Account page.
    $bankAccounts = $user->linkedAccounts()->where('type', 'bank')->latest()->get();
    $cardAccounts = $user->linkedAccounts()->where('type', 'card')->latest()->get();
?>
<div class="send-wrap">

  <div class="page-header fade-in d1">
    <a href="<?= e(url()->previous()) ?>" class="icon-btn">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
    </a>
    <h1><?= e(__('topup.title')) ?></h1>
    <a href="<?= e(route('dashboard')) ?>" class="icon-btn">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><path d="M3 11l9-8 9 8"/><path d="M5 10v10h14V10"/></svg>
    </a>
  </div>

  <?php if (session('topUpError')): ?>
    <div class="send-alert fade-in d1"><?= e(session('topUpError')) ?></div>
  <?php endif; ?>
  <?php /* No transaction PIN created yet — see RequiresTransactionPin. */ ?>
  <?php if (session('pinRequired')): ?>
    <div class="send-alert fade-in d1">
      <?= e(__('topup.pin_required_prefix')) ?>
      <a href="<?= e(route('setting.transaction-pin')) ?>" style="text-decoration:underline; color:inherit;"><?= e(__('topup.pin_required_link')) ?></a>.
    </div>
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

  <p class="section-label"><?= e(__('topup.to')) ?></p>
  <div class="from-row fade-in d2">
    <div class="bank-mark">L</div>
    <div class="bank-info">
      <p class="name">Ledger Federal Credit Union</p>
      <p class="meta"><?= e($accountLabel) ?> •••• <?= e(substr((string) $primaryAccountNumber, -4)) ?></p>
    </div>
    <div class="bank-balance">
      <p class="amt">$<?= e(number_format((float) $user->balance, 2)) ?></p>
      <p class="tag"><?= e(__('topup.current_balance')) ?></p>
    </div>
  </div>

  <form method="POST" action="<?= e(route('top-up.store')) ?>" id="topUpForm">
    <?= csrf_field() ?>
    <input type="hidden" name="source_type" id="sourceTypeInput" value="bank">
    <input type="hidden" name="linked_account_id" id="linkedAccountIdInput" value="<?= e(optional($bankAccounts->first())->id) ?>">
    <input type="hidden" name="amount" id="amountHidden" value="0">

    <p class="section-label"><?= e(__('topup.top_up_from')) ?></p>
    <div class="fade-in d2">
      <div class="segmented" style="margin-bottom:14px;">
        <button class="seg-btn active" id="segFromBank" onclick="setTopUpMode('bank')" type="button">
          <svg viewBox="0 0 24 24" fill="none"><path d="M3 21h18M4 21V10l8-6 8 6v11M9 21v-6h6v6"/></svg>
          <?= e(__('topup.linked_bank')) ?>
        </button>
        <button class="seg-btn" id="segFromCard" onclick="setTopUpMode('card')" type="button">
          <svg viewBox="0 0 24 24" fill="none"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>
          <?= e(__('topup.debit_credit_card')) ?>
        </button>
      </div>

      <div id="topUpBankPanel">
        <?php if ($bankAccounts->isEmpty()): ?>
          <p style="padding:10px 4px; opacity:0.65; font-size:13px;">
            <?= e(__('topup.no_linked_bank')) ?> <a href="<?= e(route('link-account')) ?>" style="color:inherit; text-decoration:underline;"><?= e(__('topup.link_one_first')) ?></a>.
          </p>
        <?php else: ?>
          <div class="linked-chips">
            <?php foreach ($bankAccounts as $index => $account): ?>
              <div class="linked-chip <?= e($index === 0 ? 'selected' : '') ?>" data-account-id="<?= e($account->id) ?>" onclick="selectSourceChip(this, 'bank', <?= e($account->id) ?>)">
                <span class="chip-mark" style="background:var(--ink);"><?= e(substr($account->displayLabel(), 0, 1)) ?></span> <?= e($account->displayLabel()) ?> <?= e($account->detailLabel()) ?>
              </div>
            <?php endforeach; ?>
          </div>
          <p class="receive-note instant">
            <svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
            <?= e(__('topup.bank_note')) ?>
          </p>
        <?php endif; ?>
      </div>

      <div id="topUpCardPanel" style="display:none;">
        <?php if ($cardAccounts->isEmpty()): ?>
          <p style="padding:10px 4px; opacity:0.65; font-size:13px;">
            <?= e(__('topup.no_linked_card')) ?> <a href="<?= e(route('link-account')) ?>" style="color:inherit; text-decoration:underline;"><?= e(__('topup.link_one_first')) ?></a>.
          </p>
        <?php else: ?>
          <div class="linked-chips">
            <?php foreach ($cardAccounts as $index => $account): ?>
              <div class="linked-chip <?= e($index === 0 ? 'selected' : '') ?>" data-account-id="<?= e($account->id) ?>" onclick="selectSourceChip(this, 'card', <?= e($account->id) ?>)">
                <span class="chip-mark" style="background:var(--ink);"><?= e(substr($account->displayLabel(), 0, 1)) ?></span> <?= e($account->displayLabel()) ?> <?= e($account->detailLabel()) ?>
              </div>
            <?php endforeach; ?>
          </div>
          <p class="receive-note">
            <svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
            <?= e(__('topup.card_note')) ?>
          </p>
        <?php endif; ?>
      </div>
    </div>

    <p class="section-label"><?= e(__('topup.amount')) ?></p>
    <div class="amount-card fade-in d3">
      <div class="amount-field">
        <span class="cur">$</span>
        <input type="text" id="amountInput" inputmode="decimal" value="100" aria-label="<?= e(__('topup.amount_aria_label')) ?>">
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
          <span><?= e(__('topup.fee_amount')) ?></span>
          <span id="feeAmountDisplay">$100.00</span>
        </div>
        <div class="fee-row">
          <span><?= e(__('topup.fee_card_processing')) ?></span>
          <span id="feeFeeDisplay">$2.90</span>
        </div>
        <div class="fee-row total">
          <span><?= e(__('topup.fee_total_charged')) ?></span>
          <span id="feeTotalDisplay">$102.90</span>
        </div>
      </div>
    </div>

    <div class="pay-btn-wrap fade-in d3">
      <button type="submit" class="pay-btn" id="topUpSubmitBtn" <?= e($bankAccounts->isEmpty() && $cardAccounts->isEmpty() ? 'disabled' : '') ?>><?= e(__('topup.add_money_btn')) ?></button>
    </div>
  </form>

</div>

<div class="processing-overlay" id="processingOverlay">
  <div class="processing-spinner"></div>
  <p class="processing-text"><?= e(__('topup.processing')) ?></p>
  <p class="processing-sub"><?= e(__('topup.processing_sub')) ?></p>
</div>

<?php /* ============ transaction PIN confirmation ============
     See send.blade.php's copy of this same modal for the full explanation. */ ?>
<div class="sheet-overlay" id="pinModalOverlay" onclick="closePinModal(event)">
  <div class="sheet" onclick="event.stopPropagation()" style="max-width:340px; margin:0 auto;">
    <div class="sheet-handle"></div>
    <div class="sheet-head">
      <h4><?= e(__('topup.pin_modal_title')) ?></h4>
      <div class="icon-btn" onclick="closePinModal()">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8"><path d="M18 6L6 18M6 6l12 12"/></svg>
      </div>
    </div>
    <div style="padding:4px 20px 22px;">
      <p style="margin:0 0 14px; font-size:13px; color:var(--text-3);"><?= e(__('topup.pin_modal_body')) ?></p>
      <input type="password" class="text-input" id="pinModalInput" inputmode="numeric" maxlength="4" placeholder="••••" style="text-align:center; font-size:22px; letter-spacing:10px;" autocomplete="off">
      <p id="pinModalError" style="display:none; color:#B4121B; font-size:12.5px; margin:8px 0 0;"></p>
      <button type="button" class="pay-btn" style="margin-top:16px; width:100%;" onclick="confirmPin()"><?= e(__('topup.pin_confirm')) ?></button>
    </div>
  </div>
</div>

<script>
  window.LedgerTopUpConfig = {
    linkAccountFirstAlert: <?= json_encode(__('topup.link_account_first_alert')) ?>,
    pinError: <?= json_encode(__('topup.pin_error')) ?>,
  };
</script>
@endsection
