<?php

namespace App\Library\Parser\Model\Target;

class TargetFactory
{
    public static function build($targetText, ?int $criticalPower = null)
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

        $powerTarget = PowerTarget::testPower($targetText, $criticalPower);

        if ($powerTarget) {
           return $powerTarget;
        }

        return null;
    }
}
