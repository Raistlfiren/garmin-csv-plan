<?php

namespace App\Library\Parser\Model\Target;

class TargetFactory
{
    public static function build($targetText)
    {
        $paceTarget = PaceTarget::testPace($targetText);

        if ($paceTarget) {
            return $paceTarget;
        }

        $hrZoneTarget = HRZoneTarget::testHR($targetText);

        if ($hrZoneTarget) {
            return $hrZoneTarget;
        }

        $hrCustomTarget = HRCustomTarget::testHR($targetText);

        if ($hrCustomTarget) {
            return $hrCustomTarget;
        }
        return null;
    }
}
