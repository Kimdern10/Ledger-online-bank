@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
  <div class="admin-header">
    <p class="eyebrow">Overview</p>
    <h1>Dashboard</h1>
    <p>A quick look at what's happening across Ledger.</p>
  </div>

  <div class="admin-stat-row">
    <div class="admin-stat is-dark">
      <div class="admin-stat-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
      </div>
      <p class="label">Total users</p>
      <p class="value">{{ number_format($userCount) }}</p>
    </div>
    <div class="admin-stat is-dark">
      <div class="admin-stat-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
      </div>
      <p class="label">Active users</p>
      <p class="value">{{ number_format($activeCount) }}</p>
    </div>
    <div class="admin-stat is-danger">
      <div class="admin-stat-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M4.9 4.9l14.2 14.2"/></svg>
      </div>
      <p class="label">Frozen / suspended / disabled</p>
      <p class="value">{{ number_format($inactiveCount) }}</p>
    </div>
    <div class="admin-stat">
      <div class="admin-stat-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 11l9-8 9 8"/><path d="M5 10v10h14V10"/></svg>
      </div>
      <p class="label">Bills tracked</p>
      <p class="value">{{ number_format($billCount) }}</p>
    </div>
    <div class="admin-stat">
      <div class="admin-stat-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>
      </div>
      <p class="label">Combined available balance</p>
      <p class="value">${{ number_format($totalBalance, 2) }}</p>
    </div>
  </div>

  <div class="admin-card">
    <p style="margin:0 0 4px; font-weight:600;">Where to go next</p>
    <p style="margin:0; color:var(--admin-text-2); font-size:13.5px;">
      Use <a href="{{ route('admin.users') }}" style="color:var(--admin-accent); font-weight:600;">Users</a> to look up an account, open its detail page, or adjust a balance,
      or <a href="{{ route('admin.bills') }}" style="color:var(--admin-accent); font-weight:600;">Bills</a> to see and remove anything added across every account.
    </p>
  </div>
@endsection
