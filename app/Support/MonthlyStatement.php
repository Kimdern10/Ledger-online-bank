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
 * @phpstan-import-type HistoryItem from TransactionFeed
 */
final class MonthlyStatement
{
    /**
     * @param  array<int, HistoryItem>  $items
     */
    private function __construct(
        public readonly User $user,
        public readonly CarbonImmutable $from,
        public readonly CarbonImmutable $to,
        public readonly array $items,
        public readonly float $moneyIn,
        public readonly float $moneyOut,
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

        $items = collect(TransactionFeed::for($user))
            ->where('affects_balance', true)
            ->filter(fn (array $item) => $item['created_at']->between($from, $to))
            ->sortBy('created_at')
            ->values();

        return new self(
            $user,
            $from,
            $to,
            $items->all(),
            (float) $items->where('amount_raw', '>', 0)->sum('amount_raw'),
            abs((float) $items->where('amount_raw', '<', 0)->sum('amount_raw')),
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
