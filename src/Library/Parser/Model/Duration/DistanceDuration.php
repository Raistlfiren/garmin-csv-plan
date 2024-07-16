<?php

namespace App\Library\Parser\Model\Duration;

use App\Library\Parser\Helper\DistanceUnit;

class DistanceDuration extends AbstractDuration
{
    public const REGEX = '/^(\d+(.\d+)?)\s*(km|mi|m|yds)$/';

    public static function testDistance($durationText): false|\App\Library\Parser\Model\Duration\DistanceDuration
    {
        $result = $durationText && preg_match(self::REGEX, (string) $durationText, $distance);
        if (!$result) {
            return false;
        }
        if (!isset($distance[1])) {
            return false;
        }
        if (empty($distance[1])) {
            return false;
        }
        if (!isset($distance[3])) {
            return false;
        }
        if (empty($distance[3])) {
            return false;
        }
        return new DistanceDuration($distance[1], $distance[3]);
    }

    public function __construct(protected $distance, protected $type)
    {
    }


    protected function getTypeKey(): string
    {
        return 'distance';
    }

    protected function getTypeId(): int
    {
        return 3;
    }

    protected function getPreferredEndConditionUnit(): array
    {
        return ['unitKey' => DistanceUnit::getFullName($this->type)];
    }

    protected function getConditionValue()
    {
        return DistanceUnit::convertToMeters($this->type, $this->distance);
    }
}
