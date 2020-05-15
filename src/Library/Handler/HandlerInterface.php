<?php

namespace App\Library\Handler;

interface HandlerInterface
{
    public function handle(HandlerOptions $handlerOptions);

    public function supports(string $command);
}
