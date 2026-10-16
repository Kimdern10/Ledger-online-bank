<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Services\BankProviders\BankProviderInterface;
use App\Services\BankProviders\OpenPaymentsBankProvider;
use App\Services\BankProviders\PlaidBankProvider;
use App\Services\BankProviders\PlaidInternationalBankProvider;
use App\Services\BankProviders\SaltEdgeBankProvider;
use App\Services\BankProviders\TokenIoBankProvider;
use App\Services\BankProviders\TrueLayerBankProvider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Backs the "Select a bank" pickers on the Send Money page — the "Another
 * bank" tab's domestic picker and the "International bank" tab's picker.
 *
 * Each picker merges TWO kinds of source, always in this order:
 *
 *  1. The admin-managed Bank directory (see App\Models\Bank /
 *     AdminBankController) — hand-curated entries, always included, always
 *     listed first. This is what keeps working even if every live API
 *     below is unconfigured, misconfigured, or down.
 *  2. One or more live third-party bank-directory APIs (see
 *     App\Services\BankProviders) — domestic search uses Plaid; the
 *     international search merges TrueLayer + Token.io + Open Payments
 *     (Europe) with Salt Edge (Asia-Pacific by default), since no single
 *     one of those covers every country. Any provider without credentials
 *     configured (see config/services.php) is silently skipped — it
 *     contributes zero results, never an error.
 *
 * Empty search (the picker was just opened):
 *  - International: live providers ARE used, so the person sees real banks
 *    before typing anything. That first result is cached for 30 minutes
 *    (and the Send page warms it up as soon as the International tab
 *    opens). If nothing at all comes back, a short built-in list of
 *    well-known banks is shown so the picker is never blank.
 *  - Domestic: only the local directory is used (Plaid requires a
 *    non-empty query anyway), with a built-in list of well-known banks as
 *    the fallback.
 *
 * International results that arrive without a currency (live APIs rarely
 * send one) get one filled in from the bank's country — see
 * fillCurrencies() — so picking a bank can fill in the Currency field.
 *
 * Results are de-duplicated by a normalized bank name (so a bank an admin
 * already added by hand doesn't also show up a second time from a live
 * API) and briefly cached per (type, query) to avoid re-hitting live APIs
 * on every debounced keystroke from the picker's search box. Empty
 * results are never cached.
 *
 * Local admin entries always go first, uncapped relative to the live
 * providers. The live providers themselves are merged ROUND-ROBIN, not
 * sequentially — confirmed live that a broad query against a provider
 * with a large catalog (Open Payments' full Nordic bank list, dozens of
 * matches) would otherwise fill the entire MAX_RESULTS cap before a
 * provider listed later in the array (e.g. Salt Edge) ever contributed a
 * single result, even with equally good matches of its own.
 */
class BankDirectoryController extends Controller
{
    /**
     * Keeps a broad query from turning the picker into an unusable wall of
     * matches.
     */
    protected const MAX_RESULTS = 25;

    /**
     * Shown in the "Another bank" picker before anyone searches, but ONLY
     * when nothing else is available. Names only — no routing numbers,
     * since those must come from the admin directory or the person typing
     * them in.
     */
    protected const POPULAR_DOMESTIC_BANKS = [
        'JPMorgan Chase Bank',
        'Bank of America',
        'Wells Fargo Bank',
        'Citibank',
        'U.S. Bank',
        'PNC Bank',
        'Capital One',
        'TD Bank',
        'Truist Bank',
        'Ally Bank',
    ];

    /**
     * Shown in the "International bank" picker before anyone searches, but
     * ONLY when no admin entry or live provider returned anything. Name,
     * country and currency only — SWIFT codes are left for the sender to
     * type, since a wrong code on a real transfer is worse than a blank.
     * Each row: [name, country, currency].
     */
    protected const POPULAR_INTERNATIONAL_BANKS = [
        ['Barclays', 'GB', 'GBP'],
        ['HSBC UK', 'GB', 'GBP'],
        ['Lloyds Bank', 'GB', 'GBP'],
        ['NatWest', 'GB', 'GBP'],
        ['Deutsche Bank', 'DE', 'EUR'],
        ['BNP Paribas', 'FR', 'EUR'],
        ['Santander', 'ES', 'EUR'],
        ['ING', 'NL', 'EUR'],
        ['Handelsbanken', 'SE', 'SEK'],
        ['Danske Bank', 'DK', 'DKK'],
        ['DNB', 'NO', 'NOK'],
        ['Nordea', 'FI', 'EUR'],
        ['DBS Bank', 'SG', 'SGD'],
        ['Mizuho Bank', 'JP', 'JPY'],
        ['HDFC Bank', 'IN', 'INR'],
    ];

