<?php

namespace App\Services\BankProviders;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Domestic ("Another bank") source — Plaid's Institutions API.
 *
 * Sign up for a free Sandbox client_id/secret at
 * https://dashboard.plaid.com/signin — set PLAID_CLIENT_ID / PLAID_SECRET
 * in .env (see config/services.php). Docs: https://plaid.com/docs/api/institutions/
 *
 * Uses POST /institutions/search — "look up an institution by name",
 * scoped to the country codes this app's own seeded domestic banks use
 * (US by default — see database/seeders/BankSeeder.php). Plaid's
 * client_id/secret are sent directly in the request body; unlike the other
 * three providers here, there's no separate OAuth token exchange step.
 */
class PlaidBankProvider implements BankProviderInterface
{
    /**
     * Set by forCountry() — narrows the next requests to one country.
     *
     * @var list<string>|null
     */
    private ?array $countryFilter = null;

    /**
     * A copy that only asks Plaid about one country — what Admin > Banks >
     * API banks uses for its Country filter. Plaid does the filtering
     * (country_codes), so its own paging and totals stay correct.
     */
    public function forCountry(string $countryCode): static
    {
        $copy = clone $this;
        $copy->countryFilter = [strtoupper(trim($countryCode))];

        return $copy;
    }

    /**
     * The countries this source covers, for the Country filter.
     *
     * @return list<string>
     */
    public function supportedCountries(): array
    {
        return $this->countryCodes();
    }

    public function isConfigured(): bool
    {
        return filled(config('services.plaid.client_id')) && filled(config('services.plaid.secret'));
    }

    public function search(string $query): array
    {
        if (! $this->isConfigured() || $query === '' || $this->requestCountryCodes() === []) {
            // institutions/search requires a non-empty query string — an
            // empty query is left to the local Bank directory to fill in.
            return [];
        }

        try {
            $env = config('services.plaid.env', 'sandbox');
            $base = match ($env) {
                'production' => 'https://production.plaid.com',
                'development' => 'https://development.plaid.com',
                default => 'https://sandbox.plaid.com',
            };

            $response = Http::timeout(5)
                ->retry(1, 200)
                ->post($base.'/institutions/search', [
                    'client_id' => config('services.plaid.client_id'),
                    'secret' => config('services.plaid.secret'),
                    'query' => $query,
                    'country_codes' => $this->requestCountryCodes(),
                    // Plaid rejects an EMPTY products array with
                    // INVALID_PRODUCT ("products must either be null or a
                    // list of strings containing at least one valid
                    // product") — omitting the key entirely (equivalent to
                    // null) is what actually means "don't filter by
                    // product," confirmed against a real Sandbox response.
                ]);

            if (! $response->successful()) {
                Log::warning('Plaid institutions/search failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return [];
            }

            return $this->formatInstitutions($response->json('institutions', []));
        } catch (\Throwable $e) {
            Log::warning('Plaid institutions/search threw', ['message' => $e->getMessage()]);

            return [];
        }
    }

    /**
     * One page of every institution Plaid supports in countryCodes(), via
     * POST /institutions/get (count + offset, with Plaid's own `total`) —
     * what Admin > Banks > Provider banks pages through when nothing is
     * searched. Null when Plaid couldn't be asked or refused.
     * Docs: https://plaid.com/docs/api/institutions/#institutionsget
     *
     * @return array{banks: array<int, array{name: string, country: string|null, swift_code: string|null, currency: string|null, routing_number: string|null, meta: string, source: string}>, total: int}|null
     */
    public function listInstitutions(int $count, int $offset): ?array
    {
        if (! $this->isConfigured()) {
            return null;
        }

        // A country this source doesn't cover — nothing to ask Plaid for.
        if ($this->requestCountryCodes() === []) {
            return ['banks' => [], 'total' => 0];
        }

        try {
            $response = Http::timeout(15)
                ->retry(1, 200)
                ->post($this->baseUrl().'/institutions/get', [
                    'client_id' => config('services.plaid.client_id'),
                    'secret' => config('services.plaid.secret'),
                    // Plaid allows 1-500 per page.
                    'count' => max(1, min(500, $count)),
                    'offset' => max(0, $offset),
                    'country_codes' => $this->requestCountryCodes(),
                ]);

            if (! $response->successful()) {
                Log::warning('Plaid institutions/get failed', [
                    'status' => $response->status(),
                    'error_code' => $response->json('error_code'),
                ]);

                return null;
            }

            return [
                'banks' => $this->formatInstitutions($response->json('institutions', [])),
                'total' => (int) $response->json('total', 0),
            ];
        } catch (\Throwable $e) {
            Log::warning('Plaid institutions/get threw', ['message' => $e->getMessage()]);

            return null;
        }
    }

    /**
     * Plaid institution rows in the shape the bank pickers expect.
     *
     * @param  array<int, array<string, mixed>>  $institutions
     * @return array<int, array{name: string, country: string|null, swift_code: string|null, currency: string|null, routing_number: string|null, meta: string, source: string}>
     */
    protected function formatInstitutions(array $institutions): array
    {
        return collect($institutions)
            ->map(function (array $inst): array {
                $routingNumber = isset($inst['routing_numbers'][0]) ? (string) $inst['routing_numbers'][0] : null;
                $country = isset($inst['country_codes'][0]) ? (string) $inst['country_codes'][0] : null;

                return [
                    'name' => (string) ($inst['name'] ?? ''),
                    'country' => $country,
                    'swift_code' => null,
                    'currency' => null,
                    'routing_number' => $routingNumber,
                    'meta' => $routingNumber !== null ? 'Routing '.$routingNumber : '',
                    'source' => 'plaid',
                ];
            })
            ->filter(fn (array $bank) => $bank['name'] !== '')
            ->values()
            ->all();
    }

    protected function baseUrl(): string
    {
        return match (config('services.plaid.env', 'sandbox')) {
            'production' => 'https://production.plaid.com',
            'development' => 'https://development.plaid.com',
            default => 'https://sandbox.plaid.com',
        };
    }

    /**
     * The countries searched — PLAID_COUNTRY_CODES (US by default) for the
     * domestic "Another bank" picker. See PlaidInternationalBankProvider
     * for the International tab's set.
     *
     * @return list<string>
     */
    /**
     * countryCodes(), narrowed to the forCountry() country when one is set
     * (empty when that country isn't one this source covers).
     *
     * @return list<string>
     */
    protected function requestCountryCodes(): array
    {
        if ($this->countryFilter === null) {
            return $this->countryCodes();
        }

        return array_values(array_intersect($this->countryFilter, $this->countryCodes()));
    }

    /**
     * @return list<string>
     */
    protected function countryCodes(): array
    {
        $codes = array_values(array_filter(array_map(
            'trim',
            explode(',', (string) config('services.plaid.country_codes', 'US'))
        )));

        return $codes ?: ['US'];
    }
}
