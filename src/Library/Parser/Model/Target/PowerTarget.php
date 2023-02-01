<?php

namespace App\Library\Parser\Model\Target;

class PowerTarget extends AbstractTarget
{
    const REGEX = '/^(\d{1,2}:\d{2})\s*-\s*(\d{1,2}:\d{2})\s*(mpk|mpm)?$/';

    protected $from;

    protected $to;

    public static function testPower($powerText)
    {
        $result = $powerText && preg_match(self::REGEX, $powerText, $power);

        if ($result && isset($power[1]) && ! empty($power[1]) && isset($power[2]) && ! empty($power[2])) {
            return new PowerTarget($power[1], $power[2]);
        }

        return false;
    }

    public function __construct($from, $to)
    {
        $this->from = $from;
        $this->to = $to;
    }

    protected function getTypeId()
    {
        return 2;
    }

    protected function getTypeKey()
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
