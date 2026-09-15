<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Live bank-directory providers (see App\Services\BankProviders)
    |--------------------------------------------------------------------------
    |
    | These back the "Select a bank" search on Send Money, ALONGSIDE the
    | admin-curated App\Models\Bank directory (App\Http\Controllers\
    | AdminBankController / BankDirectoryController) — never instead of it.
    | Every one of these is optional: a provider whose credentials are blank
    | below just contributes nothing to search results (BankProviderInterface
    | ::isConfigured() returns false, no HTTP call is made, nothing errors).
    | None of these can be signed up for on your behalf — each is a real
    | fintech developer account you create yourself, then paste the
    | resulting keys into .env. See each provider class's doc comment for
    | where to sign up and what the sandbox limits are.
    |
    */

    // Plaid Institutions API — powers the domestic ("Another bank") search.
    // Sign up: https://dashboard.plaid.com/signin (free Sandbox tier).
    'plaid' => [
        'client_id' => env('PLAID_CLIENT_ID'),
        'secret' => env('PLAID_SECRET'),
        // sandbox | development | production
        'env' => env('PLAID_ENV', 'sandbox'),
        // Matches this app's existing seeded domestic banks (all US).
        'country_codes' => env('PLAID_COUNTRY_CODES', 'US'),
    ],

    // TrueLayer Payments v3 provider search — one of three international
    // sources. Sign up: https://console.truelayer.com/ (free Sandbox tier).
    'truelayer' => [
        'client_id' => env('TRUELAYER_CLIENT_ID'),
        'client_secret' => env('TRUELAYER_CLIENT_SECRET'),
        // sandbox | live
        'env' => env('TRUELAYER_ENV', 'sandbox'),
    ],

    // Token.io Banks v2 — one of three international sources. Sandbox uses
    // simple API-key ("Basic") auth; production requires JWT request
    // signing, which is out of scope here (see TokenIoBankProvider).
    // Sign up: https://console.token.io/.
    'token_io' => [
        'api_key' => env('TOKENIO_API_KEY'),
        'member_id' => env('TOKENIO_MEMBER_ID'),
        'env' => env('TOKENIO_ENV', 'sandbox'),
    ],

    // Open Payments (openbankingplatform.com) ASPSP directory — one of
    // four international sources. Sign up: https://developer.openpayments.io/.
    'open_payments' => [
        'client_id' => env('OPENPAYMENTS_CLIENT_ID'),
        'client_secret' => env('OPENPAYMENTS_CLIENT_SECRET'),
    ],

    // Salt Edge Providers API — one of four international sources, and the
    // one covering Asia-Pacific (TrueLayer/Token.io/Open Payments above are
    // Europe-only). Sign up: https://www.saltedge.com/clients/sign_up.
    'salt_edge' => [
        'app_id' => env('SALTEDGE_APP_ID'),
        'secret' => env('SALTEDGE_SECRET'),
        // sandbox | production — informational only; Salt Edge itself
        // decides what your App-id/Secret can see (see SaltEdgeBankProvider).
        'env' => env('SALTEDGE_ENV', 'sandbox'),
        // Comma-separated ISO country codes to keep this scoped to Asia-
        // Pacific rather than duplicating the Europe providers above.
        // Blank = no country filter (every provider Salt Edge returns).
        'country_codes' => env('SALTEDGE_COUNTRY_CODES', 'JP,SG,HK,CN,IN,PH,VN,MY,TH,KR,ID,TW'),
    ],

];
