<?php

namespace App\Library\Parser\Model\Step;

class CooldownStep extends AbstractStep
{
    protected function getStepTypeId()
    {
        return 2;
    }

    protected function getStepTypeKey()
    {
        return 'cooldown';
    }
}
