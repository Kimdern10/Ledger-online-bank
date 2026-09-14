<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\CardRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CardController extends Controller
{
    /**
     * The customer's own (non-closed) cards, plus whatever their most
     * recent card request looks like — a brand-new customer now sees an
     * empty carousel and a "Need a card?" panel instead of getting cards
     * for free. See CardRequest and AdminCardController::approve(), which
     * is the only place a Card actually gets created for a customer
     * (besides CardController::replace(), which is the customer replacing
     * one they already own).
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $cards = Card::where('user_id', $user->id)
            ->where('status', '!=', 'closed')
            ->with('transactions')
            ->oldest()
            ->get();

        $latestRequest = CardRequest::where('user_id', $user->id)->latest()->first();

        return view('cards', ['cards' => $cards, 'latestRequest' => $latestRequest]);
    }

    /**
     * Backs the "Need a card?" panel — this only ever creates a
     * CardRequest, never a real Card. Blocked while a request is already
     * pending so the admin queue doesn't fill up with duplicates from one
     * customer double-clicking.
     */
    public function requestCard(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'type' => ['required', 'in:physical,virtual'],
        ]);

        $hasPending = CardRequest::where('user_id', $user->id)->where('status', 'pending')->exists();

        if ($hasPending) {
            return redirect()->route('card')->with('status', 'You already have a card request awaiting approval.');
        }

        CardRequest::create([
            'user_id' => $user->id,
            'type' => $data['type'],
        ]);

        return redirect()->route('card')->with('status', 'Your card request has been sent to our team for approval.');
    }

    public function toggleFreeze(Request $request, Card $card): JsonResponse
    {
        $this->authorizeCard($request, $card);

        if ($card->isClosed()) {
            return response()->json(['error' => 'This card is closed.'], 422);
        }

        $card->update(['status' => $card->isFrozen() ? 'active' : 'frozen']);

        return response()->json(['status' => $card->status, 'badge' => $card->badgeLabel()]);
    }

    /**
     * Changing the PIN requires the current one — unless the card
     * somehow doesn't have one yet — exactly like changing a password
     * requires the old password.
     */
    public function updatePin(Request $request, Card $card): JsonResponse
    {
        $this->authorizeCard($request, $card);

        $data = $request->validate([
            'current_pin' => ['nullable', 'digits:4'],
            'pin' => ['required', 'digits:4', 'confirmed'],
        ]);

        if ($card->pin && $data['current_pin'] !== $card->pin) {
            return response()->json(['error' => "That current PIN isn't right."], 422);
        }

        $card->update(['pin' => $data['pin']]);

        return response()->json(['ok' => true]);
    }

    public function updateLimit(Request $request, Card $card): JsonResponse
    {
        $this->authorizeCard($request, $card);

        $data = $request->validate([
            'limit' => ['nullable', 'numeric', 'min:0', 'max:1000000'],
        ]);

        $card->update(['spending_limit' => $data['limit'] ?? null]);

        return response()->json(['limit_label' => $card->fresh()->limitLabel()]);
    }

    public function toggleContactless(Request $request, Card $card): JsonResponse
    {
        $this->authorizeCard($request, $card);

        $card->update(['contactless_enabled' => ! $card->contactless_enabled]);

        return response()->json(['enabled' => $card->contactless_enabled]);
    }

    public function toggleOnlinePayments(Request $request, Card $card): JsonResponse
    {
        $this->authorizeCard($request, $card);

        $card->update(['online_payments_enabled' => ! $card->online_payments_enabled]);

        return response()->json(['enabled' => $card->online_payments_enabled]);
    }

    /**
     * Freezes the card and flags it (with a timestamp, so admin's
     * "Reported cards" list can be sorted by when it happened) rather than
     * closing it outright — the customer still needs "Replace" to
     * actually get a new card number.
     */
    public function reportLostOrStolen(Request $request, Card $card): RedirectResponse
    {
        $this->authorizeCard($request, $card);

        $card->update([
            'status' => 'frozen',
            'reported_lost_stolen' => true,
            'reported_at' => now(),
        ]);

        return redirect()->route('card')
            ->with('status', 'Card reported and frozen. Use Replace when you\'re ready for a new card number.');
    }

    public function close(Request $request, Card $card): RedirectResponse
    {
        $this->authorizeCard($request, $card);

        $card->update([
            'status' => 'closed',
            'closed_at' => now(),
        ]);

        return redirect()->route('card')->with('status', 'Card closed.');
    }

    /**
     * Closes the current card and issues a brand-new one of the same type,
     * carrying over its spending limit and contactless/online-payments
     * settings — exactly what "Replace" means on a real card (new number,
     * same preferences). The replacement starts with no activity of its
     * own, since it's a fresh piece of plastic. This is the one place a
     * customer can still get a new Card without going through admin
     * approval — they already own the card being replaced, so there's
     * nothing to approve.
     */
    public function replace(Request $request, Card $card): RedirectResponse
    {
        $this->authorizeCard($request, $card);

        $card->update(['status' => 'closed', 'closed_at' => now()]);

        Card::issueFor($request->user(), $card->type, [
            'spending_limit' => $card->spending_limit,
            'contactless_enabled' => $card->contactless_enabled,
            'online_payments_enabled' => $card->online_payments_enabled,
        ]);

        return redirect()->route('card')->with('status', 'Your new card is ready.');
    }

    private function authorizeCard(Request $request, Card $card): void
    {
        abort_unless($card->user_id === $request->user()->id, 404);
    }
}
