<?php

namespace App\Services\BankProviders;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * International bank source #2 of 3 — Token.io's Banks v2 endpoint
 * (EU open-banking coverage).
 *
 * Sign up for a free Sandbox API key at https://console.token.io/ — set
 * TOKENIO_API_KEY (and TOKENIO_MEMBER_ID, the TPP member id Token.io
 * assigns your app on signup) in .env (see config/services.php).
 * Docs: https://docs.token.io/products/tpp/api/reference/banks-v2/getbanksv2
 *
 * Token.io's PRODUCTION API requires asymmetric-key request signing (via
 * their SDK) for every call — that's real cryptographic work this class
 * deliberately does not reimplement. Sandbox, however, accepts simple
 * "Basic" API-key auth (see Token.io's Authentication docs), which is
 * what this class uses. If you later move this app off Sandbox, this
 * provider needs Token.io's official SDK in place of this plain HTTP call.
 */
class TokenIoBankProvider implements BankProviderInterface
{
    public function isConfigured(): bool
    {
        return filled(config('services.token_io.api_key'))
            && filled(config('services.token_io.member_id'))
            && config('services.token_io.env', 'sandbox') === 'sandbox';
    }

    public function search(string $query): array
    {
        if (! $this->isConfigured()) {
            return [];
        }

        try {
            $response = Http::timeout(5)
                ->retry(1, 200)
                ->withHeaders([
                    'Authorization' => 'Basic '.base64_encode(config('services.token_io.api_key').':'),
                ])
                ->get('https://api.sandbox.token.io/v2/banks', array_filter([
                    'memberId' => config('services.token_io.member_id'),
                    'search' => $query !== '' ? $query : null,
                ]));

            if (! $response->successful()) {
                Log::warning('Token.io banks-v2 request failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return [];
            }

            $banks = $response->json('banks', []);

            return collect($banks)
                ->map(fn (array $b): array => [
                    'name' => (string) ($b['name'] ?? ''),
                    'country' => $b['countries'][0] ?? null,
                    'swift_code' => $b['bic'] ?? null,
                    'currency' => null,
                    'routing_number' => null,
                    'meta' => trim(collect([$b['countries'][0] ?? null, $b['bic'] ?? null])->filter()->implode(' · ')),
                    'source' => 'token_io',
                ])
                ->filter(fn (array $bank) => $bank['name'] !== '')
                ->values()
                ->all();
        } catch (\Throwable $e) {
            Log::warning('Token.io banks-v2 request threw', ['message' => $e->getMessage()]);

            return [];
        }
    }
}
