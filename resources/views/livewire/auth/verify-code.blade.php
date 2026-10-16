<?php

use App\Support\PhoneNumber;
use App\Support\Sms;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

new class extends Component
{
    public string $code = '';

    public ?string $status = null;

    // ISO timestamp for the JS countdown below — mirrors whatever's already
    // on the user's row (a code was already sent by the time this page can
    // be reached, either at registration or from a previous visit here).
    public ?string $codeExpiresAt = null;

    public function mount(): void
    {
        $this->codeExpiresAt = optional(Auth::user()->verification_code_expires_at)->toIso8601String();
    }

    /**
     * Checks the typed code against what's stored on the user record.
     * Both the code and its expiry clear on success, so it can never be
     * reused, and a fresh one is required if it ever expires.
     */
    public function verify(): void
    {
        $this->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $user = Auth::user();

        if (
            ! $user->verification_code
            || $user->verification_code !== $this->code
            || ! $user->verification_code_expires_at
            || $user->verification_code_expires_at->isPast()
        ) {
            $this->addError('code', __('authpage.error_code_invalid'));

            return;
        }

        $user->forceFill([
            'verification_code' => null,
            'verification_code_expires_at' => null,
        ])->save();

        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        // Verification is the LAST step of account opening — onboarding
        // normally already happened by the time anyone reaches this page.
        // The only way to land here first is by visiting /email/verify
        // directly before finishing the wizard, so this still checks and
        // sends them back to finish it rather than assuming.
        if (! $user->profile?->onboarding_completed) {
            $this->redirect(route('onboarding'), navigate: true);

            return;
        }

        // Account opening is fully done at this point — log them out and
        // send them to log back in, instead of dropping them straight onto
        // the dashboard.
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        session()->flash('status', __('authpage.status_account_verified'));

        $this->redirect(route('login'), navigate: true);
    }

    /**
     * Reuses the exact same code-sending logic as registration, since both
     * live in User::sendEmailVerificationNotification().
     */
    public function resend(): void
    {
        Auth::user()->sendEmailVerificationNotification();

        // sendEmailVerificationNotification() saved the fresh expiry onto
        // this same in-memory User instance (Auth::user() is cached per
        // request), so this already reflects the new 10-minute window.
        $this->codeExpiresAt = optional(Auth::user()->verification_code_expires_at)->toIso8601String();
        $this->status = __('authpage.status_new_code_sent');
        $this->dispatch('code-sent', expiresAt: $this->codeExpiresAt);
    }

    /**
     * "Text the code to •••• 5678 instead" — a fresh code by text message to
     * the phone on the account (only shown when there's one that can be
     * texted). Limited per number, since texts cost money.
     */
    public function textCode(): void
    {
        $user = Auth::user();
        $phone = $user->smsPhone();

        if ($phone === null) {
            $this->status = __('authpage.sms_add_phone');

            return;
        }

        if (! Sms::allowed($phone)) {
            $this->status = __('authpage.sms_too_many');

            return;
        }

        if (! $user->sendVerificationCodeBySms()) {
            $this->status = __('authpage.sms_failed');

            return;
        }

        $this->codeExpiresAt = optional($user->verification_code_expires_at)->toIso8601String();
        $this->status = __('authpage.status_code_texted', ['phone' => PhoneNumber::masked($phone)]);
        $this->dispatch('code-sent', expiresAt: $this->codeExpiresAt);
    }
}; ?>

<div class="flex flex-col gap-6">
    <div>
        <h1 class="text-2xl font-semibold" style="font-family:'Newsreader',serif;">{{ __('authpage.verify_email_title') }}</h1>
        <p class="mt-1 text-sm" style="color:var(--ledger-text-2);">
            {{ __('authpage.verify_sent_prefix') }} <strong>{{ auth()->user()->email }}</strong> {{ __('authpage.verify_sent_suffix') }}
        </p>
    </div>

    @if ($status)
        <div class="text-sm" style="color:var(--ledger-sage);">{{ $status }}</div>
    @endif

    {{-- The deadline and translated words ride along as data-* attributes:
         Livewire serves this component's <script> as a separate JS file that
         Blade never renders, so {{ }} / @js / @json can't be used inside it. --}}
    <div wire:ignore id="verifyCodeCountdown" class="text-sm" style="color:var(--ledger-text-2);"
         data-expires-at="{{ $codeExpiresAt }}"
         data-expires-in="{{ __('authpage.js_code_expires_in') }}"
         data-expired="{{ __('authpage.js_code_expired_request') }}"
         data-no-active-code="{{ __('authpage.js_no_active_code') }}"></div>

    <form wire:submit="verify" class="flex flex-col gap-5">
        <flux:input
            wire:model="code"
            label="{{ __('authpage.field_verification_code') }}"
            inputmode="numeric"
            autocomplete="one-time-code"
            maxlength="6"
            autofocus
            required
        />

        <flux:button type="submit" variant="primary" class="w-full" style="margin-top:12px;" wire:loading.attr="disabled" wire:target="verify">
            {{ __('authpage.verify_button') }}
        </flux:button>
    </form>

    <flux:button type="button" variant="ghost" wire:click="resend" class="w-full" wire:loading.attr="disabled" wire:target="resend">
        {{ __('authpage.resend_code') }}
    </flux:button>

    {{-- Only with a phone number that can be texted (User::smsPhone()). --}}
    @if ($smsPhone = auth()->user()->smsPhone())
        <flux:button type="button" variant="ghost" wire:click="textCode" class="w-full" wire:loading.attr="disabled" wire:target="textCode" style="margin-top:-12px;">
            {{ __('authpage.text_code_instead', ['phone' => \App\Support\PhoneNumber::masked($smsPhone)]) }}
        </flux:button>
    @endif
</div>

<script>
    // Outside the component's root <div> on purpose (same as
    // forgot-password-code): Livewire then runs it as the component's own
    // script, after Livewire has loaded. Inside the <div> it ran inline before
    // Livewire existed, so Livewire.on() threw and "Resend code" never
    // restarted the countdown.
    (function () {
        const el = document.getElementById('verifyCodeCountdown');
        let deadline = el.dataset.expiresAt ? new Date(el.dataset.expiresAt).getTime() : null;
        let interval = null;

        // Translated on the server and read from the countdown's own
        // data-* attributes (see the element above).
        const I18N = {
            expiresIn: el.dataset.expiresIn,
            expired: el.dataset.expired,
            noActiveCode: el.dataset.noActiveCode,
        };

        function tick() {
            if (!deadline) {
                // No code currently on file for this account (e.g. it was
                // never actually sent) — say so instead of leaving this
                // blank, which otherwise looks like the feature is broken.
                el.textContent = I18N.noActiveCode;
                return;
            }

            const remaining = Math.max(0, Math.floor((deadline - Date.now()) / 1000));
            const mm = String(Math.floor(remaining / 60)).padStart(2, '0');
            const ss = String(remaining % 60).padStart(2, '0');

            el.textContent = remaining > 0
                ? `${I18N.expiresIn} ${mm}:${ss}`
                : I18N.expired;
        }

        function startTimer(newDeadline) {
            if (newDeadline) {
                deadline = newDeadline;
            }

            tick();

            if (interval) {
                clearInterval(interval);
            }

            if (deadline) {
                interval = setInterval(tick, 1000);
            }
        }

        startTimer();

        Livewire.on('code-sent', (data) => {
            startTimer(new Date(data.expiresAt).getTime());
        });
    })();
</script>
