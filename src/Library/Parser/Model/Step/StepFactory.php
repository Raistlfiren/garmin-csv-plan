<?php

namespace App\Library\Parser\Model\Step;

class StepFactory
{
    public static function build($header, $parameters, $notes, $order, $swimming = false)
    {
        switch ($header) {
            case 'warmup':
                return new WarmupStep($parameters, $notes, $order, $swimming);
                break;
            case 'cooldown':
                return new CooldownStep($parameters, $notes, $order, $swimming);
                break;
            case 'run':
            case 'bike':
            case 'go':
            case 'other':
            case 'swim':
                return new IntervalStep($parameters, $notes, $order, $swimming);
                break;
            case 'recover':
                return new RecoverStep($parameters, $notes, $order, $swimming);
                break;
            case 'rest':
                return new RestStep($parameters, $notes, $order, $swimming);
                break;
            case 'repeat':
                return new RepeaterStep($parameters, $order);
            default:
                break;
        }
    }
}
