<?php

namespace App\Services\PayeeVerification;

/**
 * What the sender entered about the recipient's account.
 */
class PayeeDetails
{
    public function __construct(
        public readonly string $countryCode,
        public readonly string $accountNumber,
        public readonly string $bankCode,
        public readonly ?string $name = null,
    ) {}

    public function hasName(): bool
    {
        return filled($this->name);
    }

    /**
     * Identifies exactly one account at one bank in one country. Results
     * are only ever reused for the same fingerprint, so a check on one
     * account can't vouch for another.
     */
    public function accountFingerprint(): string
    {
        return sha1(implode('|', [
            strtoupper(trim($this->countryCode)),
            strtoupper(trim($this->bankCode)),
            trim($this->accountNumber),
        ]));
    }

    /**
     * Lowercase with spacing collapsed, so "Ada  Okafor" and "ada okafor"
     * count as the same name — nothing looser than that.
     */
    public static function normalizeName(?string $name): string
    {
        return mb_strtolower(trim((string) preg_replace('/\s+/u', ' ', (string) $name)));
    }
}
