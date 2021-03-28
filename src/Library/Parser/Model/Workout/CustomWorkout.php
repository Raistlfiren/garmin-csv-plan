<?php

namespace App\Library\Parser\Model\Workout;

class CustomWorkout extends AbstractWorkout
{
    protected function getSportTypeId()
    {
        return 3;
    }

    protected function getSportTypeKey()
    {
        return 'other';
    }
}
