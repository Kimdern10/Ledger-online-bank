@extends('layouts.admin')

@section('title', 'Activity log')

@section('content')
  <div class="admin-header">
    <p class="eyebrow">Overview</p>
    <h1>Activity log</h1>
    <p>Every change an admin has made, newest first: account status, balances, approvals, banks, support and more.</p>
  </div>

  @include('partials.admin-decision-filter', [
    'action' => route('admin.activity'),
    'search' => $search,
    'selected' => $action,
    'field' => 'action',
    'options' => \App\Models\AdminActivity::ACTION_LABELS,
    'allLabel' => 'All actions',
    'placeholder' => 'Search by admin, customer or what happened',
  ])

  <div class="admin-card" style="padding:0; overflow-x:auto;">
    @if($activities->isEmpty())
      <p class="admin-empty">{{ $search !== '' || $action ? 'No activity matches your search.' : 'No admin activity yet. Changes admins make will show up here.' }}</p>
    @else
      <table class="admin-table">
        <thead>
          <tr>
            <th>When</th>
            <th>Admin</th>
            <th>Action</th>
            <th>What happened</th>
            <th>Customer</th>
          </tr>
        </thead>
        <tbody>
          @foreach($activities as $activity)
            <tr>
              <td title="{{ $activity->created_at->format('M j, Y g:i:s A') }}">
                {{ $activity->created_at->format('M j, Y g:i A') }}
                <p style="margin:2px 0 0; font-size:11.5px; color:var(--admin-text-2);">{{ $activity->created_at->diffForHumans() }}</p>
              </td>
              <td>
                {{ $activity->admin?->name ?? 'Removed admin' }}
                @if($activity->ip_address)
                  <p style="margin:2px 0 0; font-size:11.5px; color:var(--admin-text-2);">{{ $activity->ip_address }}</p>
                @endif
              </td>
              <td><span class="admin-badge">{{ $activity->actionLabel() }}</span></td>
              <td style="white-space:normal; min-width:220px;">{{ $activity->description }}</td>
              <td>
                @if($activity->user)
                  <a href="{{ route('admin.users.show', $activity->user) }}" style="color:var(--admin-accent); font-weight:600;">{{ $activity->user->name }}</a>
                @else
                  <span style="color:var(--admin-text-2);">-</span>
                @endif
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
      <div style="padding:0 20px 16px;">
        @include('partials.admin-pagination', ['paginator' => $activities])
      </div>
    @endif
  </div>
@endsection
