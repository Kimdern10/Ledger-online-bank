<x-layouts::auth :title="__('authpage.confirm_password_title')">
    <div class="flex flex-col gap-6">
        <x-auth-header
            :title="__('authpage.confirm_password_title')"
            :description="__('authpage.confirm_password_description')"
        />

        <x-auth-session-status class="text-center" :status="session('status')" />

        <x-passkey-verify
            options-route="passkey.confirm-options"
            submit-route="passkey.confirm"
            :label="__('authpage.confirm_with_passkey')"
            :loading-label="__('authpage.confirming')"
            :separator="__('authpage.or_confirm_with_password')"
        />

        <form method="POST" action="<?= e(route('password.confirm.store')) ?>" class="flex flex-col gap-6">
            <?= csrf_field() ?>

            <flux:input
                name="password"
                :label="__('authpage.field_password')"
                type="password"
                required
                autocomplete="current-password"
                :placeholder="__('authpage.field_password')"
                viewable
            />

            <flux:button variant="primary" type="submit" class="w-full" data-test="confirm-password-button">
                <?= e(__('authpage.confirm_button')) ?>
            </flux:button>
        </form>
    </div>
</x-layouts::auth>
