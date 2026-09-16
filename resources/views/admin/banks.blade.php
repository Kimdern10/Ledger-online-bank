@extends('layouts.admin')

@section('title', 'Banks')

@section('content')
  <div class="admin-header" style="display:flex; align-items:flex-start; justify-content:space-between; gap:14px; flex-wrap:wrap;">
    <div>
      <h1>Banks</h1>
      <p>The directory customers pick from when sending to another bank or sending internationally.</p>
    </div>
    <a href="{{ route('admin.banks.create') }}" class="admin-btn admin-btn-primary">Add bank</a>
  </div>

  <div class="admin-tabs" style="margin-bottom:16px;">
    <a href="{{ route('admin.banks') }}" class="admin-tab {{ ! $type ? 'active' : '' }}" style="text-decoration:none; display:inline-block;">All</a>
    <a href="{{ route('admin.banks', ['type' => 'external']) }}" class="admin-tab {{ $type === 'external' ? 'active' : '' }}" style="text-decoration:none; display:inline-block;">Domestic ({{ $externalCount }})</a>
    <a href="{{ route('admin.banks', ['type' => 'international']) }}" class="admin-tab {{ $type === 'international' ? 'active' : '' }}" style="text-decoration:none; display:inline-block;">International ({{ $internationalCount }})</a>
  </div>

  <div class="admin-card" style="padding:0; overflow-x:auto;">
    @if($banks->isEmpty())
      <p class="admin-empty">No banks in the directory yet. Add one to get started.</p>
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
