@extends('layouts.admin')

@section('title', 'User Detail')

@section('content')
<?php
    // Same "which account do we actually have" preference used on the
    // consumer dashboard/receive/pay-bills pages: checking first, savings
    // as the fallback.
    $primaryAccountType = null;
    $primaryAccountNumber = null;
    if (! empty($user->checking_account_number)) {
        $primaryAccountType = 'Checking';
        $primaryAccountNumber = $user->checking_account_number;
    } elseif (! empty($user->savings_account_number)) {
        $primaryAccountType = 'Savings';
        $primaryAccountNumber = $user->savings_account_number;
    }

    // Every OTHER status becomes one action button — whichever one you're
    // NOT currently in. So an active account shows Freeze/Suspend/Disable;
    // a frozen one shows Activate/Suspend/Disable; and so on. No separate
    // "Release" action — reactivating from Frozen and reactivating from
    // Suspended both go through the same "Activate" button below, rather
    // than a second, overlapping control that would just do the same
    // thing under a different name.
    $statusLabels = ['active' => 'Activate', 'frozen' => 'Freeze wallet', 'suspended' => 'Suspend', 'disabled' => 'Disable'];
    $statusButtonClass = ['active' => 'admin-btn-primary', 'frozen' => 'admin-btn-outline', 'suspended' => 'admin-btn-outline', 'disabled' => 'admin-btn-danger'];
    $statusConfirmText = [
        'active' => 'reactivate',
        'frozen' => 'freeze the wallet for',
        'suspended' => 'suspend',
        'disabled' => 'disable',
    ];
    $otherStatuses = collect($statusLabels)->forget($user->account_status);

    // Backs the "Feature access" toggles below. Each slug is one of the
    // six values web.php's route constraint accepts, mapped to the exact
    // boolean column on users that EnsureFeatureEnabled checks.
    $features = [
        'send' => 'Send money',
        'pay-bills' => 'Pay bills',
        'link-account' => 'Link account',
        'withdraw' => 'Withdraw',
        'top-up' => 'Top up',
        'cards' => 'Manage cards',
        'receive' => 'Add money',
        'scan' => 'Scan to pay',
    ];
    $featureColumns = [
        'send' => 'can_send',
        'pay-bills' => 'can_pay_bills',
        'link-account' => 'can_link_account',
        'withdraw' => 'can_withdraw',
        'top-up' => 'can_top_up',
        'cards' => 'can_manage_cards',
        'receive' => 'can_receive',
        'scan' => 'can_scan',
    ];
?>

<a href="{{ route('admin.users') }}" class="admin-back-link">
  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
  Back to users
</a>

