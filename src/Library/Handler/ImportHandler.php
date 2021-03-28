<?php

namespace App\Library\Handler;

class ImportHandler extends AbstractHandler
{
    public function supports(string $command)
    {
        return $command === 'import';
    }
}
