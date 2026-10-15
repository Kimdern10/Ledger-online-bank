@extends('layouts.admin')

@section('title', 'Banks')

@section('content')
  <div class="admin-header" style="display:flex; align-items:flex-start; justify-content:space-between; gap:14px; flex-wrap:wrap;">
    <div>
      <h1>Banks</h1>
      <p>Banks you added yourself — for banks the APIs don't have. Customers see them first in the Send Money pickers. The banks from the APIs are under API banks.</p>
    </div>
    <a href="{{ route('admin.banks.create') }}" class="admin-btn admin-btn-primary">Add bank</a>
  </div>

  @include('admin.partials.banks-section-tabs', ['current' => 'directory'])

  <div class="admin-tabs" style="margin-bottom:12px;">
    <a href="{{ route('admin.banks', array_filter(['q' => $search, 'status' => $status])) }}" class="admin-tab {{ ! $type ? 'active' : '' }}" style="text-decoration:none; display:inline-block;">All</a>
    <a href="{{ route('admin.banks', array_filter(['type' => 'external', 'q' => $search, 'status' => $status])) }}" class="admin-tab {{ $type === 'external' ? 'active' : '' }}" style="text-decoration:none; display:inline-block;">Domestic ({{ $externalCount }})</a>
    <a href="{{ route('admin.banks', array_filter(['type' => 'international', 'q' => $search, 'status' => $status])) }}" class="admin-tab {{ $type === 'international' ? 'active' : '' }}" style="text-decoration:none; display:inline-block;">International ({{ $internationalCount }})</a>
  </div>

  {{-- Searches name, country, routing number and SWIFT/BIC on the server
       (see AdminBankController::index()), so it covers every page, not
       just the one on screen. --}}
  <form method="GET" class="admin-filter-row" action="{{ route('admin.banks') }}" style="display:flex; gap:10px; flex-wrap:wrap; align-items:center; margin-bottom:16px;">
    @if($type) <input type="hidden" name="type" value="{{ $type }}"> @endif
    <label class="admin-search" style="flex:1 1 240px;">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.35-4.35"/></svg>
      <input type="text" name="q" value="{{ $search }}" placeholder="Search by name, country, routing or SWIFT">
    </label>
    <select name="status" onchange="this.form.submit()">
      <option value="">Any status</option>
      <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active</option>
      <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Inactive</option>
    </select>
    <button type="submit" class="admin-btn admin-btn-outline">Search</button>
    @if($search !== '' || $status !== '')
      <a href="{{ route('admin.banks', array_filter(['type' => $type])) }}" class="admin-btn admin-btn-outline">Clear</a>
    @endif
  </form>

  <div class="admin-card" style="padding:0; overflow-x:auto;">
    @if($banks->isEmpty())
      <p class="admin-empty">{{ $search !== '' || $status !== '' ? 'No banks match your search.' : 'You have not added any banks yet. Use Add bank, or Add to my banks on API banks.' }}</p>
    @else
      <table class="admin-table">
        <thead>
          <tr>
            <th>Name</th>
            <th>Type</th>
            <th>Country</th>
            <th>Routing #</th>
            <th>SWIFT/BIC</th>
            <th>Status</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          @foreach($banks as $bank)
            <tr>
              <td>{{ $bank->name }}</td>
              <td><span class="admin-badge">{{ $bank->typeLabel() }}</span></td>
              <td>{{ $bank->country ?? '—' }}</td>
              <td>{{ $bank->routing_number ?? '—' }}</td>
              <td>{{ $bank->swift_code ?? '—' }}</td>
              <td>
                <span class="admin-pill {{ $bank->is_active ? 'status-active' : 'status-disabled' }}">{{ $bank->is_active ? 'Active' : 'Inactive' }}</span>
              </td>
              <td>
                <div style="display:flex; gap:6px; flex-wrap:wrap;">
                  <a href="{{ route('admin.banks.edit', $bank) }}" class="admin-btn admin-btn-outline">Edit</a>
                  <form method="POST" action="{{ route('admin.banks.toggle', $bank) }}">
                    @csrf
                    <button type="submit" class="admin-btn admin-btn-outline">{{ $bank->is_active ? 'Deactivate' : 'Activate' }}</button>
                  </form>
                  <form method="POST" action="{{ route('admin.banks.destroy', $bank) }}" data-confirm="Remove {{ $bank->name }} from the directory?" data-confirm-danger="1" data-confirm-button="Remove">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="admin-btn admin-btn-danger">Remove</button>
                  </form>
                </div>
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
