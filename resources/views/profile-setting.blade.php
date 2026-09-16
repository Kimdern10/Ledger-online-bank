@extends('layouts.app')

@section('content')
<div class="send-wrap">

  <div class="page-header fade-in d1">
    <a href="{{ route('setting') }}" class="icon-btn">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
    </a>
    <h1>{{ __('setting.profile_setting') }}</h1>
    <a href="{{ route('dashboard') }}" class="icon-btn">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><path d="M3 11l9-8 9 8"/><path d="M5 10v10h14V10"/></svg>
    </a>
  </div>

  @if(session('status'))
    <div class="card-detail-card fade-in d1" style="margin-bottom:18px;">
      <p style="margin:0; font-size:13.5px;">{{ session('status') }}</p>
    </div>
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
    <form method="POST" action="{{ route('setting.profile.update') }}">
      @csrf

      <div class="field-group">
        <p class="label">{{ __('account.first_name') }}</p>
        <input type="text" class="text-input" name="first_name" value="{{ old('first_name', auth()->user()->first_name) }}" required>
      </div>

      <div class="field-group">
        <p class="label">{{ __('account.last_name') }}</p>
        <input type="text" class="text-input" name="last_name" value="{{ old('last_name', auth()->user()->last_name) }}" required>
      </div>

      <div class="field-group">
        <p class="label">{{ __('account.middle_name') }} <span style="font-weight:400; color:var(--text-3);">{{ __('account.optional') }}</span></p>
        <input type="text" class="text-input" name="middle_name" value="{{ old('middle_name', auth()->user()->middle_name) }}">
      </div>

      <div class="field-group">
        <p class="label">{{ __('account.email') }}</p>
        <input type="email" class="text-input" name="email" value="{{ old('email', auth()->user()->email) }}" required>
      </div>

      <div class="field-group">
        <p class="label">{{ __('account.phone') }} <span style="font-weight:400; color:var(--text-3);">{{ __('account.optional') }}</span></p>
        <input type="tel" class="text-input" name="phone" value="{{ old('phone', auth()->user()->phone) }}">
      </div>

      <div class="pay-btn-wrap" style="margin-top:8px;">
        <button type="submit" class="pay-btn">{{ __('account.save_changes') }}</button>
      </div>
    </form>
  </div>

</div>
@endsection
