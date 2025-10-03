<?php

namespace App\Library\Handler;

use App\Http\Mfa\CodeProviderInterface;

class HandlerFactory
{
    public function __construct(
        /**
         * @var $iterableHandlers
         */
        protected iterable $iterableHandlers
    ) {
    }

    public function buildCommand(HandlerOptions $handlerOptions, CodeProviderInterface $mfaCodeProvider): void
    {
        foreach ($this->iterableHandlers as $handler) {
            if ($handler->supports($handlerOptions->getCommand())) {
                $handler->handle($handlerOptions, $mfaCodeProvider);
                return;
            }
        }

        throw new \Exception('Invalid handler. Please use a valid handler other than what was supplied - ' . $handlerOptions->getCommand());
    }
}
