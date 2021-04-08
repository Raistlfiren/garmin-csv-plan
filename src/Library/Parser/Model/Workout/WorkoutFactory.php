<?php

namespace App\Library\Parser\Model\Workout;

class WorkoutFactory
{
    public static function build($type, $name, $steps, $poolSize = null)
    {
        switch ($type) {
            case 'running':
                $workout = new RunningWorkout($name);
                return $workout->steps($steps);
            case 'cycling':
                $workout = new CyclingWorkout($name);
                return $workout->steps($steps);
            case 'swimming':
                $workout = new SwimmingWorkout($name, $poolSize);
                return $workout->steps($steps, true);
            case 'custom':
                $workout = new CustomWorkout($name);
                return $workout->steps($steps);
            default:
                break;
        }
    }
}
