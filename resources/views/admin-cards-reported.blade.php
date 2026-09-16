@extends('layouts.admin')

@section('title', 'Reported cards')

@section('content')
  <div class="admin-header">
    <h1>Reported cards</h1>
    <p>Every card a customer has marked lost or stolen, most recent first.</p>
  </div>

  <div class="admin-card" style="padding:0; overflow-x:auto;">
    <?php if ($cards->isEmpty()): ?>
      <p class="admin-empty">No cards have been reported lost or stolen.</p>
    <?php else: ?>
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
          <?php foreach ($cards as $card): ?>
            <tr onclick="window.location='<?= e(route('admin.users.show', $card->user)) ?>'" style="cursor:pointer;">
              <td>
                <?= e($card->user?->name ?? 'Deleted user') ?>
                <p style="margin:2px 0 0; font-size:11.5px; color:var(--admin-text-2);"><?= e($card->user?->email) ?></p>
              </td>
              <td>
                <?= e($card->isVirtual() ? 'Virtual' : 'Physical') ?> &middot; •••• <?= e($card->last4()) ?>
              </td>
              <td>
                <span class="admin-pill <?= e($card->isClosed() ? 'status-frozen' : 'status-disabled') ?>">
                  <?= e($card->isClosed() ? 'Closed' : 'Frozen') ?>
                </span>
              </td>
              <td><?= e(optional($card->reported_at)->diffForHumans() ?? '-') ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <div style="padding:0 20px 16px;">
        @include('partials.admin-pagination', ['paginator' => $cards])
      </div>
    <?php endif; ?>
  </div>
@endsection
