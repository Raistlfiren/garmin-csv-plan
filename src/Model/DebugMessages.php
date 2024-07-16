<?php

namespace App\Model;

trait DebugMessages
{
    protected $debugMessages = [];

    public function getDebugMessages(): array
    {
        return $this->debugMessages;
    }

    /**
     * @return $this
     */
    public function setDebugMessages(array $debugMessages)
    {
        $this->debugMessages = $debugMessages;
        return $this;
    }
}