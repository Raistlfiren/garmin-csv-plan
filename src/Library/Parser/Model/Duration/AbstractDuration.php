<?php

namespace App\Library\Parser\Model\Duration;

abstract class AbstractDuration implements \JsonSerializable
{
    abstract protected function getTypeKey();

    abstract protected function getTypeId();

    abstract protected function getPreferredEndConditionUnit();

    abstract protected function getConditionValue();

    public function jsonSerialize()
    {
        return [
            'endCondition' => [
                'conditionTypeKey' => $this->getTypeKey(),
                'conditionTypeId' => $this->getTypeId()
            ],
            'preferredEndConditionUnit' => $this->getPreferredEndConditionUnit(),
            'endConditionValue' => $this->getConditionValue(),
            'endConditionCompare' => null,
            'endConditionZone' => null
        ];
    }
}
