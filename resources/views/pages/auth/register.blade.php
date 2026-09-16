<x-layouts::auth.simple :title="__('authpage.register_title')">
    <div>
        <h1 class="text-2xl font-semibold" style="font-family:'Newsreader',serif;"><?= e(__('authpage.register_title')) ?></h1>
        <p class="mt-1 text-sm" style="color:var(--ledger-text-2);"><?= e(__('authpage.register_subtitle')) ?></p>
    </div>

    <form method="POST" action="<?= e(route('register')) ?>" class="flex flex-col gap-5">
        <?= csrf_field() ?>

        <div class="ledger-auth-field-grid">
            <flux:input name="first_name" :label="__('authpage.field_first_name')" type="text" required autofocus autocomplete="given-name" :value="old('first_name')" />
            <flux:input name="last_name" :label="__('authpage.field_last_name')" type="text" required autocomplete="family-name" :value="old('last_name')" />
            <flux:input name="middle_name" :label="__('authpage.field_middle_name')" type="text" autocomplete="additional-name" :value="old('middle_name')" />
            <flux:input name="phone" :label="__('authpage.field_phone')" type="tel" autocomplete="tel" :value="old('phone')" />
        </div>

        <?php /* Falls back to a ?email= query param so the "Open an account"
             capture on the welcome page (see welcome.blade.php's hero-form)
             can hand off the email the guest already typed there instead of
             making them type it twice. old('email') still wins whenever
             this is a redisplay after a failed submission. */ ?>
        <flux:input name="email" :label="__('authpage.field_email')" type="email" required autocomplete="email" :value="old('email', request()->query('email', ''))" />

        <flux:input name="password" :label="__('authpage.field_password')" type="password" required autocomplete="new-password" viewable />

        <flux:input name="password_confirmation" :label="__('authpage.field_confirm_password')" type="password" required autocomplete="new-password" viewable />

        <flux:button type="submit" variant="primary" class="w-full" style="margin-top:12px;">
            <?= e(__('authpage.register_button')) ?>
        </flux:button>
    </form>

    <div class="text-center text-sm" style="color:var(--ledger-text-2);">
        <?= e(__('authpage.already_have_account')) ?>
        <flux:link :href="route('login')"><?= e(__('authpage.login_title')) ?></flux:link>
    </div>
</x-layouts::auth.simple>
