@extends('layouts.app')

@section('content')
<div class="send-wrap">

  <div class="page-header fade-in d1">
    <a href="{{ route('setting') }}" class="icon-btn">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
    </a>
    <h1>{{ __('setting.monthly_budget') }}</h1>
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
    <p style="margin:0 0 18px; font-size:13px; color:var(--text-3); line-height:1.5;">
      {{ __('account.budget_intro') }}
    </p>

    <form method="POST" action="{{ route('setting.budget.update') }}">
      @csrf

      <div class="field-group">
        <p class="label">{{ __('account.monthly_budget_field') }}</p>
        <input type="number" class="text-input" name="monthly_budget" step="0.01" min="1" max="999999.99" value="{{ old('monthly_budget', $currentBudget) }}" required>
      </div>

      <div class="pay-btn-wrap" style="margin-top:8px;">
        <button type="submit" class="pay-btn">{{ __('account.save_budget') }}</button>
      </div>
    </form>
  </div>

</div>
@endsection
