{{--
  Search + status filter for the admin lists of decisions already made —
  see App\Http\Controllers\Concerns\FiltersAdminDecisions. Pass in:
    $action      the page's own URL (the form submits back to it)
    $search      the current ?q= text
    $selected    the current status filter, or null
    $field       the query-string name for the filter (e.g. "decision")
    $options     [value => label] for the filter dropdown
    $allLabel    the dropdown's "no filter" label
    $placeholder the search box's placeholder
--}}
<form method="GET" action="{{ $action }}" class="admin-filter-row" style="display:flex; gap:10px; flex-wrap:wrap; align-items:center; margin:0 0 12px;">
  <label class="admin-search" style="flex:1 1 240px;">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.35-4.35"/></svg>
    <input type="text" name="q" value="{{ $search }}" placeholder="{{ $placeholder }}" maxlength="100">
  </label>
  <select name="{{ $field }}" onchange="this.form.submit()" aria-label="Filter">
    <option value="">{{ $allLabel }}</option>
    @foreach($options as $value => $label)
      <option value="{{ $value }}" {{ $selected === $value ? 'selected' : '' }}>{{ $label }}</option>
    @endforeach
  </select>
  <button type="submit" class="admin-btn admin-btn-outline">Search</button>
  @if($search !== '' || $selected)
    <a href="{{ $action }}" class="admin-btn admin-btn-outline">Clear</a>
  @endif
</form>
