<?php

namespace App\Services\PayeeVerification;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * United States — Plaid Database Auth, POST /auth/verify.
 * Docs: https://plaid.com/docs/api/products/auth/ and
 * https://plaid.com/docs/auth/coverage/database-auth/
 *
 * Sends an ACH routing + account number with the name the sender typed
 * (`legal_name`), and gets back how well that name matches the account
 * (`verification_insights.name_match_score`, 0-100). It never returns the
 * name itself — so for Chase or any other US bank the sender always types
 * the name, and the result is a match/no-match, not a retrieved name.
 *
 * /auth/verify is in Early Availability: Plaid has to enable it, and name
 * matching, on the account behind PLAID_CLIENT_ID / PLAID_SECRET first.
 */
class PlaidPayeeVerifier implements PayeeVerifier
{
    /**
     * Plaid's guidance: 70+ is likely a match, 50-69 may be one and
     * deserves a second look, below 50 is unlikely.
     */
    private const MATCH_SCORE = 70;

    private const CLOSE_MATCH_SCORE = 50;

    public function key(): string
    {
        return 'plaid';
    }

    public function label(): string
    {
        return 'Plaid name match';
    }

    public function isConfigured(): bool
    {
        return filled(config('services.plaid.client_id')) && filled(config('services.plaid.secret'));
    }

    public function returnsName(): bool
    {
        return false;
    }

    public function bankCodeLabel(): string
    {
        return 'Routing number';
    }

    /**
     * ABA routing numbers are exactly 9 digits; US account numbers are
     * digits only, up to 17.
     */
    public function acceptsDetails(PayeeDetails $details): bool
    {
        return preg_match('/^\d{9}$/', $details->bankCode) === 1
            && preg_match('/^\d{4,17}$/', $details->accountNumber) === 1;
    }

    public function verify(PayeeDetails $details): PayeeResult
    {
        if (! $details->hasName()) {
            return new PayeeResult(PayeeResult::NEEDS_NAME, $this->key());
        }

        $host = match (config('services.plaid.env', 'sandbox')) {
            'production' => 'https://production.plaid.com',
            'development' => 'https://development.plaid.com',
            default => 'https://sandbox.plaid.com',
        };

        $response = Http::timeout(15)->acceptJson()->asJson()->post($host.'/auth/verify', [
            'client_id' => config('services.plaid.client_id'),
            'secret' => config('services.plaid.secret'),
            'legal_name' => $details->name,
            'numbers' => [
                'ach' => [
                    'account' => $details->accountNumber,
                    'routing' => $details->bankCode,
                ],
            ],
        ]);

        if (! $response->successful()) {
            // Status and Plaid's error code only — no account details.
            Log::warning('Plaid auth/verify failed', [
                'status' => $response->status(),
                'error_code' => $response->json('error_code'),
            ]);

            return new PayeeResult(PayeeResult::FAILED, $this->key());
        }

        if ($response->json('verification_status') === 'database_insights_fail') {
            return new PayeeResult(PayeeResult::INVALID, $this->key());
        }

        $score = $response->json('verification_insights.name_match_score');

        // No score = Plaid had no name on record to compare against, or
        // name matching isn't enabled — "couldn't check", never "no match".
        if (! is_numeric($score)) {
            return new PayeeResult(PayeeResult::UNAVAILABLE, $this->key());
        }

        return new PayeeResult(match (true) {
            $score >= self::MATCH_SCORE => PayeeResult::MATCH,
            $score >= self::CLOSE_MATCH_SCORE => PayeeResult::CLOSE_MATCH,
            default => PayeeResult::NO_MATCH,
        }, $this->key());
    }
}
