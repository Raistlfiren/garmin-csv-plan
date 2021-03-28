<?php

namespace App\Model;

trait DebugMessages
{
    protected $debugMessages = [];

    /**
     * @return array
     */
    public function getDebugMessages(): array
    {
        return $this->debugMessages;
    }

    /**
     * @param array $debugMessages
     * @return $this
     */
    public function setDebugMessages(array $debugMessages)
    {
        $this->debugMessages = $debugMessages;
        return $this;
    }
}