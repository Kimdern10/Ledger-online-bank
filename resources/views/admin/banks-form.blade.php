@extends('layouts.admin')

@section('title', $bank->exists ? 'Edit bank' : 'Add bank')

@section('content')
  <style>
    .admin-form-row{ margin-bottom:16px; }
    .admin-form-row label{ display:block; font-size:12.5px; font-weight:600; margin-bottom:6px; }
    .admin-form-row input, .admin-form-row select{
      width:100%; border:1px solid var(--admin-border); border-radius:8px; padding:9px 12px;
      font-size:13.5px; font-family:inherit; background:#fff; color:var(--admin-text);
    }
    .admin-form-hint{ font-size:11.5px; color:var(--admin-text-2); margin:5px 0 0; }
    .admin-form-check{ display:flex; align-items:center; gap:8px; }
    .admin-form-check input{ width:auto; }
  </style>

  <div class="admin-header">
    <h1>{{ $bank->exists ? 'Edit bank' : 'Add bank' }}</h1>
    <p>{{ $bank->exists ? 'Update this directory entry.' : 'Add a domestic or international bank customers can send money to.' }}</p>
  </div>

  <div class="admin-card" style="max-width:520px;">
    <form method="POST" action="{{ $bank->exists ? route('admin.banks.update', $bank) : route('admin.banks.store') }}">
      @csrf
      @if($bank->exists) @method('PUT') @endif

      <div class="admin-form-row">
        <label for="type">Type</label>
        <select name="type" id="type" onchange="toggleBankTypeFields()">
          <option value="external" {{ old('type', $bank->type) === 'external' ? 'selected' : '' }}>Domestic (external bank)</option>
          <option value="international" {{ old('type', $bank->type) === 'international' ? 'selected' : '' }}>International</option>
        </select>
        @error('type') <p class="admin-field-error">{{ $message }}</p> @enderror
      </div>

      <div class="admin-form-row">
        <label for="name">Bank name</label>
        <input type="text" name="name" id="name" value="{{ old('name', $bank->name) }}" required maxlength="150">
        @error('name') <p class="admin-field-error">{{ $message }}</p> @enderror
      </div>

      <div class="admin-form-row" id="field-country">
        <label for="country">Country</label>
        <input type="text" name="country" id="country" value="{{ old('country', $bank->country) }}" maxlength="100" placeholder="e.g. United Kingdom">
        @error('country') <p class="admin-field-error">{{ $message }}</p> @enderror
      </div>

      <div class="admin-form-row" id="field-routing">
        <label for="routing_number">Routing number</label>
        <input type="text" name="routing_number" id="routing_number" value="{{ old('routing_number', $bank->routing_number) }}" maxlength="20">
        <p class="admin-form-hint">Domestic banks only.</p>
        @error('routing_number') <p class="admin-field-error">{{ $message }}</p> @enderror
      </div>

      <div class="admin-form-row" id="field-swift">
        <label for="swift_code">SWIFT / BIC code</label>
        <input type="text" name="swift_code" id="swift_code" value="{{ old('swift_code', $bank->swift_code) }}" maxlength="20" placeholder="e.g. BARCGB22">
        <p class="admin-form-hint">International banks only.</p>
        @error('swift_code') <p class="admin-field-error">{{ $message }}</p> @enderror
      </div>

      <div class="admin-form-row" id="field-currency">
        <label for="currency">Currency code</label>
        <input type="text" name="currency" id="currency" value="{{ old('currency', $bank->currency) }}" maxlength="3" placeholder="e.g. GBP">
        @error('currency') <p class="admin-field-error">{{ $message }}</p> @enderror
      </div>

      <div class="admin-form-row admin-form-check">
        <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $bank->exists ? $bank->is_active : true) ? 'checked' : '' }}>
        <label for="is_active" style="margin:0;">Active (visible to customers)</label>
      </div>

      <div style="display:flex; gap:10px; margin-top:20px;">
        <button type="submit" class="admin-btn admin-btn-primary">{{ $bank->exists ? 'Save changes' : 'Add bank' }}</button>
        <a href="{{ route('admin.banks') }}" class="admin-btn admin-btn-outline">Cancel</a>
      </div>
    </form>
  </div>
@endsection

@section('scripts')
  <script>
    function toggleBankTypeFields(){
      var type = document.getElementById('type').value;
      document.getElementById('field-routing').style.display = type === 'external' ? '' : 'none';
      document.getElementById('field-country').style.display = type === 'international' ? '' : 'none';
      document.getElementById('field-swift').style.display = type === 'international' ? '' : 'none';
      document.getElementById('field-currency').style.display = type === 'international' ? '' : 'none';
    }
    toggleBankTypeFields();
  </script>
@endsection
