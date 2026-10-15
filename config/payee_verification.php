<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Recipient name verification, per destination country
    |--------------------------------------------------------------------------
    |
    | Which provider checks a transfer recipient's name for each country —
    | see App\Services\PayeeVerification. A country that isn't listed has no
    | check: the sender types the name and it's saved as "manual", labelled
    | "Not verified by the bank".
    |
    | Only two countries are listed because only these two providers are
    | documented to check an account WITHOUT the account's owner signing in:
    |
    |  - US -> plaid:    Plaid /auth/verify. Returns a name MATCH SCORE for a
    |                    name the sender typed — never the name itself.
    |  - IN -> cashfree: Cashfree Bank Account Verification. Returns the
    |                    account holder's name as the bank records it.
    |
    | `aliases` are the other ways the International tab's free-text
    | country field might name the country.
    |
    */

    'countries' => [
        'US' => [
            'provider' => 'plaid',
            'aliases' => ['united states', 'united states of america', 'usa', 'us'],
        ],
        'IN' => [
            'provider' => 'cashfree',
            'aliases' => ['india', 'in'],
        ],
    ],
];
