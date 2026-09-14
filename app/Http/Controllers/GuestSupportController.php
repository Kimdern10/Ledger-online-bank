<?php

namespace App\Http\Controllers;

use App\Models\GuestSupportConversation;
use App\Models\GuestSupportMessage;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;

/**
 * Powers the floating "chat with us" widget on the public marketing page
 * (welcome.blade.php, via layouts/partials/guest-support-widget.blade.php)
 * — the anonymous-visitor counterpart to SupportController, which is only
 * ever reachable once someone's logged in.
 *
 * There's no account, so a visitor is identified purely by the
 * guest_support_token cookie set in start() below: an unguessable random
 * string (see GuestSupportConversation::generateToken()) that ties every
 * later request on this browser back to the same conversation, without
 * ever asking them to sign up. It's a normal Laravel cookie, so it goes
 * through the same encrypt/decrypt the session cookie does — nothing else
 * to set up.
 */
class GuestSupportController extends Controller
{
    private const COOKIE_NAME = 'guest_support_token';

    private const COOKIE_MINUTES = 60 * 24 * 60; // 60 days

    private const GREETING = "Thanks for reaching out! A member of our team will be with you shortly — feel free to add any more details while you wait.";

    /**
     * Called once, the first time a visitor opens the widget on a given
     * page load, to decide what to show them: their conversation so far
     * (open or already ended), or the opening name/email/message form for
     * someone chatting for the first time.
     */
    public function init(Request $request): JsonResponse
    {
        $conversation = GuestSupportConversation::findByToken($request->cookie(self::COOKIE_NAME));

        if (! $conversation) {
            return response()->json(['has_conversation' => false]);
        }

        $messages = $conversation->messages()->orderBy('created_at')->get();

        // Opening the widget counts as the guest reading whatever the
        // support side (admin or the automatic greeting) has sent so far —
        // same rule poll() below applies on every subsequent check.
        $conversation->messages()
            ->whereNotNull('sender_id')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([
            'has_conversation' => true,
            'conversation_open' => $conversation->isOpen(),
            'guest_name' => $conversation->guest_name,
            'messages' => $messages->map(fn ($m) => $this->messagePayload($m)),
            'admin_typing' => $conversation->isOpen() && Cache::has('guest-support-typing-admin-'.$conversation->id),
        ]);
    }

    /**
     * The opening form submit: name, email, and their first message all at
     * once. Creates the conversation, the guest's first message, and (if
     * an admin account exists to attribute it to) the automatic greeting —
     * then hands back a cookie that makes every later request on this
     * browser resolve straight back to this same conversation.
     */
    public function start(Request $request): JsonResponse
    {
        $data = $request->validate([
            'guest_name' => ['required', 'string', 'max:100'],
            'guest_email' => ['required', 'email', 'max:255'],
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $conversation = GuestSupportConversation::create([
            'guest_name' => $data['guest_name'],
            'guest_email' => $data['guest_email'],
            'guest_token' => GuestSupportConversation::generateToken(),
            'status' => 'open',
        ]);

        $messages = collect([
            $conversation->messages()->create([
                'sender_id' => null,
                'body' => $data['body'],
            ]),
        ]);

        $admin = User::where('is_admin', true)->first();

        if ($admin) {
            $messages->push($conversation->messages()->create([
                'sender_id' => $admin->id,
                'is_system' => true,
                'body' => self::GREETING,
            ]));
        }

        return response()->json([
            'conversation_open' => true,
            'guest_name' => $conversation->guest_name,
            'messages' => $messages->map(fn ($m) => $this->messagePayload($m)),
        ])->cookie(self::COOKIE_NAME, $conversation->guest_token, self::COOKIE_MINUTES);
    }

    /**
     * One more message from the guest in an already-open conversation —
     * the widget only shows the input box once start() has already run,
     * so there's always a cookie by the time this is called for real.
     * Always sent as multipart/form-data from the widget now, not JSON,
     * since a JSON body can't carry a file — same reasoning
     * SupportController::store() gives for the logged-in version.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'body' => ['nullable', 'string', 'max:2000'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,webp,pdf', 'max:5120'],
        ]);

        if (empty($data['body']) && ! $request->hasFile('attachment')) {
            return response()->json(['error' => 'Type a message or attach a photo.'], 422);
        }

        $conversation = GuestSupportConversation::findByToken($request->cookie(self::COOKIE_NAME));

        if (! $conversation || ! $conversation->isOpen()) {
            return response()->json([
                'error' => 'This conversation has ended — refresh the chat to start a new one.',
            ], 422);
        }

        [$path, $name, $mime] = $this->storeAttachment($request);

        $message = $conversation->messages()->create([
            'sender_id' => null,
            'body' => $data['body'] ?? '',
            'attachment_path' => $path,
            'attachment_name' => $name,
            'attachment_mime' => $mime,
        ]);

        // The guest just replied — any "support is typing" indicator the
        // widget was showing is now stale.
        Cache::forget('guest-support-typing-admin-'.$conversation->id);

        return response()->json($this->messagePayload($message));
    }

    /**
     * Saves an uploaded photo/PDF to the "public" disk (needs
     * `php artisan storage:link`) and returns [path, originalName,
     * mimeType] — or [null, null, null] when this request has no file.
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
     * Lets the guest end their own conversation from inside the widget —
     * the visitor-side counterpart to AdminGuestSupportController::end().
     */
    public function end(Request $request): JsonResponse
    {
        $conversation = GuestSupportConversation::findByToken($request->cookie(self::COOKIE_NAME));

        if ($conversation && $conversation->isOpen()) {
            $conversation->close('guest');
        }

        return response()->json(['ok' => true]);
    }

    /**
     * Polled every few seconds while the widget is open, so a reply an
     * admin sends shows up without the visitor needing to do anything.
     * Same shape as SupportController::poll() for the logged-in version.
     */
    public function poll(Request $request): JsonResponse
    {
        $afterId = (int) $request->query('after', 0);
        $conversation = GuestSupportConversation::findByToken($request->cookie(self::COOKIE_NAME));

        if (! $conversation) {
            return response()->json(['messages' => [], 'conversation_open' => false]);
        }

        $messages = $conversation->messages()
            ->where('id', '>', $afterId)
            ->orderBy('created_at')
            ->get();

        $conversation->messages()
            ->whereNotNull('sender_id')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([
            'messages' => $messages->map(fn ($m) => $this->messagePayload($m)),
            'conversation_open' => $conversation->isOpen(),
            'admin_typing' => $conversation->isOpen() && Cache::has('guest-support-typing-admin-'.$conversation->id),
        ]);
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
