<?php

namespace App\Services\BankProviders;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * International bank source #1 of 3 — TrueLayer's Payments v3 provider
 * search (UK/EU open-banking coverage).
 *
 * Sign up for a free Sandbox client_id/secret at
 * https://console.truelayer.com/ — set TRUELAYER_CLIENT_ID /
 * TRUELAYER_CLIENT_SECRET in .env (see config/services.php).
 * Docs: https://docs.truelayer.com/docs/get-information-about-banking-providers
 *
 * TrueLayer uses standard OAuth2 client-credentials: an access token is
 * fetched from its auth server (cached for its ~1hr lifetime, so a
 * fresh token isn't requested on every keystroke) and sent as a Bearer
 * header to POST /v3/payment-providers/search on the Payments API host.
 */
class TrueLayerBankProvider implements BankProviderInterface
{
    public function isConfigured(): bool
    {
        return filled(config('services.truelayer.client_id')) && filled(config('services.truelayer.client_secret'));
    }

    public function search(string $query): array
    {
        if (! $this->isConfigured()) {
            return [];
        }

        try {
            $token = $this->accessToken();

            if ($token === null) {
                return [];
            }

            $isSandbox = config('services.truelayer.env', 'sandbox') !== 'live';
            $apiBase = $isSandbox ? 'https://api.truelayer-sandbox.com' : 'https://api.truelayer.com';

            $response = Http::timeout(5)
                ->retry(1, 200)
                ->withToken($token)
                ->post($apiBase.'/v3/payment-providers/search', [
                    // GB/EU-wide sweep — this app doesn't restrict which
                    // countries show up in the "International bank" tab.
                    'countries' => ['GB', 'DE', 'FR', 'ES', 'IT', 'NL', 'IE'],
                    'customer_segments' => ['retail', 'business'],
                    'capabilities' => ['payments' => ['bank_transfer' => new \stdClass]],
                    'authorization_flow' => ['configuration' => ['redirect' => new \stdClass]],
                ]);

            if (! $response->successful()) {
                Log::warning('TrueLayer payment-providers/search failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return [];
            }

            $providers = $response->json('results', $response->json() ?? []);
            $needle = mb_strtolower($query);

            return collect($providers)
                ->map(fn (array $p): array => [
                    'name' => (string) ($p['display_name'] ?? $p['provider_id'] ?? ''),
                    'country' => $p['country'] ?? ($p['country_code'] ?? null),
                    'swift_code' => null,
                    'currency' => null,
                    'routing_number' => null,
                    'meta' => '',
                    'source' => 'truelayer',
                ])
                ->filter(fn (array $bank) => $bank['name'] !== '' && ($needle === '' || str_contains(mb_strtolower($bank['name']), $needle)))
                ->values()
                ->all();
        } catch (\Throwable $e) {
            Log::warning('TrueLayer provider search threw', ['message' => $e->getMessage()]);

            return [];
        }
    }

    /**
     * TrueLayer tokens live ~1hr; cached so a debounced keystroke-by-
     * keystroke search doesn't re-authenticate every time.
     */
    private function accessToken(): ?string
    {
        $isSandbox = config('services.truelayer.env', 'sandbox') !== 'live';
        $cacheKey = 'truelayer:token:'.($isSandbox ? 'sandbox' : 'live');

        return Cache::remember($cacheKey, now()->addMinutes(50), function () use ($isSandbox) {
            $authBase = $isSandbox ? 'https://auth.truelayer-sandbox.com' : 'https://auth.truelayer.com';

            $response = Http::asForm()->timeout(5)->post($authBase.'/connect/token', [
                'grant_type' => 'client_credentials',
                'client_id' => config('services.truelayer.client_id'),
                'client_secret' => config('services.truelayer.client_secret'),
                'scope' => 'payments',
            ]);

            if (! $response->successful()) {
                Log::warning('TrueLayer token request failed', ['status' => $response->status()]);

                return null;
            }

            return $response->json('access_token');
        });
    }
}
