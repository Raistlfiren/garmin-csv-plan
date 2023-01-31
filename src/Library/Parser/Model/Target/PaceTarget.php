<?php

namespace App\Library\Parser\Model\Target;

use App\Library\Parser\Helper\DistanceUnit;

class PaceTarget extends AbstractTarget
{
    const REGEX = '/^(\d{1,2}:\d{2})\s*-\s*(\d{1,2}:\d{2})\s*(mpk|mpm)?$/';

    protected $from;

    protected $to;

    protected $uom;

    protected $metric;

    public static function testPace($paceText)
    {
        $result = preg_match(self::REGEX, $paceText, $pace);

        if ($result && isset($pace[1]) && ! empty($pace[1]) && isset($pace[2]) && ! empty($pace[2])) {
            $from = $pace[1];
            $to = $pace[2];
            $uom = 'km';

            if (isset($pace[3]) && ! empty($pace[3])) {
                $uom = DistanceUnit::withPaceUOM($pace[3]);
            }

            return new PaceTarget($from, $to, $uom);
        }

        return false;
    }

    public function __construct($from, $to, $uom)
    {
        $this->from = $from;
        $this->to = $to;
        $this->uom = DistanceUnit::DISTANCE[$uom];
    }

    protected function getTypeId()
    {
        return 6;
    }

    protected function getTypeKey()
    {
        return 'pace.zone';
    }

    protected function getTargetValueOne()
    {
        return $this->handlePace($this->from);
    }

    protected function getTargetValueTwo()
    {
        return $this->handlePace($this->to);
    }

    protected function handlePace($time)
    {
        $minutes = 0;
        $seconds = 0;

        $timeArray = explode(':', $time);

        if (isset($timeArray[0]) && ! empty($timeArray[0])) {
            $minutes = trim($timeArray[0]);
        }

        if (isset($timeArray[1]) && ! empty($timeArray[1])) {
            $seconds = trim($timeArray[1]);
        }

        return round($this->uom['toMeters'] / ($minutes * 60 + $seconds), 6);
    }
}
