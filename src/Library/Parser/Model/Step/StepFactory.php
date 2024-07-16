<?php

namespace App\Library\Parser\Model\Step;

class StepFactory
{
    public static function build($header, $parameters, $notes, $order, $swimming = false): WarmupStep|CooldownStep|IntervalStep|RecoverStep|RestStep|RepeaterStep|null
    {
        switch ($header) {
            case 'warmup':
                return new WarmupStep($parameters, $notes, $order, $swimming);
            case 'cooldown':
                return new CooldownStep($parameters, $notes, $order, $swimming);
            case 'run':
            case 'bike':
            case 'go':
            case 'other':
            case 'swim':
                return new IntervalStep($parameters, $notes, $order, $swimming);
            case 'recover':
                return new RecoverStep($parameters, $notes, $order, $swimming);
            case 'rest':
                return new RestStep($parameters, $notes, $order, $swimming);
            case 'repeat':
                return new RepeaterStep($parameters, $order);
            default:
                break;
        }
        return null;
    }
}
