@extends('layouts.app')

@section('content')
<div class="send-wrap">

  <div class="page-header fade-in d1">
    <a href="<?= e(route('support')) ?>" class="icon-btn">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
    </a>
    <h1><?= e(__('support.past_conversations_title')) ?></h1>
    <a href="<?= e(route('dashboard')) ?>" class="icon-btn">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><path d="M3 11l9-8 9 8"/><path d="M5 10v10h14V10"/></svg>
    </a>
  </div>

  <?php /* Every conversation here has ended — a still-open one lives at
       /support instead. Each row opens a read-only transcript of that one
       session — see SupportController::showSession(). */ ?>
  <?php $__ledger_forelse_1 = true; foreach ($conversations as $conversation): $__ledger_forelse_1 = false; ?>
    <?php $last = $conversation->messages->first(); ?>
    <div class="date-group fade-in d2">
      <div class="tx-card">
        <a href="<?= e(route('support.history.show', $conversation)) ?>" class="tx-row out">
          <div class="tx-bar"></div>
          <div class="tx-mid">
            <p class="name"><?= e(__('support.conversation_from', ['date' => $conversation->created_at->format('M j, Y')])) ?></p>
            <p class="meta">
              <?= e($conversation->messages_count) ?> <?= e(\Illuminate\Support\Str::plural('message', $conversation->messages_count)) ?>
              <?php if ($last): ?> &middot; <?= e($last->preview(40)) ?> <?php endif; ?>
            </p>
          </div>
          <div class="tx-amt" style="font-size:12px; font-weight:600; color:var(--text-3, #5C6B72);">
            <?= e(optional($conversation->closed_at)->diffForHumans()) ?>
          </div>
        </a>
      </div>
    </div>
  <?php endforeach; if ($__ledger_forelse_1): ?>
    <p class="no-tx-empty fade-in d2">
      <?= e(__('support.no_past_conversations')) ?>
    </p>
  <?php endif; ?>

</div>

@endsection
