<?php

namespace App\Library\Parser\Model\Target;

class HRZoneTarget extends AbstractTarget
{
    const REGEX = '/^z(\d)$/';

    protected $zone;

    public static function testHR($hrText)
    {
        $result = $hrText && preg_match(self::REGEX, $hrText, $hr);

        if ($result && isset($hr[1]) && ! empty($hr[1])) {
            return new HRZoneTarget($hr[1]);
        }

        return false;
    }

    public function __construct($zone)
    {
        $this->zone = $zone;
    }

    protected function getTypeId()
    {
        return 4;
    }

    protected function getTypeKey()
    {
        return 'heart.rate.zone';
    }

    protected function getZoneNumber()
    {
        return $this->zone;
    }
}
