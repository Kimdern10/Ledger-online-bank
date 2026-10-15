<?php

namespace App\Services\BankProviders;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * International bank source #4 — Salt Edge's Providers API. Added for
 * Asia-Pacific coverage (TrueLayer/Token.io/Open Payments are Europe-only);
 * Salt Edge is global, so by default this is scoped to a configurable list
 * of Asia-Pacific country codes rather than duplicating Europe.
 *
 * Sign up for a free App-id/Secret at https://www.saltedge.com/clients/sign_up
 * — set SALTEDGE_APP_ID / SALTEDGE_SECRET in .env (see config/services.php).
 * Docs: https://docs.saltedge.com/v6/#providers (v5 is deprecated and now
 * returns 410 Gone — confirmed live; this uses the current v6 base URL).
 *
 * Salt Edge authenticates with plain App-id/Secret headers (no OAuth token
 * exchange). New Salt Edge clients only see Salt Edge's own "fake" sandbox
 * providers until Salt Edge approves the account for real bank data (their
 * sales/onboarding process) — same situation as Open Payments in this app:
 * this class works either way, it just shows whatever the configured
 * App-id/Secret can currently see.
 *
 * Unlike Plaid/TrueLayer/Token.io, Salt Edge's /providers endpoint doesn't
 * document a "search by name" query parameter, so this fetches the full
 * provider catalog (paginated, capped at 10 pages) once, caches it for an
 * hour, and filters by name + country client-side — the catalog changes
 * rarely, so this avoids re-hitting Salt Edge on every keystroke.
 *
 * Salt Edge returns a country code for each provider but never a currency
 * or SWIFT code, so the currency is derived from the country here — that's
 * what lets the Send Money picker fill in Country and Currency when a bank
 * is chosen.
 */
/**
 * @phpstan-import-type BankRow from BankRows
 */
class SaltEdgeBankProvider implements BankProviderInterface
{
    // v3: the v2 catalog cached before the per-country fetch below was
    // almost entirely German banks (see fetchCatalog()).
    private const CATALOG_CACHE_KEY = 'saltedge:providers:catalog:v4';

    /**
     * Providers per page — Salt Edge's per_page parameter. At 1000 the whole
     * catalogue (~2,250 active providers, confirmed live) is 3 requests.
     */
    private const PER_PAGE = 1000;

    /**
     * Salt Edge's pseudo-countries: XF is its fake sandbox banks, XO its
     * country-less entries (wallets like PayPal) — neither is a bank a
     * customer can send to.
     */
    private const NOT_A_COUNTRY = ['XF', 'XO'];

    private const CATALOG_CACHE_MINUTES = 60;

    private const MAX_PAGES = 10;

    /**
     * Country (ISO 3166-1 alpha-2) => currency. Covers the Asia-Pacific
     * default list plus common neighbours, in case SALTEDGE_COUNTRY_CODES
     * is widened later.
     */
    private const COUNTRY_CURRENCY = [
        'JP' => 'JPY', 'SG' => 'SGD', 'HK' => 'HKD', 'CN' => 'CNY', 'IN' => 'INR',
        'PH' => 'PHP', 'VN' => 'VND', 'MY' => 'MYR', 'TH' => 'THB', 'KR' => 'KRW',
        'ID' => 'IDR', 'TW' => 'TWD', 'AU' => 'AUD', 'NZ' => 'NZD', 'PK' => 'PKR',
        'BD' => 'BDT', 'LK' => 'LKR', 'AE' => 'AED', 'SA' => 'SAR', 'IL' => 'ILS',
        'EG' => 'EGP', 'ZA' => 'ZAR', 'NG' => 'NGN', 'KE' => 'KES', 'GH' => 'GHS',
        'US' => 'USD', 'CA' => 'CAD', 'MX' => 'MXN', 'BR' => 'BRL', 'GB' => 'GBP',
        // Countries the all-countries catalogue added (see fetchCatalog()).
        'UA' => 'UAH', 'TR' => 'TRY', 'CH' => 'CHF', 'RO' => 'RON', 'HU' => 'HUF',
        'AR' => 'ARS', 'EC' => 'USD', 'DO' => 'DOP', 'DE' => 'EUR', 'ES' => 'EUR',
        'AT' => 'EUR', 'IE' => 'EUR', 'IT' => 'EUR', 'NL' => 'EUR',
    ];

    public function isConfigured(): bool
    {
        return filled(config('services.salt_edge.app_id')) && filled(config('services.salt_edge.secret'));
    }

