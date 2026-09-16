{{--
  Shared pagination footer for any customer-facing page listing records —
  pass in the paginator as $paginator (a LengthAwarePaginator from
  ->paginate()). Mirrors partials/admin-pagination.blade.php but themed
  with the customer-app's own CSS variables/classes instead of the admin
  ones. Renders nothing when there's only one page.
--}}
@if($paginator->hasPages())
  <?php
    $appPagFirst = max(1, $paginator->currentPage() - 2);
    $appPagLast = min($paginator->lastPage(), $paginator->currentPage() + 2);
  ?>
  <div class="app-pagination">
    <p class="app-pagination-summary">
      Showing {{ $paginator->firstItem() }}&ndash;{{ $paginator->lastItem() }} of {{ $paginator->total() }}
    </p>
    <div class="app-pagination-links">
      <a href="{{ $paginator->previousPageUrl() ?? '#' }}" class="app-page-link @disabled($paginator->onFirstPage())" aria-label="Previous page">&larr;</a>

      @if($appPagFirst > 1)
        <a href="{{ $paginator->url(1) }}" class="app-page-link">1</a>
        @if($appPagFirst > 2)
          <span class="app-page-ellipsis">&hellip;</span>
        @endif
      @endif

      @for($page = $appPagFirst; $page <= $appPagLast; $page++)
        <a href="{{ $paginator->url($page) }}" class="app-page-link {!! $page == $paginator->currentPage() ? 'active' : '' !!}">{{ $page }}</a>
      @endfor

      @if($appPagLast < $paginator->lastPage())
        @if($appPagLast < $paginator->lastPage() - 1)
          <span class="app-page-ellipsis">&hellip;</span>
        @endif
        <a href="{{ $paginator->url($paginator->lastPage()) }}" class="app-page-link">{{ $paginator->lastPage() }}</a>
      @endif

      <a href="{{ $paginator->nextPageUrl() ?? '#' }}" class="app-page-link @disabled(! $paginator->hasMorePages())" aria-label="Next page">&rarr;</a>
    </div>
  </div>
@endif
