<?php

use App\Concerns\PasswordValidationRules;
use App\Mail\PasswordResetCodeMail;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Mail;
use Laravel\Fortify\Contracts\ResetsUserPasswords;
use Livewire\Component;

new class extends Component
{
    use PasswordValidationRules;

    public string $email = '';

    public int $step = 1;

    public string $code = '';

    public string $password = '';

    public string $password_confirmation = '';

    public ?string $status = null;

    /**
     * Step 1: look up the account and, if one exists, email it a 6-digit
     * code (see User's password_reset_code columns). The same status
     * message shows either way — this never reveals whether an email is
     * registered, same reasoning as any normal "forgot password" flow.
     */
    public function sendCode(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        // Computed once and reused for both the DB write and the browser
        // countdown, so the two can never drift apart by even a second.
        $expiresAt = now()->addMinutes(10);
        $user = User::where('email', $this->email)->first();

        if ($user) {
            $this->issueCodeFor($user, $expiresAt);
        }

        // The countdown starts either way, whether or not that email has an
        // account — same anti-enumeration reasoning as the status message.
        $this->status = __('authpage.status_code_sent_known', ['email' => $this->email]);
        $this->step = 2;
        $this->dispatch('code-sent', expiresAt: $expiresAt->toIso8601String());
    }

    /**
     * Same as sendCode() but reachable from step 2's "Resend code" button
     * without re-validating the email field twice in a row.
     */
    public function resend(): void
    {
        $expiresAt = now()->addMinutes(10);
        $user = User::where('email', $this->email)->first();

        if ($user) {
            $this->issueCodeFor($user, $expiresAt);
        }

        $this->code = '';
        $this->status = __('authpage.status_code_resent');
        $this->dispatch('code-sent', expiresAt: $expiresAt->toIso8601String());
    }

    private function issueCodeFor(User $user, CarbonInterface $expiresAt): void
    {
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->forceFill([
            'password_reset_code' => $code,
            'password_reset_code_expires_at' => $expiresAt,
        ])->save();

        Mail::to($user->email)->send(new PasswordResetCodeMail($code));
    }

    /**
     * Step 2: checks the typed code the same way verify-code.blade.php
     * checks its own (matching value, not expired), then hands off to
     * whatever action Fortify::resetUserPasswordsUsing() is bound to
     * (ResetUserPassword by default) so password rules and hashing stay
     * identical to the rest of the app instead of being duplicated here.
     */
    public function resetPassword(): void
    {
        $this->validate([
            'code' => ['required', 'digits:6'],
            'password' => $this->passwordRules(),
        ]);

        $user = User::where('email', $this->email)->first();

        if (
            ! $user
            || ! $user->password_reset_code
            || $user->password_reset_code !== $this->code
            || ! $user->password_reset_code_expires_at
            || $user->password_reset_code_expires_at->isPast()
        ) {
            $this->addError('code', __('authpage.error_code_invalid'));

            return;
        }

        app(ResetsUserPasswords::class)->reset($user, [
            'password' => $this->password,
            'password_confirmation' => $this->password_confirmation,
        ]);

        $user->forceFill([
            'password_reset_code' => null,
            'password_reset_code_expires_at' => null,
        ])->save();

        event(new PasswordReset($user));

        session()->flash('status', __('authpage.status_password_reset_done'));

        $this->redirect(route('login'), navigate: true);
    }

    public function backToEmail(): void
    {
        $this->step = 1;
        $this->code = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->status = null;
        $this->resetErrorBag();
        $this->dispatch('code-cleared');
    }
}; ?>

