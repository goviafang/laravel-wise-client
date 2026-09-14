<?php

declare(strict_types=1);

namespace Govia\WiseClient\Support;

/**
 * Wise response fields have no guaranteed type. This narrows mixed into a concrete
 * type so each DTO's fromArray() doesn't repeat the same guard logic.
 */
final class Caster
{
    public static function string(mixed $value, string $default = ''): string
    {
        return match (true) {
            is_string($value) => $value,
            is_scalar($value) => (string) $value,
            default => $default,
        };
    }

    public static function int(mixed $value, int $default = 0): int
    {
        return is_scalar($value) ? (int) $value : $default;
    }

    public static function float(mixed $value, float $default = 0.0): float
    {
        return is_scalar($value) ? (float) $value : $default;
    }

    /**
     * @return array<array-key, mixed>
     */
    public static function array(mixed $value): array
    {
        return is_array($value) ? $value : [];
    }

    /**
     * @return array<int, string>
     */
    public static function stringList(mixed $value): array
    {
        return array_values(array_map(self::string(...), self::array($value)));
    }

    /**
     * @return array<int, array<array-key, mixed>>
     */
    public static function listOfArrays(mixed $value): array
    {
        return array_values(array_filter(self::array($value), 'is_array'));
    }
}
