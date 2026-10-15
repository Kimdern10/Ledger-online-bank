@extends('layouts.admin')

@section('title', 'Identity verification')

@section('content')
  <div class="admin-header">
    <h1>Identity verification</h1>
    <p>{{ $pending->total() }} awaiting review.</p>
  </div>

  <p class="section-label" style="margin:0 0 8px;">Pending</p>
  <div style="display:flex; flex-direction:column; gap:14px; margin-bottom:24px;">
    @if($pending->isEmpty())
      <div class="admin-card"><p class="admin-empty">No identity verifications are waiting on you right now.</p></div>
    @else
      @foreach($pending as $kyc)
        <div class="admin-card" style="display:flex; gap:18px; flex-wrap:wrap; align-items:center;">
          <div style="display:flex; gap:10px;">
            <a href="{{ route('admin.kyc.image', [$kyc, 'id']) }}" target="_blank" title="Open ID document full size">
              <img src="{{ route('admin.kyc.image', [$kyc, 'id']) }}" alt="ID document" style="width:120px; height:80px; object-fit:cover; border-radius:8px; border:1px solid var(--admin-border);">
            </a>
            <a href="{{ route('admin.kyc.image', [$kyc, 'selfie']) }}" target="_blank" title="Open selfie full size">
              <img src="{{ route('admin.kyc.image', [$kyc, 'selfie']) }}" alt="Selfie" style="width:80px; height:80px; object-fit:cover; border-radius:8px; border:1px solid var(--admin-border);">
            </a>
          </div>

          <div style="flex:1; min-width:180px;">
            <p style="margin:0; font-weight:700; font-size:13.5px;">{{ $kyc->user?->name ?? 'Deleted user' }}</p>
            <p style="margin:2px 0 0; font-size:11.5px; color:var(--admin-text-2);">{{ $kyc->user?->email }}</p>
            <p style="margin:6px 0 0; font-size:12px; color:var(--admin-text-2);">{{ $kyc->documentTypeLabel() }} &middot; submitted {{ $kyc->created_at->diffForHumans() }}</p>
          </div>

          <div style="display:flex; gap:8px; flex-wrap:wrap; align-items:center;">
            <form method="POST" action="{{ route('admin.kyc.approve', $kyc) }}" data-confirm="Approve this identity verification? Send Money will unlock immediately and this selfie will become their profile picture." data-confirm-button="Approve">
              @csrf
              <button type="submit" class="admin-btn admin-btn-outline">Approve</button>
            </form>
            <form method="POST" action="{{ route('admin.kyc.decline', $kyc) }}" data-confirm="Decline this submission?" data-confirm-danger="1" data-confirm-button="Decline" style="display:flex; gap:6px; align-items:center;">
              @csrf
              <input type="text" name="reason" placeholder="Reason (optional)" class="admin-select" style="width:160px;">
              <button type="submit" class="admin-btn admin-btn-danger">Decline</button>
            </form>
          </div>
        </div>
      @endforeach
      @include('partials.admin-pagination', ['paginator' => $pending])
    @endif
  </div>

  <p class="section-label" style="margin:0 0 8px;">Decided recently</p>
  <div class="admin-card" style="padding:0; overflow-x:auto;">
    @if($decided->isEmpty())
      <p class="admin-empty">No submissions have been decided yet.</p>
    @else
      <table class="admin-table">
        <thead>
          <tr>
            <th>Customer</th>
            <th>ID type</th>
            <th>Decision</th>
            <th>Reviewed by</th>
            <th>When</th>
          </tr>
        </thead>
        <tbody>
          @foreach($decided as $kyc)
            <tr>
              <td>{{ $kyc->user?->name ?? 'Deleted user' }}</td>
              <td>{{ $kyc->documentTypeLabel() }}</td>
              <td>
                <span class="admin-pill {{ $kyc->isApproved() ? 'status-active' : 'status-frozen' }}">{{ $kyc->statusLabel() }}</span>
                @if($kyc->isRejected() && $kyc->rejection_reason)
                  <p style="margin:4px 0 0; font-size:11.5px; color:var(--admin-text-2);">{{ $kyc->rejection_reason }}</p>
                @endif
              </td>
              <td>{{ $kyc->reviewer?->name ?? '-' }}</td>
              <td>{{ optional($kyc->reviewed_at)->diffForHumans() ?? '-' }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
      <div style="padding:0 20px 16px;">
        @include('partials.admin-pagination', ['paginator' => $decided])
      </div>
    @endif
  </div>
@endsection
