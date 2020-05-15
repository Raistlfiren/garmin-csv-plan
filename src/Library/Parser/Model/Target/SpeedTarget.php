<?php

namespace App\Library\Parser\Model\Target;

class SpeedTarget extends AbstractTarget
{
    const REGEX = '/^(\d{1,3}(\.\d{1})?)\s*-\s*(\d{1,3}(\.\d{1})?)\s*(kph|mph)?/';

    protected $from;

    protected $to;

    public static function testSpeed($speedText)
    {
        $result = preg_match(self::REGEX, $speedText, $speed);

        if ($result && isset($speed[1]) && ! empty($speed[1]) && isset($speed[2]) && ! empty($speed[2])) {
            return new SpeedTarget($speed[1], $speed[2]);
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
        return 5;
    }

    protected function getTypeKey()
    {
        return 'speed.zone';
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
