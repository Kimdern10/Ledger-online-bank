<?php

namespace App\Support;

use App\Mail\TransferCodeMail;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

/**
 * The one-time 4-digit code a user confirms each transfer, withdrawal or
 * top-up with — emailed fresh every time, in place of a fixed transaction
 * PIN. See TransferCodeController (sends it) and
 * Concerns\RequiresTransactionPin (checks it).
 *
 * Only a hash of the code is kept, in the cache, per user:
 *  - it works once — a correct code is deleted the moment it's used;
 *  - it expires after EXPIRES_MINUTES;
 *  - MAX_ATTEMPTS wrong tries delete it, so it can't be guessed;
 *  - requesting a new code replaces the old one.
 */
class TransferCode
{
    public const EXPIRES_MINUTES = 10;

    public const MAX_ATTEMPTS = 5;

    /**
     * Creates and stores a new code for $user, replacing any earlier one,
     * and returns it (in plain text, only to be emailed).
     */
    public static function issue(User $user): string
    {
        $code = str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);
        $expiresAt = now()->addMinutes(self::EXPIRES_MINUTES);

        Cache::put(self::key($user), [
            'hash' => Hash::make($code),
            'attempts' => 0,
            'expires_at' => $expiresAt->getTimestamp(),
        ], $expiresAt);

        return $code;
    }

    /**
     * Issues a new code and emails it to the user's own address.
     */
    public static function send(User $user): void
    {
        Mail::to($user->email)->send(new TransferCodeMail(self::issue($user)));
    }

    /**
     * True when $code is the user's current code — which also uses it up.
     */
    public static function verify(User $user, string $code): bool
    {
        return self::attempt($user, $code, consume: true);
    }

    /**
     * Whether $code is right WITHOUT using it up — what the confirmation
     * pop-up asks before submitting, so a typo keeps the user in the pop-up
     * instead of sending the form back. A wrong guess still counts towards
     * MAX_ATTEMPTS, so checking first can't be used to guess codes.
     */
    public static function check(User $user, string $code): bool
    {
        return self::attempt($user, $code, consume: false);
    }

    private static function attempt(User $user, string $code, bool $consume): bool
    {
        $stored = Cache::get(self::key($user));

        if (! is_array($stored) || $stored['expires_at'] < now()->getTimestamp()) {
            Cache::forget(self::key($user));

            return false;
        }

        if (preg_match('/^\d{4}$/', $code) === 1 && Hash::check($code, $stored['hash'])) {
            if ($consume) {
                Cache::forget(self::key($user));
            }

            return true;
        }

        $stored['attempts']++;

        if ($stored['attempts'] >= self::MAX_ATTEMPTS) {
            Cache::forget(self::key($user));
        } else {
            Cache::put(self::key($user), $stored, now()->setTimestamp($stored['expires_at']));
        }

        return false;
    }

    /**
     * "j•••e@gmail.com" — enough for the user to recognise where the code
     * went without printing their full address on screen.
     */
    public static function maskedEmail(string $email): string
    {
        [$name, $domain] = array_pad(explode('@', $email, 2), 2, '');

        $masked = mb_strlen($name) <= 2
            ? mb_substr($name, 0, 1).'•'
            : mb_substr($name, 0, 1).str_repeat('•', min(5, mb_strlen($name) - 2)).mb_substr($name, -1);

        return $domain === '' ? $masked : $masked.'@'.$domain;
    }

    private static function key(User $user): string
    {
        return 'transfer-code:'.$user->getKey();
    }
}
