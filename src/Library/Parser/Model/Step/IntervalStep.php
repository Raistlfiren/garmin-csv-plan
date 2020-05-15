<?php

namespace App\Library\Parser\Model\Step;

class IntervalStep extends AbstractStep
{
    protected function getStepTypeId()
    {
        return 3;
    }

    protected function getStepTypeKey()
    {
        return 'interval';
    }
}
