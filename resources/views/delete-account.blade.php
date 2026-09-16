@extends('layouts.app')

@section('content')
<div class="send-wrap">

  <div class="page-header fade-in d1">
    <a href="{{ route('setting') }}" class="icon-btn">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
    </a>
    <h1>{{ __('setting.delete_account') }}</h1>
    <a href="{{ route('dashboard') }}" class="icon-btn">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><path d="M3 11l9-8 9 8"/><path d="M5 10v10h14V10"/></svg>
    </a>
  </div>

  @if(session('deleteError'))
    <div class="send-alert fade-in d1">{{ session('deleteError') }}</div>
  @endif

  @if($errors->any())
    <div class="send-alert fade-in d1">
      <ul style="margin:0; padding-left:18px;">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="send-card fade-in d2">
    <p style="margin:0 0 10px; font-size:13.5px; font-weight:700; color:#C1503C;">{{ __('account.delete_warning_title') }}</p>
    <p style="margin:0 0 18px; font-size:13px; color:var(--text-3); line-height:1.6;">
      {!! __('account.delete_intro', ['support_link' => '<a href="' . route('support') . '" style="color:#C9A24B; font-weight:600;">' . __('account.support_link_text') . '</a>']) !!}
    </p>

    <div class="wallet-meta" style="margin-bottom:18px;">
      <div class="wallet-meta-row"><span>{{ __('account.current_balance') }}</span><span>${{ number_format((float) auth()->user()->balance, 2) }}</span></div>
    </div>

    <form method="POST" action="{{ route('setting.delete-account.destroy') }}" data-confirm="{{ __('account.delete_confirm_dialog') }}" data-confirm-danger="1" data-confirm-button="{{ __('account.delete_my_account') }}">
      @csrf

      <div class="field-group">
        <p class="label">{{ __('account.confirm_password_field') }}</p>
        <input type="password" class="text-input" name="password" autocomplete="current-password" required>
      </div>

      <div class="pay-btn-wrap" style="margin-top:8px;">
        <button type="submit" class="pay-btn" style="background:#C1503C;" {{ (float) auth()->user()->balance > 0 ? 'disabled' : '' }}>{{ __('account.delete_my_account') }}</button>
      </div>

      @if((float) auth()->user()->balance > 0)
        <p style="margin:10px 0 0; font-size:12px; color:#C1503C; text-align:center;">{{ __('account.withdraw_first') }}</p>
      @endif
    </form>
  </div>

</div>
@endsection
