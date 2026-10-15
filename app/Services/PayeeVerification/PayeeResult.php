<?php

namespace App\Services\PayeeVerification;

/**
 * The outcome of one recipient check.
 */
class PayeeResult
{
    /** The provider returned the account holder's name ($verifiedName). */
    public const NAME_RETRIEVED = 'verified';

    /** The provider says the typed name belongs to the account. */
    public const MATCH = 'match';

    /** Similar but not the same — the sender has to look again. */
    public const CLOSE_MATCH = 'close_match';

    /** The provider says the typed name does not belong to the account. */
    public const NO_MATCH = 'no_match';

    /** The account details are wrong (bad format, or the provider says so). */
    public const INVALID = 'invalid';

    /** This provider only scores a typed name, and none was typed yet. */
    public const NEEDS_NAME = 'needs_name';

    /** No provider covers this country, or it isn't configured. */
    public const UNSUPPORTED = 'unsupported';

    /** The provider answered but had no name or score to give. */
    public const UNAVAILABLE = 'unavailable';

    /** The provider couldn't be reached, or returned an error. */
    public const FAILED = 'failed';

    public function __construct(
        public readonly string $status,
        public readonly ?string $provider = null,
        public readonly ?string $verifiedName = null,
    ) {}

    /**
     * A definite answer from the provider — safe to reuse briefly instead
     * of paying for the same lookup again. Failures never are.
     */
    public function isConclusive(): bool
    {
        return in_array($this->status, [self::NAME_RETRIEVED, self::MATCH, self::CLOSE_MATCH, self::NO_MATCH, self::INVALID], true);
    }
}
