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
 */
class SaltEdgeBankProvider implements BankProviderInterface
{
    private const CATALOG_CACHE_KEY = 'saltedge:providers:catalog';

    private const CATALOG_CACHE_MINUTES = 60;

    private const MAX_PAGES = 10;

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
     */
    private function catalog(): array
    {
        // Deliberately not Cache::remember() — that would also cache an
        // empty result from a failed/misconfigured request for the full
        // hour, hiding a real fix behind a stale cached failure. Only a
        // genuinely non-empty catalog gets cached.
        $cached = Cache::get(self::CATALOG_CACHE_KEY);

        if ($cached !== null) {
            return $cached;
        }

        $catalog = $this->fetchCatalog();

        if ($catalog !== []) {
            Cache::put(self::CATALOG_CACHE_KEY, $catalog, now()->addMinutes(self::CATALOG_CACHE_MINUTES));
        }

        return $catalog;
    }

    /**
     * Fetch the full provider catalog from Salt Edge, already normalized.
     * Returns [] on any failure — never throws.
     */
    private function fetchCatalog(): array
    {
        try {
            $all = [];
            $fromId = null;

            for ($page = 0; $page < self::MAX_PAGES; $page++) {
                $response = Http::timeout(5)
                    ->retry(1, 200)
                    ->withHeaders([
                        'App-id' => config('services.salt_edge.app_id'),
                        'Secret' => config('services.salt_edge.secret'),
                        'Accept' => 'application/json',
                    ])
                    ->get('https://www.saltedge.com/api/v6/providers', array_filter([
                        'from_id' => $fromId,
                    ]));

                if (! $response->successful()) {
                    Log::warning('Salt Edge providers request failed', [
                        'status' => $response->status(),
                        'body' => $response->body(),
                    ]);

                    break;
                }

                foreach ($response->json('data', []) as $p) {
                    $all[] = [
                        'name' => (string) ($p['name'] ?? ''),
                        'country' => $p['country_code'] ?? null,
                        'swift_code' => null,
                        'currency' => null,
                        'routing_number' => null,
                        'meta' => '',
                        'source' => 'salt_edge',
                    ];
                }

                $fromId = $response->json('meta.next_id');

                if (! $fromId) {
                    break;
                }
            }

            return array_values(array_filter($all, fn (array $b) => $b['name'] !== ''));
        } catch (\Throwable $e) {
            Log::warning('Salt Edge providers request threw', ['message' => $e->getMessage()]);

            return [];
        }
    }
}
