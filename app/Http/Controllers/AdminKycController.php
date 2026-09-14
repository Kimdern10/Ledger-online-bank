<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\KycVerification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminKycController extends Controller
{
    /**
     * Pending first (oldest first, so the longest-waiting customer is seen
     * first), then already-decided ones most-recent first — same shape as
     * AdminCardController::requests().
     */
    public function requests(): View
    {
        $pending = KycVerification::with('user')->where('status', 'pending')->oldest()
            ->paginate(15, ['*'], 'pending_page');

        $decided = KycVerification::with(['user', 'reviewer'])
            ->where('status', '!=', 'pending')
            ->latest('reviewed_at')
            ->paginate(15, ['*'], 'decided_page');

        return view('admin-kyc', ['pending' => $pending, 'decided' => $decided]);
    }

    /**
     * Streams the ID photo or selfie for one submission so an admin can
     * look at them side by side and compare by eye — there's no
     * third-party face-matching anywhere in this app; this manual look IS
     * the "facial verification". Both files live on the private "local"
     * disk (see KycController::store()), so this admin-only route is the
     * only way either image is ever actually served.
     */
    public function image(KycVerification $kycVerification, string $type): StreamedResponse
    {
        abort_unless(in_array($type, ['id', 'selfie'], true), 404);

        $path = $type === 'id' ? $kycVerification->id_document_path : $kycVerification->selfie_path;

        abort_unless($path && Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->response($path);
    }

    /**
     * Approving unlocks Send Money — EnsureKycApproved only ever checks
     * kyc_status — and reuses the selfie already on file as the account's
     * profile picture, so there's no separate "upload a profile picture"
     * step for the user to do afterward. One upload, two jobs.
     */
    public function approve(Request $request, KycVerification $kycVerification): RedirectResponse
    {
        abort_unless($kycVerification->isPending(), 422);

        $kycVerification->update([
            'status' => 'approved',
            'rejection_reason' => null,
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        $user = $kycVerification->user;
        $user->kyc_status = 'approved';
        $user->profile_picture_path = $kycVerification->selfie_path;
        $user->save();

        AppNotification::notify(
            $user,
            'Identity verified',
            "You're all set — your ID has been verified and Send Money is now unlocked.",
            route('setting.profile'),
        );

        return redirect()->route('admin.kyc')
            ->with('status', $user->name."'s identity verification was approved.");
    }

    /**
     * Declining leaves kyc_status as 'rejected' (still blocking Send Money,
     * same as 'pending' or 'not_submitted' — EnsureKycApproved only lets
     * 'approved' through) and, unlike CardRequest's declined state, isn't a
     * dead end: KycController::create() shows the reason and lets the user
     * resubmit right away.
     */
    public function decline(Request $request, KycVerification $kycVerification): RedirectResponse
    {
        abort_unless($kycVerification->isPending(), 422);

        $data = $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $kycVerification->update([
            'status' => 'rejected',
            'rejection_reason' => $data['reason'] ?? null,
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        $user = $kycVerification->user;
        $user->kyc_status = 'rejected';
        $user->save();

        AppNotification::notify(
            $user,
            'Identity verification needs another look',
            $data['reason'] ?? 'Your submitted ID or selfie could not be verified. Please try again from Settings.',
            route('kyc.create'),
        );

        return redirect()->route('admin.kyc')->with('status', 'Identity verification declined for '.$user->name.'.');
    }
}
