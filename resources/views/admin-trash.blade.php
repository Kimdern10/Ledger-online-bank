@extends('layouts.admin')

@section('title', 'Trash')

@section('content')
  <div class="admin-header">
    <p class="eyebrow">User management</p>
    <h1>Trash</h1>
    <p><?= e($users->total()) ?> deleted <?= e(Str::plural('account', $users->total())) ?>, permanently erased 30 days after deletion unless restored.</p>
  </div>

  <?php if (session('status')): ?>
    <div class="admin-card" style="margin-bottom:14px;">
      <p style="margin:0; font-size:13.5px;"><?= e(session('status')) ?></p>
    </div>
  <?php endif; ?>

  <?php if (session('adminError')): ?>
    <div class="admin-card" style="margin-bottom:14px; border-color:var(--admin-accent-2);">
      <p style="margin:0; font-size:13.5px; color:var(--admin-accent-2);"><?= e(session('adminError')) ?></p>
    </div>
  <?php endif; ?>

  <div class="admin-card" style="padding:0; overflow-x:auto;">
    <?php if ($users->isEmpty()): ?>
      <p class="admin-empty">Nothing in Trash right now.</p>
    <?php else: ?>
      <table class="admin-table">
        <thead>
          <tr>
            <th>User</th>
            <th>Deleted on</th>
            <th>Purges in</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($users as $u): ?>
            <?php $daysLeft = $u->daysUntilPurge(); ?>
            <tr>
              <td>
                <div style="display:flex; align-items:center; gap:11px;">
                  <div class="admin-avatar"><?= e($u->initials()) ?></div>
                  <div>
                    <p style="margin:0; font-weight:600;"><?= e($u->name) ?></p>
                    <p style="margin:0; font-size:12.5px; color:var(--admin-text-2);"><?= e($u->email) ?></p>
                  </div>
                </div>
              </td>
              <td><?= e($u->account_deleted_at->format('M j, Y')) ?></td>
              <td>
                <span class="admin-pill <?= e($daysLeft <= 3 ? 'status-disabled' : 'status-frozen') ?>">
                  <?= e($daysLeft) ?> <?= e(Str::plural('day', $daysLeft)) ?> left
                </span>
              </td>
              <td style="text-align:right; white-space:nowrap;">
                <form method="POST" action="<?= e(route('admin.trash.restore', $u)) ?>" style="display:inline-block;">
                  <?= csrf_field() ?>
                  <button type="submit" class="admin-btn admin-btn-outline" style="font-size:12.5px; padding:6px 12px;">Restore</button>
                </form>
                <form method="POST" action="<?= e(route('admin.trash.purge', $u)) ?>" style="display:inline-block;" data-confirm="Permanently delete this account right now? This can't be undone." data-confirm-danger="1" data-confirm-button="Delete permanently">
                  <?= csrf_field() ?>
                  <button type="submit" class="admin-btn admin-btn-danger" style="font-size:12.5px; padding:6px 12px;">Delete now</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <div style="padding:0 20px 16px;">
        @include('partials.admin-pagination', ['paginator' => $users])
      </div>
    <?php endif; ?>
  </div>
@endsection
