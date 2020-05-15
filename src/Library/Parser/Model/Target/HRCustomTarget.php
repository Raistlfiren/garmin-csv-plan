<?php

namespace App\Library\Parser\Model\Target;

class HRCustomTarget extends AbstractTarget
{
    const REGEX = '/^(\d{1,3})\s*-\s*(\d{1,3})\s*bpm$/';

    protected $from;

    protected $to;

    public static function testHR($hrText)
    {
        $result = preg_match(self::REGEX, $hrText, $hr);

        if ($result && isset($hr[1]) && ! empty($hr[1]) && isset($hr[2]) && ! empty($hr[2])) {
            return new HRCustomTarget($hr[1], $hr[2]);
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
        return 4;
    }

    protected function getTypeKey()
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
