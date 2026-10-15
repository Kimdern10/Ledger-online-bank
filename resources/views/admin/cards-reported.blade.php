@extends('layouts.admin')

@section('title', 'Reported cards')

@section('content')
  <div class="admin-header">
    <h1>Reported cards</h1>
    <p>Every card a customer has marked lost or stolen, most recent first.</p>
  </div>

  <div class="admin-card" style="padding:0; overflow-x:auto;">
    @if($cards->isEmpty())
      <p class="admin-empty">No cards have been reported lost or stolen.</p>
    @else
      <table class="admin-table">
        <thead>
          <tr>
            <th>Customer</th>
            <th>Card</th>
            <th>Status</th>
            <th>Reported</th>
          </tr>
        </thead>
        <tbody>
          @foreach($cards as $card)
            <tr onclick="window.location='{{ route('admin.users.show', $card->user) }}'" style="cursor:pointer;">
              <td>
                {{ $card->user?->name ?? 'Deleted user' }}
                <p style="margin:2px 0 0; font-size:11.5px; color:var(--admin-text-2);">{{ $card->user?->email }}</p>
              </td>
              <td>
                {{ $card->isVirtual() ? 'Virtual' : 'Physical' }} &middot; •••• {{ $card->last4() }}
              </td>
              <td>
                <span class="admin-pill {{ $card->isClosed() ? 'status-frozen' : 'status-disabled' }}">
                  {{ $card->isClosed() ? 'Closed' : 'Frozen' }}
                </span>
              </td>
              <td>{{ optional($card->reported_at)->diffForHumans() ?? '-' }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
      <div style="padding:0 20px 16px;">
        @include('partials.admin-pagination', ['paginator' => $cards])
      </div>
    @endif
  </div>
@endsection
