<?php

namespace App\Library\Handler;

use App\Http\Mfa\CodeProviderInterface;

interface HandlerInterface
{
    public function handle(HandlerOptions $handlerOptions, CodeProviderInterface $mfaCodeProvider): void;

    public function supports(string $command);
}
