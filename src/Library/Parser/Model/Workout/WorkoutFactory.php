<?php

namespace App\Library\Parser\Model\Workout;

class WorkoutFactory
{
    public static function build($type, $name, $steps)
    {
        switch ($type) {
            case 'running':
                $workout = new RunningWorkout($name);
                return $workout->steps($steps);
                break;
            case 'cycling':
                break;
            default:
                break;
        }
    }
}
