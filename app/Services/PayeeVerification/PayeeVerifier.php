<?php

namespace App\Services\PayeeVerification;

/**
 * One third-party service that can check who a bank account belongs to
 * WITHOUT the account's owner signing in — the only kind of check that
 * works for a transfer recipient. Assigned to countries in
 * config/payee_verification.php.
 *
 * An implementation must never invent a name: PayeeResult::$verifiedName
 * is only ever a string the provider's API itself returned.
 */
interface PayeeVerifier
{
    /**
     * Used in config/payee_verification.php and stored on transfers.
     */
    public function key(): string;

    public function label(): string;

    /**
     * Whether this app has the credentials the provider needs.
     */
    public function isConfigured(): bool;

    /**
     * True when the provider hands back the name the bank holds, so the
     * sender never types one. False when it only scores a typed name.
     */
    public function returnsName(): bool;

    /**
     * What the provider calls the bank identifier it needs beside the
     * account number — "Routing number", "IFSC code".
     */
    public function bankCodeLabel(): string;

    /**
     * A cheap format check on the account number and bank code before
     * anything is sent to the provider.
     */
    public function acceptsDetails(PayeeDetails $details): bool;

    public function verify(PayeeDetails $details): PayeeResult;
}
