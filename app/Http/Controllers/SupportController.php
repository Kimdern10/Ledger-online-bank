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

class SupportController extends Controller
{
    /**
     * Canned first-line answers, keyed by a stable category slug — each
     * with a list of keywords/phrases that trigger it. Matching is
     * substring-based (case-insensitive) against whatever the customer
     * actually typed, not an exact match, so free-text like "my card got
     * declined at the store" or "I don't recognize a charge on my
     * account" now finds a specific, relevant reply instead of always
     * falling through to the generic default below. The four original
     * quick-reply buttons ("Card issue", "Payment problem", "Account
     * access", "Something else") still work exactly as before — their
     * exact text is the first keyword in each matching group. The slug
     * itself is also what gets stored as the conversation's "category" —
     * see maybeSendBotReply() below.
     */
    private const TOPIC_RESPONSES = [
        // Checked before the broader "card issue" bucket below on purpose —
        // "someone used my card" or "I don't recognize this charge" should
        // land here, not in the generic card-issue reply, even though both
        // mention a card. Order matters: matchTopic() takes the first
        // matching topic, top to bottom.
        'fraud or dispute' => [
            'keywords' => ['fraud', 'unauthorized', 'dispute', 'scam', "don't recognize", 'do not recognize', 'someone used my', 'stolen my'],
            'response' => "That sounds serious — let's lock this down first. If you haven't already, freeze your card from My Cards right now. Can you tell me the amount and roughly when you noticed it?",
        ],
        'card issue' => [
            'keywords' => ['card issue', 'card', 'debit', 'credit card', 'declined', 'freeze my card', 'block my card'],
            'response' => "Sorry to hear that. Is your card lost/stolen, not working at a terminal, or showing the wrong balance? You can also freeze it instantly from My Cards while we look into it.",
        ],
        'payment problem' => [
            'keywords' => ['payment problem', 'payment', 'transfer', 'send money', 'wire', 'transaction', 'charged twice', 'double charge', 'missing payment', 'failed transfer'],
            'response' => "Got it — can you tell me roughly when the payment happened and whether it was a send, a bill payment, or a card purchase? I'll pull up the details.",
        ],
        'account access' => [
            'keywords' => ['account access', 'password', 'log in', 'login', 'sign in', 'locked out', 'verification code', 'two factor', '2fa', 'otp', 'reset password'],
            'response' => "Let's get you back in. Are you having trouble with your password, a verification code, or a locked account message?",
        ],
        'account status' => [
            'keywords' => ['frozen', 'suspended', 'disabled', 'why is my account', 'locked account', 'restricted'],
            'response' => "I can see account status changes go through our review team. Can you tell me a bit more about what you're seeing on your end, and I'll get this looked at?",
        ],
        'balance or statement' => [
            'keywords' => ['balance', 'statement', 'missing money', "where is my money", 'incorrect balance'],
            'response' => "Let's take a look. Can you tell me which account — checking or savings — and roughly what amount looks off to you?",
        ],
        'bill pay' => [
            'keywords' => ['bill pay', 'autopay', 'auto-pay', 'scheduled payment', 'bill'],
            'response' => "Happy to help with that. Is this about a bill that didn't go through, one you want to change, or setting up a new one?",
        ],
        'something else' => [
            'keywords' => ['something else'],
            'response' => "No problem, just describe what's going on and I'll point you in the right direction.",
        ],
    ];

    /**
     * What the bot says when nothing above matches. There's more than one
     * so a longer back-and-forth (before a real admin joins) doesn't just
     * repeat the identical sentence every time — see maybeSendBotReply(),
     * which cycles through these based on how many bot replies this
     * conversation has already had. Each one still makes clear a real
     * person is coming, which is true: from this point on every message
     * just sits there until an admin opens /admin/support and replies.
     */
    private const DEFAULT_RESPONSES = [
        "Thanks for the details — a support specialist will follow up shortly. In the meantime, is there anything else I can help clarify?",
        "Got it, that's noted for the team. Is there anything else you want to add before someone picks this up?",
        "Thanks for explaining — I've passed this along. Feel free to add any other details while you wait for a reply.",
    ];