<div class="admin-detail-grid">
  <div>
    <div class="admin-card profile-card">
      <div class="profile-banner">
        <div class="profile-avatar">
          @if($user->avatar)
            <img src="{{ $user->avatar }}" alt="{{ $user->name }}" style="width:100%; height:100%; border-radius:inherit; object-fit:cover;">
          @else
            {{ $user->initials() }}
          @endif
        </div>
      </div>
      <div class="profile-body">
        <p class="profile-name">{{ $user->name }}</p>
        <p class="profile-email">{{ $user->email }}</p>

        @if($primaryAccountNumber)
          <div class="profile-ac">
            <span>A/C {{ $primaryAccountNumber }}</span>
            <button type="button" onclick="copyAccountNumber(this)" data-value="{{ $primaryAccountNumber }}" aria-label="Copy account number">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
            </button>
          </div>
        @endif

        <div class="profile-actions">
          <a href="mailto:{{ $user->email }}" class="admin-btn admin-btn-outline">Email</a>
          <form method="POST" action="{{ route('admin.users.password-reset', $user) }}" data-confirm="Send a password reset email to this user?" data-confirm-button="Send email">
            @csrf
            <button type="submit" class="admin-btn admin-btn-outline">Password</button>
          </form>
        </div>
      </div>
    </div>

    <div class="admin-card">
      <p style="margin:0 0 2px; font-weight:700; font-size:13.5px;">Account verification</p>

      <div class="verify-row">
        <div>
          <p class="label">Email verification</p>
        </div>
        <form method="POST" action="{{ route('admin.users.email-verification', $user) }}">
          @csrf
          <button type="submit" class="admin-toggle {{ $user->email_verified_at ? 'on' : '' }}" aria-label="Toggle email verification"></button>
        </form>
      </div>

      <div class="verify-row">
        <div>
          <p class="label">Two-factor authentication</p>
          @if(! $user->two_factor_confirmed_at)
            <p class="sub">Off: password-only sign in. Only the user can turn this on, from their own account.</p>
          @endif
        </div>
        @if($user->two_factor_confirmed_at)
          <form method="POST" action="{{ route('admin.users.two-factor.reset', $user) }}" data-confirm="Reset two-factor authentication for this user? They will sign in with just their password until they set it up again." data-confirm-danger="1" data-confirm-button="Reset 2FA">
            @csrf
            <button type="submit" class="admin-btn admin-btn-outline" style="white-space:nowrap;">Reset 2FA</button>
          </form>
        @else
          <span style="font-size:12px; color:var(--admin-text-2); font-weight:600;">Off</span>
        @endif
      </div>
    </div>

    <div class="admin-card">
      <p style="margin:0 0 2px; font-weight:700; font-size:13.5px;">Identity verification (KYC)</p>
      <?php $userKyc = $user->kycVerification; ?>
      <div class="verify-row">
        <div>
          <p class="label">Status</p>
          @if($userKyc)
            <p class="sub">{{ $userKyc->documentTypeLabel() }} &middot; submitted {{ $userKyc->created_at->diffForHumans() }}</p>
          @else
            <p class="sub">Hasn't submitted an ID yet.</p>
          @endif
        </div>
        <span class="admin-pill {{ $user->kyc_status === 'approved' ? 'status-active' : ($user->kyc_status === 'rejected' ? 'status-disabled' : 'status-frozen') }}">
          {{ $userKyc ? $userKyc->statusLabel() : 'Not submitted' }}
        </span>
      </div>
      @if($userKyc && $userKyc->isPending())
        <a href="{{ route('admin.kyc') }}" class="admin-btn admin-btn-outline" style="margin-top:8px; display:inline-block;">Review in queue</a>
      @endif
    </div>

    <div class="admin-card">
      <p style="margin:0 0 2px; font-weight:700; font-size:13.5px;">Feature access</p>
      <p class="feature-row-sub" style="margin-bottom:8px;">What this user can open. Off shows a "not available" message instead of the page.</p>

      @foreach($features as $slug => $label)
        <div class="verify-row">
          <p class="label">{{ $label }}</p>
          <form method="POST" action="{{ route('admin.users.features.toggle', [$user, $slug]) }}">
            @csrf
            <button type="submit" class="admin-toggle {{ $user->{$featureColumns[$slug]} ? 'on' : '' }}" aria-label="Toggle {{ $label }}"></button>
          </form>
        </div>
      @endforeach
    </div>
  </div>

  <div>
    <div class="admin-card">
      <div class="status-row">
        <div>
          <p style="margin:0 0 6px; font-size:12px; color:var(--admin-text-2); font-weight:600;">Account status</p>
          <span class="admin-pill status-{{ $user->account_status }}">{{ $user->accountStatusLabel() }}</span>
          @if($user->isRestricted())
            <span class="admin-pill status-disabled" style="margin-left:6px;">Restricted</span>
          @endif
        </div>
        <div class="status-actions">
          @foreach($otherStatuses as $value => $label)
            <form method="POST" action="{{ route('admin.users.status', $user) }}" data-confirm="Are you sure you want to {{ $statusConfirmText[$value] }} this account?" @if($value !== 'active') data-confirm-danger="1" @endif data-confirm-button="Confirm">
              @csrf
              <input type="hidden" name="account_status" value="{{ $value }}">
              <button type="submit" class="admin-btn {{ $statusButtonClass[$value] }}">{{ $label }}</button>
            </form>
          @endforeach
        </div>
      </div>

      {{-- Separate from account_status above — this doesn't sign the user
           out or stop them opening their dashboard, it only blocks Send,
           Withdraw, Top Up, and Scan (see EnsureAccountIsNotRestricted).
           They see a popup pointing to Support instead of each form. --}}
      <div class="verify-row" style="margin-top:4px;">
        <div>
          <p class="label">Restrict transactions</p>
          <p class="sub">Blocks Send, Withdraw, Top Up &amp; Scan to Pay. Sign-in and the dashboard stay unaffected.</p>
        </div>
        <form method="POST" action="{{ route('admin.users.restriction.toggle', $user) }}" data-confirm="Are you sure you want to {{ $user->isRestricted() ? 'remove the restriction from' : 'restrict' }} this account?" data-confirm-danger="1" data-confirm-button="Confirm">
          @csrf
          <button type="submit" class="admin-toggle {{ $user->isRestricted() ? 'on' : '' }}" aria-label="Toggle account restriction"></button>
        </form>
      </div>
    </div>

    <div class="wallet-card">
      <div class="wallet-card-head">
        <p>Cash wallet</p>
        <span class="admin-pill status-{{ $user->account_status }}" style="background:rgba(255,255,255,0.16); color:#fff;">{{ $user->accountStatusLabel() }}</span>
      </div>
      <div class="wallet-figures">
        <div>
          <p class="label">Available</p>
          <p class="value">${{ number_format((float) $user->balance, 2) }}</p>
        </div>
      </div>
      <div class="wallet-meta">
        <div class="wallet-meta-row"><span>Account</span><span>{{ $primaryAccountNumber ?? 'Not set up yet' }}</span></div>
        <div class="wallet-meta-row"><span>Bank</span><span>Ledger Federal Credit Union</span></div>
      </div>
    </div>

    <button type="button" class="admin-btn admin-btn-dark" style="width:100%; padding:11px; font-size:13.5px;" onclick="openCreditDebit()">Credit / Debit</button>

    <div class="admin-card" style="margin-top:18px;">
      <p style="margin:0 0 4px; font-weight:700; font-size:13.5px;">Recent balance adjustments</p>
      @if($adjustments->isEmpty())
        <p class="admin-empty" style="padding:14px 0;">No admin adjustments yet.</p>
      @else
        @foreach($adjustments as $adj)
          <div class="adjustment-row">
            <span>
              {{ ucfirst($adj->direction) }} · {{ $adj->balance_pot === 'account' ? 'Account balance' : 'Available balance' }}
              @if($adj->sender_name) · from {{ $adj->sender_name }} @endif
            </span>
            <span style="white-space:nowrap; font-weight:600;">${{ number_format((float) $adj->amount, 2) }}</span>
          </div>
        @endforeach
      @endif
    </div>
  </div>
