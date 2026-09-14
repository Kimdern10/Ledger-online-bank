<?php

namespace App\Http\Controllers;

use App\Models\GuestSupportConversation;
use App\Models\GuestSupportMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

/**
 * The admin side of the floating-widget guest chat — see
 * GuestSupportController for the anonymous-visitor side and
 * guest_support_conversations' migration for why guests live in their own
 * tables instead of alongside logged-in customers. Deliberately simpler
 * than AdminSupportController in one respect: no SSE stream, poll only —
 * everything else (typing indicator, photo attachments from the guest)
 * mirrors it.
 */
class AdminGuestSupportController extends Controller
{
    /**
     * Every guest conversation, most recently active first. Unlike the
     * logged-in support inbox there's no "one thread per customer" to
     * collapse down to — a guest has no account to tie repeat visits
     * together beyond the conversation itself, so every conversation just
     * shows on its own.
     */
    public function index(): View
    {
        // Computed from the full table, not just the current page, so the
        // "Open now" tile stays accurate no matter which page is showing.
        $openCount = GuestSupportConversation::where('status', 'open')->count();

        $conversations = GuestSupportConversation::withCount(['messages as unread_count' => function ($query) {
            $query->whereNull('read_at')->whereNull('sender_id');
        }])
            ->with(['messages' => fn ($q) => $q->latest()->limit(1)])
            ->latest('updated_at')
            ->paginate(20);

        return view('admin.support-guests', ['conversations' => $conversations, 'openCount' => $openCount]);
    }

    public function show(GuestSupportConversation $conversation): View
    {
        $messages = $conversation->messages()->orderBy('created_at')->get();

        $conversation->messages()
            ->whereNull('sender_id')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return view('admin.support-guest-show', [
            'conversation' => $conversation,
            'messages' => $messages,
        ]);
    }

    /**
     * An admin's reply — text, a photo, or both, sent as multipart/form-data
     * the same way AdminSupportController::store() sends replies to
     * logged-in customers. Guests could already attach photos; this brings
     * admins to parity so either side can send one.
     */
    public function store(Request $request, GuestSupportConversation $conversation): JsonResponse
    {
        $data = $request->validate([
            'body' => ['nullable', 'string', 'max:2000'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,webp,pdf', 'max:5120'],
        ]);

        if (empty($data['body']) && ! $request->hasFile('attachment')) {
            return response()->json(['error' => 'Type a message or attach a photo.'], 422);
        }

        if (! $conversation->isOpen()) {
            return response()->json([
                'error' => "This conversation has ended and can't take new replies.",
            ], 422);
        }

        $path = $name = $mime = null;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $path = $file->store('support-attachments', 'public');
            $name = $file->getClientOriginalName();
            $mime = $file->getMimeType();
        }

        $message = $conversation->messages()->create([
            'sender_id' => $request->user()->id,
            'body' => $data['body'] ?? '',
            'attachment_path' => $path,
            'attachment_name' => $name,
            'attachment_mime' => $mime,
        ]);

        Cache::forget('guest-support-typing-admin-'.$conversation->id);

        return response()->json($this->messagePayload($message));
    }

    /**
     * Pinged on every keystroke in the reply box (throttled client-side to
     * once every 2.5s — see admin/support-guest-show.blade.php's
     * pingTyping()), same mechanism as AdminSupportController::typing()
     * for the logged-in version. The guest's poll() picks this up as
     * admin_typing and shows the animated dots in the widget.
     */
    public function typing(GuestSupportConversation $conversation): JsonResponse
    {
        Cache::put('guest-support-typing-admin-'.$conversation->id, true, now()->addSeconds(6));

        return response()->json(['ok' => true]);
    }

    public function poll(Request $request, GuestSupportConversation $conversation): JsonResponse
    {
        $afterId = (int) $request->query('after', 0);

        $messages = $conversation->messages()
            ->where('id', '>', $afterId)
            ->orderBy('created_at')
            ->get();

        $conversation->messages()
            ->whereNull('sender_id')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([
            'messages' => $messages->map(fn ($m) => $this->messagePayload($m)),
            'conversation_open' => $conversation->isOpen(),
        ]);
    }

    public function end(GuestSupportConversation $conversation): RedirectResponse
    {
        if ($conversation->isOpen()) {
            $conversation->close('admin');
        }

        return redirect()->route('admin.support.guests.show', $conversation)
            ->with('status', 'Conversation ended.');
    }

    private function messagePayload(GuestSupportMessage $m): array
    {
        return [
            'id' => $m->id,
            'body' => $m->body,
            'is_from_admin' => $m->isFromAdmin(),
            'time' => $m->created_at->format('g:i A'),
            'attachment_url' => $m->attachmentUrl(),
            'attachment_name' => $m->attachment_name,
            'is_image' => $m->isImageAttachment(),
        ];
    }
}