    /**
     * The customer's current conversation — their most recent one, whether
     * still open or just closed (including a timeout that only gets
     * noticed right here, via SupportConversation::latestFor()). A
     * brand-new customer with no conversation at all yet gets one created
     * silently, so the page always has something to show.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $conversation = SupportConversation::latestFor($user)
            ?? SupportConversation::create(['user_id' => $user->id, 'status' => 'open']);

        $messages = $conversation->messages()->orderBy('created_at')->get();

        $conversation->messages()
            ->where('sender_id', '!=', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return view('support', [
            'conversation' => $conversation,
            'messages' => $messages,
            'hasHistory' => SupportConversation::where('user_id', $user->id)->where('status', 'closed')->exists(),
            'isHistoryView' => false,
        ]);
    }

    /**
     * Ends the current conversation if one is still open (attributed to
     * the customer themselves — closed_by = 'customer') and starts a
     * brand new, empty one.
     */
    public function startNew(Request $request): RedirectResponse
    {
        $user = $request->user();
        $current = SupportConversation::latestFor($user);

        if ($current && $current->isOpen()) {
            $current->close('customer');
        }

        SupportConversation::create(['user_id' => $user->id, 'status' => 'open']);

        return redirect()->route('support');
    }

    /**
     * One message from the logged-in user to support — text, an
     * attachment, or both (at least one is required). Always sent as
     * multipart/form-data from the browser now, not JSON, since a JSON
     * body can't carry a file. If no real admin has replied in this
     * conversation yet, this also generates the bot's automatic reply
     * right away (see maybeSendBotReply()) and hands it back in the same
     * JSON response.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'body' => ['nullable', 'string', 'max:2000'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,webp,pdf', 'max:5120'],
        ]);

        if (empty($data['body']) && ! $request->hasFile('attachment')) {
            return response()->json(['error' => 'Type a message or attach a file.'], 422);
        }

        $user = $request->user();
        $conversation = SupportConversation::latestFor($user);

        if (! $conversation || ! $conversation->isOpen()) {
            return response()->json([
                'error' => "This conversation has ended — start a new one to keep chatting.",
            ], 422);
        }

        [$path, $name, $mime] = $this->storeAttachment($request);

        $message = $conversation->messages()->create([
            'user_id' => $user->id,
            'sender_id' => $user->id,
            'body' => $data['body'] ?? '',
            'attachment_path' => $path,
            'attachment_name' => $name,
            'attachment_mime' => $mime,
        ]);

        // The customer just replied, so any "auto-close if they don't
        // reply in time" deadline an admin had set no longer applies.
        if ($conversation->timeout_at) {
            $conversation->update(['timeout_at' => null]);
        }

        $botReply = $this->maybeSendBotReply($conversation, $data['body'] ?? '');

        return response()->json([
            'id' => $message->id,
            'time' => $message->created_at->format('g:i A'),
            'attachment_url' => $message->attachmentUrl(),
            'attachment_name' => $message->attachment_name,
            'is_image' => $message->isImageAttachment(),
            'bot_reply' => $botReply ? [
                'id' => $botReply->id,
                'body' => $botReply->body,
                'time' => $botReply->created_at->format('g:i A'),
            ] : null,
        ]);
    }

    /**
     * Saves an uploaded attachment to the "public" disk (needs
     * `php artisan storage:link` to have been run once) and returns
     * [path, originalName, mimeType] — or [null, null, null] when this
     * request didn't include a file at all.
     */
    private function storeAttachment(Request $request): array
    {
        if (! $request->hasFile('attachment')) {
            return [null, null, null];
        }

        $file = $request->file('attachment');

        return [
            $file->store('support-attachments', 'public'),
            $file->getClientOriginalName(),
            $file->getMimeType(),
        ];
    }

    /**
     * Finds which topic (if any) a customer's message matches, by looking
     * for any of that topic's keywords anywhere in the (lowercased)
     * message — not requiring an exact match. Checked in the order
     * TOPIC_RESPONSES is defined, so the first topic with a matching
     * keyword wins if a message happens to mention more than one.
     */
    private function matchTopic(string $message): ?string
    {
        $haystack = strtolower(trim($message));

        if ($haystack === '') {
            return null;
        }

        foreach (self::TOPIC_RESPONSES as $slug => $topic) {
            foreach ($topic['keywords'] as $keyword) {
                if (str_contains($haystack, $keyword)) {
                    return $slug;
                }
            }
        }

        return null;
    }

