<?php

namespace App\Library\Handler\Event;

use App\Library\Handler\HandlerOptions;
use Symfony\Contracts\EventDispatcher\Event;

class HandlerEvent extends Event
{
    private ?bool $stop = null;

    /**
     * @param string|null $debugMessages
     */
    public function __construct(private HandlerOptions $handlerOptions, private $debugMessages = null)
    {
    }

    public function getHandlerOptions(): HandlerOptions
    {
        return $this->handlerOptions;
    }

    public function setHandlerOptions(HandlerOptions $handlerOptions): HandlerEvent
    {
        $this->handlerOptions = $handlerOptions;
        return $this;
    }

    /**
     * @return null
     */
    public function getDebugMessages()
    {
        return $this->debugMessages;
    }

    /**
     * @param null $debugMessages
     */
    public function setDebugMessages($debugMessages): static
    {
        $this->debugMessages = $debugMessages;
        return $this;
    }

    public function getStop(): ?bool
    {
        return $this->stop;
    }

    public function setStop(?bool $stop): HandlerEvent
    {
        $this->stop = $stop;
        return $this;
    }
}
