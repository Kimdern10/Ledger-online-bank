@extends('layouts.admin')

@section('title', 'Support history · '.$customer->name)

@section('content')
  <div class="admin-header">
    <p class="eyebrow"><a href="<?= e(route('admin.support.show', $customer)) ?>">← Back to <?= e($customer->name) ?></a></p>
    <h1>Conversation history</h1>
    <p><?= e($customer->name) ?> &middot; <?= e($customer->email) ?> &middot; <?= e($conversations->count()) ?> conversation<?= e($conversations->count() === 1 ? '' : 's') ?> total.</p>
  </div>

  <div class="admin-card" style="padding:0; overflow-x:auto;">
    <?php if ($conversations->isEmpty()): ?>
      <p class="admin-empty">No conversations yet.</p>
    <?php else: ?>
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
          <?php foreach ($conversations as $conversation): ?>
            <?php $last = $conversation->messages->first(); ?>
            <tr onclick="window.location='<?= e(route('admin.support.session', [$customer, $conversation])) ?>'" style="cursor:pointer;">
              <td><?= e($conversation->created_at->format('M j, Y g:i A')) ?></td>
              <td>
                <span class="admin-pill <?= e($conversation->isOpen() ? 'status-active' : 'status-frozen') ?>"><?= e($conversation->statusLabel()) ?></span>
                <?php if ($conversation->isUrgent()): ?>
                  <span class="admin-pill status-disabled">Urgent</span>
                <?php endif; ?>
                <p style="margin:4px 0 0; font-size:11.5px; color:var(--admin-text-2);">
                  <?= e($conversation->categoryLabel()) ?>
                  <?php if (! $conversation->isOpen()): ?> &middot; <?= e($conversation->closedReasonLabel()) ?> <?php endif; ?>
                </p>
              </td>
              <td><?= e($conversation->messages_count) ?></td>
              <td style="max-width:320px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; color:var(--admin-text-2);">
                <?php if ($last): ?>
                  <?php if ($last->is_bot): ?>
                    Bot: <?= e($last->preview()) ?>
                  <?php elseif ($last->isFromAdmin()): ?>
                    You: <?= e($last->preview()) ?>
                  <?php else: ?>
                    <?= e($last->preview()) ?>
                  <?php endif; ?>
                <?php else: ?>
                  -
                <?php endif; ?>
              </td>
              <td style="text-align:right; color:var(--admin-text-2);">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:16px; height:16px;"><path d="M9 18l6-6-6-6"/></svg>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
@endsection
