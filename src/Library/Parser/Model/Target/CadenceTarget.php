<?php

namespace App\Library\Parser\Model\Target;

class CadenceTarget extends AbstractTarget
{
    const REGEX = '/^(\d{1,3})\s*-\s*(\d{1,3})\s*rpm$/';

    protected $from;

    protected $to;

    public static function testCadence($cadenceText)
    {
        $result = $cadenceText && preg_match(self::REGEX, $cadenceText, $cadence);

        if ($result && isset($cadence[1]) && ! empty($cadence[1]) && isset($cadence[2]) && ! empty($cadence[2])) {
            return new CadenceTarget($cadence[1], $cadence[2]);
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
        return 3;
    }

    protected function getTypeKey()
    {
        return 'cadence.zone';
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
