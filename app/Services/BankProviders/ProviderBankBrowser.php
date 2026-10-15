<?php

namespace App\Services\BankProviders;

use Illuminate\Support\Facades\Cache;

/**
 * Backs Admin > Banks > Provider banks: every bank a configured provider
 * offers, searchable and paginated, so an admin can see what customers
 * are being offered live and copy any of them into the local directory.
 *
 * How each source is paged:
 *  - Plaid (US / International) with nothing searched: Plaid's own
 *    /institutions/get pagination (count + offset + total), so the full
 *    list of thousands is never downloaded at once.
 *  - Plaid with a search, and every other provider: their search() result
 *    paged here. Salt Edge and Open Payments return their whole (cached)
 *    catalogue for an empty search; TrueLayer and Token.io search live.
 *
 * Pages are cached briefly per source/search/page so moving back and forth
 * doesn't re-call the provider; failures are never cached.
 *
 * @phpstan-import-type BankRow from BankRows
 */
class ProviderBankBrowser
{
    private const CACHE_MINUTES = 10;

    /**
     * @return array<string, array{label: string, provider: BankProviderInterface}>
     */
    public function sources(): array
    {
        return [
            'plaid' => ['label' => 'Plaid United States', 'provider' => app(PlaidBankProvider::class)],
            'plaid_international' => ['label' => 'Plaid  International', 'provider' => app(PlaidInternationalBankProvider::class)],
            'salt_edge' => ['label' => 'Salt Edge', 'provider' => app(SaltEdgeBankProvider::class)],
            'open_payments' => ['label' => 'Open Payments', 'provider' => app(OpenPaymentsBankProvider::class)],
            'truelayer' => ['label' => 'TrueLayer', 'provider' => app(TrueLayerBankProvider::class)],
            'token_io' => ['label' => 'Token.io', 'provider' => app(TokenIoBankProvider::class)],
        ];
    }

    /**
     * One page of a source's banks, plus how many there are in all (for the
     * page links) and whether the source failed to answer.
     *
     * @return array{items: array<int, BankRow>, total: int, failed: bool}
     */
    public function page(string $source, string $query, int $page, int $perPage, ?string $country = null): array
    {
        $provider = $this->sources()[$source]['provider'] ?? null;
        $page = max(1, $page);

        return $provider === null || ! $provider->isConfigured()
            ? $this->emptyResult()
            : $this->fetch($source, $provider, $query, $page, $perPage, $country);
    }

    /**
     * The countries a source has banks in, for the Country filter — ISO
     * code => English name, sorted by name. Plaid's come from its country
     * list; the others from the banks they actually return (their full
     * list is cached by the provider, so this is cheap).
     *
     * @return array<string, string>
     */
    public function countries(string $source): array
    {
        $provider = $this->sources()[$source]['provider'] ?? null;

        if ($provider === null || ! $provider->isConfigured()) {
            return [];
        }

        $codes = Cache::remember('admin-provider-bank-countries:'.$source, now()->addMinutes(self::CACHE_MINUTES), function () use ($provider) {
            return $provider instanceof PlaidBankProvider
                ? $provider->supportedCountries()
                : collect($provider->search(''))->pluck('country')->filter()->unique()->values()->all();
        });

        return collect($codes)
            ->map(fn (string $code) => strtoupper($code))
            ->mapWithKeys(fn (string $code) => [$code => self::countryName($code)])
            ->sort()
            ->all();
    }

    public static function countryName(string $code): string
    {
        // getDisplayRegion() returns false when it can't name the region.
        $name = class_exists(\Locale::class) ? (\Locale::getDisplayRegion('-'.$code, 'en') ?: '') : '';

        return $name !== '' && $name !== $code ? $name : $code;
    }

    /**
     * No banks: what a source that isn't set up shows.
     *
     * @return array{items: array<int, BankRow>, total: int, failed: bool}
     */
    private function emptyResult(): array
    {
        return ['items' => [], 'total' => 0, 'failed' => false];
    }

    /**
     * @return array{items: array<int, BankRow>, total: int, failed: bool}
     */
    private function fetch(string $source, BankProviderInterface $provider, string $query, int $page, int $perPage, ?string $country): array
    {
        $key = 'admin-provider-banks:'.$source.':'.($country ?? 'all').':'.sha1(mb_strtolower($query)).':'.$page.':'.$perPage;
        $cached = Cache::get($key);

        if (is_array($cached)) {
            return [
                'items' => BankRows::normalize($cached['items'] ?? null),
                'total' => is_int($cached['total'] ?? null) ? $cached['total'] : 0,
                'failed' => false,
            ];
        }

        // Plaid filters by country itself; everyone else is filtered below.
        if ($provider instanceof PlaidBankProvider && $country !== null) {
            $provider = $provider->forCountry($country);
        }

        if ($provider instanceof PlaidBankProvider && $query === '') {
            $listing = $provider->listInstitutions($perPage, ($page - 1) * $perPage);

            $result = $listing === null
                ? ['items' => [], 'total' => 0, 'failed' => true]
                : ['items' => $listing['banks'], 'total' => $listing['total'], 'failed' => false];
        } else {
            // search() never throws — an empty list is either "no matches"
            // or a provider failure it has already logged.
            $all = $provider->search($query);

            if ($country !== null) {
                $all = array_filter($all, fn (array $bank) => strtoupper((string) $bank['country']) === $country);
            }

            $all = array_values($all);
            $result = [
                'items' => array_slice($all, ($page - 1) * $perPage, $perPage),
                'total' => count($all),
                'failed' => false,
            ];
        }

        if (! $result['failed'] && $result['items'] !== []) {
            Cache::put($key, $result, now()->addMinutes(self::CACHE_MINUTES));
        }

        return $result;
    }
}
