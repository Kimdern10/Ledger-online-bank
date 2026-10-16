<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

/**
 * Sends text messages through Twilio's REST API (config/services.php
 * 'twilio'). Used for the one-time codes a customer can choose to get by
 * text instead of email: sign-up verification, password reset and
 * transfer codes.
 *
 * Texts cost money, so each phone number can be sent at most
 * MAX_PER_WINDOW of them every WINDOW_MINUTES (see allowed()).
 */
final class Sms
{
    public const MAX_PER_WINDOW = 3;

    public const WINDOW_MINUTES = 10;

    public static function isConfigured(): bool
    {
        return filled(config('services.twilio.sid'))
            && filled(config('services.twilio.token'))
            && filled(config('services.twilio.from'));
    }

    /**
     * Whether another text may go to $phone right now — and counts this
     * one if so.
     */
    public static function allowed(string $phone): bool
    {
        $key = 'sms:'.sha1($phone);

        if (RateLimiter::tooManyAttempts($key, self::MAX_PER_WINDOW)) {
            return false;
        }

        RateLimiter::hit($key, self::WINDOW_MINUTES * 60);

        return true;
    }

    /**
     * Sends $message to $to (international form, see PhoneNumber::forSms()).
     * Returns false — never throws — when Twilio isn't set up or the text
     * couldn't be sent; the reason is logged.
     */
    public static function send(string $to, string $message): bool
    {
        if (! self::isConfigured()) {
            Log::warning('Text message not sent: Twilio is not set up (TWILIO_SID, TWILIO_AUTH_TOKEN, TWILIO_FROM).', ['to' => PhoneNumber::masked($to)]);

            return false;
        }

        $sid = (string) config('services.twilio.sid');
        $from = (string) config('services.twilio.from');

        try {
            $response = Http::asForm()
                ->timeout(10)
                ->withBasicAuth($sid, (string) config('services.twilio.token'))
                ->post("https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json", array_filter([
                    'To' => $to,
                    // A Messaging Service SID (MG…) goes in its own field.
                    'From' => str_starts_with($from, 'MG') ? null : $from,
                    'MessagingServiceSid' => str_starts_with($from, 'MG') ? $from : null,
                    'Body' => $message,
                ]));
        } catch (\Throwable $e) {
            Log::warning('Text message failed: '.$e->getMessage(), ['to' => PhoneNumber::masked($to)]);

            return false;
        }

        if (! $response->successful()) {
            Log::warning('Twilio refused a text message', [
                'to' => PhoneNumber::masked($to),
                'status' => $response->status(),
                'error' => $response->json('message'),
            ]);

            return false;
        }

        return true;
    }
}
