<?php

namespace App\Support;

use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\CarbonImmutable;

/**
 * One customer's statement for a period — a calendar month (the automatic
 * email on the 1st) or any From/To dates the customer picks on History.
 * Lists every transaction that moved their balance in that period (oldest
 * first) with the period's money in and out. Statements are only ever
 * emailed, as a PDF attachment (App\Mail\StatementMail) — there's no page in
 * the app that shows them.
 *
 * Balances are worked backwards from the customer's balance today: the
 * closing balance is today's balance minus everything that moved it after
 * the period, and each row's balance runs forward from the opening one.
 *
 * @phpstan-import-type HistoryItem from TransactionFeed
 *
 * @phpstan-type StatementRow array{type: string, name: mixed, label: mixed, amount_raw: float, amount_display: string, created_at: mixed, url: string|null, affects_balance: bool, description: string, balance: float}
 */
final class MonthlyStatement
{
    /**
     * @param  array<int, StatementRow>  $items
     */
    private function __construct(
        public readonly User $user,
        public readonly CarbonImmutable $from,
        public readonly CarbonImmutable $to,
        public readonly array $items,
        public readonly float $moneyIn,
        public readonly float $moneyOut,
        public readonly float $openingBalance,
        public readonly float $closingBalance,
    ) {}

    /**
     * A whole calendar month.
     */
    public static function for(User $user, CarbonImmutable $month): self
    {
        return self::between($user, $month->startOfMonth(), $month->endOfMonth());
    }

    /**
     * From the start of $from's day to the end of $to's day.
     */
    public static function between(User $user, CarbonImmutable $from, CarbonImmutable $to): self
    {
        $from = $from->startOfDay();
        $to = $to->endOfDay();

        $moving = collect(TransactionFeed::for($user))->where('affects_balance', true);

        $items = $moving
            ->filter(fn (array $item) => $item['created_at']->between($from, $to))
            ->sortBy('created_at')
            ->values();

        $movedSince = (float) $moving->filter(fn (array $item) => $item['created_at']->greaterThan($to))->sum('amount_raw');
        $closingBalance = round((float) $user->balance - $movedSince, 2);
        $openingBalance = round($closingBalance - (float) $items->sum('amount_raw'), 2);

        $running = $openingBalance;
        $rows = $items->map(function (array $item) use (&$running): array {
            $running = round($running + $item['amount_raw'], 2);

            return $item + ['balance' => $running];
        });

        return new self(
            $user,
            $from,
            $to,
            $rows->all(),
            (float) $items->where('amount_raw', '>', 0)->sum('amount_raw'),
            abs((float) $items->where('amount_raw', '<', 0)->sum('amount_raw')),
            $openingBalance,
            $closingBalance,
        );
    }

    /**
     * Whether the period is exactly one calendar month.
     */
    public function isWholeMonth(): bool
    {
        return $this->from->equalTo($this->from->startOfMonth())
            && $this->to->isSameDay($this->from->endOfMonth());
    }

    /**
     * "October 2026" for a whole month, otherwise "3 Oct 2026 – 17 Oct 2026".
     */
    public function periodLabel(): string
    {
        if ($this->isWholeMonth()) {
            return $this->from->translatedFormat('F Y');
        }

        return $this->from->translatedFormat('j M Y').' – '.$this->to->translatedFormat('j M Y');
    }

    /**
     * The statement as a PDF (resources/views/pdf/statement.blade.php) —
     * what the statement email attaches.
     */
    public function pdf(): string
    {
        return Pdf::loadView('pdf.statement', ['statement' => $this])
            ->setPaper('a4')
            // Embed only the letters actually used — keeps the file small
            // (without it, the fonts alone made it over 1 MB).
            ->setOption('isFontSubsettingEnabled', true)
            ->output();
    }

    public function filename(): string
    {
        return 'Ledger-Statement-'.($this->isWholeMonth()
            ? $this->from->format('Y-m')
            : $this->from->format('Y-m-d').'-to-'.$this->to->format('Y-m-d')).'.pdf';
    }
}
