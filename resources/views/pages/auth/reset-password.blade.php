<x-layouts::auth.simple :title="__('authpage.reset_password_title')">
    <div>
        <h1 class="text-2xl font-semibold" style="font-family:'Newsreader',serif;">{{ __('authpage.reset_password_title') }}</h1>
        <p class="mt-1 text-sm" style="color:var(--ledger-text-2);">{{ __('authpage.reset_password_description') }}</p>
    </div>

    @if(session('status'))
        <div class="text-sm text-center" style="color:var(--ledger-sage);">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.update') }}" class="flex flex-col gap-5">
        @csrf
        <input type="hidden" name="token" value="{{ request()->route('token') }}">

        <flux:input
            name="email"
            value="{{ request('email') }}"
            :label="__('authpage.field_email_short')"
            type="email"
            required
            autocomplete="email"
        />

        <flux:input
            name="password"
            :label="__('authpage.field_password')"
            type="password"
            required
            autocomplete="new-password"
            :placeholder="__('authpage.field_password')"
            passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
            viewable
        />

        <flux:input
            name="password_confirmation"
            :label="__('authpage.field_confirm_password')"
            type="password"
            required
            autocomplete="new-password"
            :placeholder="__('authpage.field_confirm_password')"
            passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
            viewable
        />

        <flux:button type="submit" variant="primary" class="w-full" style="margin-top:12px;" data-test="reset-password-button">
            {{ __('authpage.reset_password_title') }}
        </flux:button>
    </form>
</x-layouts::auth.simple>
