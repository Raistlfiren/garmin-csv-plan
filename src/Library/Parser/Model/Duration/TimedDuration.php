<?php

namespace App\Library\Parser\Model\Duration;

class TimedDuration extends AbstractDuration
{
    public const REGEX = '/^(\d{1,3}):(\d{2})$/';

    public static function testTimed($durationText): false|\App\Library\Parser\Model\Duration\TimedDuration
    {
        $result = $durationText && preg_match(self::REGEX, (string) $durationText, $timed);
        if (!$result) {
            return false;
        }
        if (!isset($timed[1])) {
            return false;
        }
        if (!isset($timed[2])) {
            return false;
        }
        if (empty($timed[1]) && empty($timed[2])) {
            return false;
        }
        return new TimedDuration($timed[1], $timed[2]);
    }

    public function __construct(protected $minutes, protected $seconds)
    {
    }

    protected function getTypeKey(): string
    {
        return 'time';
    }

    protected function getTypeId(): int
    {
        return 2;
    }

    protected function getPreferredEndConditionUnit()
    {
        return null;
    }

    protected function getConditionValue(): int|float
    {
        return $this->minutes * 60 + $this->seconds;
    }
}
