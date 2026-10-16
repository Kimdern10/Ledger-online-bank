<?php

namespace App\Support;

/**
 * Phone numbers for text-message codes. Customers type their number freely
 * (sign-up, account setup, Profile settings), but SMS needs the full
 * international form — "+" and the country code, e.g. +2348012345678 —
 * so a number is only textable once it can be read that way.
 */
final class PhoneNumber
{
    /**
     * The number in international (E.164) form, or null when it can't be
     * texted: spaces, dashes, dots and brackets are ignored and a leading
     * "00" counts as "+", but the country code must be there.
     */
    public static function forSms(?string $phone): ?string
    {
        if ($phone === null) {
            return null;
        }

        $digits = preg_replace('/[\s\-().]/', '', $phone) ?? '';

        if (str_starts_with($digits, '00')) {
            $digits = '+'.substr($digits, 2);
        }

        return preg_match('/^\+[1-9]\d{7,14}$/', $digits) === 1 ? $digits : null;
    }

    /**
     * "•••• 5678" — enough to recognise where a code went without showing
     * the whole number.
     */
    public static function masked(string $phone): string
    {
        return '•••• '.substr($phone, -4);
    }
}
