<?php

namespace App\Library\Parser\Model\Target;

class HRCustomTarget extends AbstractTarget
{
    public const REGEX = '/^(\d{1,3})\s*-\s*(\d{1,3})\s*bpm$/';

    public static function testHR($hrText): false|\App\Library\Parser\Model\Target\HRCustomTarget
    {
        $result = $hrText && preg_match(self::REGEX, (string) $hrText, $hr);
        if (!$result) {
            return false;
        }
        if (!isset($hr[1])) {
            return false;
        }
        if (empty($hr[1])) {
            return false;
        }
        if (!isset($hr[2])) {
            return false;
        }
        if (empty($hr[2])) {
            return false;
        }
        return new HRCustomTarget($hr[1], $hr[2]);
    }

    public function __construct(protected $from, protected $to)
    {
    }

    protected function getTypeId(): int
    {
        return 4;
    }

    protected function getTypeKey(): string
    {
        return 'heart.rate.zone';
    }

    protected function getTargetValueOne()
    {
        return $this->from;
    }

    protected function getTargetValueTwo()
    {
        return $this->to;
    }
}
