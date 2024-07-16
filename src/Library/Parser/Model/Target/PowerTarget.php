<?php

namespace App\Library\Parser\Model\Target;

class PowerTarget extends AbstractTarget
{
    public const REGEX = '/^(\d{1,2}:\d{2})\s*-\s*(\d{1,2}:\d{2})\s*(mpk|mpm)?$/';

    public static function testPower($powerText): false|\App\Library\Parser\Model\Target\PowerTarget
    {
        $result = $powerText && preg_match(self::REGEX, (string) $powerText, $power);
        if (!$result) {
            return false;
        }
        if (!isset($power[1])) {
            return false;
        }
        if (empty($power[1])) {
            return false;
        }
        if (!isset($power[2])) {
            return false;
        }
        if (empty($power[2])) {
            return false;
        }
        return new PowerTarget($power[1], $power[2]);
    }

    public function __construct(protected $from, protected $to)
    {
    }

    protected function getTypeId(): int
    {
        return 2;
    }

    protected function getTypeKey(): string
    {
        return 'power.zone';
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
