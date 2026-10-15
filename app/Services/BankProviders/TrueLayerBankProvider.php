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
 * header to POST /v3/payments-providers/search on the Payments API host
 * (note: "payments-providers", plural — a singular "/v3/payment-providers/
 * search" 404s; confirmed against TrueLayer's own API reference).
 *
 * TrueLayer only returns a bank's name and country — never a SWIFT code or
 * currency — so the currency is derived from the country here (every
 * country searched below is either GB or a eurozone country), which lets
 * the Send Money picker fill in Country and Currency when a bank is chosen.
 */
class TrueLayerBankProvider implements BankProviderInterface
{
    /**
     * Currency for each country this provider searches.
     */
    private const COUNTRY_CURRENCY = [
        'GB' => 'GBP',
        'DE' => 'EUR',
        'FR' => 'EUR',
        'ES' => 'EUR',
        'IT' => 'EUR',
        'NL' => 'EUR',
        'IE' => 'EUR',
    ];

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
                ->post($apiBase.'/v3/payments-providers/search', [
                    // GB/EU-wide sweep — this app doesn't restrict which
                    // countries show up in the "International bank" tab.
                    'countries' => ['GB', 'DE', 'FR', 'ES', 'IT', 'NL', 'IE'],
                    // Required — omitting it (as this originally did) is
                    // accepted at the routing level (no longer a 404 once
                    // the path was fixed) but TrueLayer's server 500s on
                    // it rather than returning a proper 400, confirmed
                    // live. Covers every country above (GBP for GB, EUR
                    // for the rest).
                    'currencies' => ['GBP', 'EUR'],
                    'customer_segments' => ['retail', 'business'],
                    'capabilities' => ['payments' => ['bank_transfer' => new \stdClass]],
                    'authorization_flow' => ['configuration' => ['redirect' => new \stdClass]],
                ]);

            if (! $response->successful()) {
                Log::warning('TrueLayer payments-providers/search failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return [];
            }

            // The documented response is { "items": [...] } — see
            // https://docs.truelayer.com/reference/search-payment-providers.
            // (This used to read "results", which never existed, and fell
            // back to mapping the whole response object.)
            $providers = $response->json('items', []);
            $needle = mb_strtolower($query);

            return collect($providers)
                ->map(function (array $p): array {
                    $country = $this->countryFrom($p);

                    return [
                        'name' => (string) ($p['display_name'] ?? $p['provider_id'] ?? ''),
                        'country' => $country,
                        'swift_code' => $p['swift_code'] ?? null,
                        'currency' => $country ? (self::COUNTRY_CURRENCY[$country] ?? null) : null,
                        'routing_number' => null,
                        // The small gray line under the bank's name in the picker.
                        'meta' => trim(collect([$country, $p['swift_code'] ?? null])->filter()->implode(' · ')),
                        'source' => 'truelayer',
                    ];
                })
                ->filter(fn (array $bank) => $bank['name'] !== '' && ($needle === '' || str_contains(mb_strtolower($bank['name']), $needle)))
                ->values()
                ->all();
        } catch (\Throwable $e) {
            Log::warning('TrueLayer provider search threw', ['message' => $e->getMessage()]);

            return [];
        }
    }

    /**
     * TrueLayer doesn't always put the country in the same field, so check
     * every place it might be and return a 2-letter uppercase code.
     */
    private function countryFrom(array $provider): ?string
    {
        $candidate = $provider['country_code']
            ?? $provider['country']
            ?? (is_array($provider['countries'] ?? null) ? ($provider['countries'][0] ?? null) : null);

        $candidate = strtoupper(trim((string) $candidate));

        return strlen($candidate) === 2 ? $candidate : null;
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
