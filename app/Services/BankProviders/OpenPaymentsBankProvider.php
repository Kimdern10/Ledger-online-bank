<?php

namespace App\Services\BankProviders;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * International bank source #3 of 3 — the Open Payments ASPSP directory
 * (openpayments.io — pan-European PSD2 bank list, filterable by country
 * and BIC).
 *
 * Sign up for a free Sandbox client_id/secret at
 * https://developer.openpayments.io/ — set OPENPAYMENTS_CLIENT_ID /
 * OPENPAYMENTS_CLIENT_SECRET in .env (see config/services.php).
 * Docs: https://docs.openpayments.io/docs/list_banks
 *
 * Standard OAuth2 client-credentials: a token (scope
 * "aspspinformation corporate") is fetched and cached for its ~1hr
 * lifetime, then sent as a Bearer header to GET /psd2/aspspinformation/v1/
 * aspsps, which also requires a fresh X-Request-ID (UUID) on every call.
 */
class OpenPaymentsBankProvider implements BankProviderInterface
{
    private const AUTH_URL = 'https://auth.openbankingplatform.com/connect/token';

    private const API_BASE = 'https://api.openbankingplatform.com';

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
                ->get(self::API_BASE.'/psd2/aspspinformation/v1/aspsps');

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

    private function accessToken(): ?string
    {
        return Cache::remember('open_payments:token', now()->addMinutes(50), function () {
            $response = Http::asForm()->timeout(5)->post(self::AUTH_URL, [
                'grant_type' => 'client_credentials',
                'client_id' => config('services.open_payments.client_id'),
                'client_secret' => config('services.open_payments.client_secret'),
                'scope' => 'aspspinformation corporate',
            ]);

            if (! $response->successful()) {
                Log::warning('Open Payments token request failed', ['status' => $response->status()]);

                return null;
            }

            return $response->json('access_token');
        });
    }
}
