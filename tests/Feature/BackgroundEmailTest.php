<?php

use App\Actions\Fortify\CreateNewUser;
use App\Mail\PasswordResetCodeMail;
use App\Mail\TransactionMail;
use App\Mail\TransferCodeMail;
use App\Mail\VerificationCodeMail;
use App\Mail\WelcomeMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

test('receipts and welcome emails are sent in the background', function (string $mailable) {
    expect(is_subclass_of($mailable, ShouldQueue::class))->toBeTrue();
})->with(['receipts' => TransactionMail::class, 'welcome' => WelcomeMail::class]);

test('one-time codes are still sent straight away', function (string $mailable) {
    expect(is_subclass_of($mailable, ShouldQueue::class))->toBeFalse();
})->with(['email verification' => VerificationCodeMail::class, 'password reset' => PasswordResetCodeMail::class, 'transfer code' => TransferCodeMail::class]);

test('signing up queues the welcome email instead of making the person wait', function () {
    Mail::fake();

    app(CreateNewUser::class)->create([
        'first_name' => 'Ada',
        'last_name' => 'Okafor',
        'email' => 'ada@example.test',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    Mail::assertQueued(WelcomeMail::class, fn (WelcomeMail $mail) => $mail->hasTo('ada@example.test'));
});
