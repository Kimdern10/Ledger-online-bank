<?php

namespace App\Http\Controllers;

use App\Models\SupportConversation;
use App\Models\SupportMessage;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminSupportController extends Controller
{
    /**
     * One row per customer who has ever messaged in, showing their most
     * recent conversation — open or closed — most recently active first.
     * "Needs a reply" (real_admin_reply_count === 0) flags any conversation
     * no real human admin has answered in yet.
     */
    public function index(Request $request): View
    {
        $latestIds = SupportConversation::selectRaw('MAX(id) as id')
            ->groupBy('user_id')
            ->pluck('id');

        $conversations = SupportConversation::with('user')
            ->withCount(['messages as unread_count' => function ($query) {
                $query->whereNull('read_at')->whereColumn('sender_id', 'user_id');
            }])
            ->withCount(['messages as real_admin_reply_count' => function ($query) {
                $query->where('is_bot', false)->whereColumn('sender_id', '!=', 'user_id');
            }])
            ->with(['messages' => function ($query) {
                $query->latest()->limit(1);
            }])
            ->whereIn('id', $latestIds)
            ->get()
            ->each(fn ($conversation) => $conversation->applyTimeoutIfExpired())
            ->sortByDesc(fn ($conversation) => optional($conversation->messages->first())->created_at)
            ->values();

        // Computed from the FULL list before paginating below — these are
        // summary stats across every conversation, not just whichever page
        // is currently showing.
        $openCount = $conversations->filter->isOpen()->count();
        $needsReplyCount = $conversations->filter(fn ($c) => $c->isOpen() && $c->real_admin_reply_count === 0)->count();

        // Sorted by each conversation's own latest-message timestamp, which
        // only exists once the messages relation is loaded — there's no
        // single column to ORDER BY at the database level, so pagination
        // has to happen on the already-sorted in-memory collection instead
        // of a real ->paginate() call. Same pattern used for the merged
        // History feed (see HistoryController::index()).
        $perPage = 20;
        $page = (int) $request->query('page', 1);

        $paginatedConversations = new \Illuminate\Pagination\LengthAwarePaginator(
            $conversations->forPage($page, $perPage)->values(),
            $conversations->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()],
        );

        return view('admin.support', [
            'conversations' => $paginatedConversations,
            'openCount' => $openCount,
            'needsReplyCount' => $needsReplyCount,
        ]);
    }

    public function show(User $user): View
    {
        abort_if($user->isAdmin(), 404);

        $conversation = SupportConversation::where('user_id', $user->id)->latest()->first();
        $conversation?->applyTimeoutIfExpired();

        return $this->renderThread($user, $conversation, isLatest: true);
    }

    public function showSession(User $user, SupportConversation $conversation): View
    {
        abort_if($user->isAdmin(), 404);
        abort_unless($conversation->user_id === $user->id, 404);

        return $this->renderThread($user, $conversation, isLatest: false);
    }

    private function renderThread(User $user, ?SupportConversation $conversation, bool $isLatest): View
    {
        $messages = collect();

        if ($conversation) {
            $messages = $conversation->messages()->orderBy('created_at')->get();

            $conversation->messages()
                ->where('sender_id', $user->id)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        }

        $earlierCount = SupportConversation::where('user_id', $user->id)
            ->when($conversation, fn ($q) => $q->where('id', '!=', $conversation->id))
            ->count();

        return view('admin.support-show', [
            'customer' => $user,
            'conversation' => $conversation,
            'messages' => $messages,
            'earlierCount' => $earlierCount,
            'isLatest' => $isLatest,
        ]);
    }

    public function history(User $user): View
    {
        abort_if($user->isAdmin(), 404);

        $conversations = SupportConversation::where('user_id', $user->id)
            ->withCount('messages')
            ->with(['messages' => fn ($q) => $q->latest()->limit(1)])
            ->latest()
            ->get();

        return view('admin.support-history', [
            'customer' => $user,
            'conversations' => $conversations,
        ]);
    }

    /**
     * An admin's reply — text, an attachment, or both, sent as
     * multipart/form-data the same way the customer side now sends
     * messages (see SupportController::store()). sender_id is the admin
     * actually typing this, not the customer.
     */
    public function store(Request $request, User $user): JsonResponse
    {
        abort_if($user->isAdmin(), 404);

        $data = $request->validate([
            'body' => ['nullable', 'string', 'max:2000'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,webp,pdf', 'max:5120'],
        ]);

        if (empty($data['body']) && ! $request->hasFile('attachment')) {
            return response()->json(['error' => 'Type a message or attach a file.'], 422);
        }

        $conversation = SupportConversation::where('user_id', $user->id)->latest()->first();
        $conversation?->applyTimeoutIfExpired();

        if (! $conversation || ! $conversation->isOpen()) {
            return response()->json([
                'error' => 'This conversation has ended and can\'t take new replies.',
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
            'user_id' => $user->id,
            'sender_id' => $request->user()->id,
            'body' => $data['body'] ?? '',
            'attachment_path' => $path,
            'attachment_name' => $name,
            'attachment_mime' => $mime,
        ]);

        Cache::forget('support-typing-admin-'.$user->id);

        return response()->json([
            'id' => $message->id,
            'time' => $message->created_at->format('g:i A'),
            'attachment_url' => $message->attachmentUrl(),
            'attachment_name' => $message->attachment_name,
            'is_image' => $message->isImageAttachment(),
        ]);
    }

    public function typing(User $user): JsonResponse
    {
        abort_if($user->isAdmin(), 404);

        Cache::put('support-typing-admin-'.$user->id, true, now()->addSeconds(6));

        return response()->json(['ok' => true]);
    }

    /**
     * Slow safety-net poll — see SupportController::poll() for why this
     * stays alongside stream() below rather than being replaced by it.
     */
    public function poll(Request $request, User $user): JsonResponse
    {
        abort_if($user->isAdmin(), 404);

        $afterId = (int) $request->query('after', 0);

        $conversation = SupportConversation::where('user_id', $user->id)->latest()->first();
        $justClosed = $conversation?->applyTimeoutIfExpired() ?? false;

        $messages = collect();

        if ($conversation) {
            $messages = $conversation->messages()
                ->where('id', '>', $afterId)
                ->orderBy('created_at')
                ->get();

            $conversation->messages()
                ->where('sender_id', $user->id)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        }

        return response()->json([
            'messages' => $messages->map(fn ($m) => $this->messagePayload($m)),
            'conversation_open' => $conversation?->isOpen() ?? false,
            'just_closed' => $justClosed,
        ]);
    }

    /**
     * Server-Sent Events for the admin thread page — pushes the
     * customer's new messages and the conversation's open/closed status
     * roughly once a second, the same mechanism (and same reasoning: no
     * websocket server to install/run) as SupportController::stream() on
     * the customer's own side.
     */
    public function stream(Request $request, User $user): StreamedResponse
    {
        abort_if($user->isAdmin(), 404);

        $seenId = max((int) $request->query('after', 0), (int) $request->header('Last-Event-ID', 0));

        return response()->stream(function () use ($user, $seenId) {
            while (ob_get_level() > 0) {
                ob_end_flush();
            }

            set_time_limit(30);
            $deadline = time() + 25;

            while (time() < $deadline) {
                if (connection_aborted()) {
                    break;
                }

                $conversation = SupportConversation::where('user_id', $user->id)->latest()->first();
                $justClosed = $conversation?->applyTimeoutIfExpired() ?? false;
                $open = $conversation?->isOpen() ?? false;

                if ($conversation) {
                    $newMessages = $conversation->messages()
                        ->where('id', '>', $seenId)
                        ->orderBy('created_at')
                        ->get();

                    if ($newMessages->isNotEmpty()) {
                        $conversation->messages()
                            ->where('sender_id', $user->id)
                            ->whereNull('read_at')
                            ->update(['read_at' => now()]);

                        foreach ($newMessages as $m) {
                            $seenId = $m->id;
                            echo "id: {$m->id}\n";
                            echo "event: message\n";
                            echo 'data: '.json_encode($this->messagePayload($m))."\n\n";
                        }
                    }
                }

                echo "event: status\n";
                echo 'data: '.json_encode(['open' => $open, 'just_closed' => $justClosed])."\n\n";

                flush();

                if (! $open) {
                    break;
                }

                sleep(1);
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
            'Connection' => 'keep-alive',
        ]);
    }

    private function messagePayload(SupportMessage $m): array
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

    /**
     * Ends the customer's current conversation and immediately opens a
     * fresh, empty one for them — the same thing that happens when a
     * customer ends their own conversation (see
     * SupportController::startNew()). The closed conversation isn't
     * deleted: it stays fully visible to the customer (and to admin, via
     * history()/showSession() above) as past conversation history. This
     * just means the customer's live chat view starts clean the moment an
     * admin ends it, instead of sitting on a closed thread until the
     * customer happens to send a new message.
     */
    public function end(User $user): RedirectResponse
    {
        abort_if($user->isAdmin(), 404);

        $conversation = SupportConversation::where('user_id', $user->id)->latest()->first();

        if ($conversation && $conversation->isOpen()) {
            $conversation->close('admin');
        }

        SupportConversation::create(['user_id' => $user->id, 'status' => 'open']);

        return redirect()->route('admin.support.show', $user)
            ->with('status', 'Conversation ended.');
    }

    public function setTimer(Request $request, User $user): RedirectResponse
    {
        abort_if($user->isAdmin(), 404);

        $data = $request->validate([
            'hours' => ['required', 'integer', 'in:1,24,72,168'],
        ]);

        $conversation = SupportConversation::where('user_id', $user->id)->latest()->first();

        abort_unless($conversation && $conversation->isOpen(), 422);

        $conversation->update(['timeout_at' => now()->addHours((int) $data['hours'])]);

        return redirect()->route('admin.support.show', $user)
            ->with('status', "This conversation will auto-close if {$user->first_name} doesn't reply in time.");
    }

    /**
     * Flips Normal <-> Urgent on the customer's current conversation.
     * Unlike category (auto-set by the bot, never touched by hand), this
     * one is entirely an admin judgment call.
     */
    public function togglePriority(User $user): RedirectResponse
    {
        abort_if($user->isAdmin(), 404);

        $conversation = SupportConversation::where('user_id', $user->id)->latest()->first();

        abort_unless($conversation, 404);

        $conversation->update(['priority' => $conversation->isUrgent() ? 'normal' : 'urgent']);

        return redirect()->route('admin.support.show', $user);
    }
}
