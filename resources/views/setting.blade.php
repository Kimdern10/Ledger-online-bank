@extends('layouts.app')

@section('content')
<div class="send-wrap">

  <div class="page-header fade-in d1">
    <a href="{{ url()->previous() }}" class="icon-btn">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
    </a>
    <h1>{{ __('setting.title') }}</h1>
    <a href="{{ route('dashboard') }}" class="icon-btn">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><path d="M3 11l9-8 9 8"/><path d="M5 10v10h14V10"/></svg>
    </a>
  </div>

  <div class="profile-card fade-in d2">
    <div class="profile-avatar">
      @if(auth()->user()->avatar ?? false)
        <img src="{{ auth()->user()->avatar }}" alt="{{ auth()->user()->name }}" class="profile-avatar" style="border:none; margin:0;">
      @else
        {{ Str::of(auth()->user()->name ?? 'Cody Lee')->explode(' ')->map(fn($n) => strtoupper($n[0]))->join('') }}
      @endif
    </div>
    <div class="profile-name-row">
      <h3>{{ auth()->user()->name ?? 'Cody Lee' }}</h3>
      <a href="{{ route('setting.profile') }}" class="profile-edit">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
      </a>
    </div>
    <p class="profile-email">{{ auth()->user()->email ?? 'codylee@gmail.com' }}</p>
    <span class="linked-tag" style="margin-top:10px;">{{ auth()->user()->tierLabel() }} &middot; ${{ number_format(auth()->user()->tierDailyLimit()) }}/day</span>
  </div>

  <div class="fade-in d3">
    <p class="setting-group-label">{{ __('setting.account_group') }}</p>
    <div class="setting-list">
      <a href="{{ route('setting.profile') }}" class="setting-item">
        <div class="s-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><circle cx="12" cy="8" r="4"/><path d="M4 21v-1a8 8 0 0 1 16 0v1"/></svg>
        </div>
        <span class="label">{{ __('setting.profile_setting') }}</span>
        <svg class="s-chev" viewBox="0 0 24 24" fill="none"><path d="M9 18l6-6-6-6"/></svg>
      </a>
      <a href="{{ route('kyc.create') }}" class="setting-item">
        <div class="s-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M7 9h10M7 13h6"/><path d="M8 4V2M16 4V2"/></svg>
        </div>
        <span class="label">{{ __('verify.identity_verification') }}<span class="linked-tag">{{ ['approved' => __('verify.badge_verified'), 'pending' => __('verify.badge_pending'), 'rejected' => __('verify.badge_needs_resubmit')][auth()->user()->kyc_status] ?? __('verify.badge_not_started') }}</span></span>
        <svg class="s-chev" viewBox="0 0 24 24" fill="none"><path d="M9 18l6-6-6-6"/></svg>
      </a>
      <a href="{{ route('address-verification.create') }}" class="setting-item">
        <div class="s-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><path d="M3 9.5 12 3l9 6.5"/><path d="M5 10v10h14V10"/><path d="M9 20v-6h6v6"/></svg>
        </div>
        <span class="label">{{ __('verify.address_verification') }}<span class="linked-tag">{{ ['approved' => __('verify.badge_verified'), 'pending' => __('verify.badge_pending'), 'rejected' => __('verify.badge_needs_resubmit')][auth()->user()->address_status] ?? __('verify.badge_not_started') }}</span></span>
        <svg class="s-chev" viewBox="0 0 24 24" fill="none"><path d="M9 18l6-6-6-6"/></svg>
      </a>
      <a href="{{ route('setting.notifications') }}" class="setting-item">
        <div class="s-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.7 21a2 2 0 0 1-3.4 0"/></svg>
        </div>
        <span class="label">{{ __('setting.notifications_setting') }}</span>
        <svg class="s-chev" viewBox="0 0 24 24" fill="none"><path d="M9 18l6-6-6-6"/></svg>
      </a>
      {{-- No standalone Language settings page anymore — the floating
           switcher that's always on screen inside the app (see
           layouts/app.blade.php's .dash-lang-float) is the one place to
           change it now, so this list doesn't need its own link to a
           second page for the same thing. --}}
      <a href="{{ route('setting.budget') }}" class="setting-item">
        <div class="s-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/><path d="M12 3v2M12 19v2M3 12h2M19 12h2"/></svg>
        </div>
        <span class="label">{{ __('setting.monthly_budget') }}<span class="linked-tag">${{ number_format(auth()->user()->monthlyBudget()) }}</span></span>
        <svg class="s-chev" viewBox="0 0 24 24" fill="none"><path d="M9 18l6-6-6-6"/></svg>
      </a>
      {{-- Link Account used to be listed here too, alongside its entry in
           the dashboard's More sheet (Top up/Withdraw/Scan/History/Link
           account) — same page reachable two ways. Kept just the More-sheet
           entry so there's a single path to it. --}}
      {{-- "Banks" (the read-only view of the admin-managed Bank directory,
           setting-banks.blade.php / BankListController) removed from this
           list per request. The route/controller/view are untouched in case
           they're wanted back — just no longer linked from here. --}}
      <a href="{{ route('setting.transaction-pin') }}" class="setting-item">
        <div class="s-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg>
        </div>
        <span class="label">{{ __('setting.transaction_pin') }}<span class="linked-tag">{{ auth()->user()->hasTransactionPin() ? __('setting.pin_set') : __('setting.pin_not_set') }}</span></span>
        <svg class="s-chev" viewBox="0 0 24 24" fill="none"><path d="M9 18l6-6-6-6"/></svg>
      </a>
      <a href="{{ route('setting.password') }}" class="setting-item">
        <div class="s-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><rect x="4" y="10" width="16" height="10" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg>
        </div>
        <span class="label">{{ __('setting.password_change') }}</span>
        <svg class="s-chev" viewBox="0 0 24 24" fill="none"><path d="M9 18l6-6-6-6"/></svg>
      </a>
    </div>

    <p class="setting-group-label">{{ __('setting.support_group') }}</p>
    <div class="setting-list">
      <a href="{{ route('support') }}" class="setting-item">
        <div class="s-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
        </div>
        <span class="label">{{ __('setting.support_ticket') }}</span>
        <svg class="s-chev" viewBox="0 0 24 24" fill="none"><path d="M9 18l6-6-6-6"/></svg>
      </a>
      <a href="{{ route('setting.terms') }}" class="setting-item">
        <div class="s-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><path d="M14 3v4a1 1 0 0 0 1 1h4"/><path d="M17 21H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h7l5 5v11a2 2 0 0 1-2 2Z"/></svg>
        </div>
        <span class="label">{{ __('setting.terms') }}</span>
        <svg class="s-chev" viewBox="0 0 24 24" fill="none"><path d="M9 18l6-6-6-6"/></svg>
      </a>
      <a href="{{ route('setting.app-info') }}" class="setting-item">
        <div class="s-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>
        </div>
        <span class="label">{{ __('setting.app_info') }}</span>
        <svg class="s-chev" viewBox="0 0 24 24" fill="none"><path d="M9 18l6-6-6-6"/></svg>
      </a>
    </div>

    <p class="setting-group-label">{{ __('setting.danger_zone') }}</p>
    <div class="setting-list">
      <a href="{{ route('setting.delete-account') }}" class="setting-item danger">
        <div class="s-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><path d="M3 6h18"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
        </div>
        <span class="label">{{ __('setting.delete_account') }}</span>
        <svg class="s-chev" viewBox="0 0 24 24" fill="none"><path d="M9 18l6-6-6-6"/></svg>
      </a>
      <form method="POST" action="{{ route('logout') }}" style="display:contents;">
        @csrf
        <button type="submit" class="setting-item danger" style="width:100%; border:none; background:none; cursor:pointer; font-family:'Inter', sans-serif; text-align:left;">
          <div class="s-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5"/><path d="M21 12H9"/></svg>
          </div>
          <span class="label">{{ __('setting.logout') }}</span>
        </button>
      </form>
    </div>
  </div>

</div>
@endsection
