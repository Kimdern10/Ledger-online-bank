<?php

namespace App\Services\PayeeVerification;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * India — Cashfree Bank Account Verification (sync, v2), POST
 * /verification/bank-account/sync.
 * Docs: https://www.cashfree.com/docs/api-reference/vrs/v2/bav-v2/bank-account-verification-sync-v2
 *
 * Sends an account number + IFSC and gets back `name_at_bank` — the account
 * holder's name as the bank records it. This is the one provider here that
 * genuinely retrieves a name, so the sender never types one.
 *
 * The service must be activated on the Cashfree account, and Cashfree may
 * require the calling server's IP to be whitelisted. Cashfree documents
 * that Deutsche Bank and Paytm Payments Bank accounts aren't supported.
 */
class CashfreePayeeVerifier implements PayeeVerifier
{
    public function key(): string
    {
        return 'cashfree';
    }

    public function label(): string
    {
        return 'Cashfree bank account verification';
    }

    public function isConfigured(): bool
    {
        return filled(config('services.cashfree.client_id')) && filled(config('services.cashfree.client_secret'));
    }

    public function returnsName(): bool
    {
        return true;
    }

    public function bankCodeLabel(): string
    {
        return 'IFSC code';
    }

    /**
     * Cashfree's documented formats: IFSC is 4 letters, a 0, then 6
     * letters/digits; the account is 6-40 alphanumeric characters.
     */
    public function acceptsDetails(PayeeDetails $details): bool
    {
        return preg_match('/^[A-Z]{4}0[A-Z0-9]{6}$/', strtoupper($details->bankCode)) === 1
            && preg_match('/^[A-Za-z0-9]{6,40}$/', $details->accountNumber) === 1;
    }

    public function verify(PayeeDetails $details): PayeeResult
    {
        $host = config('services.cashfree.env', 'sandbox') === 'production'
            ? 'https://api.cashfree.com'
            : 'https://sandbox.cashfree.com';

        $response = Http::timeout(20)
            ->acceptJson()
            ->asJson()
            ->withHeaders([
                'x-client-id' => config('services.cashfree.client_id'),
                'x-client-secret' => config('services.cashfree.client_secret'),
            ])
            ->post($host.'/verification/bank-account/sync', [
                'bank_account' => $details->accountNumber,
                'ifsc' => strtoupper($details->bankCode),
            ]);

        if (! $response->successful()) {
            Log::warning('Cashfree bank account verification failed', [
                'status' => $response->status(),
                'code' => $response->json('code'),
            ]);

            return new PayeeResult(PayeeResult::FAILED, $this->key());
        }

        if ($response->json('account_status') === 'INVALID') {
            return new PayeeResult(PayeeResult::INVALID, $this->key());
        }

        // Anything other than a clear VALID is never treated as success.
        if ($response->json('account_status') !== 'VALID') {
            return new PayeeResult(PayeeResult::FAILED, $this->key());
        }

        $nameAtBank = trim((string) $response->json('name_at_bank'));

        // A valid account whose bank returned no name: "couldn't retrieve
        // it" — the sender falls back to typing one, unverified.
        if ($nameAtBank === '') {
            return new PayeeResult(PayeeResult::UNAVAILABLE, $this->key());
        }

        return new PayeeResult(PayeeResult::NAME_RETRIEVED, $this->key(), $nameAtBank);
    }
}
