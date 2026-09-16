<?php /*
  Shared pagination footer for every admin list page — pass in the
  paginator as $paginator (a LengthAwarePaginator from ->paginate()).
  Renders nothing when there's only one page, so pages with few rows don't
  show a pointless single-page control. Query-string filters (like the
  Banks page's ?type=... tab) are preserved automatically as long as the
  controller called ->withQueryString() on the paginator.
*/ ?>
<?php if ($paginator->hasPages()): ?>
  <?php
    $adminPagFirst = max(1, $paginator->currentPage() - 2);
    $adminPagLast = min($paginator->lastPage(), $paginator->currentPage() + 2);
  ?>
  <div class="admin-pagination">
    <p class="admin-pagination-summary">
      Showing <?= e($paginator->firstItem()) ?>&ndash;<?= e($paginator->lastItem()) ?> of <?= e($paginator->total()) ?>
    </p>
    <div class="admin-pagination-links">
      <a href="<?= e($paginator->previousPageUrl() ?? '#') ?>" class="admin-page-link <?= $paginator->onFirstPage() ? 'disabled' : '' ?>" aria-label="Previous page">&larr;</a>

      <?php if ($adminPagFirst > 1): ?>
        <a href="<?= e($paginator->url(1)) ?>" class="admin-page-link">1</a>
        <?php if ($adminPagFirst > 2): ?>
          <span class="admin-page-ellipsis">&hellip;</span>
        <?php endif; ?>
      <?php endif; ?>

      <?php for ($page = $adminPagFirst; $page <= $adminPagLast; $page++): ?>
        <a href="<?= e($paginator->url($page)) ?>" class="admin-page-link <?= $page == $paginator->currentPage() ? 'active' : '' ?>"><?= e($page) ?></a>
      <?php endfor; ?>

      <?php if ($adminPagLast < $paginator->lastPage()): ?>
        <?php if ($adminPagLast < $paginator->lastPage() - 1): ?>
          <span class="admin-page-ellipsis">&hellip;</span>
        <?php endif; ?>
        <a href="<?= e($paginator->url($paginator->lastPage())) ?>" class="admin-page-link"><?= e($paginator->lastPage()) ?></a>
      <?php endif; ?>

      <a href="<?= e($paginator->nextPageUrl() ?? '#') ?>" class="admin-page-link <?= ! $paginator->hasMorePages() ? 'disabled' : '' ?>" aria-label="Next page">&rarr;</a>
    </div>
  </div>
<?php endif; ?>
