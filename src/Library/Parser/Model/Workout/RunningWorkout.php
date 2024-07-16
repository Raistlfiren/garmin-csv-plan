<?php

namespace App\Library\Parser\Model\Workout;

class RunningWorkout extends AbstractWorkout
{
    protected function getSportTypeId(): int
    {
        return 1;
    }

    protected function getSportTypeKey(): string
    {
        return 'running';
    }
}
