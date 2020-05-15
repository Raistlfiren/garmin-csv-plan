<?php

namespace App\Library\Handler;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\OutputStyle;

class HandlerFactory
{
    /**
     * @var $iterableHandlers
     */
    protected $iterableHandlers;

    public function __construct(iterable $handlers)
    {
        $this->iterableHandlers = $handlers;
    }

    public function buildCommand(HandlerOptions $handlerOptions)
    {
        foreach($this->iterableHandlers as $handler) {
            if ($handler->supports($handlerOptions->getCommand())) {
                $handler->handle($handlerOptions);
            }
        }
    }
}
