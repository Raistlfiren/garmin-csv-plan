<?php

namespace App\Library\Parser\Model\Step;

class RecoverStep extends AbstractStep
{
    protected function getStepTypeId()
    {
        return 4;
    }

    protected function getStepTypeKey()
    {
        return 'recover';
    }
}
