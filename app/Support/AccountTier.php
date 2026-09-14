<?php

namespace App\Support;

/**
 * Single source of truth for Ledger's 3-tier verification system and the
 * daily outbound limit each tier gets. "Outbound" means the combined total
 * of Send Money — Ledger-to-Ledger, Another bank (domestic), AND
 * International bank, all three count the same way — plus Withdraw, for one
 * calendar day. See Http\Controllers\Concerns\EnforcesTierLimits::
 * movedTodayBy(), which is what actually adds those up and compares against
 * the limit below before letting a transfer or withdrawal go through, for
 * both an immediate send and a scheduled one once it actually executes.
 * Receiving money, Pay Bills, and everything else in the app is never
 * limited by tier.
 *
 * Tier 1 — every account, from the moment it's created. No extra step.
 * Tier 2 — a government ID + selfie approved (see KycController /
 *          AdminKycController) — the same flag EnsureKycApproved already
 *          gates Send Money's page on.
 * Tier 3 — Tier 2, plus a proof-of-address document approved (see
 *          AddressVerificationController / AdminAddressController). Can't
 *          be reached without Tier 2 first — see User::tier().
 *
 * Changing a limit here is the only place it needs to change — the same
 * numbers are what's enforced (EnforcesTierLimits) and what's shown to the
 * user (User::tierLabel()/tierDailyLimit(), setting.blade.php).
 */
final class AccountTier
{
    public const DAILY_LIMITS = [
        1 => 1000.00,
        2 => 10000.00,
        // Deliberately high rather than truly unlimited — even a fully
        // verified (ID + address) customer still gets a ceiling, same as a
        // real bank's highest tier would. A cap that's actually enforced by
        // code beats an "unlimited" that only holds until someone finds the
        // one path that forgot to check it.
        3 => 250000.00,
    ];

    public static function dailyLimit(int $tier): float
    {
        return self::DAILY_LIMITS[$tier] ?? self::DAILY_LIMITS[1];
    }

    /**
     * Resolved through __() rather than kept as class constants, so the
     * label/description follow the viewer's locale like everything else in
     * the app — see lang/{locale}/verify.php's tier_1/tier_2/tier_3 and
     * tier_1_desc/tier_2_desc/tier_3_desc keys.
     */
    public static function label(int $tier): string
    {
        $tier = array_key_exists($tier, self::DAILY_LIMITS) ? $tier : 1;

        return __('verify.tier_'.$tier);
    }

    public static function description(int $tier): string
    {
        $tier = array_key_exists($tier, self::DAILY_LIMITS) ? $tier : 1;

        return __('verify.tier_'.$tier.'_desc');
    }
}
