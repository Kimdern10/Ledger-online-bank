<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Services\BankProviders\ProviderBankBrowser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

/**
 * Admin CRUD for the hand-curated half of the bank directory (see
 * App\Models\Bank). Every bank created here shows up for customers in two
 * places: type=external ones in the "Another bank" picker on Send Money,
 * type=international ones in the "International bank" tab — and both
 * together on the read-only Settings > Banks page (see BankListController).
 *
 * As of App\Services\BankProviders, the Send Money pickers ALSO merge in
 * live results from third-party APIs (Plaid for domestic, TrueLayer/
 * Token.io/Open Payments for international — see
 * BankDirectoryController::merge()) whenever those are configured. This
 * admin list stays the reliable fallback either way: it's what a customer
 * sees even with zero live-API credentials configured, and it's the only
 * way to add a bank none of those APIs happen to cover.
 */
class AdminBankController extends Controller
{
    public function index(Request $request): View
    {
        $type = $request->query('type');
        $search = trim((string) $request->query('q', ''));
        $status = (string) $request->query('status', '');

        $query = Bank::query()->latest();

        if (in_array($type, ['external', 'international'], true)) {
            $query->where('type', $type);
        }

        // Name, country or either code — one box, like the Users page.
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%'.$search.'%')
                    ->orWhere('country', 'like', '%'.$search.'%')
                    ->orWhere('routing_number', 'like', '%'.$search.'%')
                    ->orWhere('swift_code', 'like', '%'.$search.'%');
            });
        }

        if ($status === 'active' || $status === 'inactive') {
            $query->where('is_active', $status === 'active');
        }

        $banks = $query->paginate(15)->withQueryString();

        return view('admin.banks', [
            'banks' => $banks,
            'type' => $type,
            'search' => $search,
            'status' => $status,
            'externalCount' => Bank::ofType('external')->count(),
            'internationalCount' => Bank::ofType('international')->count(),
        ]);
    }

    /**
     * Admin > Banks > Provider banks — every bank each configured provider
     * offers, searchable and paginated (see ProviderBankBrowser). Read-only:
     * nothing here changes what customers see until a bank is added to the
     * directory with import().
     */
    public function providerBanks(Request $request, ProviderBankBrowser $browser): View
    {
        $sources = $browser->sources();
        $source = (string) $request->query('source', 'plaid');
        $source = isset($sources[$source]) ? $source : array_key_first($sources);
        $search = trim(mb_substr((string) $request->query('q', ''), 0, 100));

        // Only a country this source actually has banks in is honoured.
        $countries = $browser->countries($source);
        $country = strtoupper((string) $request->query('country', ''));
        $country = isset($countries[$country]) ? $country : null;

        $page = max(1, (int) $request->query('page', 1));
        $result = $browser->page($source, $search, $page, 25, $country);
        $banks = new LengthAwarePaginator($result['items'], $result['total'], 25, $page, [
            'path' => route('admin.banks.providers'),
            'pageName' => 'page',
        ]);
        $banks->appends(array_filter(['source' => $source, 'q' => $search, 'country' => $country]));

        // Which of this page's banks are already in the directory, so the
        // row shows "In directory" instead of an Add button.
        $inDirectory = Bank::query()
            ->whereIn('name', collect($banks->items())->pluck('name'))
            ->get(['name', 'type'])
            ->map(fn (Bank $bank) => $bank->type.'|'.mb_strtolower($bank->name))
            ->flip();

        return view('admin.banks-providers', [
            'sources' => collect($sources)->map(fn (array $entry) => [
                'label' => $entry['label'],
                'configured' => $entry['provider']->isConfigured(),
            ]),
            'source' => $source,
            'search' => $search,
            'countries' => $countries,
            'country' => $country,
            'banks' => $banks,
            'failed' => $result['failed'],
            'configured' => $sources[$source]['provider']->isConfigured(),
            'inDirectory' => $inDirectory,
        ]);
    }

    /**
     * The "Add to directory" button on Provider banks — copies one bank in
     * as an active directory entry. A bank already there (same name and
     * type) is left as it is rather than duplicated.
     */
    public function import(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'type' => ['required', 'in:external,international'],
            'country' => ['nullable', 'string', 'max:100'],
            'routing_number' => ['nullable', 'string', 'max:20'],
            'swift_code' => ['nullable', 'string', 'max:20'],
            'currency' => ['nullable', 'string', 'max:3'],
        ]);

        $bank = Bank::firstOrCreate(
            ['name' => $data['name'], 'type' => $data['type']],
            $data + ['is_active' => true]
        );

        if ($bank->wasRecentlyCreated) {
            $this->clearBankSearchCache();
        }

        return back()->with('status', $bank->wasRecentlyCreated
            ? $bank->name.' added to the directory.'
            : $bank->name.' is already in the directory.');
    }

    public function create(): View
    {
        return view('admin.banks-form', ['bank' => new Bank(['type' => 'external', 'is_active' => true])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        Bank::create($data);
        $this->clearBankSearchCache();

        return redirect()->route('admin.banks')->with('status', 'Bank added to the directory.');
    }

    public function edit(Bank $bank): View
    {
        return view('admin.banks-form', ['bank' => $bank]);
    }

    public function update(Request $request, Bank $bank): RedirectResponse
    {
        $data = $this->validated($request);

        $bank->update($data);
        $this->clearBankSearchCache();

        return redirect()->route('admin.banks')->with('status', 'Bank updated.');
    }

    public function destroy(Bank $bank): RedirectResponse
    {
        $bank->delete();
        $this->clearBankSearchCache();

        return redirect()->route('admin.banks')->with('status', 'Bank removed from the directory.');
    }

    /**
     * A soft alternative to deleting — a bank a customer already sent
     * money to/from stays on their receipt (those store the name as plain
     * text, not a foreign key to this table) even after it's deactivated
     * here; deactivating just stops it from showing up as a pickable
     * option for new transfers.
     */
    public function toggle(Bank $bank): RedirectResponse
    {
        $bank->update(['is_active' => ! $bank->is_active]);
        $this->clearBankSearchCache();

        return redirect()->route('admin.banks')
            ->with('status', $bank->is_active ? 'Bank activated.' : 'Bank deactivated.');
    }

    /**
     * BankDirectoryController::search()/searchInternational() cache their
     * merged (local + live-API) results per exact search query text for 5
     * minutes — see that class's doc comment. That cache key space is
     * unbounded (one key per distinct query string ever searched), so
     * there's no way to invalidate just the affected key(s) when a bank
     * changes here. Confirmed live: without this, a bank an admin just
     * deactivated or deleted kept appearing in the picker for up to 5
     * minutes, because the *previous* (still-valid-looking) cached result
     * for that search term was served as-is. A full flush is safe here —
     * this app's cache store (CACHE_STORE=database) is separate from its
     * sessions and queue tables, so this never logs anyone out or drops a
     * queued job; it only means the next search (and the next live-API
     * call) does a fresh lookup instead of reading a cached one.
     */
    private function clearBankSearchCache(): void
    {
        Cache::flush();
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'type' => ['required', 'in:external,international'],
            'country' => ['nullable', 'string', 'max:100'],
            'routing_number' => ['nullable', 'string', 'max:20'],
            'swift_code' => ['nullable', 'string', 'max:20'],
            'currency' => ['nullable', 'string', 'max:3'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
