{{--
  Shared pagination footer for every admin list page — pass in the
  paginator as $paginator (a LengthAwarePaginator from ->paginate()).
  Renders nothing when there's only one page, so pages with few rows don't
  show a pointless single-page control. Query-string filters (like the
  Banks page's ?type=... tab) are preserved automatically as long as the
  controller called ->withQueryString() on the paginator.
--}}
@if($paginator->hasPages())
  @php
    $adminPagFirst = max(1, $paginator->currentPage() - 2);
    $adminPagLast = min($paginator->lastPage(), $paginator->currentPage() + 2);
  @endphp
  <div class="admin-pagination">
    <p class="admin-pagination-summary">
      Showing {{ $paginator->firstItem() }}&ndash;{{ $paginator->lastItem() }} of {{ $paginator->total() }}
    </p>
    <div class="admin-pagination-links">
      <a href="{{ $paginator->previousPageUrl() ?? '#' }}" class="admin-page-link {{ $paginator->onFirstPage() ? 'disabled' : '' }}" aria-label="Previous page">&larr;</a>

      @if($adminPagFirst > 1)
        <a href="{{ $paginator->url(1) }}" class="admin-page-link">1</a>
        @if($adminPagFirst > 2)
          <span class="admin-page-ellipsis">&hellip;</span>
        @endif
      @endif

      @for($page = $adminPagFirst; $page <= $adminPagLast; $page++)
        <a href="{{ $paginator->url($page) }}" class="admin-page-link {{ $page == $paginator->currentPage() ? 'active' : '' }}">{{ $page }}</a>
      @endfor

      @if($adminPagLast < $paginator->lastPage())
        @if($adminPagLast < $paginator->lastPage() - 1)
          <span class="admin-page-ellipsis">&hellip;</span>
        @endif
        <a href="{{ $paginator->url($paginator->lastPage()) }}" class="admin-page-link">{{ $paginator->lastPage() }}</a>
      @endif

      <a href="{{ $paginator->nextPageUrl() ?? '#' }}" class="admin-page-link {{ ! $paginator->hasMorePages() ? 'disabled' : '' }}" aria-label="Next page">&rarr;</a>
    </div>
  </div>
@endif
