@extends('layouts.admin')

@section('title', 'Banks')

@section('content')
  <div class="admin-header" style="display:flex; align-items:flex-start; justify-content:space-between; gap:14px; flex-wrap:wrap;">
    <div>
      <h1>Banks</h1>
      <p>The directory customers pick from when sending to another bank or sending internationally.</p>
    </div>
    <a href="<?= e(route('admin.banks.create')) ?>" class="admin-btn admin-btn-primary">Add bank</a>
  </div>

  <div class="admin-tabs" style="margin-bottom:16px;">
    <a href="<?= e(route('admin.banks')) ?>" class="admin-tab <?= e(! $type ? 'active' : '') ?>" style="text-decoration:none; display:inline-block;">All</a>
    <a href="<?= e(route('admin.banks', ['type' => 'external'])) ?>" class="admin-tab <?= e($type === 'external' ? 'active' : '') ?>" style="text-decoration:none; display:inline-block;">Domestic (<?= e($externalCount) ?>)</a>
    <a href="<?= e(route('admin.banks', ['type' => 'international'])) ?>" class="admin-tab <?= e($type === 'international' ? 'active' : '') ?>" style="text-decoration:none; display:inline-block;">International (<?= e($internationalCount) ?>)</a>
  </div>

  <div class="admin-card" style="padding:0; overflow-x:auto;">
    <?php if ($banks->isEmpty()): ?>
      <p class="admin-empty">No banks in the directory yet. Add one to get started.</p>
    <?php else: ?>
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
          <?php foreach ($banks as $bank): ?>
            <tr>
              <td><?= e($bank->name) ?></td>
              <td><span class="admin-badge"><?= e($bank->typeLabel()) ?></span></td>
              <td><?= e($bank->country ?? '—') ?></td>
              <td><?= e($bank->routing_number ?? '—') ?></td>
              <td><?= e($bank->swift_code ?? '—') ?></td>
              <td>
                <span class="admin-pill <?= e($bank->is_active ? 'status-active' : 'status-disabled') ?>"><?= e($bank->is_active ? 'Active' : 'Inactive') ?></span>
              </td>
              <td>
                <div style="display:flex; gap:6px; flex-wrap:wrap;">
                  <a href="<?= e(route('admin.banks.edit', $bank)) ?>" class="admin-btn admin-btn-outline">Edit</a>
                  <form method="POST" action="<?= e(route('admin.banks.toggle', $bank)) ?>">
                    <?= csrf_field() ?>
                    <button type="submit" class="admin-btn admin-btn-outline"><?= e($bank->is_active ? 'Deactivate' : 'Activate') ?></button>
                  </form>
                  <form method="POST" action="<?= e(route('admin.banks.destroy', $bank)) ?>" data-confirm="Remove <?= e($bank->name) ?> from the directory?" data-confirm-danger="1" data-confirm-button="Remove">
                    <?= csrf_field() ?>
                    <?= method_field('DELETE') ?>
                    <button type="submit" class="admin-btn admin-btn-danger">Remove</button>
                  </form>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <div style="padding:0 20px 16px;">
        @include('partials.admin-pagination', ['paginator' => $banks])
      </div>
    <?php endif; ?>
  </div>
@endsection