    /**
     * ISO 3166-1 alpha-2 country code => that country's currency code.
     */
    protected const COUNTRY_CURRENCY = [
        // Eurozone
        'AT' => 'EUR', 'BE' => 'EUR', 'CY' => 'EUR', 'DE' => 'EUR', 'EE' => 'EUR',
        'ES' => 'EUR', 'FI' => 'EUR', 'FR' => 'EUR', 'GR' => 'EUR', 'HR' => 'EUR',
        'IE' => 'EUR', 'IT' => 'EUR', 'LT' => 'EUR', 'LU' => 'EUR', 'LV' => 'EUR',
        'MT' => 'EUR', 'NL' => 'EUR', 'PT' => 'EUR', 'SI' => 'EUR', 'SK' => 'EUR',
        // Rest of Europe
        'GB' => 'GBP', 'SE' => 'SEK', 'NO' => 'NOK', 'DK' => 'DKK', 'IS' => 'ISK',
        'PL' => 'PLN', 'CZ' => 'CZK', 'HU' => 'HUF', 'RO' => 'RON', 'BG' => 'BGN',
        'CH' => 'CHF', 'TR' => 'TRY', 'UA' => 'UAH', 'RS' => 'RSD', 'MD' => 'MDL',
        'GE' => 'GEL',
        // Asia-Pacific
        'AU' => 'AUD', 'NZ' => 'NZD', 'JP' => 'JPY', 'CN' => 'CNY', 'HK' => 'HKD',
        'SG' => 'SGD', 'KR' => 'KRW', 'IN' => 'INR', 'ID' => 'IDR', 'MY' => 'MYR',
        'TH' => 'THB', 'PH' => 'PHP', 'VN' => 'VND', 'TW' => 'TWD', 'PK' => 'PKR',
        'BD' => 'BDT', 'LK' => 'LKR', 'KZ' => 'KZT',
        // Middle East & Africa
        'AE' => 'AED', 'SA' => 'SAR', 'IL' => 'ILS', 'EG' => 'EGP', 'ZA' => 'ZAR',
        'QA' => 'QAR', 'KW' => 'KWD', 'BH' => 'BHD', 'OM' => 'OMR',
        'NG' => 'NGN', 'KE' => 'KES', 'GH' => 'GHS',
        // Americas
        'US' => 'USD', 'CA' => 'CAD', 'MX' => 'MXN', 'BR' => 'BRL', 'AR' => 'ARS',
        'CL' => 'CLP', 'CO' => 'COP', 'PE' => 'PEN', 'UY' => 'UYU', 'DO' => 'DOP',
        // Ecuador uses the US dollar.
        'EC' => 'USD',
    ];

    /**
     * For live APIs that send a country NAME instead of a 2-letter code.
     * Lowercase name => ISO code (which COUNTRY_CURRENCY then maps).
     */
    protected const COUNTRY_NAME_TO_CODE = [
        'austria' => 'AT', 'belgium' => 'BE', 'cyprus' => 'CY', 'germany' => 'DE',
        'estonia' => 'EE', 'spain' => 'ES', 'finland' => 'FI', 'france' => 'FR',
        'greece' => 'GR', 'croatia' => 'HR', 'ireland' => 'IE', 'italy' => 'IT',
        'lithuania' => 'LT', 'luxembourg' => 'LU', 'latvia' => 'LV', 'malta' => 'MT',
        'netherlands' => 'NL', 'portugal' => 'PT', 'slovenia' => 'SI', 'slovakia' => 'SK',
        'united kingdom' => 'GB', 'uk' => 'GB', 'great britain' => 'GB',
        'sweden' => 'SE', 'norway' => 'NO', 'denmark' => 'DK', 'iceland' => 'IS',
        'poland' => 'PL', 'czech republic' => 'CZ', 'czechia' => 'CZ', 'hungary' => 'HU',
        'romania' => 'RO', 'bulgaria' => 'BG', 'switzerland' => 'CH', 'turkey' => 'TR',
        'australia' => 'AU', 'new zealand' => 'NZ', 'japan' => 'JP', 'china' => 'CN',
        'hong kong' => 'HK', 'singapore' => 'SG', 'south korea' => 'KR', 'korea' => 'KR',
        'india' => 'IN', 'indonesia' => 'ID', 'malaysia' => 'MY', 'thailand' => 'TH',
        'philippines' => 'PH', 'vietnam' => 'VN', 'taiwan' => 'TW',
        'united arab emirates' => 'AE', 'uae' => 'AE', 'saudi arabia' => 'SA',
        'israel' => 'IL', 'egypt' => 'EG', 'south africa' => 'ZA', 'nigeria' => 'NG',
        'kenya' => 'KE', 'ghana' => 'GH',
        'united states' => 'US', 'usa' => 'US', 'canada' => 'CA', 'mexico' => 'MX',
        'brazil' => 'BR',
    ];

