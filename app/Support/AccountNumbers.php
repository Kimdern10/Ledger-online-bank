<?php

namespace App\Support;

use App\Models\User;

/**
 * Generates and assigns every fake-but-valid-format banking number Ledger
 * hands out to a user: the routing number and SWIFT/BIC code every account
 * gets at registration (see User::booted()'s `creating` hook, which calls
 * uniqueRoutingNumber()/uniqueSwiftCode() below), and the checking/savings
 * account number(s) assigned once someone picks an account type on step 5
 * of onboarding (assignForType() below, called from Profile::booted()'s
 * `saved` hook the moment account_type is saved).
 *
 * Moved here from App\Models\User, which used to hold both the "assign and
 * save" logic and the pure number-generation helpers directly. None of this
 * is business logic a controller could reasonably own instead — nothing
 * here is triggered by an HTTP request; it's triggered by two different
 * models' own creation/save lifecycle — so it lives on this shared,
 * framework-agnostic class rather than pretending it belongs to one
 * specific controller.
 */
class AccountNumbers
{
    /**
     * Fills in whichever account number(s) match the account type chosen
     * on step 5 of onboarding — "checking", "savings", or "both" — and
     * only the one(s) that don't already exist yet, so calling this twice
     * (e.g. someone clicks back to step 5 and re-submits) never overwrites
     * a number that's already been generated and shown to them.
     */
    public static function assignForType(User $user, string $accountType): void
    {
        $accountType = strtolower($accountType);
        $dirty = false;

        if (in_array($accountType, ['checking', 'both'], true) && empty($user->checking_account_number)) {
            $user->checking_account_number = self::uniquePrefixedAccountNumber('checking_account_number', '74');
            $dirty = true;
        }

        if (in_array($accountType, ['savings', 'both'], true) && empty($user->savings_account_number)) {
            $user->savings_account_number = self::uniquePrefixedAccountNumber('savings_account_number', '50');
            $dirty = true;
        }

        if ($dirty) {
            $user->save();
        }
    }

    /**
     * A routing number no existing user already has. Real routing numbers
     * start with a 2-digit Federal Reserve prefix: 00-32 (banks/thrifts),
     * 61-72 (electronic institutions), or 80 (traveler's checks). Prefixes
     * 33-60 have never been assigned to any real institution, so picking
     * one there guarantees this number can't collide with — or be mistaken
     * for — someone's actual bank.
     *
     * Note for later: in real banking a routing number identifies the BANK
     * (or branch), not the individual customer — every customer of the same
     * bank shares one routing number, and only the account number differs
     * per person. Ledger gives each user their own purely because that's
     * what was asked for; it isn't how real routing numbers behave.
     */
    public static function uniqueRoutingNumber(): string
    {
        do {
            $number = self::generateFakeRoutingNumber();
        } while (User::where('routing_number', $number)->exists());

        return $number;
    }

    /**
     * An 11-character fake SWIFT/BIC code no existing user already has: a
     * fictional 4-letter "bank code" (LDGR, for Ledger — not a real
     * registered SWIFT bank code), the US country code, then a random
     * 2-character location code and 3-character branch code. Same caveat
     * as the routing number above: real SWIFT/BIC codes identify an
     * institution/branch, not a person.
     */
    public static function uniqueSwiftCode(): string
    {
        do {
            $code = self::generateFakeSwiftCode();
        } while (User::where('swift_code', $code)->exists());

        return $code;
    }

    private static function uniquePrefixedAccountNumber(string $column, string $prefix): string
    {
        do {
            $number = self::generatePrefixedAccountNumber($prefix);
        } while (User::where($column, $number)->exists());

        return $number;
    }

    private static function generateFakeRoutingNumber(): string
    {
        $prefix = (string) random_int(33, 60);
        $middle = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $digits = array_map('intval', str_split($prefix.$middle));

        // Standard ABA checksum: 3*(d1+d4+d7) + 7*(d2+d5+d8) + 1*(d3+d6+d9) ≡ 0 (mod 10)
        $weights = [3, 7, 1, 3, 7, 1, 3, 7];
        $sum = 0;
        foreach ($digits as $i => $d) {
            $sum += $d * $weights[$i];
        }
        $checkDigit = (10 - ($sum % 10)) % 10;

        return $prefix.$middle.$checkDigit;
    }

    private static function generateFakeSwiftCode(): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $random = fn (int $length) => collect(range(1, $length))
            ->map(fn () => $alphabet[random_int(0, strlen($alphabet) - 1)])
            ->implode('');

        return 'LDGRUS'.$random(2).$random(3);
    }

    /**
     * Builds a 10-digit account number that always starts with the given
     * prefix (e.g. "74" for checking, "50" for savings), with the
     * remaining digits random. Same idea as generateFakeRoutingNumber()'s
     * safe prefix trick, but here the "prefix" is just a made-up house
     * style — account numbers aren't nationally standardized in the US the
     * way routing numbers are, so there's no real-world number this could
     * ever collide with or be mistaken for.
     */
    private static function generatePrefixedAccountNumber(string $prefix): string
    {
        $remainingLength = 10 - strlen($prefix);
        $rest = str_pad((string) random_int(0, (10 ** $remainingLength) - 1), $remainingLength, '0', STR_PAD_LEFT);

        return $prefix.$rest;
    }
}
