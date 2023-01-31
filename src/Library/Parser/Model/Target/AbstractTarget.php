<?php

namespace App\Library\Parser\Model\Target;

abstract class AbstractTarget implements \JsonSerializable
{
    abstract protected function getTypeId();

    abstract protected function getTypeKey();

    protected function getTargetValueOne() {
        return '';
    }

    protected function getTargetValueTwo() {
        return '';
    }

    protected function getZoneNumber() {
        return null;
    }

    public function jsonSerialize(): array
    {
        return [
            'targetType' => [
                'workoutTargetTypeId' => $this->getTypeId(),
                'workoutTargetTypeKey' => $this->getTypeKey()
            ],
            'targetValueOne' => $this->getTargetValueOne(),
            'targetValueTwo' => $this->getTargetValueTwo(),
            'zoneNumber' => $this->getZoneNumber()
        ];
    }
}
