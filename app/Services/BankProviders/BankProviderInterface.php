<?php

namespace App\Services\BankProviders;

/**
 * A live, third-party bank-directory lookup — one of several optional
 * sources merged alongside the admin-curated App\Models\Bank directory by
 * App\Http\Controllers\BankDirectoryController. See that controller's doc
 * comment for how results from several providers get combined.
 *
 * Every implementation MUST:
 *  - return false from isConfigured() when its required credentials are
 *    missing, so the controller can skip it silently (no HTTP call, no
 *    error, nothing shown to the user);
 *  - never throw out of search() — catch every failure internally (a
 *    timeout, a 401, a malformed response) and return [] instead, so one
 *    slow/broken/misconfigured provider never breaks the "Select a bank"
 *    picker for the other sources;
 *  - normalize every result to exactly this shape, the same shape
 *    BankDirectoryController already returns for local Bank rows:
 *      [
 *          'name' => string,
 *          'country' => ?string,
 *          'swift_code' => ?string,
 *          'currency' => ?string,
 *          'routing_number' => ?string,
 *          'meta' => string,   // one line of secondary text for the row
 *          'source' => string, // this provider's short name, for de-dupe/debug only — never shown to the user
 *      ]
 *    The picker only ever reads name/country/swift_code/currency (see
 *    selectIntlBank() in send.blade.php) — there's no local `id` to supply
 *    for a bank that only exists in a third-party API, and none is needed;
 *    the sender types/edits these as plain text fields either way.
 */
interface BankProviderInterface
{
    /**
     * True when this provider has everything it needs (API keys, etc.) to
     * attempt a real request. BankDirectoryController checks this BEFORE
     * calling search(), so a misconfigured/unused provider costs nothing.
     */
    public function isConfigured(): bool;

    /**
     * @param  string  $query  Free-text bank name fragment; may be empty
     *                         (an empty query means "show some reasonable
     *                         default list", matching the local directory's
     *                         own behavior when the sheet first opens).
     * @return array<int, array{name: string, country: ?string, swift_code: ?string, currency: ?string, routing_number: ?string, meta: string, source: string}>
     */
    public function search(string $query): array;
}
