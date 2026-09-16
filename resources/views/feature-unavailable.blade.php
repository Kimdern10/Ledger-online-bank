@extends('layouts.app')

@section('content')
<div class="send-wrap">

  <div class="page-header fade-in d1">
    <a href="{{ route('dashboard') }}" class="icon-btn">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
    </a>
    <h1>{{ $feature }}</h1>
    <span style="width:38px; display:inline-block;"></span>
  </div>

  <div class="card-detail-card fade-in d2" style="text-align:center; padding:36px 24px;">
    <div style="width:56px; height:56px; border-radius:50%; background:rgba(47,111,98,0.12); display:flex; align-items:center; justify-content:center; margin:0 auto 18px;">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" style="width:26px; height:26px;">
        <circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/>
      </svg>
    </div>

    <p style="margin:0 0 8px; font-size:16.5px; font-weight:700;">{{ $feature }} {{ __('feature.unavailable_suffix') }}</p>

    <p style="margin:0 auto 22px; max-width:380px; font-size:13.5px; opacity:0.75; line-height:1.55;">
      {{ __('feature.body') }}
    </p>

    <div style="display:flex; gap:10px; justify-content:center; flex-wrap:wrap;">
      <a href="{{ route('dashboard') }}" class="btn btn-outline">{{ __('feature.back_to_dashboard') }}</a>
      <a href="{{ route('support') }}" class="btn btn-primary">{{ __('feature.message_support') }}</a>
    </div>
  </div>

</div>
@endsection
