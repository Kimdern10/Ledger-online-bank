<?php /* <x-layouts::app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
            </div>
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
            </div>
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
            </div>
        </div>
        <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
            <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
        </div>
    </div>
</x-layouts::app> */ ?>

@extends('layouts.app')
@section('content')

    <?php
        $user = auth()->user();

        // Balance always shows — 0.00 for a brand new account, not blank.
        // $balanceValue (a plain float) is what the count-up animation in
        // the script below actually animates toward; $balanceWhole /
        // $balanceCents are only the pre-animation fallback shown if
        // JavaScript is off.
        $balanceValue = (float) ($user->balance ?? 0);
        $formattedBalance = number_format($balanceValue, 2);
        [$balanceWhole, $balanceCents] = explode('.', $formattedBalance);

        // Real spending this month — every completed Ledger transfer sent,
        // external ("another bank") transfer sent, international transfer
        // sent, and withdrawal, added up. Deliberately NOT counting money
        // coming in (received
        // transfers, top-ups) or admin balance adjustments — this is meant
        // to answer "how much has actually left my account", the way a
        // spending tracker would, not "how has my balance moved."
        // Card purchases aren't included either — see HistoryController's
        // own comment on Card::transactions(): those are seed/demo data
        // that never touch the real balance, so counting them here would
        // overstate real spending.
        $monthStart = now()->startOfMonth();

        $spendItems = collect();

        $user->sentTransfers()
            ->where('status', 'completed')
            ->where('created_at', '>=', $monthStart)
            ->get(['amount', 'category'])
            ->each(fn ($t) => $spendItems->push(['amount' => (float) $t->amount, 'category' => $t->category ?: 'Transfers']));

        $user->externalTransfers()
            ->where('status', 'completed')
            ->where('created_at', '>=', $monthStart)
            ->get(['amount', 'category'])
            ->each(fn ($t) => $spendItems->push(['amount' => (float) $t->amount, 'category' => $t->category ?: 'Transfers']));

        $user->internationalTransfers()
            ->where('status', 'completed')
            ->where('created_at', '>=', $monthStart)
            ->get(['amount', 'category'])
            ->each(fn ($t) => $spendItems->push(['amount' => (float) $t->amount, 'category' => $t->category ?: 'Transfers']));

        $user->withdrawals()
            ->where('created_at', '>=', $monthStart)
            ->get(['amount'])
            ->each(fn ($w) => $spendItems->push(['amount' => (float) $w->amount, 'category' => 'Withdrawals']));

        $spentAmount = (float) $spendItems->sum('amount');

        // Real, user-set budget — see BudgetSettingController and
        // User::monthlyBudget() (falls back to $3,000 until someone visits
        // Set Budget for the first time).
        $budgetAmount = $user->monthlyBudget();
        $isOverBudget = $spentAmount > $budgetAmount;

        $spendPercent = $budgetAmount > 0 ? min(100, round(($spentAmount / $budgetAmount) * 100)) : 0;
        $ringRadius = 32;
        $ringCircumference = 2 * M_PI * $ringRadius;
        $ringTargetOffset = $ringCircumference * (1 - $spendPercent / 100);

        // Transfer/ExternalTransfer both have a free-text 'category' field
        // (whatever the user typed when sending, if anything — see
        // send.blade.php), so the three chips below now show whatever
        // categories this user has actually spent under this month, biggest
        // first, instead of a hardcoded Living/Travel/Dining that had no
        // connection to real data.
        $spendCategoryColors = ['#2F6F62', '#C9A24B', '#C1503C'];
        $topSpendCategories = $spendItems
            ->groupBy('category')
            ->map(fn ($items, $cat) => ['label' => $cat, 'total' => $items->sum('amount')])
            ->sortByDesc('total')
            ->take(3)
            ->values();

        $hour = now()->hour;
        $greeting = $hour < 12 ? __('dashboard.greeting_morning') : ($hour < 18 ? __('dashboard.greeting_afternoon') : __('dashboard.greeting_evening'));

        // Builds the list of account chips to show — one entry per account
        // number the user actually has. Someone who picked "checking" or
        // "savings" only at onboarding gets one chip; "both" gets two, and
        // the chip becomes clickable/auto-cycling (see the script below).
        $accounts = collect([
            $user->checking_account_number ? ['label' => __('dashboard.checking'), 'last4' => substr($user->checking_account_number, -4)] : null,
            $user->savings_account_number ? ['label' => __('dashboard.savings'), 'last4' => substr($user->savings_account_number, -4)] : null,
        ])->filter()->values();

        if ($accounts->isEmpty()) {
            $accounts = collect([['label' => __('dashboard.account_fallback'), 'last4' => '----']]);
        }

        // Seeds the bell dropdown with real content on first paint — the
        // script below polls /notifications/poll every ~20s after this to
        // keep it live, but nobody should see an empty bell for the second
        // or two before that first poll lands.
        $initialNotifications = $user->appNotifications()->latest()->take(10)->get();
        $initialUnreadCount = $initialNotifications->whereNull('read_at')->count();
    ?>

    

    <div class="topbar fade-in d1">
        <div class="greeting-row">
            <div class="avatar">
                <?php if ($user->avatar): ?>
                    <img src="<?= e($user->avatar) ?>" alt="<?= e($user->name) ?>" style="width:100%; height:100%; border-radius:inherit; object-fit:cover;">
                <?php else: ?>
                    <?= e($user->initials()) ?>
                <?php endif; ?>
            </div>
            <div class="greeting">
                <p><?= e($greeting) ?></p>
                <h2><?= e($user->name) ?></h2>
            </div>
        </div>
        <div class="bell-wrap">
            <button type="button" class="bell" id="notifBell" onclick="toggleNotifDropdown()" aria-label="<?= e(__('dashboard.notifications')) ?>" aria-haspopup="true" aria-expanded="false">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="1.6"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.7 21a2 2 0 0 1-3.4 0"/></svg>
                <span class="bell-badge" id="notifBadge" <?php if ($initialUnreadCount === 0): ?> hidden <?php endif; ?>><?= e($initialUnreadCount > 9 ? '9+' : $initialUnreadCount) ?></span>
            </button>

            <div class="notif-dropdown" id="notifDropdown">
                <div class="notif-dropdown-head">
                    <h4><?= e(__('dashboard.notifications')) ?></h4>
                    <div class="notif-head-actions">
                        <button type="button" class="notif-mark-all" onclick="markAllNotificationsRead()"><?= e(__('dashboard.mark_all_read')) ?></button>
                        <button type="button" class="notif-clear-all" onclick="clearAllNotifications()"><?= e(__('dashboard.clear_all')) ?></button>
                    </div>
                </div>
                <div class="notif-list" id="notifList">
                    <?php $__ledger_forelse_1 = true; foreach ($initialNotifications as $n): $__ledger_forelse_1 = false; ?>
                        <div class="notif-item <?= e($n->read_at ? '' : 'unread') ?>" data-id="<?= e($n->id) ?>" data-url="<?= e($n->url) ?>">
                            <p class="notif-title"><?= e($n->title) ?></p>
                            <?php if ($n->body): ?><p class="notif-body"><?= e($n->body) ?></p><?php endif; ?>
                            <p class="notif-time"><?= e($n->created_at->diffForHumans()) ?></p>
                        </div>
                    <?php endforeach; if ($__ledger_forelse_1): ?>
                        <p class="notif-empty"><?= e(__('dashboard.no_notifications')) ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="top-grid fade-in d2">
        <div class="ledger">
            <div class="ledger-top">
                <p class="ledger-label"><?= e(__('dashboard.available_balance')) ?></p>
                <div class="acct-chip-wrap">
                    <div
                        class="acct-chip"
                        id="acctChip"
                        <?php if ($accounts->count() > 1): ?> role="button" tabindex="0" aria-live="polite" aria-label="<?= e(__('dashboard.acct_aria', ['label' => $accounts[0]['label']])) ?>" <?php endif; ?>
                    >
                        <span class="acct-chip-type" id="acctChipType"><?= e($accounts[0]['label']) ?></span>
                        <span class="acct-chip-num" id="acctChipNum">•••• <?= e($accounts[0]['last4']) ?></span>
                    </div>
                    <?php if ($accounts->count() > 1): ?>
                        <div class="acct-chip-dots" id="acctChipDots">
                            <?php foreach ($accounts as $i => $acc): ?>
                                <span class="acct-dot <?php if ($i === 0): ?> active <?php endif; ?>"></span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="balance" id="balanceDisplay">$<span id="balanceWhole"><?= e($balanceWhole) ?></span><sup>.<span id="balanceCents"><?= e($balanceCents) ?></span></sup></div>
            <div class="balance-underline"></div>
            <div class="ledger-bottom">
                <span class="ledger-meta"><?= e(__('dashboard.updated_today', ['time' => now()->format('H:i')])) ?></span>
                <svg width="72" height="24" viewBox="0 0 72 24" fill="none">
                    <polyline points="0,18 10,15 20,17 30,10 40,12 50,6 60,8 72,2" stroke="#C9A24B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
        </div>

        <div class="spend-card">
            <div class="ring">
                <svg width="76" height="76" viewBox="0 0 76 76">
                    <circle cx="38" cy="38" r="<?= e($ringRadius) ?>" fill="none" stroke="#EDEAE0" stroke-width="8"/>
                    <circle
                        id="spendRingProgress"
                        cx="38" cy="38" r="<?= e($ringRadius) ?>" fill="none" stroke="<?= e($isOverBudget ? '#C1503C' : '#2F6F62') ?>" stroke-width="8" stroke-linecap="round"
                        stroke-dasharray="<?= e(round($ringCircumference, 2)) ?>"
                        stroke-dashoffset="<?= e(round($ringTargetOffset, 2)) ?>"
                        data-circumference="<?= e(round($ringCircumference, 2)) ?>"
                        data-target-offset="<?= e(round($ringTargetOffset, 2)) ?>"
                        transform="rotate(-90 38 38)"
                    />
                </svg>
                <div class="ring-label">
                    <strong id="spendPercentLabel"><?= e($spendPercent) ?>%</strong>
                    <span><?= e(__('dashboard.of_budget')) ?></span>
                </div>
            </div>
            <div class="spend-detail">
                <div class="spend-detail-head">
                    <h4><?= e(__('dashboard.spending_this_month')) ?></h4>
                    <a href="<?= e(route('setting.budget')) ?>" class="spend-edit" aria-label="<?= e(__('dashboard.set_budget_aria')) ?>">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                    </a>
                </div>
                <p>
                    <?php if ($spentAmount > 0): ?>
                        <?= e(__('dashboard.spent_of_budget', ['spent' => '$'.number_format($spentAmount, 2), 'budget' => '$'.number_format($budgetAmount)])) ?><?= e($isOverBudget ? __('dashboard.over_budget_suffix') : '') ?>.
                    <?php else: ?>
                        <?= e(__('dashboard.nothing_spent_yet')) ?>
                    <?php endif; ?>
                </p>
                <div class="figs">
                    <?php $__ledger_forelse_2 = true; foreach ($topSpendCategories as $i => $cat): $__ledger_forelse_2 = false; ?>
                        <div class="fig"><span class="dot" style="background:<?= e($spendCategoryColors[$i]) ?>"></span><?= e($cat['label']) ?></div>
                    <?php endforeach; if ($__ledger_forelse_2): ?>
                        <div class="fig" style="color:var(--text-3);"><?= e(__('dashboard.no_spending_yet')) ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="actions fade-in d3">
        <a href="<?= e(route('send')) ?>" class="action primary">
            <div class="circle"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M12 19V5M5 12l7-7 7 7"/></svg></div>
            <span><?= e(__('dashboard.action_send')) ?></span>
        </a>
        <a href="<?= e(route('receive')) ?>" class="action">
            <div class="circle"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M12 5v14M5 12l7 7 7-7"/></svg></div>
            <span><?= e(__('dashboard.action_receive')) ?></span>
        </a>
        <a href="<?= e(route('pay-bills')) ?>" class="action">
            <div class="circle"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><rect x="3" y="6" width="18" height="13" rx="2"/><path d="M3 10h18M7 15h4"/></svg></div>
            <span><?= e(__('dashboard.action_pay_bills')) ?></span>
        </a>
        <button type="button" class="action" onclick="openMoreSheet()">
            <div class="circle"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="5" cy="12" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="19" cy="12" r="1.5"/></svg></div>
            <span><?= e(__('dashboard.action_more')) ?></span>
        </button>
    </div>

    <!-- ============ More: bottom sheet with extra actions ============ -->
    <div class="sheet-overlay" id="moreSheetOverlay" onclick="closeMoreSheet(event)">
      <div class="sheet" onclick="event.stopPropagation()">
        <div class="sheet-handle"></div>
        <div class="sheet-head">
          <h4><?= e(__('dashboard.action_more')) ?></h4>
          <div class="icon-btn" onclick="closeMoreSheet()">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8"><path d="M18 6L6 18M6 6l12 12"/></svg>
          </div>
        </div>

        <a href="<?= e(route('top-up') ?? '#') ?>" class="setting-item">
          <div class="s-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><rect x="3" y="6" width="18" height="13" rx="2"/><path d="M3 10h18M7 15h4"/></svg>
          </div>
          <span class="label"><?= e(__('dashboard.more_top_up')) ?></span>
          <svg class="s-chev" viewBox="0 0 24 24" fill="none"><path d="M9 18l6-6-6-6"/></svg>
        </a>
        <a href="<?= e(route('withdraw') ?? '#') ?>" class="setting-item">
          <div class="s-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><path d="M12 5v14M5 12l7 7 7-7"/></svg>
          </div>
          <span class="label"><?= e(__('dashboard.more_withdraw')) ?></span>
          <svg class="s-chev" viewBox="0 0 24 24" fill="none"><path d="M9 18l6-6-6-6"/></svg>
        </a>
        <a href="<?= e(route('scan') ?? '#') ?>" class="setting-item">
          <div class="s-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><rect x="4" y="4" width="7" height="7"/><rect x="13" y="4" width="7" height="7"/><rect x="4" y="13" width="7" height="7"/><path d="M14 15h5M16.5 12.5v5"/></svg>
          </div>
          <span class="label"><?= e(__('dashboard.more_scan')) ?></span>
          <svg class="s-chev" viewBox="0 0 24 24" fill="none"><path d="M9 18l6-6-6-6"/></svg>
        </a>
        <a href="<?= e(route('history') ?? '#') ?>" class="setting-item">
          <div class="s-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg>
          </div>
          <span class="label"><?= e(__('dashboard.more_history')) ?></span>
          <svg class="s-chev" viewBox="0 0 24 24" fill="none"><path d="M9 18l6-6-6-6"/></svg>
        </a>
        <a href="<?= e(route('link-account')) ?>" class="setting-item">
          <div class="s-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><path d="M10 13a5 5 0 0 0 7.5.5l2-2a5 5 0 0 0-7-7l-1.5 1.5"/><path d="M14 11a5 5 0 0 0-7.5-.5l-2 2a5 5 0 0 0 7 7l1.5-1.5"/></svg>
          </div>
          <span class="label"><?= e(__('dashboard.more_link_account')) ?></span>
          <svg class="s-chev" viewBox="0 0 24 24" fill="none"><path d="M9 18l6-6-6-6"/></svg>
        </a>
      </div>
    </div>

    <script>
      window.LedgerDashboardConfig = {
        balanceValue: <?= e($balanceValue) ?>,
        spendPercent: <?= e($spendPercent) ?>,
        accounts: <?= json_encode($accounts) ?>,
        acctAriaTemplate: <?= json_encode(__('dashboard.acct_aria', ['label' => '__LABEL__'])) ?>,
        csrfToken: '<?= e(csrf_token()) ?>',
        notifEmptyText: <?= json_encode(__('dashboard.no_notifications')) ?>,
        clearAllConfirmText: <?= json_encode(__('dashboard.clear_all_confirm')) ?>,
        clearAllText: <?= json_encode(__('dashboard.clear_all')) ?>,
      };
    </script>

    <?php /* Shown when EnsureAccountIsNotRestricted bounces someone back here
         from Send/Withdraw/Top Up/Scan. session('restricted') carries the
         exact message to show — see that middleware for where it's set. */ ?>
    <?php if (session('restricted')): ?>
      <div class="sheet-overlay open" id="restrictedOverlay">
        <div class="sheet" onclick="event.stopPropagation()" style="text-align:center; padding-bottom:22px;">
          <div class="sheet-handle"></div>
          <div style="width:52px; height:52px; border-radius:50%; background:rgba(193,80,60,0.12); display:flex; align-items:center; justify-content:center; margin:4px auto 14px;">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#C1503C" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path d="M12 8v5M12 16h.01"/></svg>
          </div>
          <h4 style="margin:0 0 8px;"><?= e(__('dashboard.account_restricted')) ?></h4>
          <p style="margin:0 0 20px; font-size:13.5px; color:var(--text-3); line-height:1.5;"><?= e(session('restricted')) ?></p>
          <a href="<?= e(route('support')) ?>" style="display:block; width:100%; padding:13px; border-radius:12px; text-decoration:none; font-weight:700; font-size:14px; text-align:center; background:#C9A24B; color:#10202F; box-sizing:border-box;"><?= e(__('dashboard.contact_support')) ?></a>
          <button type="button" onclick="document.getElementById('restrictedOverlay').classList.remove('open')" style="margin-top:10px; width:100%; background:none; border:none; padding:10px; font-size:13.5px; font-weight:600; color:var(--text-3); cursor:pointer;"><?= e(__('dashboard.dismiss')) ?></button>
        </div>
      </div>
    <?php endif; ?>

    

    

    

@endsection
