<?php

namespace App\Services\PayeeVerification;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Picks the provider config/payee_verification.php assigns to the
 * destination country and runs the check. It also remembers, per sender
 * and per exact recipient account, what the provider last said — that
 * record, never anything the browser submits, is what TransferController
 * stores as the transfer's recipient_name_status (see outcomeFor()).
 */
class PayeeVerificationManager
{
    /**
     * How long the last answer for an account stays usable for the
     * transfer that follows it.
     */
    private const OUTCOME_MINUTES = 30;

    /**
     * How long a definite provider answer is reused instead of paying for
     * the same lookup again.
     */
    private const RESULT_MINUTES = 10;

    /**
     * @return array<string, PayeeVerifier>
     */
    public function providers(): array
    {
        return collect([app(PlaidPayeeVerifier::class), app(CashfreePayeeVerifier::class)])
            ->keyBy(fn (PayeeVerifier $provider) => $provider->key())
            ->all();
    }

    /**
     * ISO code for a country given as a code or a name ("IN", "India"),
     * among the countries that have a provider — null for every other.
     */
    public function countryCode(?string $country): ?string
    {
        $value = mb_strtolower(trim((string) $country));

        foreach (config('payee_verification.countries', []) as $code => $settings) {
            if ($value === mb_strtolower($code) || in_array($value, $settings['aliases'] ?? [], true)) {
                return $code;
            }
        }

        return null;
    }

    /**
     * The provider that will actually run for this country right now, or
     * null — none assigned, or its credentials aren't configured.
     */
    public function providerFor(?string $countryCode): ?PayeeVerifier
    {
        $key = config('payee_verification.countries.'.strtoupper((string) $countryCode).'.provider');
        $provider = $this->providers()[$key ?? ''] ?? null;

        return $provider?->isConfigured() ? $provider : null;
    }

    public function verify(User $sender, PayeeDetails $details): PayeeResult
    {
        $provider = $this->providerFor($details->countryCode);

        if ($provider === null) {
            return new PayeeResult(PayeeResult::UNSUPPORTED);
        }

        if (! $provider->acceptsDetails($details)) {
            return new PayeeResult(PayeeResult::INVALID, $provider->key());
        }

        $result = $this->cachedOrFresh($provider, $details);

        if ($result->status !== PayeeResult::NEEDS_NAME) {
            Cache::put($this->outcomeKey($sender, $details), [
                'status' => $result->status,
                'provider' => $result->provider,
                'verified_name' => $result->verifiedName,
                'checked_name' => PayeeDetails::normalizeName($details->name),
            ], now()->addMinutes(self::OUTCOME_MINUTES));
        }

        return $result;
    }

    /**
     * What to store on a transfer to this account with this name, from the
     * last check THIS sender ran on THIS exact account:
     *
     *  - verified: the provider returned the holder's name, and it's the
     *              name being sent
     *  - match / close_match / no_match: the provider scored exactly this
     *              name against the account
     *  - invalid:  the provider said the account details are wrong —
     *              TransferController refuses the transfer
     *  - unavailable / failed: a check ran but the provider couldn't answer
     *  - manual:   no check covered this name (none ran, no provider for the
     *              country, or the name was changed after the check)
     *
     * @return array{status: string, provider: ?string}
     */
    public function outcomeFor(User $sender, PayeeDetails $details): array
    {
        $outcome = Cache::get($this->outcomeKey($sender, $details));

        if (! is_array($outcome)) {
            return ['status' => 'manual', 'provider' => null];
        }

        $submitted = PayeeDetails::normalizeName($details->name);

        $status = match (true) {
            $outcome['status'] === PayeeResult::INVALID => PayeeResult::INVALID,
            $outcome['status'] === PayeeResult::NAME_RETRIEVED
                && PayeeDetails::normalizeName($outcome['verified_name']) === $submitted => PayeeResult::NAME_RETRIEVED,
            in_array($outcome['status'], [PayeeResult::MATCH, PayeeResult::CLOSE_MATCH, PayeeResult::NO_MATCH], true)
                && $outcome['checked_name'] === $submitted => $outcome['status'],
            in_array($outcome['status'], [PayeeResult::UNAVAILABLE, PayeeResult::FAILED], true) => $outcome['status'],
            default => 'manual',
        };

        return ['status' => $status, 'provider' => $status === 'manual' ? null : $outcome['provider']];
    }

    /**
     * The provider is only called for a combination it hasn't answered in
     * the last few minutes. The cache key covers the account AND, for
     * providers that score a typed name, that name — so one account's or
     * one name's answer is never reused for another.
     */
    private function cachedOrFresh(PayeeVerifier $provider, PayeeDetails $details): PayeeResult
    {
        $name = $provider->returnsName() ? '' : PayeeDetails::normalizeName($details->name);
        $key = 'payee-result:'.$provider->key().':'.$details->accountFingerprint().':'.sha1($name);
        $cached = Cache::get($key);

        if (is_array($cached)) {
            return new PayeeResult($cached['status'], $provider->key(), $cached['verified_name']);
        }

        try {
            $result = $provider->verify($details);
        } catch (\Throwable $e) {
            Log::warning('Payee verification threw', ['provider' => $provider->key(), 'error' => class_basename($e)]);

            $result = new PayeeResult(PayeeResult::FAILED, $provider->key());
        }

        if ($result->isConclusive()) {
            Cache::put($key, ['status' => $result->status, 'verified_name' => $result->verifiedName], now()->addMinutes(self::RESULT_MINUTES));
        }

        return $result;
    }

    private function outcomeKey(User $sender, PayeeDetails $details): string
    {
        return 'payee-outcome:'.$sender->getAuthIdentifier().':'.$details->accountFingerprint();
    }
}
