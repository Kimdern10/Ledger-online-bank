@extends('layouts.admin')

@section('title', 'Bills')

@section('content')
  <div class="admin-header">
    <h1>Bills</h1>
    <p>Every bill added across every account.</p>
  </div>

  <div class="admin-card" style="padding:0; overflow-x:auto;">
    @if($bills->isEmpty())
      <p class="admin-empty">No bills have been added yet.</p>
    @else
      <table class="admin-table">
        <thead>
          <tr>
            <th>User</th>
            <th>Category</th>
            <th>Biller</th>
            <th>Amount</th>
            <th>Due</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          @foreach($bills as $bill)
            <tr>
              <td>{{ $bill->user?->name ?? 'Deleted user' }}</td>
              <td>{{ $bill->category }}</td>
              <td>{{ $bill->biller }}</td>
              <td>${{ number_format($bill->amount, 2) }}</td>
              <td>{{ $bill->due_date->format('M j, Y') }}</td>
              <td>
                <form method="POST" action="{{ route('admin.bills.destroy', $bill) }}" data-confirm="Remove this bill?" data-confirm-danger="1" data-confirm-button="Remove">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="admin-btn admin-btn-danger">Remove</button>
                </form>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
      <div style="padding:0 20px 16px;">
        @include('partials.admin-pagination', ['paginator' => $bills])
      </div>
    @endif
  </div>
@endsection
