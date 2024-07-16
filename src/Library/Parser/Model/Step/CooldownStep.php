<?php

namespace App\Library\Parser\Model\Step;

class CooldownStep extends AbstractStep
{
    protected function getStepTypeId(): int
    {
        return 2;
    }

    protected function getStepTypeKey(): string
    {
        return 'cooldown';
    }
}
