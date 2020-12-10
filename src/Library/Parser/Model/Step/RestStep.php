<?php

namespace App\Library\Parser\Model\Step;

class RestStep extends AbstractStep
{
    protected function getStepTypeId()
    {
        return 5;
    }

    protected function getStepTypeKey()
    {
        return 'rest';
    }
}
