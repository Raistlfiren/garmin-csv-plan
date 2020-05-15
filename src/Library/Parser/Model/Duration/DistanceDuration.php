<?php

namespace App\Library\Parser\Model\Duration;

use App\Library\Parser\Helper\DistanceUnit;

class DistanceDuration extends AbstractDuration
{
    const REGEX = '/^(\d+(.\d+)?)\s*(km|mi|m)$/';

    protected $distance;

    protected $type;

    public static function testDistance($durationText)
    {
        $result = preg_match(self::REGEX, $durationText, $distance);

        if ($result && isset($distance[1]) && ! empty($distance[1]) && isset($distance[3]) && ! empty($distance[3])) {
            return new DistanceDuration($distance[1], $distance[3]);
        }

        return false;
    }

    public function __construct($distance, $type)
    {
        $this->distance = $distance;
        $this->type = $type;
    }


    protected function getTypeKey()
    {
        return 'distance';
    }

    protected function getTypeId()
    {
        return 3;
    }

    protected function getPreferredEndConditionUnit()
    {
        return ['unitKey' => DistanceUnit::getFullName($this->type)];
    }

    protected function getConditionValue()
    {
        return DistanceUnit::convertToMeters($this->type, $this->distance);
    }
}