</div>

<div class="admin-card" style="margin-top:18px;">
  <p style="margin:0 0 4px; font-weight:700; font-size:13.5px;">Linked accounts</p>
  <p style="margin:0 0 14px; font-size:12px; color:var(--admin-text-2);">Read-only: banks and cards this user has linked themselves from the Link Account page.</p>
  @if($linkedAccounts->isEmpty())
    <p class="admin-empty" style="padding:14px 0;">No linked accounts yet.</p>
  @else
    @foreach($linkedAccounts as $account)
      <div class="adjustment-row">
        <span>{{ $account->typeLabel() }} · {{ $account->displayLabel() }} {{ $account->detailLabel() }}</span>
        <span style="white-space:nowrap; color:var(--admin-text-2);">{{ $account->created_at->format('M j, Y') }}</span>
      </div>
    @endforeach
  @endif
</div>

<div class="admin-card" style="margin-top:18px;">
  <p style="margin:0 0 4px; font-weight:700; font-size:13.5px;">Recent withdrawals &amp; top-ups</p>
  <p style="margin:0 0 14px; font-size:12px; color:var(--admin-text-2);">Read-only: these process instantly for the user, same as Send Money.</p>
  @if($withdrawals->isEmpty() && $topUps->isEmpty())
    <p class="admin-empty" style="padding:14px 0;">No withdrawals or top-ups yet.</p>
  @else
    @foreach($withdrawals as $withdrawal)
      <div class="adjustment-row">
        <span>Withdrawal to {{ $withdrawal->destination_label }} •••• {{ $withdrawal->destination_last4 }} · {{ $withdrawal->created_at->format('M j, Y') }}</span>
        <span style="white-space:nowrap; font-weight:600;">–${{ number_format((float) $withdrawal->amount, 2) }}</span>
      </div>
    @endforeach
    @foreach($topUps as $topUp)
      <div class="adjustment-row">
        <span>Top-up from {{ $topUp->source_label }} •••• {{ $topUp->source_last4 }} · {{ $topUp->created_at->format('M j, Y') }}</span>
        <span style="white-space:nowrap; font-weight:600;">+${{ number_format((float) $topUp->amount, 2) }}</span>
      </div>
    @endforeach
  @endif
</div>

