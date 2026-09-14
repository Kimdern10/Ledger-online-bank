<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AvatarController extends Controller
{
    /**
     * Streams a user's profile picture — the selfie from their approved KYC
     * submission (see AdminKycController::approve(), the only place
     * profile_picture_path ever gets set). Kept on the private "local"
     * disk rather than "public" — it's the same photo used to verify a
     * government ID, so it's served through this authenticated route
     * instead of a guessable /storage/... URL. Viewable by the user
     * themselves (wherever their own avatar shows up) or by any admin (the
     * per-user admin page) — nobody else. See User::avatar(), which is
     * what actually builds this URL for every view that shows one.
     */
    public function show(Request $request, User $user): StreamedResponse
    {
        $viewer = $request->user();

        abort_unless($viewer && ($viewer->id === $user->id || $viewer->isAdmin()), 403);
        abort_unless($user->profile_picture_path, 404);
        abort_unless(Storage::disk('local')->exists($user->profile_picture_path), 404);

        return Storage::disk('local')->response($user->profile_picture_path);
    }
}
