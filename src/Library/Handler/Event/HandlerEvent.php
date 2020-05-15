<?php

namespace App\Library\Handler\Event;

use App\Library\Handler\HandlerOptions;
use Symfony\Contracts\EventDispatcher\Event;

class HandlerEvent extends Event
{
    /**
     * @var HandlerOptions
     */
    private $handlerOptions;

    /**
     * @var string|null
     */
    private $debugMessages;

    /**
     * @var bool|null
     */
    private $stop;

    public function __construct(HandlerOptions $handlerOptions, $debugMessages = null)
    {
        $this->handlerOptions = $handlerOptions;
        $this->debugMessages = $debugMessages;
    }

    /**
     * @return HandlerOptions
     */
    public function getHandlerOptions(): HandlerOptions
    {
        return $this->handlerOptions;
    }

    /**
     * @param HandlerOptions $handlerOptions
     * @return HandlerEvent
     */
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
     * @return HandlerEvent
     */
    public function setDebugMessages($debugMessages)
    {
        $this->debugMessages = $debugMessages;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getStop(): ?bool
    {
        return $this->stop;
    }

    /**
     * @param bool|null $stop
     * @return HandlerEvent
     */
    public function setStop(?bool $stop): HandlerEvent
    {
        $this->stop = $stop;
        return $this;
    }
}
