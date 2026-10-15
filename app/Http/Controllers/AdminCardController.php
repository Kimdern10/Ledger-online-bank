<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\FiltersAdminDecisions;
use App\Models\Card;
use App\Models\CardRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminCardController extends Controller
{
    use FiltersAdminDecisions;

    /**
     * Pending requests first (oldest first, so the longest-waiting
     * customer is seen first), then already-decided ones most-recent
     * first — same "needs attention first" idea as the support inbox.
     */
    public function requests(Request $request): View
    {
        $search = $this->decisionSearch($request);
        $decision = $this->decisionFilter($request, ['approved', 'declined']);

        $pending = CardRequest::with('user')->where('status', 'pending')->oldest()
            ->paginate(15, ['*'], 'pending_page')
            ->withQueryString();

        $decided = CardRequest::with(['user', 'reviewer', 'card'])
            ->where('status', '!=', 'pending')
            ->when($decision, fn ($query) => $query->where('status', $decision))
            ->tap(fn ($query) => $this->whereCustomerMatches($query, $search))
            ->latest('reviewed_at')
            ->paginate(15, ['*'], 'decided_page')
            ->withQueryString();

        return view('admin.card-requests', [
            'pending' => $pending,
            'decided' => $decided,
            'search' => $search,
            'decision' => $decision,
        ]);
    }

    /**
     * The only place (besides a customer replacing their own card) that a
     * real Card gets created — see Card::issueFor().
     */
    public function approve(Request $request, CardRequest $cardRequest): RedirectResponse
    {
        abort_unless($cardRequest->isPending(), 422);

        $card = Card::issueFor($cardRequest->user, $cardRequest->type);

        $cardRequest->update([
            'status' => 'approved',
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
            'card_id' => $card->id,
        ]);

        return redirect()->route('admin.card-requests')
            ->with('status', 'Card approved and issued to '.$cardRequest->user->name.'.');
    }

    public function decline(Request $request, CardRequest $cardRequest): RedirectResponse
    {
        abort_unless($cardRequest->isPending(), 422);

        $data = $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $cardRequest->update([
            'status' => 'declined',
            'decline_reason' => $data['reason'] ?? null,
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        return redirect()->route('admin.card-requests')->with('status', 'Card request declined.');
    }

    /**
     * Every card, across every customer, that's ever been reported lost or
     * stolen — most recently reported first — so admin has one place to
     * follow up instead of having to happen across it on a customer's own
     * page.
     */
    public function reported(Request $request): View
    {
        $search = $this->decisionSearch($request);
        $status = $this->decisionFilter($request, ['frozen', 'closed'], 'status');

        $cards = Card::with('user')
            ->where('reported_lost_stolen', true)
            ->when($status, fn ($query) => $query->where('status', $status))
            // Digits only (like "4471") look for the card's last digits;
            // anything else searches the customer's name and email.
            ->when(ctype_digit($search),
                fn ($query) => $query->where('card_number', 'like', '%'.$search),
                fn ($query) => $this->whereCustomerMatches($query, $search))
            ->latest('reported_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.cards-reported', [
            'cards' => $cards,
            'search' => $search,
            'status' => $status,
        ]);
    }
}
