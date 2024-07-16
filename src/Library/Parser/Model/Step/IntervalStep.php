<?php

namespace App\Library\Parser\Model\Step;

class IntervalStep extends AbstractStep
{
    protected function getStepTypeId(): int
    {
        return 3;
    }

    protected function getStepTypeKey(): string
    {
        return 'interval';
    }
}
