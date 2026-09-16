<x-layouts::auth :title="__('authpage.login_title')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('authpage.login_heading')" :description="__('authpage.login_description')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <x-passkey-verify
            :label="__('authpage.passkey_signin')"
            :loading-label="__('authpage.passkey_authenticating')"
            :separator="__('authpage.passkey_or_email')"
        />

        <form method="POST" action="<?= e(route('login.store')) ?>" class="flex flex-col gap-6">
            <?= csrf_field() ?>

            <!-- Email Address -->
            <flux:input
                name="email"
                :label="__('authpage.field_email')"
                :value="old('email')"
                type="email"
                required
                autofocus
                autocomplete="email"
                placeholder="email@example.com"
            />

            <!-- Password -->
            <div class="relative">
                <flux:input
                    name="password"
                    :label="__('authpage.field_password')"
                    type="password"
                    required
                    autocomplete="current-password"
                    :placeholder="__('authpage.field_password')"
                    viewable
                />

                <?php if (Route::has('password.request')): ?>
                    <flux:link class="absolute top-0 text-sm end-0" :href="route('password.request')" wire:navigate>
                        <?= e(__('authpage.forgot_password_link')) ?>
                    </flux:link>
                <?php endif; ?>
            </div>

            <!-- Remember Me -->
            <flux:checkbox name="remember" :label="__('authpage.remember_me')" :checked="old('remember')" />

            <div class="flex items-center justify-end">
                <flux:button variant="primary" type="submit" class="w-full" data-test="login-button">
                    <?= e(__('authpage.login_title')) ?>
                </flux:button>
            </div>
        </form>

        <div class="space-x-1 text-sm text-center rtl:space-x-reverse text-zinc-600 dark:text-zinc-400">
            <span><?= e(__('authpage.no_account')) ?></span>
            <flux:link :href="route('register')" wire:navigate><?= e(__('authpage.sign_up')) ?></flux:link>
        </div>
    </div>
</x-layouts::auth>
