<?php

namespace App\Library\Parser\Model\Step;

class RestStep extends AbstractStep
{
    protected function getStepTypeId(): int
    {
        return 5;
    }

    protected function getStepTypeKey(): string
    {
        return 'rest';
    }
}
