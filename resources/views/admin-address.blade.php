@extends('layouts.admin')

@section('title', 'Address verification')

@section('content')
  <div class="admin-header">
    <h1>Address verification</h1>
    <p><?= e($pending->total()) ?> awaiting review.</p>
  </div>

  <p class="section-label" style="margin:0 0 8px;">Pending</p>
  <div style="display:flex; flex-direction:column; gap:14px; margin-bottom:24px;">
    <?php if ($pending->isEmpty()): ?>
      <div class="admin-card"><p class="admin-empty">No address verifications are waiting on you right now.</p></div>
    <?php else: ?>
      <?php foreach ($pending as $address): ?>
        <div class="admin-card" style="display:flex; gap:18px; flex-wrap:wrap; align-items:center;">
          <a href="<?= e(route('admin.address.image', $address)) ?>" target="_blank" title="Open document full size">
            <img src="<?= e(route('admin.address.image', $address)) ?>" alt="Proof of address" style="width:120px; height:80px; object-fit:cover; border-radius:8px; border:1px solid var(--admin-border); background:#f3f3f0;">
          </a>

          <div style="flex:1; min-width:180px;">
            <p style="margin:0; font-weight:700; font-size:13.5px;"><?= e($address->user?->name ?? 'Deleted user') ?></p>
            <p style="margin:2px 0 0; font-size:11.5px; color:var(--admin-text-2);"><?= e($address->user?->email) ?></p>
            <p style="margin:6px 0 0; font-size:12px; color:var(--admin-text-2);"><?= e($address->documentTypeLabel()) ?> &middot; submitted <?= e($address->created_at->diffForHumans()) ?></p>
          </div>

          <div style="display:flex; gap:8px; flex-wrap:wrap; align-items:center;">
            <form method="POST" action="<?= e(route('admin.address.approve', $address)) ?>" data-confirm="Approve this address verification? Their daily limit will go up immediately." data-confirm-button="Approve">
              <?= csrf_field() ?>
              <button type="submit" class="admin-btn admin-btn-outline">Approve</button>
            </form>
            <form method="POST" action="<?= e(route('admin.address.decline', $address)) ?>" data-confirm="Decline this submission?" data-confirm-danger="1" data-confirm-button="Decline" style="display:flex; gap:6px; align-items:center;">
              <?= csrf_field() ?>
              <input type="text" name="reason" placeholder="Reason (optional)" class="admin-select" style="width:160px;">
              <button type="submit" class="admin-btn admin-btn-danger">Decline</button>
            </form>
          </div>
        </div>
      <?php endforeach; ?>
      @include('partials.admin-pagination', ['paginator' => $pending])
    <?php endif; ?>
  </div>

  <p class="section-label" style="margin:0 0 8px;">Decided recently</p>
  <div class="admin-card" style="padding:0; overflow-x:auto;">
    <?php if ($decided->isEmpty()): ?>
      <p class="admin-empty">No submissions have been decided yet.</p>
    <?php else: ?>
      <table class="admin-table">
        <thead>
          <tr>
            <th>Customer</th>
            <th>Document</th>
            <th>Decision</th>
            <th>Reviewed by</th>
            <th>When</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($decided as $address): ?>
            <tr>
              <td><?= e($address->user?->name ?? 'Deleted user') ?></td>
              <td><?= e($address->documentTypeLabel()) ?></td>
              <td>
                <span class="admin-pill <?= e($address->isApproved() ? 'status-active' : 'status-frozen') ?>"><?= e($address->statusLabel()) ?></span>
                <?php if ($address->isRejected() && $address->rejection_reason): ?>
                  <p style="margin:4px 0 0; font-size:11.5px; color:var(--admin-text-2);"><?= e($address->rejection_reason) ?></p>
                <?php endif; ?>
              </td>
              <td><?= e($address->reviewer?->name ?? '-') ?></td>
              <td><?= e(optional($address->reviewed_at)->diffForHumans() ?? '-') ?></td>
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
