<?php

namespace App\Http\Controllers;

use App\Models\AddressVerification;
use App\Models\AppNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminAddressController extends Controller
{
    /**
     * Same "pending oldest-first, decided most-recent-first" shape
     * AdminKycController::requests() uses.
     */
    public function requests(): View
    {
        $pending = AddressVerification::with('user')->where('status', 'pending')->oldest()
            ->paginate(15, ['*'], 'pending_page');

        $decided = AddressVerification::with(['user', 'reviewer'])
            ->where('status', '!=', 'pending')
            ->latest('reviewed_at')
            ->paginate(15, ['*'], 'decided_page');

        return view('admin-address', ['pending' => $pending, 'decided' => $decided]);
    }

    /**
     * Streams the proof-of-address document so an admin can look it over —
     * same authorization-gated private-disk streaming pattern
     * AdminKycController::image() uses.
     */
    public function image(AddressVerification $addressVerification): StreamedResponse
    {
        $path = $addressVerification->document_path;

        abort_unless($path && Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->response($path);
    }

    /**
     * Approving raises the account to Tier 3 — App\Support\AccountTier is
     * what actually turns that into a higher daily limit, via
     * User::tier()/tierDailyLimit().
     */
    public function approve(Request $request, AddressVerification $addressVerification): RedirectResponse
    {
        abort_unless($addressVerification->isPending(), 422);

        $addressVerification->update([
            'status' => 'approved',
            'rejection_reason' => null,
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        $user = $addressVerification->user;
        $user->address_status = 'approved';
        $user->save();

        AppNotification::notify(
            $user,
            'Address verified',
            "You're fully verified — your daily limit just went up.",
            route('setting'),
        );

        return redirect()->route('admin.address')
            ->with('status', $user->name."'s address verification was approved.");
    }

    /**
     * Declining leaves address_status as 'rejected' and lets the user
     * resubmit right away from Settings — same "not a dead end" reasoning
     * AdminKycController::decline() uses.
     */
    public function decline(Request $request, AddressVerification $addressVerification): RedirectResponse
    {
        abort_unless($addressVerification->isPending(), 422);

        $data = $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $addressVerification->update([
            'status' => 'rejected',
            'rejection_reason' => $data['reason'] ?? null,
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        $user = $addressVerification->user;
        $user->address_status = 'rejected';
        $user->save();

        AppNotification::notify(
            $user,
            'Address verification needs another look',
            $data['reason'] ?? 'Your submitted document could not be verified. Please try again from Settings.',
            route('address-verification.create'),
        );

        return redirect()->route('admin.address')->with('status', 'Address verification declined for '.$user->name.'.');
    }
}
