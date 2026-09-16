@extends('layouts.admin')

@section('title', 'Card requests')

@section('content')
  <div class="admin-header">
    <h1>Card requests</h1>
    <p><?= e($pending->total()) ?> awaiting a decision.</p>
  </div>

  <p class="section-label" style="margin:0 0 8px;">Pending</p>
  <div class="admin-card" style="padding:0; overflow-x:auto; margin-bottom:24px;">
    <?php if ($pending->isEmpty()): ?>
      <p class="admin-empty">No card requests are waiting on you right now.</p>
    <?php else: ?>
      <table class="admin-table">
        <thead>
          <tr>
            <th>Customer</th>
            <th>Type</th>
            <th>Requested</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($pending as $cardRequest): ?>
            <tr>
              <td>
                <?= e($cardRequest->user?->name ?? 'Deleted user') ?>
                <p style="margin:2px 0 0; font-size:11.5px; color:var(--admin-text-2);"><?= e($cardRequest->user?->email) ?></p>
              </td>
              <td><?= e($cardRequest->typeLabel()) ?></td>
              <td><?= e($cardRequest->created_at->diffForHumans()) ?></td>
              <td>
                <div style="display:flex; gap:8px; flex-wrap:wrap; align-items:center;">
                  <form method="POST" action="<?= e(route('admin.card-requests.approve', $cardRequest)) ?>" data-confirm="Approve this request? A real card number will be generated and issued right away." data-confirm-button="Approve">
                    <?= csrf_field() ?>
                    <button type="submit" class="admin-btn admin-btn-outline">Approve</button>
                  </form>
                  <form method="POST" action="<?= e(route('admin.card-requests.decline', $cardRequest)) ?>" data-confirm="Decline this request?" data-confirm-danger="1" data-confirm-button="Decline" style="display:flex; gap:6px; align-items:center;">
                    <?= csrf_field() ?>
                    <input type="text" name="reason" placeholder="Reason (optional)" class="admin-select" style="width:160px;">
                    <button type="submit" class="admin-btn admin-btn-danger">Decline</button>
                  </form>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <div style="padding:0 20px 16px;">
        @include('partials.admin-pagination', ['paginator' => $pending])
      </div>
    <?php endif; ?>
  </div>

  <p class="section-label" style="margin:0 0 8px;">Decided recently</p>
  <div class="admin-card" style="padding:0; overflow-x:auto;">
    <?php if ($decided->isEmpty()): ?>
      <p class="admin-empty">No requests have been decided yet.</p>
    <?php else: ?>
      <table class="admin-table">
        <thead>
          <tr>
            <th>Customer</th>
            <th>Type</th>
            <th>Decision</th>
            <th>Reviewed by</th>
            <th>When</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($decided as $cardRequest): ?>
            <tr>
              <td><?= e($cardRequest->user?->name ?? 'Deleted user') ?></td>
              <td><?= e($cardRequest->typeLabel()) ?></td>
              <td>
                <span class="admin-pill <?= e($cardRequest->isApproved() ? 'status-active' : 'status-frozen') ?>"><?= e($cardRequest->statusLabel()) ?></span>
                <?php if ($cardRequest->isApproved() && $cardRequest->card): ?>
                  <p style="margin:4px 0 0; font-size:11.5px; color:var(--admin-text-2);">Card ending <?= e($cardRequest->card->last4()) ?></p>
                <?php elseif ($cardRequest->isDeclined() && $cardRequest->decline_reason): ?>
                  <p style="margin:4px 0 0; font-size:11.5px; color:var(--admin-text-2);"><?= e($cardRequest->decline_reason) ?></p>
                <?php endif; ?>
              </td>
              <td><?= e($cardRequest->reviewer?->name ?? '-') ?></td>
              <td><?= e(optional($cardRequest->reviewed_at)->diffForHumans() ?? '-') ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <div style="padding:0 20px 16px;">
        @include('partials.admin-pagination', ['paginator' => $decided])
      </div>
    <?php endif; ?>
  </div>
@endsection
