{{-- <x-layouts::app :title="__('Dashboard')">
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
</x-layouts::app> --}}

@extends('layouts.app')
@section('content')

    @php
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
    @endphp

    <style>
      /* Balance card's account-type chip — cycles Checking / Savings when a
         user has both, with a quick fade+slide swap on change. */
      .acct-chip-wrap{ display:flex; flex-direction:column; align-items:flex-end; gap:6px; }
      .acct-chip{ transition: transform .22s cubic-bezier(.16,.84,.44,1), opacity .22s ease; }
      .acct-chip[role="button"]{ cursor:pointer; }
      .acct-chip[role="button"]:hover{ transform:translateY(-1px); }
      .acct-chip[role="button"]:focus-visible{ outline:2px solid #C9A24B; outline-offset:2px; }
      .acct-chip.is-switching{ opacity:0; transform:translateY(-5px) scale(.96); }
      .acct-chip-type{ font-size:10.5px; font-weight:700; letter-spacing:.04em; text-transform:uppercase; opacity:.7; margin-right:5px; }
      .acct-chip-dots{ display:flex; gap:5px; }
      .acct-dot{ width:5px; height:5px; border-radius:50%; background:rgba(16,32,47,0.18); transition:background .2s ease, transform .2s ease; }
      html.dark .acct-dot{ background:rgba(246,244,238,0.24); }
      .acct-dot.active{ background:#C9A24B; transform:scale(1.35); }

      /* The topbar (avatar/greeting + bell) sits right above .top-grid (the
         balance card + "Spending this month" card) in the markup below. A
         plain `position:relative` with no z-index doesn't actually create a
         new stacking context, so without this, anything in .top-grid that
         DOES get a stacking context from the site's global stylesheet can
         paint on top of the bell button and its dropdown — which is exactly
         what was happening ("the spending [card] is blocking [the bell]").
         Giving .topbar its own explicit z-index here settles that for good,
         regardless of what the global CSS does. */
      .topbar{ position:relative; z-index:50; }

      /* Notification bell + dropdown */
      .bell-wrap{ position:relative; }
      .bell{ position:relative; background:none; border:none; padding:0; margin:0; cursor:pointer; color:inherit; display:flex; font:inherit; }
      .bell-badge{
        position:absolute; top:-5px; right:-5px; min-width:16px; height:16px; padding:0 4px;
        border-radius:999px; background:#C1503C; color:#fff; font-size:10px; font-weight:700;
        display:flex; align-items:center; justify-content:center; line-height:1;
      }
      .notif-dropdown{
        display:none; position:absolute; top:calc(100% + 12px); right:0; width:300px; max-width:86vw;
        max-height:380px; overflow-y:auto; background:#fff; border:1px solid var(--border);
        border-radius:14px; box-shadow:0 12px 32px rgba(16,32,47,0.16); z-index:9999;
      }
      html.dark .notif-dropdown{ background:#16232F; border-color:rgba(246,244,238,0.1); }
      .notif-dropdown.open{ display:block; }
      .notif-dropdown-head{
        display:flex; align-items:center; justify-content:space-between; padding:12px 14px;
        border-bottom:1px solid var(--border); position:sticky; top:0; background:inherit;
      }
      .notif-dropdown-head h4{ margin:0; font-size:13.5px; }
      .notif-head-actions{ display:flex; align-items:center; gap:10px; }
      .notif-mark-all{ background:none; border:none; padding:0; font-size:11.5px; font-weight:700; color:#C9A24B; cursor:pointer; }
      .notif-clear-all{ background:none; border:none; padding:0; font-size:11.5px; font-weight:700; color:var(--text-3); cursor:pointer; }
      .notif-clear-all:hover{ color:#C1503C; }
      .notif-list{ padding:6px; }
      .notif-item{ padding:10px; border-radius:10px; cursor:pointer; }
      .notif-item:hover{ background:rgba(201,162,75,0.08); }
      .notif-item.unread{ background:rgba(201,162,75,0.07); }
      .notif-title{ margin:0; font-size:13px; font-weight:600; display:flex; align-items:center; }
      .notif-item.unread .notif-title::before{ content:''; width:6px; height:6px; border-radius:50%; background:#C9A24B; margin-right:7px; flex-shrink:0; }
      .notif-body{ margin:3px 0 0 13px; font-size:12px; color:var(--text-3); line-height:1.4; }
      .notif-time{ margin:4px 0 0 13px; font-size:11px; color:var(--text-3); }
      .notif-empty{ margin:0; padding:26px 10px; text-align:center; font-size:12.5px; color:var(--text-3); }

      /* "Set budget" pencil next to the Spending this month heading */
      .spend-detail-head{ display:flex; align-items:center; gap:8px; }
      .spend-detail-head h4{ margin:0; }
      .spend-edit{ display:flex; color:var(--text-3); opacity:.7; }
      .spend-edit:hover{ opacity:1; color:#C9A24B; }
      .spend-edit svg{ width:15px; height:15px; }
    </style>

    <div class="topbar fade-in d1">
        <div class="greeting-row">
            <div class="avatar">
                @if($user->avatar)
                    <img src="{{ $user->avatar }}" alt="{{ $user->name }}" style="width:100%; height:100%; border-radius:inherit; object-fit:cover;">
                @else
                    {{ $user->initials() }}
                @endif
            </div>
            <div class="greeting">
                <p>{{ $greeting }}</p>
                <h2>{{ $user->name }}</h2>
            </div>
        </div>
        <div class="bell-wrap">
            <button type="button" class="bell" id="notifBell" onclick="toggleNotifDropdown()" aria-label="{{ __('dashboard.notifications') }}" aria-haspopup="true" aria-expanded="false">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="1.6"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.7 21a2 2 0 0 1-3.4 0"/></svg>
                <span class="bell-badge" id="notifBadge" @if($initialUnreadCount === 0) hidden @endif>{{ $initialUnreadCount > 9 ? '9+' : $initialUnreadCount }}</span>
            </button>

            <div class="notif-dropdown" id="notifDropdown">
                <div class="notif-dropdown-head">
                    <h4>{{ __('dashboard.notifications') }}</h4>
                    <div class="notif-head-actions">
                        <button type="button" class="notif-mark-all" onclick="markAllNotificationsRead()">{{ __('dashboard.mark_all_read') }}</button>
                        <button type="button" class="notif-clear-all" onclick="clearAllNotifications()">{{ __('dashboard.clear_all') }}</button>
                    </div>
                </div>
                <div class="notif-list" id="notifList">
                    @forelse($initialNotifications as $n)
                        <div class="notif-item {{ $n->read_at ? '' : 'unread' }}" data-id="{{ $n->id }}" data-url="{{ $n->url }}">
                            <p class="notif-title">{{ $n->title }}</p>
                            @if($n->body)<p class="notif-body">{{ $n->body }}</p>@endif
                            <p class="notif-time">{{ $n->created_at->diffForHumans() }}</p>
                        </div>
                    @empty
                        <p class="notif-empty">{{ __('dashboard.no_notifications') }}</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="top-grid fade-in d2">
        <div class="ledger">
            <div class="ledger-top">
                <p class="ledger-label">{{ __('dashboard.available_balance') }}</p>
                <div class="acct-chip-wrap">
                    <div
                        class="acct-chip"
                        id="acctChip"
                        @if($accounts->count() > 1) role="button" tabindex="0" aria-live="polite" aria-label="{{ __('dashboard.acct_aria', ['label' => $accounts[0]['label']]) }}" @endif
                    >
                        <span class="acct-chip-type" id="acctChipType">{{ $accounts[0]['label'] }}</span>
                        <span class="acct-chip-num" id="acctChipNum">•••• {{ $accounts[0]['last4'] }}</span>
                    </div>
                    @if($accounts->count() > 1)
                        <div class="acct-chip-dots" id="acctChipDots">
                            @foreach($accounts as $i => $acc)
                                <span class="acct-dot @if($i === 0) active @endif"></span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
            <div class="balance" id="balanceDisplay">$<span id="balanceWhole">{{ $balanceWhole }}</span><sup>.<span id="balanceCents">{{ $balanceCents }}</span></sup></div>
            <div class="balance-underline"></div>
            <div class="ledger-bottom">
                <span class="ledger-meta">{{ __('dashboard.updated_today', ['time' => now()->format('H:i')]) }}</span>
                <svg width="72" height="24" viewBox="0 0 72 24" fill="none">
                    <polyline points="0,18 10,15 20,17 30,10 40,12 50,6 60,8 72,2" stroke="#C9A24B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
        </div>

        <div class="spend-card">
            <div class="ring">
                <svg width="76" height="76" viewBox="0 0 76 76">
                    <circle cx="38" cy="38" r="{{ $ringRadius }}" fill="none" stroke="#EDEAE0" stroke-width="8"/>
                    <circle
                        id="spendRingProgress"
                        cx="38" cy="38" r="{{ $ringRadius }}" fill="none" stroke="{{ $isOverBudget ? '#C1503C' : '#2F6F62' }}" stroke-width="8" stroke-linecap="round"
                        stroke-dasharray="{{ round($ringCircumference, 2) }}"
                        stroke-dashoffset="{{ round($ringTargetOffset, 2) }}"
                        data-circumference="{{ round($ringCircumference, 2) }}"
                        data-target-offset="{{ round($ringTargetOffset, 2) }}"
                        transform="rotate(-90 38 38)"
                    />
                </svg>
                <div class="ring-label">
                    <strong id="spendPercentLabel">{{ $spendPercent }}%</strong>
                    <span>{{ __('dashboard.of_budget') }}</span>
                </div>
            </div>
            <div class="spend-detail">
                <div class="spend-detail-head">
                    <h4>{{ __('dashboard.spending_this_month') }}</h4>
                    <a href="{{ route('setting.budget') }}" class="spend-edit" aria-label="{{ __('dashboard.set_budget_aria') }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                    </a>
                </div>
                <p>
                    @if($spentAmount > 0)
                        {{ __('dashboard.spent_of_budget', ['spent' => '$'.number_format($spentAmount, 2), 'budget' => '$'.number_format($budgetAmount)]) }}{{ $isOverBudget ? __('dashboard.over_budget_suffix') : '' }}.
                    @else
                        {{ __('dashboard.nothing_spent_yet') }}
                    @endif
                </p>
                <div class="figs">
                    @forelse($topSpendCategories as $i => $cat)
                        <div class="fig"><span class="dot" style="background:{{ $spendCategoryColors[$i] }}"></span>{{ $cat['label'] }}</div>
                    @empty
                        <div class="fig" style="color:var(--text-3);">{{ __('dashboard.no_spending_yet') }}</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="actions fade-in d3">
        <a href="{{ route('send') }}" class="action primary">
            <div class="circle"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M12 19V5M5 12l7-7 7 7"/></svg></div>
            <span>{{ __('dashboard.action_send') }}</span>
        </a>
        <a href="{{ route('receive') }}" class="action">
            <div class="circle"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M12 5v14M5 12l7 7 7-7"/></svg></div>
            <span>{{ __('dashboard.action_receive') }}</span>
        </a>
        <a href="{{ route('pay-bills') }}" class="action">
            <div class="circle"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><rect x="3" y="6" width="18" height="13" rx="2"/><path d="M3 10h18M7 15h4"/></svg></div>
            <span>{{ __('dashboard.action_pay_bills') }}</span>
        </a>
        <button type="button" class="action" onclick="openMoreSheet()">
            <div class="circle"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="5" cy="12" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="19" cy="12" r="1.5"/></svg></div>
            <span>{{ __('dashboard.action_more') }}</span>
        </button>
    </div>

    <!-- ============ More: bottom sheet with extra actions ============ -->
    <div class="sheet-overlay" id="moreSheetOverlay" onclick="closeMoreSheet(event)">
      <div class="sheet" onclick="event.stopPropagation()">
        <div class="sheet-handle"></div>
        <div class="sheet-head">
          <h4>{{ __('dashboard.action_more') }}</h4>
          <div class="icon-btn" onclick="closeMoreSheet()">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8"><path d="M18 6L6 18M6 6l12 12"/></svg>
          </div>
        </div>

        <a href="{{ route('top-up') ?? '#' }}" class="setting-item">
          <div class="s-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><rect x="3" y="6" width="18" height="13" rx="2"/><path d="M3 10h18M7 15h4"/></svg>
          </div>
          <span class="label">{{ __('dashboard.more_top_up') }}</span>
          <svg class="s-chev" viewBox="0 0 24 24" fill="none"><path d="M9 18l6-6-6-6"/></svg>
        </a>
        <a href="{{ route('withdraw') ?? '#' }}" class="setting-item">
          <div class="s-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><path d="M12 5v14M5 12l7 7 7-7"/></svg>
          </div>
          <span class="label">{{ __('dashboard.more_withdraw') }}</span>
          <svg class="s-chev" viewBox="0 0 24 24" fill="none"><path d="M9 18l6-6-6-6"/></svg>
        </a>
        <a href="{{ route('scan') ?? '#' }}" class="setting-item">
          <div class="s-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><rect x="4" y="4" width="7" height="7"/><rect x="13" y="4" width="7" height="7"/><rect x="4" y="13" width="7" height="7"/><path d="M14 15h5M16.5 12.5v5"/></svg>
          </div>
          <span class="label">{{ __('dashboard.more_scan') }}</span>
          <svg class="s-chev" viewBox="0 0 24 24" fill="none"><path d="M9 18l6-6-6-6"/></svg>
        </a>
        <a href="{{ route('history') ?? '#' }}" class="setting-item">
          <div class="s-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg>
          </div>
          <span class="label">{{ __('dashboard.more_history') }}</span>
          <svg class="s-chev" viewBox="0 0 24 24" fill="none"><path d="M9 18l6-6-6-6"/></svg>
        </a>
        <a href="{{ route('link-account') }}" class="setting-item">
          <div class="s-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><path d="M10 13a5 5 0 0 0 7.5.5l2-2a5 5 0 0 0-7-7l-1.5 1.5"/><path d="M14 11a5 5 0 0 0-7.5-.5l-2 2a5 5 0 0 0 7 7l1.5-1.5"/></svg>
          </div>
          <span class="label">{{ __('dashboard.more_link_account') }}</span>
          <svg class="s-chev" viewBox="0 0 24 24" fill="none"><path d="M9 18l6-6-6-6"/></svg>
        </a>
      </div>
    </div>

    <script>
      function openMoreSheet(){ document.getElementById('moreSheetOverlay').classList.add('open'); }
      function closeMoreSheet(e){
        if(e) e.stopPropagation();
        document.getElementById('moreSheetOverlay').classList.remove('open');
      }
    </script>

    {{-- Shown when EnsureAccountIsNotRestricted bounces someone back here
         from Send/Withdraw/Top Up/Scan. session('restricted') carries the
         exact message to show — see that middleware for where it's set. --}}
    @if(session('restricted'))
      <div class="sheet-overlay open" id="restrictedOverlay">
        <div class="sheet" onclick="event.stopPropagation()" style="text-align:center; padding-bottom:22px;">
          <div class="sheet-handle"></div>
          <div style="width:52px; height:52px; border-radius:50%; background:rgba(193,80,60,0.12); display:flex; align-items:center; justify-content:center; margin:4px auto 14px;">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#C1503C" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path d="M12 8v5M12 16h.01"/></svg>
          </div>
          <h4 style="margin:0 0 8px;">{{ __('dashboard.account_restricted') }}</h4>
          <p style="margin:0 0 20px; font-size:13.5px; color:var(--text-3); line-height:1.5;">{{ session('restricted') }}</p>
          <a href="{{ route('support') }}" style="display:block; width:100%; padding:13px; border-radius:12px; text-decoration:none; font-weight:700; font-size:14px; text-align:center; background:#C9A24B; color:#10202F; box-sizing:border-box;">{{ __('dashboard.contact_support') }}</a>
          <button type="button" onclick="document.getElementById('restrictedOverlay').classList.remove('open')" style="margin-top:10px; width:100%; background:none; border:none; padding:10px; font-size:13.5px; font-weight:600; color:var(--text-3); cursor:pointer;">{{ __('dashboard.dismiss') }}</button>
        </div>
      </div>
    @endif

    <script>
      // Shared count-up/draw-in helper used by the balance and the spending
      // ring below — runs an eased animation over `duration` ms, calling
      // onUpdate(easedProgress) on every frame with a value from 0 to 1.
      // Respects prefers-reduced-motion by jumping straight to the end
      // instead of animating, rather than ignoring that setting.
      function animateEased(opts){
        const duration = opts.duration || 900;
        const prefersReducedMotion = window.matchMedia
          && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        if (prefersReducedMotion) {
          opts.onUpdate(1);
          if (opts.onDone) opts.onDone();
          return;
        }

        function easeOutCubic(t){ return 1 - Math.pow(1 - t, 3); }

        const start = performance.now();
        function step(now){
          const t = Math.min((now - start) / duration, 1);
          opts.onUpdate(easeOutCubic(t));
          if (t < 1) {
            requestAnimationFrame(step);
          } else if (opts.onDone) {
            opts.onDone();
          }
        }
        requestAnimationFrame(step);
      }

      // Balance counts up from $0.00 to the real balance on page load,
      // instead of just appearing.
      (function animateBalance(){
        const target = {{ $balanceValue }};
        const wholeEl = document.getElementById('balanceWhole');
        const centsEl = document.getElementById('balanceCents');
        if (!wholeEl || !centsEl) return;

        animateEased({
          duration: 900,
          onUpdate: function(eased){
            const formatted = (target * eased).toLocaleString('en-US', {
              minimumFractionDigits: 2,
              maximumFractionDigits: 2,
            });
            const [whole, cents] = formatted.split('.');
            wholeEl.textContent = whole;
            centsEl.textContent = cents;
          },
        });
      })();

      // Spending ring draws in from empty to its real percentage, and the
      // "65%" label counts up in step with it, instead of both just
      // appearing already filled in.
      (function animateSpendRing(){
        const ring = document.getElementById('spendRingProgress');
        const label = document.getElementById('spendPercentLabel');
        if (!ring) return;

        const circumference = parseFloat(ring.dataset.circumference);
        const targetOffset = parseFloat(ring.dataset.targetOffset);
        const targetPercent = {{ $spendPercent }};
        const startOffset = circumference; // fully empty

        ring.style.strokeDashoffset = startOffset;
        if (label) label.textContent = '0%';

        animateEased({
          duration: 900,
          onUpdate: function(eased){
            ring.style.strokeDashoffset = startOffset - (startOffset - targetOffset) * eased;
            if (label) label.textContent = Math.round(targetPercent * eased) + '%';
          },
        });
      })();
    </script>

    <script>
      // Cycles the balance card's account chip between Checking and Savings
      // when the user has both — click, tap, or Enter/Space to switch, or
      // just wait and it advances on its own. Does nothing (no listeners,
      // no timer) when there's only one account, since there's nothing to
      // cycle to.
      (function initAccountChip(){
        const accounts = @json($accounts);
        // Same :label pattern as dashboard.acct_aria, translated server-side
        // — see the blade aria-label above this script for the non-JS
        // (first-paint) render of the exact same text.
        const acctAriaTemplate = @json(__('dashboard.acct_aria', ['label' => '__LABEL__']));
        if (accounts.length < 2) return;

        const chip = document.getElementById('acctChip');
        const typeEl = document.getElementById('acctChipType');
        const numEl = document.getElementById('acctChipNum');
        const dots = document.querySelectorAll('#acctChipDots .acct-dot');
        if (!chip) return;

        const SWITCH_MS = 220;
        const AUTO_CYCLE_MS = 4000;
        let index = 0;
        let autoTimer = null;

        function render(i){
          const acc = accounts[i];
          chip.classList.add('is-switching');
          setTimeout(function(){
            typeEl.textContent = acc.label;
            numEl.textContent = '•••• ' + acc.last4;
            chip.setAttribute('aria-label', acctAriaTemplate.replace('__LABEL__', acc.label));
            chip.classList.remove('is-switching');
            dots.forEach(function(dot, di){ dot.classList.toggle('active', di === i); });
          }, SWITCH_MS);
        }

        function goToNext(){
          index = (index + 1) % accounts.length;
          render(index);
        }

        function stopAutoCycle(){
          if (autoTimer) {
            clearInterval(autoTimer);
            autoTimer = null;
          }
        }

        function startAutoCycle(){
          stopAutoCycle();
          if (document.visibilityState === 'visible') {
            autoTimer = setInterval(goToNext, AUTO_CYCLE_MS);
          }
        }

        chip.addEventListener('click', function(){
          goToNext();
          startAutoCycle();
        });

        chip.addEventListener('keydown', function(e){
          if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            goToNext();
            startAutoCycle();
          }
        });

        // Pause while someone's actually looking at/focused on it, resume
        // once they're not — an auto-swap mid-read is just annoying.
        chip.addEventListener('mouseenter', stopAutoCycle);
        chip.addEventListener('mouseleave', startAutoCycle);
        chip.addEventListener('focus', stopAutoCycle);
        chip.addEventListener('blur', startAutoCycle);

        // Don't keep cycling (or burn battery) on a backgrounded tab.
        document.addEventListener('visibilitychange', function(){
          if (document.visibilityState === 'visible') {
            startAutoCycle();
          } else {
            stopAutoCycle();
          }
        });

        startAutoCycle();
      })();
    </script>

    <script>
      // The notification bell — polls /notifications/poll every ~20s (same
      // "slow safety-net poll" idea as support.blade.php's message poll) so
      // the badge count and dropdown list both stay live while the
      // dashboard is open, without needing a websocket server. Every list
      // item is built with textContent (never innerHTML) since a
      // notification's title/body can echo back another user's name (e.g.
      // "You received $50 from {sender's name}") — building it as raw HTML
      // would let a name containing HTML/script run on whoever's dashboard
      // displays it.
      const csrfToken = '{{ csrf_token() }}';
      const notifEmptyText = @json(__('dashboard.no_notifications'));
      let notifDropdownOpen = false;

      function toggleNotifDropdown(){
        notifDropdownOpen = !notifDropdownOpen;
        const dropdown = document.getElementById('notifDropdown');
        const bell = document.getElementById('notifBell');
        if (dropdown) dropdown.classList.toggle('open', notifDropdownOpen);
        if (bell) bell.setAttribute('aria-expanded', notifDropdownOpen ? 'true' : 'false');
        if (notifDropdownOpen) pollNotifications();
      }

      document.addEventListener('click', function(e){
        const wrap = document.querySelector('.bell-wrap');
        if (notifDropdownOpen && wrap && !wrap.contains(e.target)) {
          notifDropdownOpen = false;
          document.getElementById('notifDropdown').classList.remove('open');
          document.getElementById('notifBell').setAttribute('aria-expanded', 'false');
        }
      });

      function renderNotifBadge(count){
        const badge = document.getElementById('notifBadge');
        if (!badge) return;
        if (count > 0) {
          badge.textContent = count > 9 ? '9+' : String(count);
          badge.hidden = false;
        } else {
          badge.hidden = true;
        }
      }

      function renderNotifList(items){
        const list = document.getElementById('notifList');
        if (!list) return;
        list.innerHTML = '';

        if (!items.length) {
          const empty = document.createElement('p');
          empty.className = 'notif-empty';
          empty.textContent = notifEmptyText;
          list.appendChild(empty);
          return;
        }

        items.forEach(function(n){
          const item = document.createElement('div');
          item.className = 'notif-item' + (n.unread ? ' unread' : '');
          item.addEventListener('click', function(){ openNotification(n); });

          const title = document.createElement('p');
          title.className = 'notif-title';
          title.textContent = n.title;
          item.appendChild(title);

          if (n.body) {
            const body = document.createElement('p');
            body.className = 'notif-body';
            body.textContent = n.body;
            item.appendChild(body);
          }

          const time = document.createElement('p');
          time.className = 'notif-time';
          time.textContent = n.time;
          item.appendChild(time);

          list.appendChild(item);
        });
      }

      function openNotification(n){
        fetch('/notifications/' + n.id + '/read', {
          method: 'POST',
          headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        }).finally(function(){
          if (n.url) window.location = n.url;
        });
      }

      function markAllNotificationsRead(){
        fetch('/notifications/read-all', {
          method: 'POST',
          headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        }).finally(pollNotifications);
      }

      function clearAllNotifications(){
        window.ledgerConfirm(
          @json(__('dashboard.clear_all_confirm')),
          function(){
            fetch('/notifications/clear-all', {
              method: 'POST',
              headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            }).finally(pollNotifications);
          },
          { confirmButtonText: @json(__('dashboard.clear_all')), danger: true }
        );
      }

      function pollNotifications(){
        fetch('/notifications/poll', { headers: { 'Accept': 'application/json' } })
          .then(function(res){ return res.ok ? res.json() : null; })
          .then(function(data){
            if (!data) return;
            renderNotifBadge(data.unread_count);
            renderNotifList(data.notifications);
          })
          .catch(function(){ /* quiet — the next poll just tries again */ });
      }

      setInterval(pollNotifications, 20000);
    </script>

@endsection
