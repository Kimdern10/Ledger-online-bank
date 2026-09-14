<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Polled every ~20s by the dashboard bell (see dashboard.blade.php) —
     * same "slow safety-net poll" pattern SupportController::poll() already
     * uses for the support chat. Returns the 10 most recent notifications
     * plus how many of THOSE are unread, so the bell's badge count and its
     * dropdown list both refresh from one request.
     */
    public function poll(Request $request): JsonResponse
    {
        $notifications = $request->user()->appNotifications()->latest()->take(10)->get();

        return response()->json([
            'unread_count' => $notifications->whereNull('read_at')->count(),
            'notifications' => $notifications->map(fn (AppNotification $n) => [
                'id' => $n->id,
                'title' => $n->title,
                'body' => $n->body,
                'url' => $n->url,
                'unread' => is_null($n->read_at),
                'time' => $n->created_at->diffForHumans(),
            ])->values(),
        ]);
    }

    /**
     * Marks one notification read — fired when it's clicked in the bell
     * dropdown, right before following its url (if it has one). Checks
     * ownership itself rather than trusting the dropdown only ever shows a
     * user their own notifications, since the route is reachable directly.
     */
    public function markRead(Request $request, AppNotification $notification): RedirectResponse|JsonResponse
    {
        abort_unless($notification->user_id === $request->user()->id, 404);

        $notification->markAsRead();

        return $request->wantsJson() ? response()->json(['status' => 'ok']) : back();
    }

    /**
     * Backs "Mark all as read" at the bottom of the bell dropdown.
     */
    public function markAllRead(Request $request): RedirectResponse|JsonResponse
    {
        $request->user()->appNotifications()->unread()->update(['read_at' => now()]);

        return $request->wantsJson() ? response()->json(['status' => 'ok']) : back();
    }

    /**
     * Backs "Clear all" in the bell dropdown — unlike markAllRead(), this
     * actually deletes the user's notifications rather than just marking
     * them read, so the list (and the badge) go empty.
     */
    public function clearAll(Request $request): RedirectResponse|JsonResponse
    {
        $request->user()->appNotifications()->delete();

        return $request->wantsJson() ? response()->json(['status' => 'ok']) : back();
    }
}
