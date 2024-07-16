<?php

namespace App\Library\Parser\Model\Swim;

class EquipmentType implements \JsonSerializable
{
    public const EQUIPMENT = [
        'fins' => 'Fins',
        'kickboard' => 'Kickboard',
        'paddles' => 'Paddles',
        'pull_buoy' => 'Pull Buoy',
        'snorkel' => 'Snorkel'
    ];

    public static function testEquipment($equipmentText): \App\Library\Parser\Model\Swim\EquipmentType
    {
        $counter = 1;
        foreach (self::EQUIPMENT as $key => $equipment) {
            if (stripos((string) $equipmentText, $equipment) !== false) {
                return new EquipmentType($key, $counter);
            }

            $counter++;
        }

        // Default to null
        return new EquipmentType(null, null);
    }

    public function __construct(protected $equipmentKey, protected $equipmentId)
    {
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