<div class="admin-card" style="margin-top:18px;">
  <p style="margin:0 0 4px; font-weight:700; font-size:13.5px;">Edit information</p>
  <p style="margin:0 0 18px; font-size:12px; color:var(--admin-text-2);">Account details, plus the employment &amp; finance answers collected during onboarding. Editable here.</p>

  <form method="POST" action="{{ route('admin.users.profile.update', $user) }}">
    @csrf

    <p class="section-label">Account</p>
    <div class="admin-form-grid">
      <label class="admin-field">
        <span>First name</span>
        <input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}" required>
        @error('first_name') <span class="admin-field-error">{{ $message }}</span> @enderror
      </label>
      <label class="admin-field">
        <span>Last name</span>
        <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}" required>
        @error('last_name') <span class="admin-field-error">{{ $message }}</span> @enderror
      </label>
      <label class="admin-field">
        <span>Middle name</span>
        <input type="text" name="middle_name" value="{{ old('middle_name', $user->middle_name) }}">
      </label>
      <label class="admin-field">
        <span>Email</span>
        <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
        @error('email') <span class="admin-field-error">{{ $message }}</span> @enderror
      </label>
      <label class="admin-field">
        <span>Phone</span>
        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}">
      </label>
    </div>

    <p class="section-label" style="margin-top:22px;">Employment &amp; finances</p>
    <div class="admin-form-grid">
      <label class="admin-field">
        <span>Employment status</span>
        <input type="text" name="employment_status" value="{{ old('employment_status', $user->profile?->employment_status) }}" placeholder="e.g. Self-employed">
      </label>
      <label class="admin-field">
        <span>Occupation</span>
        <input type="text" name="occupation" value="{{ old('occupation', $user->profile?->occupation) }}">
      </label>
      <label class="admin-field">
        <span>Industry</span>
        <input type="text" name="industry" value="{{ old('industry', $user->profile?->industry) }}">
      </label>
      <label class="admin-field">
        <span>Source of income</span>
        <input type="text" name="main_source_of_income" value="{{ old('main_source_of_income', $user->profile?->main_source_of_income) }}">
      </label>
      <label class="admin-field">
        <span>Annual income</span>
        <input type="text" inputmode="decimal" name="annual_income" value="{{ old('annual_income', $user->profile?->annual_income) }}" placeholder="0.00">
        @error('annual_income') <span class="admin-field-error">{{ $message }}</span> @enderror
      </label>
      <label class="admin-field">
        <span>Expected annual income</span>
        <input type="text" inputmode="decimal" name="expected_annual_income" value="{{ old('expected_annual_income', $user->profile?->expected_annual_income) }}" placeholder="0.00">
        @error('expected_annual_income') <span class="admin-field-error">{{ $message }}</span> @enderror
      </label>
      <label class="admin-field">
        <span>Net worth</span>
        <input type="text" inputmode="decimal" name="net_worth" value="{{ old('net_worth', $user->profile?->net_worth) }}" placeholder="0.00">
        @error('net_worth') <span class="admin-field-error">{{ $message }}</span> @enderror
      </label>
    </div>

    <button type="submit" class="admin-btn admin-btn-dark" style="margin-top:20px; padding:10px 22px;">Save changes</button>
  </form>
</div>

<!-- ============ Credit / Debit slide-over ============ -->
<div class="admin-slideover-overlay" id="cbOverlay" onclick="closeCreditDebit()"></div>
<div class="admin-slideover" id="cbSlideover">
  <div class="slideover-head">
    <div>
      <h3>Credit / Debit cash wallet</h3>
      <p>{{ $user->name }} - {{ $primaryAccountNumber ?? 'No account' }}</p>
    </div>
    <button type="button" class="slideover-close" onclick="closeCreditDebit()" aria-label="Close">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6L6 18M6 6l12 12"/></svg>
    </button>
  </div>

  <form method="POST" action="{{ route('admin.users.balance', $user) }}">
    @csrf
    <input type="hidden" name="direction" id="cbDirection" value="credit">

    <p class="slideover-label">Direction</p>
    <div class="toggle-group" id="directionGroup">
      <button type="button" class="toggle-btn active" data-value="credit" onclick="selectDirection(this, 'credit')">Credit (add funds)</button>
      <button type="button" class="toggle-btn" data-value="debit" onclick="selectDirection(this, 'debit')">Debit (remove funds)</button>
    </div>

    <p class="slideover-label">Amount</p>
    <input type="text" name="amount" inputmode="decimal" class="slideover-input" placeholder="0.00" required>
    @error('amount')
      <p class="admin-field-error">{{ $message }}</p>
    @enderror

    <div id="senderDetailsSection">
      <p class="slideover-label" style="margin-top:0;">Sender details <span class="slideover-sub">(optional: describes where a credit came from)</span></p>

      <p class="slideover-label">Sender name</p>
      <input type="text" name="sender_name" class="slideover-input" placeholder="e.g. John Smith">

      <p class="slideover-label">Sender account name <span class="slideover-sub">(optional)</span></p>
      <input type="text" name="sender_account_name" class="slideover-input" placeholder="Same as name if blank">

      <p class="slideover-label">Sender account number</p>
      <input type="text" name="sender_account_number" class="slideover-input" placeholder="e.g. 1234567890">

      <p class="slideover-label">Sender bank name</p>
      <input type="text" name="sender_bank_name" class="slideover-input" placeholder="e.g. Chase Bank">

      <p class="slideover-label">Bank address <span class="slideover-sub">(optional)</span></p>
      <input type="text" name="bank_address" class="slideover-input" placeholder="Street, city, country">
    </div>

    <button type="submit" class="admin-btn admin-btn-dark" style="width:100%; padding:12px; font-size:13.5px; margin-top:22px;">Apply</button>
  </form>
</div>
@endsection
