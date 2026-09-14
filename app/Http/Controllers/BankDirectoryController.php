<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Backs the "Select a bank" pickers on the Send Money page — the "Another
 * bank" tab's domestic picker and the "International bank" tab's picker.
 * Both search the same admin-managed Bank directory (see App\Models\Bank),
 * filtered by type — there's no live third-party "list every bank" API
 * behind this any more. That's a deliberate choice, not a missing feature:
 * no free/feasible API actually covers "every domestic AND international
 * bank", so admins curate this list by hand instead (see
 * AdminBankController) and it's what customers see here, on the read-only
 * Settings > Banks page (see BankListController), and nowhere else.
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
        return response()->json(['banks' => $this->searchDirectory($request, 'external')]);
    }

    /**
     * "International bank" tab — international entries only.
     */
    public function searchInternational(Request $request): JsonResponse
    {
        return response()->json(['banks' => $this->searchDirectory($request, 'international')]);
    }

    private function searchDirectory(Request $request, string $type): array
    {
        $query = trim((string) $request->query('q', ''));

        $banks = Bank::query()->active()->ofType($type);

        if ($query !== '') {
            $banks->where('name', 'like', '%'.$query.'%');
        }

        return $banks->orderBy('name')
            ->limit(self::MAX_RESULTS)
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
}
