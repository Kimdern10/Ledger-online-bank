<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Model;

/**
 * Receipt links use the record's reference ("LDG261014K7Q2MX") instead of
 * its database id — it's what's printed on the receipt, it says nothing
 * about the kind of transfer, and unlike 1, 2, 3 it can't be counted
 * through. route('send.receipt', $transfer) and friends pick this up
 * automatically.
 *
 * A plain number is still accepted when a link is opened, so receipt links
 * created before this change (in emails, notifications, bookmarks) keep
 * working.
 */
trait HasReferenceRouteKey
{
    public function getRouteKeyName(): string
    {
        return 'reference';
    }

    /**
     * @param  mixed  $value
     * @param  string|null  $field
     */
    public function resolveRouteBinding($value, $field = null): ?Model
    {
        if ($field === null && ctype_digit((string) $value)) {
            return $this->whereKey((int) $value)->first();
        }

        return $this->where($field ?? 'reference', $value)->first();
    }
}
