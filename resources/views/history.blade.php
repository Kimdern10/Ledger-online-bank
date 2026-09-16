@extends('layouts.app')

@section('content')
<div class="send-wrap">

  <div class="page-header fade-in d1">
    <a href="<?= e(url()->previous()) ?>" class="icon-btn">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
    </a>
    <h1><?= e(__('history.title')) ?></h1>
    <a href="<?= e(route('dashboard')) ?>" class="icon-btn">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><path d="M3 11l9-8 9 8"/><path d="M5 10v10h14V10"/></svg>
    </a>
  </div>

  <div class="summary-row fade-in d2">
    <div class="summary-stat in">
      <p class="ss-label"><?= e(__('history.money_in')) ?></p>
      <p class="ss-value">$<?= e(number_format($moneyIn, 2)) ?></p>
    </div>
    <div class="summary-stat out">
      <p class="ss-label"><?= e(__('history.money_out')) ?></p>
      <p class="ss-value">$<?= e(number_format($moneyOut, 2)) ?></p>
    </div>
  </div>

  <div class="sheet-search fade-in d2">
    <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.35-4.35"/></svg>
    <input type="text" id="historySearch" placeholder="<?= e(__('history.search_placeholder')) ?>" oninput="applyFilters()">
  </div>

  <div class="filter-row fade-in d2">
    <button type="button" class="quick-amt-btn active" onclick="setFilter(this,'all')"><?= e(__('history.filter_all')) ?></button>
    <button type="button" class="quick-amt-btn" onclick="setFilter(this,'sent')"><?= e(__('history.filter_sent')) ?></button>
    <button type="button" class="quick-amt-btn" onclick="setFilter(this,'received')"><?= e(__('history.filter_received')) ?></button>
  </div>

  <?php /* Every group and row below comes straight from HistoryController —
       every real transaction type this user has (transfers, withdrawals,
       top-ups, admin balance adjustments, and card purchases), grouped by
       real dates. Rows with a receipt page (transfers, withdrawals,
       top-ups) link to it, exactly like Send Money's own receipt. Admin
       adjustments and card purchases have no receipt of their own — those
       render as plain, non-clickable rows instead. */ ?>
  <?php $__ledger_forelse_1 = true; foreach ($groups as $groupLabel => $items): $__ledger_forelse_1 = false; ?>
    <div class="date-group fade-in d3">
      <p class="date-group-label"><?= e($groupLabel) ?></p>
      <div class="tx-card">
        <?php foreach ($items as $item): ?>
          <?php if ($item['url']): ?>
            <a
              href="<?= e($item['url']) ?>"
              class="tx-row <?= e($item['type'] === 'received' ? 'in' : 'out') ?>"
              data-type="<?= e($item['type']) ?>"
              data-name="<?= e(strtolower($item['name'])) ?>"
            >
              <div class="tx-bar"></div>
              <div class="tx-mid">
                <p class="name"><?= e($item['name']) ?></p>
                <p class="meta"><?= e($item['meta']) ?></p>
              </div>
              <div class="tx-amt"><?= e($item['amount_display']) ?></div>
            </a>
          <?php else: ?>
            <?php /* A scheduled/cancelled/failed transfer has a real receipt
                 now (view_url — see HistoryController) but still can't be a
                 plain <a> the way the branch above is: a still-'scheduled'
                 row needs its Cancel form to sit inside it, and a <form>
                 nested in an <a> is invalid HTML. So this row navigates via
                 its own onclick instead, and the Cancel button's existing
                 onclick="event.stopPropagation()" keeps a click on IT from
                 also triggering the row's. A row with neither (card
                 purchases, which have no receipt at all) just isn't
                 clickable, same as before. */ ?>
            <div
              class="tx-row not-clickable <?= e($item['type'] === 'received' ? 'in' : 'out') ?>"
              data-type="<?= e($item['type']) ?>"
              data-name="<?= e(strtolower($item['name'])) ?>"
              <?php if ($item['view_url'] ?? null): ?>
                onclick="window.location='<?= e($item['view_url']) ?>'"
                style="cursor:pointer;"
              <?php endif; ?>
            >
              <div class="tx-bar"></div>
              <div class="tx-mid">
                <p class="name"><?= e($item['name']) ?></p>
                <p class="meta"><?= e($item['meta']) ?></p>
              </div>
              <div class="tx-amt">
                <?= e($item['amount_display']) ?>
                <?php /* Only a still-'scheduled' row gets one of these — see
                     HistoryController. */ ?>
                <?php if ($item['cancel_route']): ?>
                  <form method="POST" action="<?= e($item['cancel_route']) ?>" class="cancel-scheduled-form" onclick="event.stopPropagation()">
                    <?= csrf_field() ?>
                    <button type="submit" class="cancel-scheduled-btn"><?= e(__('history.cancel')) ?></button>
                  </form>
                <?php endif; ?>
              </div>
            </div>
          <?php endif; ?>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endforeach; if ($__ledger_forelse_1): ?>
    <p class="no-tx-empty fade-in d3">
      <?= e(__('history.no_transactions_yet')) ?>
    </p>
  <?php endif; ?>

  <p id="noResults" style="display:none; text-align:center; font-size:13px; color:var(--text-3); padding:30px 0;">
    <?= e(__('history.no_matches')) ?>
  </p>

  @include('partials.pagination', ['paginator' => $transactions])

</div>

@endsection