    /**
     * "Another bank" tab — domestic (type=external) entries only.
     */
    public function search(Request $request): JsonResponse
    {
        $query = trim((string) $request->query('q', ''));

        // "v4" in the key on purpose: it skips any older cached results
        // from before these behaviors changed.
        $results = $this->rememberNonEmpty(
            'banks:v4:external:'.mb_strtolower($query),
            5,
            function () use ($query) {
                $results = $this->merge(
                    $this->localResults($query, 'external'),
                    $query,
                    [app(PlaidBankProvider::class)]
                );

                if ($query === '' && $results === []) {
                    $results = $this->popularDomesticBanks();
                }

                return $results;
            }
        );

        return response()->json(['banks' => $results]);
    }

    /**
     * "International bank" tab — international entries only.
     */
    public function searchInternational(Request $request): JsonResponse
    {
        $query = trim((string) $request->query('q', ''));

        // The opening list (empty query) is the slow one — it asks every
        // live provider — so it's kept longer than a typed search.
        $minutes = $query === '' ? 30 : 5;

        $results = $this->rememberNonEmpty(
            'banks:v4:international:'.mb_strtolower($query),
            $minutes,
            function () use ($query) {
                $results = $this->fillCurrencies($this->merge(
                    $this->localResults($query, 'international'),
                    $query,
                    [
                        // First, so UK/EU/Canadian banks lead the round-robin
                        // ahead of Open Payments' mostly-Nordic list.
                        app(PlaidInternationalBankProvider::class),
                        app(TrueLayerBankProvider::class),
                        app(TokenIoBankProvider::class),
                        app(OpenPaymentsBankProvider::class),
                        app(SaltEdgeBankProvider::class),
                    ],
                    true // live providers are used even for an empty query
                ));

                if ($query === '' && $results === []) {
                    $results = $this->popularInternationalBanks();
                }

                return $results;
            }
        );

        return response()->json(['banks' => $results]);
    }

