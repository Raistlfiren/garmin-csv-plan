<?php

namespace App\Library\Parser\Helper;

class DistanceUnit
{
    const DISTANCE = [
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
        ]
    ];

    public static function getFullName($shortName)
    {
        if (self::isValid($shortName)) {
            $attributes = self::DISTANCE[$shortName];

            return $attributes['name'];
        }
    }

    public static function convertToMeters($shortName, $distance)
    {
        if (self::isValid($shortName)) {
            $attributes = self::DISTANCE[$shortName];

            return $attributes['toMeters'] * $distance;
        }
    }

    public static function withPaceUOM($shortName)
    {
        switch($shortName) {
            case 'mpk':
                return 'km';
                break;
            case 'mpm':
                return 'mi';
                break;
        }
    }

    public static function withSpeedUOM($shortName)
    {
        switch($shortName) {
            case 'kph':
                return 'km';
                break;
            case 'mph':
                return 'mi';
                break;
        }
    }

    public static function isValid($shortName)
    {
        if (array_key_exists($shortName, self::DISTANCE)) {
            return true;
        }

        throw new \Exception('Invalid distance! It must either be km, mi, or m. You provided - ' . $shortName);
    }
}
