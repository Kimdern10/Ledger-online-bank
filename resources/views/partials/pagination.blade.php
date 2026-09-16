<?php /*
  Shared pagination footer for any customer-facing page listing records —
  pass in the paginator as $paginator (a LengthAwarePaginator from
  ->paginate()). Mirrors partials/admin-pagination.blade.php but themed
  with the customer-app's own CSS variables/classes instead of the admin
  ones. Renders nothing when there's only one page.
*/ ?>
<?php if ($paginator->hasPages()): ?>
  <?php
    $appPagFirst = max(1, $paginator->currentPage() - 2);
    $appPagLast = min($paginator->lastPage(), $paginator->currentPage() + 2);
  ?>
  <div class="app-pagination">
    <p class="app-pagination-summary">
      Showing <?= e($paginator->firstItem()) ?>&ndash;<?= e($paginator->lastItem()) ?> of <?= e($paginator->total()) ?>
    </p>
    <div class="app-pagination-links">
      <a href="<?= e($paginator->previousPageUrl() ?? '#') ?>" class="app-page-link <?= $paginator->onFirstPage() ? 'disabled' : '' ?>" aria-label="Previous page">&larr;</a>

      <?php if ($appPagFirst > 1): ?>
        <a href="<?= e($paginator->url(1)) ?>" class="app-page-link">1</a>
        <?php if ($appPagFirst > 2): ?>
          <span class="app-page-ellipsis">&hellip;</span>
        <?php endif; ?>
      <?php endif; ?>

      <?php for ($page = $appPagFirst; $page <= $appPagLast; $page++): ?>
        <a href="<?= e($paginator->url($page)) ?>" class="app-page-link <?= $page == $paginator->currentPage() ? 'active' : '' ?>"><?= e($page) ?></a>
      <?php endfor; ?>

      <?php if ($appPagLast < $paginator->lastPage()): ?>
        <?php if ($appPagLast < $paginator->lastPage() - 1): ?>
          <span class="app-page-ellipsis">&hellip;</span>
        <?php endif; ?>
        <a href="<?= e($paginator->url($paginator->lastPage())) ?>" class="app-page-link"><?= e($paginator->lastPage()) ?></a>
      <?php endif; ?>

      <a href="<?= e($paginator->nextPageUrl() ?? '#') ?>" class="app-page-link <?= ! $paginator->hasMorePages() ? 'disabled' : '' ?>" aria-label="Next page">&rarr;</a>
    </div>
  </div>
<?php endif; ?>
