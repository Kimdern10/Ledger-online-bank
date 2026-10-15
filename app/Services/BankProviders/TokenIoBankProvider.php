<?php

namespace App\Services\BankProviders;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * International bank source #2 of 3 — Token.io's Banks v2 endpoint
 * (EU open-banking coverage).
 *
 * Sign up for a free Sandbox API key at https://console.token.io/ — set
 * TOKENIO_API_KEY (and TOKENIO_MEMBER_ID, the TPP member id Token.io
 * assigns your app on signup) in .env (see config/services.php).
 * Docs: https://docs.token.io/products/tpp/api/reference/banks-v2/getbanksv2
 *
 * Token.io's PRODUCTION API requires asymmetric-key request signing (via
 * their SDK) for every call — that's real cryptographic work this class
 * deliberately does not reimplement. Sandbox, however, accepts simple
 * "Basic" API-key auth (see Token.io's Authentication docs), which is
 * what this class uses. If you later move this app off Sandbox, this
 * provider needs Token.io's official SDK in place of this plain HTTP call.
 *
 * Token.io returns a BIC for each bank but not always a country, and
 * never a currency. A BIC carries its country in characters 5-6
 * (HANDSESS -> SE), so the country is read from there when the API
 * doesn't send one, and the currency is derived from the country — that's
 * what lets the Send Money picker fill in Country, SWIFT and Currency
 * when a bank is chosen.
 */
class TokenIoBankProvider implements BankProviderInterface
{
    /**
     * Country (ISO 3166-1 alpha-2) => currency, for the European countries
     * Token.io covers.
     */
    private const COUNTRY_CURRENCY = [
        // Eurozone
        'AT' => 'EUR', 'BE' => 'EUR', 'CY' => 'EUR', 'DE' => 'EUR', 'EE' => 'EUR',
        'ES' => 'EUR', 'FI' => 'EUR', 'FR' => 'EUR', 'GR' => 'EUR', 'HR' => 'EUR',
        'IE' => 'EUR', 'IT' => 'EUR', 'LT' => 'EUR', 'LU' => 'EUR', 'LV' => 'EUR',
        'MT' => 'EUR', 'NL' => 'EUR', 'PT' => 'EUR', 'SI' => 'EUR', 'SK' => 'EUR',
        // Rest of Europe
        'GB' => 'GBP', 'SE' => 'SEK', 'NO' => 'NOK', 'DK' => 'DKK', 'IS' => 'ISK',
        'PL' => 'PLN', 'CZ' => 'CZK', 'HU' => 'HUF', 'RO' => 'RON', 'BG' => 'BGN',
        'CH' => 'CHF',
    ];

    public function isConfigured(): bool
    {
        return filled(config('services.token_io.api_key'))
            && filled(config('services.token_io.member_id'))
            && config('services.token_io.env', 'sandbox') === 'sandbox';
    }

    public function search(string $query): array
    {
        if (! $this->isConfigured()) {
            return [];
        }

        try {
            $response = Http::timeout(5)
                ->retry(1, 200)
                ->withHeaders([
                    'Authorization' => 'Basic '.base64_encode(config('services.token_io.api_key').':'),
                ])
                ->get('https://api.sandbox.token.io/v2/banks', array_filter([
                    'memberId' => config('services.token_io.member_id'),
                    'search' => $query !== '' ? $query : null,
                ]));

            if (! $response->successful()) {
                Log::warning('Token.io banks-v2 request failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return [];
            }

            $banks = $response->json('banks', []);

            if (! is_array($banks)) {
                return [];
            }

            return collect($banks)
                ->map(function (array $b): array {
                    $bic = $this->bicFrom($b);
                    // Country from the API if it sends one, otherwise from
                    // characters 5-6 of the BIC.
                    $country = $this->countryFrom($b) ?? $this->countryFromBic($bic);

                    return [
                        'name' => (string) ($b['name'] ?? ''),
                        'country' => $country,
                        'swift_code' => $bic,
                        'currency' => $country ? (self::COUNTRY_CURRENCY[$country] ?? null) : null,
                        'routing_number' => null,
                        'meta' => trim(collect([$country, $bic])->filter()->implode(' · ')),
                        'source' => 'token_io',
                    ];
                })
                ->filter(fn (array $bank) => $bank['name'] !== '')
                ->values()
                ->all();
        } catch (\Throwable $e) {
            Log::warning('Token.io banks-v2 request threw', ['message' => $e->getMessage()]);

            return [];
        }
    }

    /**
     * Token.io lists a bank's countries as an array — use the first one,
     * and fall back to a single "country" field if that's what comes back.
     * Always returns a 2-letter uppercase code, or null.
     *
     * @param  array<string, mixed>  $bank
     */
    private function countryFrom(array $bank): ?string
    {
        $candidate = is_array($bank['countries'] ?? null)
            ? ($bank['countries'][0] ?? null)
            : ($bank['country'] ?? null);

        $candidate = strtoupper(trim((string) $candidate));

        return strlen($candidate) === 2 ? $candidate : null;
    }

    /**
     * A BIC's characters 5-6 are the bank's country (HANDSESS -> SE).
     */
    private function countryFromBic(?string $bic): ?string
    {
        if ($bic === null || strlen($bic) < 6) {
            return null;
        }

        $code = strtoupper(substr($bic, 4, 2));

        return ctype_alpha($code) ? $code : null;
    }

    /**
     * @param  array<string, mixed>  $bank
     */
    private function bicFrom(array $bank): ?string
    {
        $bic = strtoupper(trim((string) ($bank['bic'] ?? '')));

        return $bic !== '' ? $bic : null;
    }
}
