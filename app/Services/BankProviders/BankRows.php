<?php

namespace App\Services\BankProviders;

/**
 * The one shape every bank picker row has (see BankProviderInterface::search()),
 * and a way to rebuild rows in that shape from data whose shape isn't known
 * for sure — for example a list read back from the cache.
 *
 * @phpstan-type BankRow array{name: string, country: string|null, swift_code: string|null, currency: string|null, routing_number: string|null, meta: string, source: string}
 */
final class BankRows
{
    /**
     * Rebuilds a list of bank rows in the picker's shape, skipping anything
     * that isn't a row with a name.
     *
     * @return list<BankRow>
     */
    public static function normalize(mixed $rows): array
    {
        if (! is_array($rows)) {
            return [];
        }

        $normalized = [];

        foreach ($rows as $row) {
            if (! is_array($row) || ! is_string($row['name'] ?? null) || $row['name'] === '') {
                continue;
            }

            $normalized[] = [
                'name' => $row['name'],
                'country' => self::stringOrNull($row['country'] ?? null),
                'swift_code' => self::stringOrNull($row['swift_code'] ?? null),
                'currency' => self::stringOrNull($row['currency'] ?? null),
                'routing_number' => self::stringOrNull($row['routing_number'] ?? null),
                'meta' => self::stringOrNull($row['meta'] ?? null) ?? '',
                'source' => self::stringOrNull($row['source'] ?? null) ?? '',
            ];
        }

        return $normalized;
    }

    private static function stringOrNull(mixed $value): ?string
    {
        return is_string($value) ? $value : null;
    }
}
