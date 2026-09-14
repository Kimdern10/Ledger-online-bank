@extends('layouts.app')

@section('content')
@php
    $hasPin = auth()->user()->hasTransactionPin();
@endphp
<div class="send-wrap">

  <div class="page-header fade-in d1">
    <a href="{{ route('setting') }}" class="icon-btn">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
    </a>
    <h1>{{ __('setting.transaction_pin') }}</h1>
    <a href="{{ route('dashboard') }}" class="icon-btn">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><path d="M3 11l9-8 9 8"/><path d="M5 10v10h14V10"/></svg>
    </a>
  </div>

  @if(session('status'))
    <div class="card-detail-card fade-in d1" style="margin-bottom:18px;">
      <p style="margin:0; font-size:13.5px;">{{ session('status') }}</p>
    </div>
  @endif

  @if(session('pinError'))
    <div class="send-alert fade-in d1">{{ session('pinError') }}</div>
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
    <p style="margin:0 0 18px; font-size:13px; color:var(--text-3); line-height:1.5;">
      @if($hasPin)
        {{ __('account.pin_intro_has') }}
      @else
        {{ __('account.pin_intro_new') }}
      @endif
    </p>

    <form method="POST" action="{{ route('setting.transaction-pin.update') }}">
      @csrf

      @if($hasPin)
        <div class="field-group">
          <p class="label">{{ __('account.current_pin') }}</p>
          <input type="password" class="text-input" name="current_pin" inputmode="numeric" maxlength="4" placeholder="••••" style="text-align:center; letter-spacing:10px;" autocomplete="off" required>
        </div>
      @endif

      <div class="field-group">
        <p class="label">{{ $hasPin ? __('account.new_pin') : __('account.create_pin') }}</p>
        <input type="password" class="text-input" name="pin" inputmode="numeric" maxlength="4" placeholder="••••" style="text-align:center; letter-spacing:10px;" autocomplete="off" required>
      </div>

      <div class="field-group">
        <p class="label">{{ $hasPin ? __('account.confirm_new_pin') : __('account.confirm_pin') }}</p>
        <input type="password" class="text-input" name="pin_confirmation" inputmode="numeric" maxlength="4" placeholder="••••" style="text-align:center; letter-spacing:10px;" autocomplete="off" required>
      </div>

      <div class="pay-btn-wrap" style="margin-top:8px;">
        <button type="submit" class="pay-btn">{{ $hasPin ? __('account.update_pin') : __('account.create_pin') }}</button>
      </div>
    </form>
  </div>

</div>
@endsection
