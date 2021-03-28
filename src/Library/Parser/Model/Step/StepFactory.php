<?php

namespace App\Library\Parser\Model\Step;

class StepFactory
{
    public static function build($header, $parameters, $notes, $order)
    {
        switch ($header) {
            case 'warmup':
                return new WarmupStep($parameters, $notes, $order);
                break;
            case 'cooldown':
                return new CooldownStep($parameters, $notes, $order);
                break;
            case 'run':
            case 'bike':
            case 'go':
                return new IntervalStep($parameters, $notes, $order);
                break;
            case 'recover':
                return new RecoverStep($parameters, $notes, $order);
                break;
            case 'rest':
                return new RestStep($parameters, $notes, $order);
                break;
            case 'repeat':
                return new RepeaterStep($parameters, $order);
                break;
            default:
                break;
        }
    }
}
