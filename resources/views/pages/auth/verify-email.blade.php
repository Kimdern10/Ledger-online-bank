<x-layouts::auth :title="__('Email verification')">
    <div class="mt-4 flex flex-col gap-6">
        <flux:text class="text-center">
            <?= e(__('Please verify your email address by clicking on the link we just emailed to you.')) ?>
        </flux:text>

        <?php if (session('status') == 'verification-link-sent'): ?>
            <flux:text class="text-center font-medium !dark:text-green-400 !text-green-600">
                <?= e(__('A new verification link has been sent to the email address you provided during registration.')) ?>
            </flux:text>
        <?php endif; ?>

        <div class="flex flex-col items-center justify-between space-y-3">
            <form method="POST" action="<?= e(route('verification.send')) ?>">
                <?= csrf_field() ?>
                <flux:button type="submit" variant="primary" class="w-full">
                    <?= e(__('Resend verification email')) ?>
                </flux:button>
            </form>

            <form method="POST" action="<?= e(route('logout')) ?>">
                <?= csrf_field() ?>
                <flux:button variant="ghost" type="submit" class="text-sm cursor-pointer" data-test="logout-button">
                    <?= e(__('Log out')) ?>
                </flux:button>
            </form>
        </div>
    </div>
</x-layouts::auth>
