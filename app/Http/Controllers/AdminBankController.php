<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

        $query = Bank::query()->latest();

        if (in_array($type, ['external', 'international'], true)) {
            $query->where('type', $type);
        }

        $banks = $query->paginate(15)->withQueryString();

        return view('admin.banks', [
            'banks' => $banks,
            'type' => $type,
            'externalCount' => Bank::ofType('external')->count(),
            'internationalCount' => Bank::ofType('international')->count(),
        ]);
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
