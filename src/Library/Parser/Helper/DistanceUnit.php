<?php

namespace App\Library\Parser\Helper;

class DistanceUnit
{
    public const DISTANCE = [
        'km' => [
            'name' => 'kilometer',
            'toMeters' => 1000
        ],
        'mi' => [
            'name' => 'mile',
            'toMeters' => 1609.344
        ],
        'm' => [
            'name' => 'meter',
            'toMeters' => 1
        ],
        'yds' => [
            'name' => 'yard',
            // Don't convert yards to meters....
            'toMeters' => 1
        ]
    ];

    public static function getFullName($shortName): ?string
    {
        if (self::isValid($shortName)) {
            $attributes = self::DISTANCE[$shortName];

            return $attributes['name'];
        }
        return null;
    }

    public static function convertToMeters($shortName, $distance): int|float|null
    {
        if (self::isValid($shortName)) {
            $attributes = self::DISTANCE[$shortName];

            return $attributes['toMeters'] * $distance;
        }
        return null;
    }

    public static function withPaceUOM($shortName): ?string
    {
        return match ($shortName) {
            'mpk' => 'km',
            'mpm' => 'mi',
            default => null,
        };
    }

    public static function withSpeedUOM($shortName): ?string
    {
        return match ($shortName) {
            'kph' => 'km',
            'mph' => 'mi',
            default => null,
        };
    }

    public static function isValid(string $shortName)
    {
        if (array_key_exists($shortName, self::DISTANCE)) {
            return true;
        }

        throw new \Exception('Invalid distance! It must either be km, mi, or m. You provided - ' . $shortName);
    }
}
