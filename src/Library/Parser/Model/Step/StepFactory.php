<?php

namespace App\Library\Parser\Model\Step;

class StepFactory
{
    public static function build($header, $parameters, $notes, $order, $swimming = false, ?int $criticalPower = null): WarmupStep|CooldownStep|IntervalStep|RecoverStep|RestStep|RepeaterStep|null
    {
        switch ($header) {
            case 'warmup':
                return new WarmupStep($parameters, $notes, $order, $swimming, $criticalPower);
            case 'cooldown':
                return new CooldownStep($parameters, $notes, $order, $swimming, $criticalPower);
            case 'run':
            case 'bike':
                return new IntervalStep($parameters, $notes, $order, $swimming, $criticalPower);
            case 'go':
            case 'other':
            case 'swim':
                return new IntervalStep($parameters, $notes, $order, $swimming, $criticalPower);
            case 'recover':
                return new RecoverStep($parameters, $notes, $order, $swimming), $criticalPower;
            case 'rest':
                return new RestStep($parameters, $notes, $order, $swimming, $criticalPower);
            case 'repeat':
                return new RepeaterStep($parameters, $order, $swimming, $criticalPower;
            default:
                break;
        }
        return null;
    }
}
