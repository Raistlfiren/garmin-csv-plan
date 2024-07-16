<?php

namespace App\Library\Parser\Model\Target;

use App\Library\Parser\Helper\DistanceUnit;

class PaceTarget extends AbstractTarget
{
    public const REGEX = '/^(\d{1,2}:\d{2})\s*-\s*(\d{1,2}:\d{2})\s*(mpk|mpm)?$/';

    protected $uom;

    protected $metric;

    public static function testPace($paceText): \App\Library\Parser\Model\Target\PaceTarget|false
    {
        $result = $paceText && preg_match(self::REGEX, (string) $paceText, $pace);

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

    public function __construct(protected $from, protected $to, $uom)
    {
        $this->uom = DistanceUnit::DISTANCE[$uom];
    }

    protected function getTypeId(): int
    {
        return 6;
    }

    protected function getTypeKey(): string
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

    protected function handlePace($time): float
    {
        $minutes = 0;
        $seconds = 0;

        $timeArray = explode(':', (string) $time);

        if (isset($timeArray[0]) && ! empty($timeArray[0])) {
            $minutes = trim($timeArray[0]);
        }

        if (isset($timeArray[1]) && ! empty($timeArray[1])) {
            $seconds = trim($timeArray[1]);
        }

        return round($this->uom['toMeters'] / ($minutes * 60 + $seconds), 6);
    }
}