<div class="flex flex-col gap-6">
    {{-- Lives outside the @if/@else on purpose: Livewire's DOM morph won't
         reliably (re-)execute a <script> tag that only appears the moment
         step flips to 2, so this stays in the markup the whole time (hidden
         by JS until a code actually gets sent) and every update — first
         send, resend, or clearing back to step 1 — goes through a
         dispatched browser event instead of a fresh render. --}}
    <div wire:ignore id="resetCodeCountdown" class="text-sm text-center" style="color:var(--ledger-text-2); display:none;"></div>

    @if ($step === 1)
        <div>
            <h1 class="text-2xl font-semibold" style="font-family:'Newsreader',serif;">{{ __('authpage.forgot_password_title') }}</h1>
            <p class="mt-1 text-sm" style="color:var(--ledger-text-2);">{{ __('authpage.forgot_password_description') }}</p>
        </div>

        <form wire:submit="sendCode" class="flex flex-col gap-5">
            <flux:input
                wire:model="email"
                :label="__('authpage.field_email')"
                type="email"
                required
                autofocus
                placeholder="email@example.com"
            />

            <flux:button variant="primary" type="submit" class="w-full" style="margin-top:12px;" wire:loading.attr="disabled" wire:target="sendCode" data-test="email-password-reset-link-button">
                {{ __('authpage.send_reset_code') }}
            </flux:button>
        </form>

        <div class="text-center text-sm" style="color:var(--ledger-text-2);">
            {{ __('authpage.or_return_to') }}
            <flux:link :href="route('login')" wire:navigate>{{ __('authpage.log_in_lowercase') }}</flux:link>
        </div>
    @else
        <div>
            <h1 class="text-2xl font-semibold" style="font-family:'Newsreader',serif;">{{ __('authpage.enter_your_code') }}</h1>
            <p class="mt-1 text-sm" style="color:var(--ledger-text-2);">
                {{ __('authpage.code_sent_to_prefix') }} <strong>{{ $email }}</strong> {{ __('authpage.code_sent_to_suffix') }}
            </p>
        </div>

        @if ($status)
            <div class="text-sm text-center" style="color:var(--ledger-sage);">{{ $status }}</div>
        @endif

        <form wire:submit="resetPassword" class="flex flex-col gap-5">
            <flux:input
                wire:model="code"
                :label="__('authpage.field_reset_code')"
                inputmode="numeric"
                autocomplete="one-time-code"
                maxlength="6"
                autofocus
                required
            />

            <flux:input
                wire:model="password"
                :label="__('authpage.field_new_password')"
                type="password"
                required
                autocomplete="new-password"
                passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
                viewable
            />

            <flux:input
                wire:model="password_confirmation"
                :label="__('authpage.field_confirm_new_password')"
                type="password"
                required
                autocomplete="new-password"
                viewable
            />

            <flux:button variant="primary" type="submit" class="w-full" style="margin-top:12px;" wire:loading.attr="disabled" wire:target="resetPassword" data-test="reset-password-button">
                {{ __('authpage.reset_password_title') }}
            </flux:button>
        </form>

        <div class="flex items-center justify-between text-sm" style="color:var(--ledger-text-2);">
            <button type="button" wire:click="backToEmail" class="cursor-pointer underline">{{ __('authpage.use_different_email') }}</button>
            <button type="button" wire:click="resend" wire:loading.attr="disabled" wire:target="resend" class="cursor-pointer underline">{{ __('authpage.resend_code') }}</button>
        </div>
    @endif
</div>

<script>
    (function () {
        const el = document.getElementById('resetCodeCountdown');
        let deadline = null;
        let interval = null;

        // Resolved server-side with __() and handed over as JSON, same
        // pattern send-receipt.blade.php's RECEIPT_I18N uses — keeps this a
        // plain .blade.php script block rather than needing a build step.
        const I18N = {
            expiresIn: @json(__('authpage.js_code_expires_in')),
            expired: @json(__('authpage.js_code_expired_resend')),
        };

        function tick() {
            if (!deadline) {
                return;
            }

            const remaining = Math.max(0, Math.floor((deadline - Date.now()) / 1000));
            const mm = String(Math.floor(remaining / 60)).padStart(2, '0');
            const ss = String(remaining % 60).padStart(2, '0');

            el.textContent = remaining > 0
                ? `${I18N.expiresIn} ${mm}:${ss}`
                : I18N.expired;
        }

        function startTimer(expiresAt) {
            deadline = new Date(expiresAt).getTime();
            el.style.display = '';
            tick();
            if (interval) {
                clearInterval(interval);
            }
            interval = setInterval(tick, 1000);
        }

        Livewire.on('code-sent', (data) => startTimer(data.expiresAt));

        Livewire.on('code-cleared', () => {
            deadline = null;
            if (interval) {
                clearInterval(interval);
            }
            el.style.display = 'none';
            el.textContent = '';
        });
    })();
</script>
