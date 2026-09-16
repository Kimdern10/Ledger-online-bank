<x-layouts::auth :title="__('authpage.two_factor_title')">
    <div class="flex flex-col gap-6">
        <div
            class="relative w-full h-auto"
            x-cloak
            x-data="{
                showRecoveryInput: <?= \Illuminate\Support\Js::from($errors->has('recovery_code')) ?>,
                code: '',
                recovery_code: '',
                focusOtp() {
                    this.$nextTick(() => this.$refs.otp?.querySelector('input')?.focus());
                },
                init() {
                    if (! this.showRecoveryInput) {
                        this.focusOtp();
                    }
                },
                toggleInput() {
                    this.showRecoveryInput = !this.showRecoveryInput;

                    this.code = '';
                    this.recovery_code = '';

                    $nextTick(() => {
                        this.showRecoveryInput
                            ? this.$refs.recovery_code?.focus()
                            : this.focusOtp();
                    });
                },
            }"
        >
            <div x-show="!showRecoveryInput">
                <x-auth-header
                    :title="__('authpage.auth_code_title')"
                    :description="__('authpage.auth_code_description')"
                />
            </div>

            <div x-show="showRecoveryInput">
                <x-auth-header
                    :title="__('authpage.recovery_code_title')"
                    :description="__('authpage.recovery_code_description')"
                />
            </div>

            <form method="POST" action="<?= e(route('two-factor.login.store')) ?>">
                <?= csrf_field() ?>

                <div class="space-y-5 text-center">
                    <div x-show="!showRecoveryInput">
                        <div class="flex items-center justify-center my-5" x-ref="otp">
                            <flux:otp
                                x-model="code"
                                length="6"
                                name="code"
                                label="<?= e(__('authpage.field_otp_code')) ?>"
                                label:sr-only
                                class="mx-auto"
                             />
                        </div>
                    </div>

                    <div x-show="showRecoveryInput">
                        <div class="my-5">
                            <flux:input
                                type="text"
                                name="recovery_code"
                                x-ref="recovery_code"
                                x-bind:required="showRecoveryInput"
                                autocomplete="one-time-code"
                                x-model="recovery_code"
                            />
                        </div>

                        <?php if ($errors->has('recovery_code')): $message = $errors->first('recovery_code'); ?>
                            <flux:text color="red">
                                <?= e($message) ?>
                            </flux:text>
                        <?php endif; ?>
                    </div>

                    <flux:button
                        variant="primary"
                        type="submit"
                        class="w-full"
                    >
                        <?= e(__('authpage.continue_button')) ?>
                    </flux:button>
                </div>

                <div class="mt-5 space-x-0.5 text-sm leading-5 text-center">
                    <span class="opacity-50"><?= e(__('authpage.or_you_can')) ?></span>
                    <div class="inline font-medium underline cursor-pointer opacity-80">
                        <span x-show="!showRecoveryInput" @click="toggleInput()"><?= e(__('authpage.login_using_recovery_code')) ?></span>
                        <span x-show="showRecoveryInput" @click="toggleInput()"><?= e(__('authpage.login_using_auth_code')) ?></span>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-layouts::auth>
