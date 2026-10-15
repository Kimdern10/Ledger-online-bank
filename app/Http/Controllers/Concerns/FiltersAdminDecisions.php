<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * Search and filter for the admin lists of decisions already made — the
 * "Decided recently" tables on Card requests, Identity verification and
 * Address verification, and the Reported cards list. Used by
 * AdminCardController, AdminKycController and AdminAddressController.
 *
 * Search matches the customer's first name, last name or email: every word
 * typed has to match one of them, so "ada okafor" finds Ada Okafor.
 */
trait FiltersAdminDecisions
{
    /**
     * The search text from ?q=, trimmed and capped at 100 characters.
     */
    protected function decisionSearch(Request $request): string
    {
        return trim(mb_substr((string) $request->query('q', ''), 0, 100));
    }

    /**
     * The status picked in the filter dropdown (?decision= by default), or
     * null when it isn't one of $allowed.
     *
     * @param  list<string>  $allowed
     */
    protected function decisionFilter(Request $request, array $allowed, string $field = 'decision'): ?string
    {
        $value = (string) $request->query($field, '');

        return in_array($value, $allowed, true) ? $value : null;
    }

    /**
     * Narrows $query to rows whose customer matches $search.
     *
     * @template TModel of Model
     *
     * @param  Builder<TModel>  $query
     * @return Builder<TModel>
     */
    protected function whereCustomerMatches(Builder $query, string $search): Builder
    {
        $words = preg_split('/\s+/', $search, -1, PREG_SPLIT_NO_EMPTY) ?: [];

        if ($words === []) {
            return $query;
        }

        return $query->whereHas('user', function (Builder $user) use ($words) {
            foreach ($words as $word) {
                $like = '%'.$word.'%';

                $user->where(fn (Builder $match) => $match
                    ->where('first_name', 'like', $like)
                    ->orWhere('last_name', 'like', $like)
                    ->orWhere('email', 'like', $like));
            }
        });
    }
}
