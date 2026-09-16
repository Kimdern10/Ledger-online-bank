<x-layouts::auth.simple :title="__('authpage.reset_password_title')">
    <div>
        <h1 class="text-2xl font-semibold" style="font-family:'Newsreader',serif;"><?= e(__('authpage.reset_password_title')) ?></h1>
        <p class="mt-1 text-sm" style="color:var(--ledger-text-2);"><?= e(__('authpage.reset_password_description')) ?></p>
    </div>

    <?php if (session('status')): ?>
        <div class="text-sm text-center" style="color:var(--ledger-sage);">
            <?= e(session('status')) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= e(route('password.update')) ?>" class="flex flex-col gap-5">
        <?= csrf_field() ?>
        <input type="hidden" name="token" value="<?= e(request()->route('token')) ?>">

        <flux:input
            name="email"
            value="<?= e(request('email')) ?>"
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
            passwordrules="<?= e(\Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString()) ?>"
            viewable
        />

        <flux:input
            name="password_confirmation"
            :label="__('authpage.field_confirm_password')"
            type="password"
            required
            autocomplete="new-password"
            :placeholder="__('authpage.field_confirm_password')"
            passwordrules="<?= e(\Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString()) ?>"
            viewable
        />

        <flux:button type="submit" variant="primary" class="w-full" style="margin-top:12px;" data-test="reset-password-button">
            <?= e(__('authpage.reset_password_title')) ?>
        </flux:button>
    </form>
</x-layouts::auth.simple>
