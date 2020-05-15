<?php

namespace App\Library\Parser\Model\Workout;

class RunningWorkout extends AbstractWorkout
{
    protected function getSportTypeId()
    {
        return 1;
    }

    protected function getSportTypeKey()
    {
        return 'running';
    }
}
