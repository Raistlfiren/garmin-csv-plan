<?php

namespace App\Library\Parser\Model\Duration;

class TimedDuration extends AbstractDuration
{
    const REGEX = '/^(\d{1,3}):(\d{2})$/';

    protected $minutes;

    protected $seconds;

    public static function testTimed($durationText)
    {
        $result = $durationText && preg_match(self::REGEX, $durationText, $timed);

        if ($result && isset($timed[1]) && isset($timed[2]) && (! empty($timed[1]) || ! empty($timed[2]))) {
            return new TimedDuration($timed[1], $timed[2]);
        }

        return false;
    }

    public function __construct($minutes, $seconds)
    {
        $this->minutes = $minutes;
        $this->seconds = $seconds;
    }

    protected function getTypeKey()
    {
        return 'time';
    }

    protected function getTypeId()
    {
        return 2;
    }

    protected function getPreferredEndConditionUnit()
    {
        return null;
    }

    protected function getConditionValue()
    {
        return $this->minutes * 60 + $this->seconds;
    }
}
