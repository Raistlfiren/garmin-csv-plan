<?php

namespace App\Library\Parser\Model\Workout;

class SwimmingWorkout extends AbstractWorkout
{
    protected function getSportTypeId()
    {
        return 4;
    }

    protected function getSportTypeKey()
    {
        return 'swimming';
    }
}
