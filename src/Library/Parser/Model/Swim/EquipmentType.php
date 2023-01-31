<?php

namespace App\Library\Parser\Model\Swim;

class EquipmentType implements \JsonSerializable
{
    const EQUIPMENT = [
        'fins' => 'Fins',
        'kickboard' => 'Kickboard',
        'paddles' => 'Paddles',
        'pull_buoy' => 'Pull Buoy',
        'snorkel' => 'Snorkel'
    ];

    protected $equipmentKey;

    protected $equipmentId;

    public static function testEquipment($equipmentText)
    {
        $counter = 1;
        foreach (self::EQUIPMENT as $key => $equipment) {
            if (stripos($equipmentText, $equipment) !== false) {
                return new EquipmentType($key, $counter);
            }
            $counter++;
        }

        // Default to null
        return new EquipmentType(null, null);
    }

    public function __construct($equipmentKey, $equipmentId)
    {
        $this->equipmentKey = $equipmentKey;
        $this->equipmentId = $equipmentId;
    }

    protected function getTypeKey()
    {
        return $this->equipmentKey;
    }

    protected function getTypeId()
    {
        return $this->equipmentId;
    }

    public function jsonSerialize(): array
    {
        return [
            'equipmentType' => [
                'equipmentTypeKey' => $this->getTypeKey(),
                'equipmentTypeId' => $this->getTypeId()
            ],
        ];
    }
}
