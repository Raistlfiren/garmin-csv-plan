<?php

namespace App\Library\Parser\Model\Target;

class HRZoneTarget extends AbstractTarget
{
    public const REGEX = '/^z(\d)$/';

    public static function testHR($hrText): false|\App\Library\Parser\Model\Target\HRZoneTarget
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
        return new HRZoneTarget($hr[1]);
    }

    public function __construct(protected $zone)
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

    protected function getZoneNumber()
    {
        return $this->zone;
    }
}
