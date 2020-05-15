<?php

namespace App\Library\Parser\Model\Duration;

class DurationFactory
{
    public static function build($durationText)
    {
        if ($durationText === 'lap-button') {
            return new LapButtonDuration();
        }

        $durationDistance = DistanceDuration::testDistance($durationText);

        if ($durationDistance) {
            return $durationDistance;
        }

        $timedDistance = TimedDuration::testTimed($durationText);

        if ($timedDistance) {
            return $timedDistance;
        }

        throw new \Exception('Invalid duration for - ' . $durationText);
    }
}
