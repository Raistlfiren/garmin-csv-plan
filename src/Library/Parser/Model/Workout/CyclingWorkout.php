<?php

namespace App\Library\Parser\Model\Workout;

class CyclingWorkout extends AbstractWorkout
{
    protected function getSportTypeId(): int
    {
        return 2;
    }

    protected function getSportTypeKey(): string
    {
        return 'cycling';
    }
}
