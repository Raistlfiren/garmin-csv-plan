<?php

namespace App\Library\Parser\Model\Workout;

class CustomWorkout extends AbstractWorkout
{
    protected function getSportTypeId(): int
    {
        return 3;
    }

    protected function getSportTypeKey(): string
    {
        return 'other';
    }
}
