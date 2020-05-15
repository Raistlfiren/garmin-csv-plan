<?php

namespace App\Library\Parser\Model\Step;

use App\Library\Parser\Model\Duration\DurationFactory;
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
    protected $order;

    /**
     * @var string|null
     */
    protected $notes;

    /**
     * @var integer|null
     */
    protected $garminID;

    /**
     * @var AbstractWorkout|null
     */
    protected $workout;

    public function __construct($stepText, $notes, $stepOrder)
    {
        $this->notes = $notes;
        $this->order = $stepOrder;
        $duration = $this->parseTextDuration($stepText);
        $target = $this->parseTextTarget($stepText);

        $this->duration = DurationFactory::build($duration);
        $this->target = TargetFactory::build($target);
    }

    public function parseTextDuration($stepDetailsText)
    {
        $regex = '/^\s*([^ ]*)/';
        $result = preg_match($regex, $stepDetailsText, $duration);

        if ($result && isset($duration[1]) && ! empty($duration[1])) {
            return trim($duration[1]);
        }

        return null;
    }

    public function parseTextTarget($stepDetailsText)
    {
        $regex = '/@\s*(.*)/';
        $result = preg_match($regex, $stepDetailsText, $target);

        if ($result && isset($target[1]) && ! empty($target[1])) {
            return trim($target[1]);
        }

        return null;
    }

    abstract protected function getStepTypeId();

    abstract protected function getStepTypeKey();

    public function jsonSerialize()
    {
        $duration = [];
        $target = [];

        if ($this->duration) {
            $duration = $this->duration->jsonSerialize();
        }

        if ($this->target) {
            $target = $this->target->jsonSerialize();
        }

        $steps = [
            'type' => 'ExecutableStepDTO',
            'stepId' => null,
            'stepOrder' => $this->order,
            'childStepId' => null,
            'description' => null,
            'stepType' => [
                'stepTypeId' => $this->getStepTypeId(),
                'stepTypeKey' => $this->getStepTypeKey()
            ]
        ];

        return array_merge($steps, $duration, $target);
    }

    /**
     * @return int|null
     */
    public function getGarminID(): ?int
    {
        return $this->garminID;
    }

    /**
     * @param int|null $garminID
     * @return AbstractStep
     */
    public function setGarminID(?int $garminID): AbstractStep
    {
        $this->garminID = $garminID;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getNotes(): ?string
    {
        return $this->notes;
    }

    /**
     * @param string|null $notes
     * @return AbstractStep
     */
    public function setNotes(?string $notes): AbstractStep
    {
        $this->notes = $notes;
        return $this;
    }

    /**
     * @return AbstractWorkout|null
     */
    public function getWorkout(): ?AbstractWorkout
    {
        return $this->workout;
    }

    /**
     * @param AbstractWorkout|null $workout
     * @return AbstractStep
     */
    public function setWorkout(?AbstractWorkout $workout): AbstractStep
    {
        $this->workout = $workout;
        return $this;
    }
}
