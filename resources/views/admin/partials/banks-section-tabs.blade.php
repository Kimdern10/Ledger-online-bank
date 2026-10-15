{{-- The two halves of Admin > Banks. $current: 'providers' (the banks the
     APIs offer — the main list) or 'directory' (banks an admin added). --}}
<div class="admin-tabs" style="margin-bottom:12px;">
  <a href="{{ route('admin.banks.providers') }}" class="admin-tab {{ $current === 'providers' ? 'active' : '' }}" style="text-decoration:none; display:inline-block;">API banks</a>
  <a href="{{ route('admin.banks') }}" class="admin-tab {{ $current === 'directory' ? 'active' : '' }}" style="text-decoration:none; display:inline-block;">Added by you</a>
</div>