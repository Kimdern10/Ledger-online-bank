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
    public function isConfigured(): bool
    {
        return filled(config('services.plaid.client_id')) && filled(config('services.plaid.secret'));
    }

    public function search(string $query): array
    {
        if (! $this->isConfigured() || $query === '') {
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

            $countryCodes = array_values(array_filter(array_map(
                'trim',
                explode(',', (string) config('services.plaid.country_codes', 'US'))
            )));

            $response = Http::timeout(5)
                ->retry(1, 200)
                ->post($base.'/institutions/search', [
                    'client_id' => config('services.plaid.client_id'),
                    'secret' => config('services.plaid.secret'),
                    'query' => $query,
                    'country_codes' => $countryCodes ?: ['US'],
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

            $institutions = $response->json('institutions', []);

            return collect($institutions)
                ->map(function (array $inst): array {
                    $routingNumbers = $inst['routing_numbers'] ?? [];
                    $countryCodes = $inst['country_codes'] ?? [];

                    return [
                        'name' => (string) ($inst['name'] ?? ''),
                        'country' => $countryCodes[0] ?? null,
                        'swift_code' => null,
                        'currency' => null,
                        'routing_number' => $routingNumbers[0] ?? null,
                        'meta' => $routingNumbers ? 'Routing '.$routingNumbers[0] : '',
                        'source' => 'plaid',
                    ];
                })
                ->filter(fn (array $bank) => $bank['name'] !== '')
                ->values()
                ->all();
        } catch (\Throwable $e) {
            Log::warning('Plaid institutions/search threw', ['message' => $e->getMessage()]);

            return [];
        }
    }
}
