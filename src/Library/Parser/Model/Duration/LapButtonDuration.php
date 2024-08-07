<?php

namespace App\Library\Parser\Model\Duration;

class LapButtonDuration extends AbstractDuration
{
    protected function getTypeKey(): string
    {
        return 'lap.button';
    }

    protected function getTypeId(): int
    {
        return 1;
    }

    protected function getPreferredEndConditionUnit()
    {
        return null;
    }

    protected function getConditionValue()
    {
        return null;
    }
}
