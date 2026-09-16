<?php

namespace App\Services\BankProviders;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * International bank source #3 of 4 — the Open Payments ASPSP directory
 * (openpayments.io — pan-European/Nordic PSD2 bank list, filterable by
 * country and BIC).
 *
 * Sign up for a free Sandbox client_id/secret at
 * https://developer.openpayments.io/ — set OPENPAYMENTS_CLIENT_ID /
 * OPENPAYMENTS_CLIENT_SECRET in .env (see config/services.php).
 * Docs: https://docs.openpayments.io/docs/list_banks
 *
 * Standard OAuth2 client-credentials, but — confirmed live against a real
 * Sandbox client — SANDBOX uses entirely different hosts AND a different
 * scope than production:
 *   - Sandbox auth host:  oauth.sandbox.openbankingplatform.com
 *   - Sandbox API host:   api.sandbox.openbankingplatform.com
 *   - Sandbox scope:      "accountinformation corporate" (NOT
 *     "aspspinformation corporate" — that scope is production-only; using
 *     it against sandbox is rejected with a 400).
 *   - Production auth/API hosts: auth.openbankingplatform.com /
 *     api.openbankingplatform.com, scope "aspspinformation corporate".
 * Getting either of those wrong for the current `open_payments.env`
 * produces exactly the failure this class originally shipped with: a
 * 400 on the token request, or a connection timeout hitting the wrong
 * host entirely. The token is cached for its ~1hr lifetime, then sent as
 * a Bearer header to GET /psd2/aspspinformation/v1/aspsps, which also
 * requires a fresh X-Request-ID (UUID) on every call.
 */
class OpenPaymentsBankProvider implements BankProviderInterface
{
    public function isConfigured(): bool
    {
        return filled(config('services.open_payments.client_id')) && filled(config('services.open_payments.client_secret'));
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

            $response = Http::timeout(5)
                ->retry(1, 200)
                ->withToken($token)
                ->withHeaders(['X-Request-ID' => (string) Str::uuid()])
                ->get($this->apiBase().'/psd2/aspspinformation/v1/aspsps');

            if (! $response->successful()) {
                Log::warning('Open Payments aspsps request failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return [];
            }

            $aspsps = $response->json('aspsps', $response->json() ?? []);
            $needle = mb_strtolower($query);

            return collect($aspsps)
                ->map(fn (array $a): array => [
                    'name' => (string) ($a['name'] ?? ''),
                    'country' => $a['countryCode'] ?? null,
                    'swift_code' => $a['bicFi'] ?? null,
                    'currency' => null,
                    'routing_number' => null,
                    'meta' => trim(collect([$a['countryCode'] ?? null, $a['bicFi'] ?? null])->filter()->implode(' · ')),
                    'source' => 'open_payments',
                ])
                ->filter(fn (array $bank) => $bank['name'] !== '' && ($needle === '' || str_contains(mb_strtolower($bank['name']), $needle)))
                ->values()
                ->all();
        } catch (\Throwable $e) {
            Log::warning('Open Payments aspsps request threw', ['message' => $e->getMessage()]);

            return [];
        }
    }

    private function isSandbox(): bool
    {
        return config('services.open_payments.env', 'sandbox') !== 'production';
    }

    private function authUrl(): string
    {
        return $this->isSandbox()
            ? 'https://oauth.sandbox.openbankingplatform.com/connect/token'
            : 'https://auth.openbankingplatform.com/connect/token';
    }

    private function apiBase(): string
    {
        return $this->isSandbox()
            ? 'https://api.sandbox.openbankingplatform.com'
            : 'https://api.openbankingplatform.com';
    }

    private function scope(): string
    {
        // Sandbox genuinely does not accept the production "aspspinformation"
        // scope — it 400s. This isn't a documentation quirk, it's how their
        // sandbox is provisioned.
        return $this->isSandbox() ? 'accountinformation corporate' : 'aspspinformation corporate';
    }

    private function accessToken(): ?string
    {
        $cacheKey = 'open_payments:token:'.($this->isSandbox() ? 'sandbox' : 'production');

        // Cache::remember would also cache a failed (null) lookup for the
        // full TTL, hiding a real fix (like this one) behind a stale cached
        // failure — so this only writes the cache on an actual token.
        $cached = Cache::get($cacheKey);

        if ($cached !== null) {
            return $cached;
        }

        $response = Http::asForm()->timeout(5)->post($this->authUrl(), [
            'grant_type' => 'client_credentials',
            'client_id' => config('services.open_payments.client_id'),
            'client_secret' => config('services.open_payments.client_secret'),
            'scope' => $this->scope(),
        ]);

        if (! $response->successful()) {
            Log::warning('Open Payments token request failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        }

        $token = $response->json('access_token');

        if ($token !== null) {
            Cache::put($cacheKey, $token, now()->addMinutes(50));
        }

        return $token;
    }
}
