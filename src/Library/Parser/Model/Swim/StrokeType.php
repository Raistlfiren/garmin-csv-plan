<?php

namespace App\Library\Parser\Model\Swim;

class StrokeType implements \JsonSerializable
{
    public const STROKES = [
        'any_stroke' => 'Any Strokes',
        'backstroke' => 'Backstroke',
        'breathstroke' => 'Breathstroke',
        'drill' => 'Drill',
        'fly' => 'Fly',
        'individual_medley' => 'Individual Medley',
        'mixed' => 'Mixed'
    ];

    public static function testStroke($swimText): \App\Library\Parser\Model\Swim\StrokeType
    {
        $counter = 1;
        foreach (self::STROKES as $key => $stroke) {
            if (stripos((string) $swimText, $stroke) !== false) {
                return new StrokeType($key, $counter);
            }

            $counter++;
        }

        // Default to any_strokes
        return new StrokeType('any_strokes', 1);
    }

    public function __construct(protected $strokeKey, protected $strokeId)
    {
    }

    protected function getTypeKey()
    {
        return $this->strokeKey;
    }

    protected function getTypeId()
    {
        return $this->strokeId;
    }

    public function jsonSerialize(): array
    {
        return [
            'strokeType' => [
                'strokeTypeKey' => $this->getTypeKey(),
                'strokeTypeId' => $this->getTypeId()
            ],
        ];
    }
}
