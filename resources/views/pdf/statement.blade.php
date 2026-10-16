{{-- The statement PDF attached to App\Mail\StatementMail: a brand bar, the
     customer's name and address beside the account number, period and
     opening/closing balances, a Summary and a Spend account box, then every
     transaction with its running balance, and the Ledger stamp and signature
     at the end. Rendered by dompdf, which only understands basic CSS, so the
     layout is built from tables rather than flexbox. $statement is an
     App\Support\MonthlyStatement. --}}
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>{{ __('statement.statement_for', ['period' => $statement->periodLabel()]) }}</title>
<style>
  @page { margin: 30px 30px 58px; }
  * { box-sizing: border-box; }
  body { font-family: 'DejaVu Sans', sans-serif; font-size: 9px; color: #10202F; margin: 0; }
  .serif { font-family: 'DejaVu Serif', serif; }
  table { border-collapse: collapse; width: 100%; }
  .brand { background: #EEF0F6; }
  .brand td { padding: 12px 16px; vertical-align: middle; }
  .holder td { vertical-align: top; padding: 0; }
  .name { font-size: 12px; font-weight: bold; color: #10202F; text-transform: uppercase; margin: 22px 0 6px; }
  .address { color: #3B4A59; line-height: 1.55; font-size: 9px; }
  .facts td { padding: 4px 0 6px; vertical-align: top; }
  .label { font-size: 7.5px; font-weight: bold; color: #3B4A59; margin-bottom: 2px; }
  .value { font-size: 10px; font-weight: bold; }
  h2 { font-size: 12px; color: #1F3A6B; margin: 22px 0 8px; }
  .box { border: 1px solid #DDE1EA; }
  .box td, .box th { padding: 9px 14px; text-align: left; }
  .box th { font-weight: normal; color: #3B4A59; font-size: 9px; border-bottom: 1px solid #EEF0F6; }
  .box .amt { font-weight: bold; color: #1F3A6B; font-size: 10px; }
  .in { color: #2F8F5B; }
  .tx { margin-top: 18px; }
  .tx th { background: #EEF0F6; font-size: 8px; font-weight: bold; text-align: left; padding: 7px 5px; }
  .tx td { padding: 9px 5px; border-bottom: 1px solid #EEF0F6; vertical-align: top; font-size: 8px; }
  .tx .num { white-space: nowrap; font-weight: bold; }
  .tx .bal { white-space: nowrap; font-weight: bold; text-align: right; }
  .tx th.bal { text-align: right; }
  .empty { text-align: center; color: #8C9298; padding: 24px 6px; }
  .note { color: #8C9298; font-size: 8px; line-height: 1.6; margin-top: 16px; }
  .sign { margin-top: 18px; page-break-inside: avoid; }
  .sign td { vertical-align: bottom; }
  .sign-line { border-top: 1px solid #10202F; padding-top: 4px; font-size: 8px; color: #3B4A59; text-align: center; }
  .footer { position: fixed; bottom: -44px; left: 0; right: 0; font-size: 7.5px; color: #8C9298; border-top: 1px solid #EEF0F6; padding-top: 6px; }
  .pagenum:after { content: counter(page); }
</style>
</head>
<body>
  <?php
      $user = $statement->user;
      $profile = $user->profile;
      $accountNumber = $user->checking_account_number ?: $user->savings_account_number;
      $money = fn (float $amount): string => ($amount < 0 ? '-' : '').'$'.number_format(abs($amount), 2);
      $addressLines = array_values(array_filter([
          $profile?->home_address,
          implode(', ', array_filter([$profile?->city, $profile?->state, $profile?->zip_code])),
          $profile?->country,
      ]));
      $image = fn (string $file): string => 'data:image/svg+xml;base64,'.base64_encode((string) file_get_contents(resource_path("views/pdf/{$file}")));
  ?>

  <div class="footer">
    <table><tr>
      <td>Ledger Financial, Inc. &middot; {{ __('statement.statement_for', ['period' => $statement->periodLabel()]) }}@if($accountNumber) &middot; {{ __('statement.account_number') }} {{ $accountNumber }}@endif</td>
      <td style="text-align:right;">{{ __('statement.page') }} <span class="pagenum"></span></td>
    </tr></table>
  </div>

  <table class="brand"><tr>
    <td style="width:34px;"><img src="{{ $image('logo.svg') }}" width="28" height="28" alt=""></td>
    <td class="serif" style="font-size:16px; font-weight:bold;">Ledger</td>
    <td style="text-align:right; font-size:11px; font-weight:bold;">{{ __('statement.eyebrow') }}</td>
  </tr></table>

  <table class="holder"><tr>
    <td style="width:52%; padding-right:24px;">
      <p class="name">{{ $user->name }}</p>
      <div class="address">
        @foreach($addressLines as $line)
          {{ $line }}<br>
        @endforeach
        {{ $user->email }}
      </div>
    </td>
    <td style="padding-top:20px;">
      <table class="facts">
        <tr>
          <td style="width:50%;"><div class="label">{{ __('statement.account_number') }}</div><div class="value">{{ $accountNumber ?: '—' }}</div></td>
          <td><div class="label">{{ __('statement.period') }}</div><div class="value">{{ $statement->from->format('d/m/Y') }} - {{ $statement->to->format('d/m/Y') }}</div></td>
        </tr>
        <tr>
          <td><div class="label">{{ __('statement.opening_balance') }}</div><div class="value">{{ $money($statement->openingBalance) }}</div></td>
          <td><div class="label">{{ __('statement.closing_balance') }}</div><div class="value">{{ $money($statement->closingBalance) }}</div></td>
        </tr>
      </table>
    </td>
  </tr></table>

  <h2>{{ __('statement.summary') }}</h2>
  <table class="box">
    <tr>
      <th style="width:40%;">{{ __('statement.type') }}</th>
      <th>{{ __('statement.opening_balance') }}</th>
      <th>{{ __('statement.closing_balance') }}</th>
    </tr>
    <tr>
      <td>{{ __('statement.spend_account') }}</td>
      <td class="amt">{{ $money($statement->openingBalance) }}</td>
      <td class="amt">{{ $money($statement->closingBalance) }}</td>
    </tr>
  </table>

  <h2>{{ __('statement.spend_account') }}</h2>
  <table class="box">
    <tr>
      <th>{{ __('statement.money_in') }}</th>
      <th>{{ __('statement.money_out') }}</th>
      <th>{{ __('statement.opening_balance') }}</th>
      <th>{{ __('statement.closing_balance') }}</th>
    </tr>
    <tr>
      <td class="amt">{{ $money($statement->moneyIn) }}</td>
      <td class="amt">{{ $money($statement->moneyOut) }}</td>
      <td class="amt">{{ $money($statement->openingBalance) }}</td>
      <td class="amt">{{ $money($statement->closingBalance) }}</td>
    </tr>
  </table>

  <table class="tx">
    <thead>
      <tr>
        <th style="width:11%;">{{ __('statement.date_time') }}</th>
        <th style="width:12%;">{{ __('statement.money_in') }}</th>
        <th style="width:12%;">{{ __('statement.money_out') }}</th>
        <th style="width:12%;">{{ __('statement.category') }}</th>
        <th style="width:19%;">{{ __('statement.to_from') }}</th>
        <th>{{ __('statement.description') }}</th>
        <th class="bal" style="width:13%;">{{ __('statement.balance') }}</th>
      </tr>
    </thead>
    <tbody>
      @forelse($statement->items as $item)
        <tr>
          <td>{{ $item['created_at']->format('d/m/y') }}<br>{{ $item['created_at']->format('H:i:s') }}</td>
          <td class="num in">{{ $item['amount_raw'] > 0 ? $money($item['amount_raw']) : '' }}</td>
          <td class="num">{{ $item['amount_raw'] < 0 ? $money(abs($item['amount_raw'])) : '' }}</td>
          <td>{{ $item['label'] }}</td>
          <td>{{ $item['name'] }}</td>
          <td>{{ $item['description'] }}</td>
          <td class="bal">{{ $money($item['balance']) }}</td>
        </tr>
      @empty
        <tr><td colspan="7" class="empty">{{ __('statement.empty') }}</td></tr>
      @endforelse
    </tbody>
  </table>

  <p class="note">{{ __('statement.footer') }}</p>

  <table class="sign"><tr>
    <td></td>
    <td style="width:120px; text-align:center;"><img src="{{ $image('stamp.svg') }}" width="104" height="104" alt=""></td>
    <td style="width:170px;">
      <div style="text-align:center;"><img src="{{ $image('signature.svg') }}" width="165" height="60" alt=""></div>
      <div class="sign-line">{{ __('statement.signature') }}</div>
    </td>
  </tr></table>
</body>
</html>
