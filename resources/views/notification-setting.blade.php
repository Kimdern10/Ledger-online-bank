@extends('layouts.app')

@section('content')
<div class="send-wrap">

  <div class="page-header fade-in d1">
    <a href="{{ route('setting') }}" class="icon-btn">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
    </a>
    <h1>{{ __('account.notifications_title') }}</h1>
    <a href="{{ route('dashboard') }}" class="icon-btn">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><path d="M3 11l9-8 9 8"/><path d="M5 10v10h14V10"/></svg>
    </a>
  </div>

  @if(session('status'))
    <div class="card-detail-card fade-in d1" style="margin-bottom:18px;">
      <p style="margin:0; font-size:13.5px;">{{ session('status') }}</p>
    </div>
  @endif

  <div class="send-card fade-in d2">
    <p style="margin:0 0 18px; font-size:13px; color:var(--text-3); line-height:1.5;">
      {{ __('account.notifications_intro') }}
    </p>

    <form method="POST" action="{{ route('setting.notifications.update') }}">
      @csrf

      <div class="cd-toggle-row" style="display:flex; align-items:center; justify-content:space-between; gap:14px; padding:14px 0; border-bottom:1px solid var(--border);">
        <div>
          <p style="margin:0; font-size:13.5px; font-weight:600;">{{ __('account.transaction_emails') }}</p>
          <p style="margin:2px 0 0; font-size:12px; color:var(--text-3);">{{ __('account.transaction_emails_desc') }}</p>
        </div>
        <label class="toggle-switch">
          <input type="checkbox" name="notify_transactions_email" value="1" {{ old('notify_transactions_email', auth()->user()->notify_transactions_email) ? 'checked' : '' }}>
          <span class="track"></span>
          <span class="thumb"></span>
        </label>
      </div>

      <div class="cd-toggle-row" style="display:flex; align-items:center; justify-content:space-between; gap:14px; padding:14px 0; border-bottom:1px solid var(--border);">
        <div>
          <p style="margin:0; font-size:13.5px; font-weight:600;">{{ __('account.security_alerts') }}</p>
          <p style="margin:2px 0 0; font-size:12px; color:var(--text-3);">{{ __('account.security_alerts_desc') }}</p>
        </div>
        <label class="toggle-switch">
          <input type="checkbox" name="notify_security_email" value="1" {{ old('notify_security_email', auth()->user()->notify_security_email) ? 'checked' : '' }}>
          <span class="track"></span>
          <span class="thumb"></span>
        </label>
      </div>

      <div class="cd-toggle-row" style="display:flex; align-items:center; justify-content:space-between; gap:14px; padding:14px 0;">
        <div>
          <p style="margin:0; font-size:13.5px; font-weight:600;">{{ __('account.promotions') }}</p>
          <p style="margin:2px 0 0; font-size:12px; color:var(--text-3);">{{ __('account.promotions_desc') }}</p>
        </div>
        <label class="toggle-switch">
          <input type="checkbox" name="notify_promotions_email" value="1" {{ old('notify_promotions_email', auth()->user()->notify_promotions_email) ? 'checked' : '' }}>
          <span class="track"></span>
          <span class="thumb"></span>
        </label>
      </div>

      <div class="pay-btn-wrap" style="margin-top:18px;">
        <button type="submit" class="pay-btn">{{ __('account.save_preferences') }}</button>
      </div>
    </form>
  </div>

</div>
@endsection