    /**
     * Like Cache::remember(), but an empty result is never stored — so a
     * failed or misconfigured request can't leave the picker blank for the
     * whole cache window.
     *
     * @return array<int|string, mixed>
     */
    private function rememberNonEmpty(string $key, int $minutes, \Closure $callback): array
    {
        $cached = Cache::get($key);

        if (is_array($cached) && $cached !== []) {
            return $cached;
        }

        $value = $callback();

        if ($value !== []) {
            Cache::put($key, $value, now()->addMinutes($minutes));
        }

        return $value;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function localResults(string $query, string $type): array
    {
        $banks = Bank::query()->active()->ofType($type);

        if ($query !== '') {
            $banks->where('name', 'like', '%'.$query.'%');
        }

        return $banks->orderBy('name')
            ->limit(self::MAX_RESULTS)
            ->get()
            ->map(fn (Bank $bank) => [
                'id' => $bank->id,
                'name' => $bank->name,
                'country' => $bank->country,
                'swift_code' => $bank->swift_code,
                'currency' => $bank->currency,
                'routing_number' => $bank->routing_number,
                // Pre-formatted here rather than in JS, since the two tabs'
                // Bank rows carry different identifying fields (routing
                // number vs. SWIFT/country) and the picker just needs one
                // line of secondary text to show either way.
                'meta' => $type === 'international'
                    ? trim(collect([$bank->country, $bank->swift_code])->filter()->implode(' · '))
                    : ($bank->routing_number ? 'Routing '.$bank->routing_number : ''),
            ])
            ->all();
    }

    /**
     * Built-in fallback for the domestic picker's empty-search state — see
     * the class doc comment. Same shape as localResults() so the picker
     * renders it exactly like a real directory entry.
     *
     * @return array<int, array<string, mixed>>
     */
    private function popularDomesticBanks(): array
    {
        return array_map(fn (string $name) => [
            'id' => null,
            'name' => $name,
            'country' => null,
            'swift_code' => null,
            'currency' => null,
            'routing_number' => null,
            'meta' => '',
        ], self::POPULAR_DOMESTIC_BANKS);
    }

    /**
     * Built-in fallback for the international picker's empty-search state.
     *
     * @return array<int, array<string, mixed>>
     */
    private function popularInternationalBanks(): array
    {
        return array_map(fn (array $row) => [
            'id' => null,
            'name' => $row[0],
            'country' => $row[1],
            'swift_code' => null,
            'currency' => $row[2],
            'routing_number' => null,
            'meta' => $row[1],
        ], self::POPULAR_INTERNATIONAL_BANKS);
    }

    /**
     * Fills in a missing currency from the bank's country, so picking a
     * live-API bank can fill in the Currency field. A currency a bank
     * already has (admin-entered, or sent by the API itself) is never
     * overwritten. If the country is missing or unrecognized, the currency
     * stays empty and the Currency field is simply left as the user had it.
     *
     * @param  array<int, array<string, mixed>>  $banks
     * @return array<int, array<string, mixed>>
     */
    private function fillCurrencies(array $banks): array
    {
        return array_map(function (array $bank) {
            if (! empty($bank['currency'])) {
                return $bank;
            }

            $currency = $this->currencyForCountry($bank['country'] ?? null);

            // No usable country? A SWIFT/BIC code carries it in characters 5-6.
            if (! $currency) {
                $swift = strtoupper(trim((string) ($bank['swift_code'] ?? '')));

                if (strlen($swift) >= 6) {
                    $currency = $this->currencyForCountry(substr($swift, 4, 2));
                }
            }

            if ($currency) {
                $bank['currency'] = $currency;
            }

            return $bank;
        }, $banks);
    }

    private function currencyForCountry(?string $country): ?string
    {
        $country = trim((string) $country);

        if ($country === '') {
            return null;
        }

        $code = strlen($country) === 2
            ? strtoupper($country)
            : (self::COUNTRY_NAME_TO_CODE[mb_strtolower($country)] ?? null);

        return $code ? (self::COUNTRY_CURRENCY[$code] ?? null) : null;
    }

    /**
     * Every configured provider's results for $query, one provider after
     * another. One provider failing just contributes nothing; it never
     * breaks the search.
     *
     * Asking them side by side in separate PHP processes (Laravel's
     * Concurrency) was tried and measured slower here — 5-7 s instead of
     * 2.5-3 s — because starting each process costs more than it saves.
     * Real side-by-side requests would need each provider rebuilt around
     * Http::pool().
     *
     * @param  BankProviderInterface[]  $providers
     * @return list<list<array<string, mixed>>>
     */
    private function searchProviders(array $providers, string $query): array
    {
        $results = [];

        foreach ($providers as $provider) {
            if (! $provider->isConfigured()) {
                continue;
            }

            try {
                $results[] = array_values($provider->search($query));
            } catch (\Throwable $e) {
                Log::warning('Bank provider search failed ('.get_class($provider).'): '.$e->getMessage());
            }
        }

        return $results;
    }

    /**
     * @param  array<int, array<string, mixed>>  $local  Already-formatted local Bank rows (see localResults()).
     * @param  BankProviderInterface[]  $providers
     * @param  bool  $liveWhenEmpty  Ask the live providers even when the query is empty.
     * @return array<int, array<string, mixed>>
     */
    private function merge(array $local, string $query, array $providers, bool $liveWhenEmpty = false): array
    {
        $seen = [];
        $deduped = [];

        $add = function (array $bank) use (&$deduped, &$seen): bool {
            $key = mb_strtolower(trim($bank['name'] ?? '').'|'.($bank['swift_code'] ?? ''));

            if (trim($bank['name'] ?? '') === '' || isset($seen[$key])) {
                return true; // skip this one, keep going
            }

            if (count($deduped) >= self::MAX_RESULTS) {
                return false; // cap reached — caller stops
            }

            $seen[$key] = true;
            unset($bank['source']); // internal only — never sent to the browser
            $deduped[] = $bank;

            return true;
        };

        // Local admin entries always win the cap first (see class doc comment).
        foreach ($local as $bank) {
            if (! $add($bank)) {
                return $deduped;
            }
        }

        // Empty search = picker just opened. Unless the caller wants live
        // banks on open too, the local list is enough to show right away.
        if ($query === '' && ! $liveWhenEmpty) {
            return $deduped;
        }

        // Fetch every configured provider's results up front, THEN
        // interleave them round-robin (one result from each provider per
        // round) rather than draining one provider's whole result list
        // before moving to the next — see class doc comment for why.
        $providerResults = $this->searchProviders($providers, $query);

        $maxCount = $providerResults === [] ? 0 : max(array_map('count', $providerResults));

        for ($i = 0; $i < $maxCount; $i++) {
            foreach ($providerResults as $results) {
                if (! array_key_exists($i, $results)) {
                    continue;
                }

                if (! $add($results[$i])) {
                    return $deduped;
                }
            }
        }

        return $deduped;
    }
}
