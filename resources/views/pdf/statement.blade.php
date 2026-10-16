{{-- The statement PDF attached to App\Mail\StatementMail — same design as
     the Ledger statement (brand bar, details, summary, transactions).
     Rendered by dompdf, which only understands basic CSS, so the layout is
     built from tables rather than flexbox. $statement is an
     App\Support\MonthlyStatement. --}}
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>{{ __('statement.statement_for', ['period' => $statement->periodLabel()]) }}</title>
<style>
  @page { margin: 34px 38px 54px; }
  * { box-sizing: border-box; }
  body { font-family: 'DejaVu Sans', sans-serif; font-size: 10.5px; color: #10202F; margin: 0; }
  .serif { font-family: 'DejaVu Serif', serif; }
  table { border-collapse: collapse; width: 100%; }
  .brand { background: #10202F; color: #F6F4EE; }
  .brand td { padding: 16px 20px; vertical-align: middle; }
  .mark { width: 30px; height: 30px; background: #C9A24B; color: #10202F; text-align: center; vertical-align: middle; font-weight: bold; font-size: 16px; padding: 0; }
  .gold { height: 4px; background: #C9A24B; }
  .eyebrow { font-size: 9px; font-weight: bold; letter-spacing: 1px; text-transform: uppercase; color: #C9A24B; margin: 22px 0 4px; }
  h1 { font-size: 22px; margin: 0 0 16px; font-weight: bold; }
  .details td { padding: 6px 0; border-bottom: 1px solid #EDEAE0; }
  .details .k { color: #8C9298; width: 32%; }
  .details .v { font-weight: bold; text-align: right; }
  .section { font-size: 9px; font-weight: bold; letter-spacing: 1px; text-transform: uppercase; color: #8C9298; margin: 22px 0 8px; }
  .summary { background: #F6F4EE; }
  .summary td { padding: 9px 14px; }
  .summary .num { text-align: right; font-weight: bold; font-size: 12px; }
  .summary .net td { border-top: 1px solid #E3E0D7; font-weight: bold; }
  .in { color: #2F6F62; }
  .out { color: #C1503C; }
  .tx th { font-size: 8.5px; font-weight: bold; letter-spacing: .5px; text-transform: uppercase; color: #8C9298; text-align: left; padding: 7px 6px; border-bottom: 1.5px solid #10202F; }
  .tx td { padding: 8px 6px; border-bottom: 1px solid #EDEAE0; vertical-align: top; }
  .tx .num { text-align: right; white-space: nowrap; }
  .tx tr.alt td { background: #FAF9F5; }
  .tx .sub { color: #8C9298; font-size: 9px; }
  .tx tfoot td { font-weight: bold; border-top: 1.5px solid #10202F; border-bottom: none; }
  .empty { text-align: center; color: #8C9298; padding: 24px 6px; }
  .note { color: #8C9298; font-size: 9px; line-height: 1.6; margin-top: 22px; }
  .footer { position: fixed; bottom: -36px; left: 0; right: 0; font-size: 8.5px; color: #8C9298; }
</style>
</head>
<body>
  <?php
      $user = $statement->user;
      $accountNumber = $user->checking_account_number ?: $user->savings_account_number;
      $net = $statement->moneyIn - $statement->moneyOut;
      $money = fn (float $amount): string => '$'.number_format(abs($amount), 2);
  ?>

  <div class="footer">
    <table><tr>
      <td>Ledger &middot; Banking, kept in order &middot; {{ __('statement.statement_for', ['period' => $statement->periodLabel()]) }}</td>
      <td style="text-align:right;">{{ __('statement.issued') }} {{ now()->translatedFormat('j M Y, g:i A') }}</td>
    </tr></table>
  </div>

  <table class="brand"><tr>
    <td style="width:42px;"><table style="width:30px;"><tr><td class="mark serif">L</td></tr></table></td>
    <td class="serif" style="font-size:18px; font-weight:bold;">Ledger</td>
    <td style="text-align:right; font-size:10px; color:#C9A24B;">{{ __('statement.eyebrow') }}</td>
  </tr></table>
  <div class="gold"></div>

  <p class="eyebrow">{{ __('statement.eyebrow') }}</p>
  <h1 class="serif">{{ $statement->periodLabel() }}</h1>

  <table class="details">
    <tr><td class="k">{{ __('statement.account_holder') }}</td><td class="v">{{ $user->name }}</td></tr>
    <tr><td class="k">Email</td><td class="v">{{ $user->email }}</td></tr>
    @if($accountNumber)
      <tr><td class="k">{{ __('statement.account') }}</td><td class="v">&bull;&bull;&bull;&bull; {{ substr($accountNumber, -4) }}</td></tr>
    @endif
    <tr><td class="k">{{ __('statement.period') }}</td><td class="v">{{ $statement->from->translatedFormat('j M Y') }} &ndash; {{ $statement->to->translatedFormat('j M Y') }}</td></tr>
    <tr><td class="k">{{ __('statement.issued') }}</td><td class="v">{{ now()->translatedFormat('j M Y') }}</td></tr>
  </table>

  <p class="section">{{ __('statement.summary') }}</p>
  <table class="summary">
    <tr><td>{{ __('statement.money_in') }}</td><td class="num in">+{{ $money($statement->moneyIn) }}</td></tr>
    <tr><td>{{ __('statement.money_out') }}</td><td class="num out">-{{ $money($statement->moneyOut) }}</td></tr>
    <tr class="net"><td>{{ __('statement.net_change') }}</td><td class="num">{{ $net < 0 ? '-' : '+' }}{{ $money($net) }}</td></tr>
  </table>

  <p class="section">{{ __('statement.transactions') }} &middot; {{ trans_choice('statement.transaction_count', count($statement->items)) }}</p>
  <table class="tx">
    <thead>
      <tr>
        <th style="width:18%;">{{ __('statement.date') }}</th>
        <th>{{ __('statement.description') }}</th>
        <th class="num" style="width:17%; text-align:right;">{{ __('statement.money_in') }}</th>
        <th class="num" style="width:17%; text-align:right;">{{ __('statement.money_out') }}</th>
      </tr>
    </thead>
    <tbody>
      @forelse($statement->items as $index => $item)
        <tr class="{{ $index % 2 ? 'alt' : '' }}">
          <td>{{ $item['created_at']->translatedFormat('j M Y') }}<br><span class="sub">{{ $item['created_at']->format('g:i A') }}</span></td>
          <td>{{ $item['name'] }}<br><span class="sub">{{ $item['label'] }}</span></td>
          <td class="num in">{{ $item['amount_raw'] > 0 ? $money($item['amount_raw']) : '' }}</td>
          <td class="num out">{{ $item['amount_raw'] < 0 ? $money($item['amount_raw']) : '' }}</td>
        </tr>
      @empty
        <tr><td colspan="4" class="empty">{{ __('statement.empty') }}</td></tr>
      @endforelse
    </tbody>
    @if(count($statement->items) > 0)
      <tfoot>
        <tr>
          <td colspan="2">{{ __('statement.total') }}</td>
          <td class="num in">{{ $money($statement->moneyIn) }}</td>
          <td class="num out">{{ $money($statement->moneyOut) }}</td>
        </tr>
      </tfoot>
    @endif
  </table>

  <p class="note">{{ __('statement.footer') }}</p>
</body>
</html>
