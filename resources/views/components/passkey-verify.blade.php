@props([
    'optionsRoute' => 'passkey.login-options',
    'submitRoute' => 'passkey.login',
    // Callers on login.blade.php / confirm-password.blade.php always pass
    // their own translated label/loadingLabel/separator explicitly — these
    // defaults only matter if some future caller doesn't, so they're kept
    // translated too rather than left as plain untranslated fallbacks.
    'label' => __('authpage.passkey_signin'),
    'loadingLabel' => __('authpage.passkey_authenticating'),
    'separator' => __('authpage.passkey_or_email'),
])

@assets
@vite('resources/js/passkeys.js')
@endassets

<div
    x-data="{
        supported: false,
        loading: false,
        error: null,
        updateSupport() {
            this.supported = Boolean(window.Passkeys?.isSupported());
        },
        init() {
            this.updateSupport();

            window.addEventListener('passkeys:ready', () => this.updateSupport(), { once: true });
        },
        async verify() {
            this.loading = true;
            this.error = null;
            try {
                const response = await window.Passkeys.verify({
                    routes: {
                        options: '<?= e(route($optionsRoute)) ?>',
                        submit: '<?= e(route($submitRoute)) ?>',
                    },
                });
                Livewire.navigate(response.redirect || '/dashboard');
            } catch (e) {
                if (e.constructor?.name !== 'UserCancelledError') {
                    this.error = e.message;
                }
            } finally {
                this.loading = false;
            }
        },
    }"
>
    <template x-if="supported">
        <div>
            <div class="grid gap-2">
                <flux:button
                    variant="outline"
                    icon="finger-print"
                    class="w-full"
                    x-on:click="verify()"
                    x-bind:disabled="loading"
                >
                    <span x-show="!loading"><?= e($label) ?></span>
                    <span x-show="loading" x-cloak><?= e($loadingLabel) ?></span>
                </flux:button>
                <p x-show="error" x-text="error" x-cloak
                   class="text-sm text-center text-red-600 dark:text-red-400"></p>
            </div>

            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-zinc-200 dark:border-zinc-700"></div>
                </div>
                <div class="relative flex justify-center text-xs uppercase">
                    <span class="px-2 text-zinc-500 dark:text-zinc-400 bg-white dark:bg-zinc-900">
                        <?= e($separator) ?>
                    </span>
                </div>
            </div>
        </div>
    </template>
</div>
