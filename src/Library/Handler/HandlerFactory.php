<?php

namespace App\Library\Handler;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\OutputStyle;

class HandlerFactory
{
    public function __construct(
        /**
         * @var $iterableHandlers
         */
        protected iterable $iterableHandlers
    ) {
    }

    public function buildCommand(HandlerOptions $handlerOptions)
    {
        foreach ($this->iterableHandlers as $handler) {
            if ($handler->supports($handlerOptions->getCommand())) {
                return $handler->handle($handlerOptions);
            }
        }

        throw new \Exception('Invalid handler. Please use a valid handler other than what was supplied - ' . $handlerOptions->getCommand());
    }
}
