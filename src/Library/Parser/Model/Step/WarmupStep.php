<?php

namespace App\Library\Parser\Model\Step;

class WarmupStep extends AbstractStep
{
    protected function getStepTypeId(): int
    {
        return 1;
    }

    protected function getStepTypeKey(): string
    {
        return 'warmup';
    }
}
