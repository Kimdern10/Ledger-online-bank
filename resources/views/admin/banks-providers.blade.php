@extends('layouts.admin')

@section('title', 'Provider banks')

@section('content')
  <div class="admin-header" style="display:flex; align-items:flex-start; justify-content:space-between; gap:14px; flex-wrap:wrap;">
    <div>
      <h1>Banks</h1>
      <p>Every bank your API providers offer, live these are what customers find in the Send Money pickers. To add a bank the APIs don't have, use Add bank; banks you add are under Added by you, where you can edit them.</p>
    </div>
    <a href="{{ route('admin.banks.create') }}" class="admin-btn admin-btn-primary">Add bank</a>
  </div>

  @include('admin.partials.banks-section-tabs', ['current' => 'providers'])

  <div class="admin-tabs" style="margin-bottom:12px; flex-wrap:wrap;">
    @foreach($sources as $key => $entry)
      <a href="{{ route('admin.banks.providers', array_filter(['source' => $key, 'q' => $search])) }}"
         class="admin-tab {{ $source === $key ? 'active' : '' }}" style="text-decoration:none; display:inline-block; {{ $entry['configured'] ? '' : 'opacity:0.55;' }}"
         title="{{ $entry['configured'] ? '' : 'No credentials in .env' }}">{{ $entry['label'] }}</a>
    @endforeach
  </div>

  <form method="GET" class="admin-filter-row" action="{{ route('admin.banks.providers') }}" style="display:flex; gap:10px; flex-wrap:wrap; align-items:center; margin-bottom:16px;">
    <input type="hidden" name="source" value="{{ $source }}">
    <label class="admin-search" style="flex:1 1 240px;">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.35-4.35"/></svg>
      <input type="text" name="q" value="{{ $search }}" placeholder="Search {{ $sources[$source]['label'] }} by bank name" maxlength="100">
    </label>
    {{-- Only the countries this source has banks in — see
         ProviderBankBrowser::countries(). --}}
    @if(count($countries) > 1)
      <select name="country" onchange="this.form.submit()" aria-label="Country">
        <option value="">All countries ({{ count($countries) }})</option>
        @foreach($countries as $code => $countryName)
          <option value="{{ $code }}" {{ $country === $code ? 'selected' : '' }}>{{ $countryName }}</option>
        @endforeach
      </select>
    @endif
    <button type="submit" class="admin-btn admin-btn-outline">Search</button>
    @if($search !== '' || $country)
      <a href="{{ route('admin.banks.providers', ['source' => $source]) }}" class="admin-btn admin-btn-outline">Clear</a>
    @endif
  </form>

  <div class="admin-card" style="padding:0; overflow-x:auto;">
    @if(! $configured)
      <p class="admin-empty">{{ $sources[$source]['label'] }} isn't set up — its credentials aren't in <code>.env</code>.</p>
    @elseif($failed)
      <p class="admin-empty">{{ $sources[$source]['label'] }} didn't answer. Try again in a moment; details are in <code>storage/logs/laravel.log</code>.</p>
    @elseif($banks->isEmpty())
      <p class="admin-empty">{{ $search !== '' || $country ? 'No banks match your search.' : 'This provider returned no banks.' }}</p>
    @else
      <p style="margin:0; padding:14px 20px 0; font-size:13px; color:var(--admin-text-2);">
        {{ number_format($banks->total()) }} {{ $banks->total() === 1 ? 'bank' : 'banks' }} · showing {{ number_format($banks->firstItem()) }}–{{ number_format($banks->lastItem()) }}
      </p>
      <table class="admin-table">
        <thead>
          <tr>
            <th>Name</th>
            <th>Country</th>
            <th>Routing #</th>
            <th>SWIFT/BIC</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          @foreach($banks as $bank)
            {{-- US banks go to the domestic ("Another bank") list, everything else to International. --}}
            <?php $bankType = ($bank['country'] ?? null) === 'US' ? 'external' : 'international'; ?>
            <tr>
              <td>{{ $bank['name'] }}</td>
              <td>{{ isset($bank['country']) ? ($countries[strtoupper($bank['country'])] ?? $bank['country']) : '—' }}</td>
              <td>{{ $bank['routing_number'] ?? '—' }}</td>
              <td>{{ $bank['swift_code'] ?? '—' }}</td>
              <td style="white-space:nowrap;">
                @if($inDirectory->has($bankType.'|'.mb_strtolower($bank['name'])))
                  <span class="admin-pill status-active">Added</span>
                @else
                  <form method="POST" action="{{ route('admin.banks.import') }}">
                    @csrf
                    <input type="hidden" name="name" value="{{ $bank['name'] }}">
                    <input type="hidden" name="type" value="{{ $bankType }}">
                    <input type="hidden" name="country" value="{{ $bank['country'] ?? '' }}">
                    <input type="hidden" name="routing_number" value="{{ $bank['routing_number'] ?? '' }}">
                    <input type="hidden" name="swift_code" value="{{ $bank['swift_code'] ?? '' }}">
                    <input type="hidden" name="currency" value="{{ $bank['currency'] ?? '' }}">
                    <button type="submit" class="admin-btn admin-btn-outline">Add to my banks</button>
                  </form>
                @endif
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
      <div style="padding:0 20px 16px;">
        @include('partials.admin-pagination', ['paginator' => $banks])
      </div>
    @endif
  </div>
@endsection
