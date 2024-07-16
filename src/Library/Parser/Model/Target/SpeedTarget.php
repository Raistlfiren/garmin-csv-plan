<?php

namespace App\Library\Parser\Model\Target;

class SpeedTarget extends AbstractTarget
{
    public const REGEX = '/^(\d{1,3}(\.\d{1})?)\s*-\s*(\d{1,3}(\.\d{1})?)\s*(kph|mph)?/';

    public static function testSpeed($speedText): false|\App\Library\Parser\Model\Target\SpeedTarget
    {
        $result = $speedText && preg_match(self::REGEX, (string) $speedText, $speed);
        if (!$result) {
            return false;
        }
        if (!isset($speed[1])) {
            return false;
        }
        if (empty($speed[1])) {
            return false;
        }
        if (!isset($speed[2])) {
            return false;
        }
        if (empty($speed[2])) {
            return false;
        }
        return new SpeedTarget($speed[1], $speed[2]);
    }

    public function __construct(protected $from, protected $to)
    {
    }

    protected function getTypeId(): int
    {
        return 5;
    }

    protected function getTypeKey(): string
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
