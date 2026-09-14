@extends('layouts.app')

@section('content')
<div class="send-wrap">

  <div class="page-header fade-in d1">
    <a href="{{ route('setting') }}" class="icon-btn">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
    </a>
    <h1>Language</h1>
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
      Choosing a language here changes what you see on Dashboard, Send, Receive, History, and this Settings page right away. The rest of Ledger (Pay Bills, Cards, Support, and a few other pages) is still English only for now. More pages will follow.
    </p>

    <form method="POST" action="{{ route('setting.language.update') }}">
      @csrf

      <div class="field-group" style="margin:0;">
        <p class="label">Language</p>
        <div class="select-wrap">
          <select name="language" class="text-input" onchange="this.form.submit()" required>
            @foreach($languages as $code => $label)
              <option value="{{ $code }}" {{ auth()->user()->language === $code ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
          </select>
          <div class="chev">
            <svg viewBox="0 0 24 24" fill="none"><path d="M6 9l6 6 6-6"/></svg>
          </div>
        </div>
      </div>

      <noscript>
        <button type="submit" class="btn btn-primary" style="margin-top:16px;">Save</button>
      </noscript>
    </form>
  </div>

</div>
@endsection
