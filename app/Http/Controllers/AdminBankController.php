<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

        return redirect()->route('admin.banks')->with('status', 'Bank updated.');
    }

    public function destroy(Bank $bank): RedirectResponse
    {
        $bank->delete();

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

        return redirect()->route('admin.banks')
            ->with('status', $bank->is_active ? 'Bank activated.' : 'Bank deactivated.');
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
