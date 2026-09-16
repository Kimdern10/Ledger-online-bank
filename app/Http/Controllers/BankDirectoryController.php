<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Services\BankProviders\BankProviderInterface;
use App\Services\BankProviders\OpenPaymentsBankProvider;
use App\Services\BankProviders\PlaidBankProvider;
use App\Services\BankProviders\SaltEdgeBankProvider;
use App\Services\BankProviders\TokenIoBankProvider;
use App\Services\BankProviders\TrueLayerBankProvider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * Backs the "Select a bank" pickers on the Send Money page — the "Another
 * bank" tab's domestic picker and the "International bank" tab's picker.
 *
 * Each picker merges TWO kinds of source, always in this order:
 *
 *  1. The admin-managed Bank directory (see App\Models\Bank /
 *     AdminBankController) — hand-curated entries, always included, always
 *     listed first. This is what keeps working even if every live API
 *     below is unconfigured, misconfigured, or down.
 *  2. One or more live third-party bank-directory APIs (see
 *     App\Services\BankProviders) — domestic search uses Plaid; the
 *     international search merges TrueLayer + Token.io + Open Payments
 *     (Europe) with Salt Edge (Asia-Pacific by default), since no single
 *     one of those covers every country. Any provider without credentials
 *     configured (see config/services.php) is silently skipped — it
 *     contributes zero results, never an error.
 *
 * Results are de-duplicated by a normalized bank name (so a bank an admin
 * already added by hand doesn't also show up a second time from a live
 * API) and briefly cached per (type, query) to avoid re-hitting live APIs
 * on every debounced keystroke from the picker's search box.
 *
 * Local admin entries always go first, uncapped relative to the live
 * providers. The live providers themselves are merged ROUND-ROBIN, not
 * sequentially — confirmed live that a broad query against a provider
 * with a large catalog (Open Payments' full Nordic bank list, dozens of
 * matches) would otherwise fill the entire MAX_RESULTS cap before a
 * provider listed later in the array (e.g. Salt Edge) ever contributed a
 * single result, even with equally good matches of its own.
 */
class BankDirectoryController extends Controller
{
    /**
     * Keeps a broad query from turning the picker into an unusable wall of
     * matches.
     */
    protected const MAX_RESULTS = 25;

    /**
     * "Another bank" tab — domestic (type=external) entries only.
     */
    public function search(Request $request): JsonResponse
    {
        $query = trim((string) $request->query('q', ''));

        $results = Cache::remember(
            'banks:external:'.mb_strtolower($query),
            now()->addMinutes(5),
            fn () => $this->merge(
                $this->localResults($query, 'external'),
                $query,
                [app(PlaidBankProvider::class)]
            )
        );

        return response()->json(['banks' => $results]);
    }

    /**
     * "International bank" tab — international entries only.
     */
    public function searchInternational(Request $request): JsonResponse
    {
        $query = trim((string) $request->query('q', ''));

        $results = Cache::remember(
            'banks:international:'.mb_strtolower($query),
            now()->addMinutes(5),
            fn () => $this->merge(
                $this->localResults($query, 'international'),
                $query,
                [
                    app(TrueLayerBankProvider::class),
                    app(TokenIoBankProvider::class),
                    app(OpenPaymentsBankProvider::class),
                    app(SaltEdgeBankProvider::class),
                ]
            )
        );

        return response()->json(['banks' => $results]);
    }

    private function localResults(string $query, string $type): array
    {
        $banks = Bank::query()->active()->ofType($type);

        if ($query !== '') {
            $banks->where('name', 'like', '%'.$query.'%');
        }

        return $banks->orderBy('name')
            ->get()
            ->map(fn (Bank $bank) => [
                'id' => $bank->id,
                'name' => $bank->name,
                'country' => $bank->country,
                'swift_code' => $bank->swift_code,
                'currency' => $bank->currency,
                'routing_number' => $bank->routing_number,
                // Pre-formatted here rather than in JS, since the two tabs'
                // Bank rows carry different identifying fields (routing
                // number vs. SWIFT/country) and the picker just needs one
                // line of secondary text to show either way.
                'meta' => $type === 'international'
                    ? trim(collect([$bank->country, $bank->swift_code])->filter()->implode(' · '))
                    : ($bank->routing_number ? 'Routing '.$bank->routing_number : ''),
            ])
            ->all();
    }

    /**
     * @param  array  $local  Already-formatted local Bank rows (see localResults()).
     * @param  BankProviderInterface[]  $providers
     */
    private function merge(array $local, string $query, array $providers): array
    {
        $seen = [];
        $deduped = [];

        $add = function (array $bank) use (&$deduped, &$seen): bool {
            $key = mb_strtolower(trim($bank['name'] ?? ''));

            if ($key === '' || isset($seen[$key])) {
                return true; // skip this one, keep going
            }

            if (count($deduped) >= self::MAX_RESULTS) {
                return false; // cap reached — caller stops
            }

            $seen[$key] = true;
            unset($bank['source']); // internal only — never sent to the browser
            $deduped[] = $bank;

            return true;
        };

        // Local admin entries always win the cap first (see class doc comment).
        foreach ($local as $bank) {
            if (! $add($bank)) {
                return $deduped;
            }
        }

        // Fetch every configured provider's results up front, THEN
        // interleave them round-robin (one result from each provider per
        // round) rather than draining one provider's whole result list
        // before moving to the next — see class doc comment for why.
        $providerResults = [];

        foreach ($providers as $provider) {
            if ($provider->isConfigured()) {
                $providerResults[] = array_values($provider->search($query));
            }
        }

        $maxCount = $providerResults === [] ? 0 : max(array_map('count', $providerResults));

        for ($i = 0; $i < $maxCount; $i++) {
            foreach ($providerResults as $results) {
                if (! array_key_exists($i, $results)) {
                    continue;
                }

                if (! $add($results[$i])) {
                    return $deduped;
                }
            }
        }

        return $deduped;
    }
}
