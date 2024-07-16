<?php

namespace App\Library\Parser\Model\Step;

use App\Library\Parser\Model\Duration\DurationFactory;
use App\Library\Parser\Model\Swim\EquipmentType;
use App\Library\Parser\Model\Swim\StrokeType;
use App\Library\Parser\Model\Target\TargetFactory;
use App\Library\Parser\Model\Workout\AbstractWorkout;

abstract class AbstractStep implements \JsonSerializable
{
    /**
     * @var string|null
     */
    protected $duration;

    /**
     * @var string|null
     */
    protected $target;

    /**
     * @var int|null
     */
    protected $garminID;

    /**
     * @var AbstractWorkout|null
     */
    protected $workout;

    /**
     * @var boolean
     */
    protected $swimming;

    /**
     * @var StrokeType
     */
    protected $swimmingStroke;

    /**
     * @var EquipmentType
     */
    protected $swimmingEquipment;

    /**
     * @param int|null $stepOrder
     * @param string|null $notes
     */
    public function __construct($stepText, protected $notes, protected $order, $swimming)
    {
        $duration = $this->parseTextDuration($stepText);
        $target = $this->parseTextTarget($stepText);

        $this->duration = DurationFactory::build($duration);
        $this->target = TargetFactory::build($target);

        // Check for specific strokes and equipment
        if ($swimming) {
            $this->swimmingStroke = StrokeType::testStroke($stepText);
            $this->swimmingEquipment = EquipmentType::testEquipment($stepText);
        }
    }

    public function parseTextDuration($stepDetailsText)
    {
        $regex = '/^\s*([^ ]*)/';
        $result = $stepDetailsText && preg_match($regex, (string) $stepDetailsText, $duration);
        if (!$result) {
            return null;
        }
        if (!isset($duration[1])) {
            return null;
        }
        if (empty($duration[1])) {
            return null;
        }
        return trim($duration[1]);
    }

    public function parseTextTarget($stepDetailsText)
    {
        $regex = '/@\s*(.*)/';
        $result = $stepDetailsText && preg_match($regex, (string) $stepDetailsText, $target);
        if (!$result) {
            return null;
        }
        if (!isset($target[1])) {
            return null;
        }
        if (empty($target[1])) {
            return null;
        }
        return trim($target[1]);
    }

    abstract protected function getStepTypeId();

    abstract protected function getStepTypeKey();

    public function jsonSerialize(): array
    {
        $duration = [];
        $target = [];
        $swimmingStroke = [];
        $swimmingEquipment = [];

        if ($this->duration) {
            $duration = $this->duration->jsonSerialize();
        }

        if ($this->target) {
            $target = $this->target->jsonSerialize();
        }

        if ($this->swimmingStroke) {
            $swimmingStroke = $this->swimmingStroke->jsonSerialize();
        }

        if ($this->swimmingEquipment) {
            $swimmingEquipment = $this->swimmingEquipment->jsonSerialize();
        }

        $steps = [
            'type' => 'ExecutableStepDTO',
            'stepId' => null,
            'stepOrder' => $this->order,
            'childStepId' => null,
            'description' => $this->notes,
            'stepType' => [
                'stepTypeId' => $this->getStepTypeId(),
                'stepTypeKey' => $this->getStepTypeKey()
            ]
        ];

        return array_merge($steps, $duration, $target, $swimmingStroke, $swimmingEquipment);
    }

    public function getGarminID(): ?int
    {
        return $this->garminID;
    }

    public function setGarminID(?int $garminID): AbstractStep
    {
        $this->garminID = $garminID;
        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): AbstractStep
    {
        $this->notes = $notes;
        return $this;
    }

    public function getWorkout(): ?AbstractWorkout
    {
        return $this->workout;
    }

    public function setWorkout(?AbstractWorkout $workout): AbstractStep
    {
        $this->workout = $workout;
        return $this;
    }
}
