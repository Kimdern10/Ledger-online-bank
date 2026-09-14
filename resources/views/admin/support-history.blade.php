@extends('layouts.admin')

@section('title', 'Support history · '.$customer->name)

@section('content')
  <div class="admin-header">
    <p class="eyebrow"><a href="{{ route('admin.support.show', $customer) }}">← Back to {{ $customer->name }}</a></p>
    <h1>Conversation history</h1>
    <p>{{ $customer->name }} &middot; {{ $customer->email }} &middot; {{ $conversations->count() }} conversation{{ $conversations->count() === 1 ? '' : 's' }} total.</p>
  </div>

  <div class="admin-card" style="padding:0; overflow-x:auto;">
    @if($conversations->isEmpty())
      <p class="admin-empty">No conversations yet.</p>
    @else
      <table class="admin-table">
        <thead>
          <tr>
            <th>Started</th>
            <th>Status</th>
            <th>Messages</th>
            <th>Last message</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          @foreach($conversations as $conversation)
            @php($last = $conversation->messages->first())
            <tr onclick="window.location='{{ route('admin.support.session', [$customer, $conversation]) }}'" style="cursor:pointer;">
              <td>{{ $conversation->created_at->format('M j, Y g:i A') }}</td>
              <td>
                <span class="admin-pill {{ $conversation->isOpen() ? 'status-active' : 'status-frozen' }}">{{ $conversation->statusLabel() }}</span>
                @if($conversation->isUrgent())
                  <span class="admin-pill status-disabled">Urgent</span>
                @endif
                <p style="margin:4px 0 0; font-size:11.5px; color:var(--admin-text-2);">
                  {{ $conversation->categoryLabel() }}
                  @if(! $conversation->isOpen()) &middot; {{ $conversation->closedReasonLabel() }} @endif
                </p>
              </td>
              <td>{{ $conversation->messages_count }}</td>
              <td style="max-width:320px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; color:var(--admin-text-2);">
                @if($last)
                  @if($last->is_bot)
                    Bot: {{ $last->preview() }}
                  @elseif($last->isFromAdmin())
                    You: {{ $last->preview() }}
                  @else
                    {{ $last->preview() }}
                  @endif
                @else
                  -
                @endif
              </td>
              <td style="text-align:right; color:var(--admin-text-2);">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:16px; height:16px;"><path d="M9 18l6-6-6-6"/></svg>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    @endif
  </div>
@endsection
