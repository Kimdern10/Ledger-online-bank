<?php

namespace App\Support;

/**
 * Converts a USD amount (every Ledger balance is USD — see the `balance`
 * column on users) into the currency an international transfer is being
 * sent in, so "Another bank" abroad and the sender both see roughly what
 * actually arrives, not just a currency code sitting next to a USD figure.
 *
 * RATES is a fixed reference table, not a live market feed — this app
 * deliberately keeps its data admin-managed / self-contained rather than
 * depending on a live third-party API (see BankDirectoryController's own
 * history: it used to call a live FDIC lookup and was switched to the
 * admin-managed Bank directory instead). A real bank integrates a licensed
 * FX data provider; here, one static table is enough to make the
 * conversion real and consistent between the live preview on the form and
 * what's actually locked in and shown on the receipt. Update the figures
 * below to keep them roughly current.
 *
 * Rates are USD -> 1 unit of that currency, e.g. GBP => 0.79 means $1.00
 * converts to £0.79.
 */
class CurrencyRates
{
    public const RATES = [
        'USD' => 1.0,
        'EUR' => 0.92,
        'GBP' => 0.79,
        'CAD' => 1.38,
        'AUD' => 1.53,
        'NZD' => 1.66,
        'CHF' => 0.88,
        'JPY' => 152.0,
        'CNY' => 7.24,
        'HKD' => 7.81,
        'SGD' => 1.34,
        'INR' => 84.1,
        'PKR' => 278.0,
        'BDT' => 119.5,
        'NGN' => 1540.0,
        'GHS' => 15.3,
        'KES' => 129.0,
        'ZAR' => 18.2,
        'EGP' => 48.5,
        'MAD' => 9.9,
        'AED' => 3.67,
        'SAR' => 3.75,
        'QAR' => 3.64,
        'ILS' => 3.72,
        'TRY' => 34.1,
        'RUB' => 92.0,
        'BRL' => 5.6,
        'MXN' => 17.1,
        'ARS' => 990.0,
        'COP' => 4100.0,
        'CLP' => 950.0,
        'PHP' => 57.8,
        'IDR' => 15900.0,
        'MYR' => 4.5,
        'THB' => 34.8,
        'VND' => 25400.0,
        'KRW' => 1370.0,
        'PLN' => 3.98,
        'SEK' => 10.4,
        'NOK' => 10.6,
        'DKK' => 6.86,
        'CZK' => 22.9,
        'HUF' => 358.0,
    ];

    /**
     * The USD -> $currency rate, or null for a currency this table doesn't
     * carry — the caller decides what "unknown currency" means (skip the
     * conversion display, fall back to a 1:1 assumption, etc.) rather than
     * this silently guessing.
     */
    public static function rate(string $currency): ?float
    {
        return self::RATES[strtoupper($currency)] ?? null;
    }

    /**
     * $usdAmount converted into $currency, rounded to 2 decimal places —
     * null (not 0) when the currency isn't one this table knows, so a
     * missing rate is never mistaken for "converts to zero".
     */
    public static function convert(float $usdAmount, string $currency): ?float
    {
        $rate = self::rate($currency);

        return $rate === null ? null : round($usdAmount * $rate, 2);
    }
}