    /**
     * Auto-replies exactly like a support bot would — UNTIL a real human
     * admin has actually sent a reply in THIS conversation, at which point
     * this permanently stops doing anything for it. Also tags the
     * conversation with a category the first time the customer's message
     * matches a topic, regardless of whether the bot ends up actually
     * replying — an admin never has to set this by hand.
     */
    private function maybeSendBotReply(SupportConversation $conversation, string $customerMessage): ?SupportMessage
    {
        $topicSlug = $this->matchTopic($customerMessage);

        if (! $conversation->category && $topicSlug) {
            $conversation->update(['category' => $topicSlug]);
        }

        $humanHasJoined = $conversation->messages()
            ->where('sender_id', '!=', $conversation->user_id)
            ->where('is_bot', false)
            ->exists();

        if ($humanHasJoined) {
            return null;
        }

        $admin = User::where('is_admin', true)->first();

        if (! $admin) {
            return null;
        }

        if ($topicSlug) {
            $body = self::TOPIC_RESPONSES[$topicSlug]['response'];
        } else {
            // Cycles through the generic fallbacks instead of repeating the
            // same sentence every time in a longer back-and-forth.
            $priorBotReplies = $conversation->messages()->where('is_bot', true)->count();
            $body = self::DEFAULT_RESPONSES[$priorBotReplies % count(self::DEFAULT_RESPONSES)];
        }

        return $conversation->messages()->create([
            'user_id' => $conversation->user_id,
            'sender_id' => $admin->id,
            'is_bot' => true,
            'body' => $body,
        ]);
    }

    /**
     * Slow safety-net poll (every 15s from the page) in case the live
     * stream() connection below ever silently stalls — e.g. behind a proxy
     * that buffers responses. Kept deliberately simple and cheap; the
     * stream is what makes replies and typing feel instant.
     */
    public function poll(Request $request): JsonResponse
    {
        $afterId = (int) $request->query('after', 0);
        $user = $request->user();

        $conversation = SupportConversation::latestFor($user);

        $messages = collect();
        $stillOpen = false;

        if ($conversation) {
            $stillOpen = $conversation->isOpen();

            $messages = $conversation->messages()
                ->where('id', '>', $afterId)
                ->orderBy('created_at')
                ->get();

            $conversation->messages()
                ->where('sender_id', '!=', $user->id)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        }

        return response()->json([
            'messages' => $messages->map(fn ($m) => $this->messagePayload($m)),
            'conversation_open' => $stillOpen,
            'admin_typing' => $stillOpen && Cache::has('support-typing-admin-'.$user->id),
        ]);
    }

    /**
     * Server-Sent Events: a long-lived HTTP response (not a websocket —
     * no extra server process, no npm/composer packages, works over plain
     * HTTP) that pushes new messages and typing/open status to the
     * browser roughly once a second. The connection is capped at ~25
     * seconds on purpose (PHP/PHP-FPM aren't meant to hold connections
     * open forever); the browser's built-in EventSource reconnects
     * automatically when that happens, resuming from the last message id
     * it saw via the standard Last-Event-ID header — see
     * support.blade.php's connectStream().
     */
    public function stream(Request $request): StreamedResponse
    {
        $user = $request->user();
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

                $conversation = SupportConversation::latestFor($user);
                $open = $conversation?->isOpen() ?? false;

                if ($conversation) {
                    $newMessages = $conversation->messages()
                        ->where('id', '>', $seenId)
                        ->orderBy('created_at')
                        ->get();

                    if ($newMessages->isNotEmpty()) {
                        $conversation->messages()
                            ->where('sender_id', '!=', $user->id)
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
                echo 'data: '.json_encode([
                    'open' => $open,
                    'typing' => $open && Cache::has('support-typing-admin-'.$user->id),
                ])."\n\n";

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
     * Every past (closed) conversation this customer has had, most recent
     * first — so returning after a conversation ended still lets them see
     * their old session instead of it just disappearing.
     */
    public function history(Request $request): View
    {
        $conversations = SupportConversation::where('user_id', $request->user()->id)
            ->where('status', 'closed')
            ->withCount('messages')
            ->with(['messages' => fn ($q) => $q->latest()->limit(1)])
            ->latest()
            ->get();

        return view('support-history', ['conversations' => $conversations]);
    }

    /**
     * A read-only look at one past conversation. Reuses the same
     * support.blade.php template as the live chat — isHistoryView, plus
     * the conversation's own (always-closed) status, is what hides the
     * input box, quick replies, and typing indicator.
     */
    public function showSession(Request $request, SupportConversation $conversation): View
    {
        abort_unless($conversation->user_id === $request->user()->id, 404);

        return view('support', [
            'conversation' => $conversation,
            'messages' => $conversation->messages()->orderBy('created_at')->get(),
            'hasHistory' => true,
            'isHistoryView' => true,
        ]);
    }
}