    public function search(string $query): array
    {
        if (! $this->isConfigured()) {
            return [];
        }

        $countryCodes = array_values(array_filter(array_map(
            'trim',
            explode(',', (string) config('services.salt_edge.country_codes', ''))
        )));

        $needle = mb_strtolower($query);

        return collect($this->catalog())
            ->filter(function (array $p) use ($needle, $countryCodes) {
                if ($countryCodes && ! in_array($p['country'], $countryCodes, true)) {
                    return false;
                }

                return $needle === '' || str_contains(mb_strtolower($p['name']), $needle);
            })
            ->values()
            ->all();
    }

    /**
     * Fetch (and cache) the full provider catalog, already normalized.
     * Returns [] on any failure — never throws.
     *
     * @return list<BankRow>
     */
    private function catalog(): array
    {
        // Deliberately not Cache::remember() — that would also cache an
        // empty result from a failed/misconfigured request for the full
        // hour, hiding a real fix behind a stale cached failure. Only a
        // genuinely non-empty catalog gets cached.
        $cached = Cache::get(self::CATALOG_CACHE_KEY);

        if ($cached !== null) {
            return BankRows::normalize($cached);
        }

        $catalog = $this->fetchCatalog();

        if ($catalog !== []) {
            Cache::put(self::CATALOG_CACHE_KEY, $catalog, now()->addMinutes(self::CATALOG_CACHE_MINUTES));
        }

        return $catalog;
    }

    /**
     * Fetch the provider catalog from Salt Edge, already normalized.
     * Returns [] on any failure — never throws.
     *
     * With SALTEDGE_COUNTRY_CODES empty, everything Salt Edge offers is
     * fetched (every country, PER_PAGE at a time). With it set, each country is fetched on its own
     * through the documented `country_code` filter. Fetching the whole
     * catalog and filtering afterwards never worked: Salt Edge pages its
     * list by provider id, so the first MAX_PAGES pages were ~99% German
     * banks (confirmed live: DE 986 of 1000) and the Asia-Pacific countries
     * this provider is configured for came back with nothing at all.
     *
     * @return list<BankRow>
     */
    private function fetchCatalog(): array
    {
        $countryCodes = array_values(array_filter(array_map(
            fn (string $code) => strtoupper(trim($code)),
            explode(',', (string) config('services.salt_edge.country_codes', ''))
        )));

        $all = [];

        foreach ($countryCodes ?: [null] as $countryCode) {
            array_push($all, ...$this->fetchProviders($countryCode));
        }

        // Also drops rows without a name.
        return BankRows::normalize($all);
    }

    /**
     * Up to MAX_PAGES pages of one country's providers (or of everything,
     * when $countryCode is null). A failed page keeps what came before it.
     *
     * @return array<int, array<string, mixed>>
     */
    private function fetchProviders(?string $countryCode): array
    {
        $providers = [];

        try {
            $fromId = null;

            for ($page = 0; $page < self::MAX_PAGES; $page++) {
                $response = Http::timeout(10)
                    ->retry(1, 200)
                    ->withHeaders([
                        'App-id' => config('services.salt_edge.app_id'),
                        'Secret' => config('services.salt_edge.secret'),
                        'Accept' => 'application/json',
                    ])
                    ->get('https://www.saltedge.com/api/v6/providers', array_filter([
                        'from_id' => $fromId,
                        'country_code' => $countryCode,
                        'exclude_inactive' => 'true',
                        'per_page' => self::PER_PAGE,
                    ]));

                if (! $response->successful()) {
                    Log::warning('Salt Edge providers request failed', [
                        'country' => $countryCode,
                        'status' => $response->status(),
                        'body' => $response->body(),
                    ]);

                    break;
                }

                foreach ($response->json('data', []) as $p) {
                    $country = strtoupper(trim((string) ($p['country_code'] ?? '')));
                    $country = strlen($country) === 2 ? $country : null;

                    if (in_array($country, self::NOT_A_COUNTRY, true)) {
                        continue;
                    }

                    $bic = ($p['bic_codes'] ?? [])[0] ?? null;

                    $providers[] = [
                        'name' => (string) ($p['name'] ?? ''),
                        'country' => $country,
                        'swift_code' => $bic,
                        'currency' => $country ? (self::COUNTRY_CURRENCY[$country] ?? null) : null,
                        'routing_number' => null,
                        // The small gray line under the bank's name in the picker.
                        'meta' => trim(collect([$country, $bic])->filter()->implode(' · ')),
                        'source' => 'salt_edge',
                    ];
                }

                $fromId = $response->json('meta.next_id');

                if (! $fromId) {
                    break;
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Salt Edge providers request threw', ['country' => $countryCode, 'message' => $e->getMessage()]);
        }

        return $providers;
    }
}
