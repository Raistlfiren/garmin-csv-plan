<?php

namespace App\Library\Parser\Model\Step;

class RecoverStep extends AbstractStep
{
    protected function getStepTypeId(): int
    {
        return 4;
    }

    protected function getStepTypeKey(): string
    {
        return 'recover';
    }
}
