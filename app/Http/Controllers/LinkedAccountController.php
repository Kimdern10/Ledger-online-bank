<?php

namespace App\Http\Controllers;

use App\Models\LinkedAccount;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LinkedAccountController extends Controller
{
    /**
     * Backs the "Add new" form on link-account.blade.php — either half
     * (bank account or card), picked by the `type` field the segmented
     * control writes into a hidden input before submit.
     *
     * Saved and usable right away (no admin approval step) — this only
     * feeds Withdraw/Top Up's "where to" chips and the admin's read-only
     * view of what a user has linked; it never moves money by itself.
     */
    public function store(Request $request): RedirectResponse
    {
        $type = $request->input('type');

        return $type === 'card'
            ? $this->storeCard($request)
            : $this->storeBank($request);
    }

    private function storeBank(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'bank_name' => ['required', 'string', 'max:150'],
            'account_holder_name' => ['required', 'string', 'max:150'],
            'account_number' => ['required', 'string', 'max:20'],
            'routing_number' => ['required', 'string', 'max:20'],
        ]);

        $user = $request->user();

        $alreadyLinked = LinkedAccount::where('user_id', $user->id)
            ->where('type', 'bank')
            ->where('account_number', $data['account_number'])
            ->exists();

        if ($alreadyLinked) {
            return back()->withInput()->with('linkError', 'That bank account is already linked.');
        }

        LinkedAccount::create([
            'user_id' => $user->id,
            'type' => 'bank',
            'bank_name' => $data['bank_name'],
            'account_holder_name' => $data['account_holder_name'],
            'account_number' => $data['account_number'],
            'routing_number' => $data['routing_number'],
        ]);

        return back()->with('status', $data['bank_name'].' linked successfully.');
    }

    private function storeCard(Request $request): RedirectResponse
    {
        // The card number field displays grouped with spaces ("4242 4242
        // 4242 4242") for readability — strip that before it's validated as
        // a plain digit string, same idea as the amount field elsewhere.
        $request->merge([
            'card_number' => preg_replace('/\s+/', '', (string) $request->input('card_number', '')),
        ]);

        $data = $request->validate([
            'card_number' => ['required', 'regex:/^\d{12,19}$/'],
            'card_name' => ['required', 'string', 'max:150'],
            'card_expiry' => ['required', 'regex:/^(0[1-9]|1[0-2])\/\d{2}$/'],
            // Validated (proves whoever's linking this has the physical
            // card in hand) and then never stored anywhere — see the
            // linked_accounts migration for why there's no cvv column.
            'cvv' => ['required', 'regex:/^\d{3,4}$/'],
        ]);

        $user = $request->user();

        $alreadyLinked = LinkedAccount::where('user_id', $user->id)
            ->where('type', 'card')
            ->where('card_number', $data['card_number'])
            ->exists();

        if ($alreadyLinked) {
            return back()->withInput()->with('linkError', 'That card is already linked.');
        }

        LinkedAccount::create([
            'user_id' => $user->id,
            'type' => 'card',
            'card_number' => $data['card_number'],
            'card_name' => $data['card_name'],
            'card_expiry' => $data['card_expiry'],
        ]);

        return back()->with('status', 'Card linked successfully.');
    }

    /**
     * Backs the "×" remove button on each row of "Already linked". Past
     * withdrawals/top-ups that used this account keep their own record (see
     * the withdrawals/top_ups migrations' nullOnDelete) — only the link
     * itself goes away.
     */
    public function destroy(Request $request, LinkedAccount $linkedAccount): RedirectResponse
    {
        abort_unless($linkedAccount->user_id === $request->user()->id, 404);

        $label = $linkedAccount->displayLabel();
        $linkedAccount->delete();

        return back()->with('status', $label.' removed.');
    }
}
