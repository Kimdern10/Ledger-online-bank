<?php

namespace App\Services\BankProviders;

/**
 * International bank source — the same Plaid Institutions API as the
 * domestic picker (PlaidBankProvider), searched across Plaid's non-US
 * countries instead. This is what covers the UK, Ireland, Spain, France,
 * Italy, Germany, the Netherlands, Canada and the rest of Plaid's list,
 * where the other international sources here are thin (Salt Edge) or
 * Nordic-only (Open Payments) — confirmed live against this app's keys.
 *
 * Same PLAID_CLIENT_ID / PLAID_SECRET; the countries come from
 * PLAID_INTERNATIONAL_COUNTRY_CODES (see config/services.php). Plaid only
 * accepts the countries it documents for institutions/search:
 * https://plaid.com/docs/api/institutions/#institutionssearch
 */
class PlaidInternationalBankProvider extends PlaidBankProvider
{
    /**
     * Plaid's documented test institutions — they only exist in Sandbox and
     * would otherwise show up as e.g. "Unhealthy Platypus Bank - Degraded".
     * See https://plaid.com/docs/sandbox/institutions/
     */
    private const SANDBOX_TEST_INSTITUTIONS = '/platypus|gingham|tattersall|tartan|houndstooth|bank of plaid/i';

    /**
     * Used for both search() and listInstitutions() — so the picker and
     * Admin > Banks > Provider banks see the same rows.
     *
     * @param  array<int, array<string, mixed>>  $institutions
     * @return array<int, array{name: string, country: string|null, swift_code: string|null, currency: string|null, routing_number: string|null, meta: string, source: string}>
     */
    protected function formatInstitutions(array $institutions): array
    {
        $isSandbox = ! in_array(config('services.plaid.env', 'sandbox'), ['production', 'development'], true);

        return collect(parent::formatInstitutions($institutions))
            ->reject(fn (array $bank) => $isSandbox && preg_match(self::SANDBOX_TEST_INSTITUTIONS, $bank['name']) === 1)
            // Routing numbers are US-only; the second line shows the
            // country instead.
            ->map(fn (array $bank) => array_merge($bank, ['meta' => (string) $bank['country']]))
            ->values()
            ->all();
    }

    /**
     * @return list<string>
     */
    protected function countryCodes(): array
    {
        $codes = array_values(array_filter(array_map(
            fn (string $code) => strtoupper(trim($code)),
            explode(',', (string) config('services.plaid.international_country_codes', ''))
        )));

        // Never US here — that's the "Another bank" tab.
        $codes = array_values(array_diff($codes, ['US']));

        return $codes ?: ['GB'];
    }
}
