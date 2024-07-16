<?php

namespace App\Library\Parser\Model\Target;

class CadenceTarget extends AbstractTarget
{
    public const REGEX = '/^(\d{1,3})\s*-\s*(\d{1,3})\s*rpm$/';

    public static function testCadence($cadenceText): false|\App\Library\Parser\Model\Target\CadenceTarget
    {
        $result = $cadenceText && preg_match(self::REGEX, (string) $cadenceText, $cadence);
        if (!$result) {
            return false;
        }
        if (!isset($cadence[1])) {
            return false;
        }
        if (empty($cadence[1])) {
            return false;
        }
        if (!isset($cadence[2])) {
            return false;
        }
        if (empty($cadence[2])) {
            return false;
        }
        return new CadenceTarget($cadence[1], $cadence[2]);
    }

    public function __construct(protected $from, protected $to)
    {
    }

    protected function getTypeId(): int
    {
        return 3;
    }

    protected function getTypeKey(): string
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
