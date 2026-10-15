<?php

namespace App\Services\BankProviders;

use Illuminate\Pagination\LengthAwarePaginator;
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
     * @return array{banks: LengthAwarePaginator, failed: bool}
     */
    public function page(string $source, string $query, int $page, int $perPage, string $path, ?string $country = null): array
    {
        $provider = $this->sources()[$source]['provider'] ?? null;
        $page = max(1, $page);

        $result = $provider === null || ! $provider->isConfigured()
            ? ['items' => [], 'total' => 0, 'failed' => false]
            : $this->fetch($source, $provider, $query, $page, $perPage, $country);

        $paginator = new LengthAwarePaginator($result['items'], $result['total'], $perPage, $page, [
            'path' => $path,
            'pageName' => 'page',
        ]);

        return ['banks' => $paginator, 'failed' => $result['failed']];
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
        $name = class_exists(\Locale::class) ? \Locale::getDisplayRegion('-'.$code, 'en') : '';

        return $name !== '' && $name !== $code ? $name : $code;
    }

    /**
     * @return array{items: list<array<string, mixed>>, total: int, failed: bool}
     */
    private function fetch(string $source, BankProviderInterface $provider, string $query, int $page, int $perPage, ?string $country): array
    {
        $key = 'admin-provider-banks:'.$source.':'.($country ?? 'all').':'.sha1(mb_strtolower($query)).':'.$page.':'.$perPage;
        $cached = Cache::get($key);

        if (is_array($cached)) {
            return $cached;
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
            $all = collect($provider->search($query))
                ->when($country !== null, fn ($banks) => $banks->filter(fn (array $bank) => strtoupper((string) ($bank['country'] ?? '')) === $country))
                ->values()
                ->all();
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
