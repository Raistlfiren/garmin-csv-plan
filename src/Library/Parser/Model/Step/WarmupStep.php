<?php

namespace App\Library\Parser\Model\Step;

class WarmupStep extends AbstractStep
{
    protected function getStepTypeId()
    {
        return 1;
    }

    protected function getStepTypeKey()
    {
        return 'warmup';
    }
}
