<?php

namespace App\Library\Parser\Model\Workout;

class CyclingWorkout extends AbstractWorkout
{
    protected function getSportTypeId()
    {
        return 2;
    }

    protected function getSportTypeKey()
    {
        return 'cycling';
    }
}
