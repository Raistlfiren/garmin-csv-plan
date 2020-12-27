<?php

namespace App\Library\Parser\Model\Workout;

use App\Library\Parser\Model\Step\AbstractStep;
use App\Library\Parser\Model\Step\RepeaterStep;
use App\Library\Parser\Model\Step\StepFactory;
use Doctrine\Common\Collections\ArrayCollection;

abstract class AbstractWorkout implements \JsonSerializable
{
    /**
     * @var ArrayCollection|[]
     */
    protected $steps;

    /**
     * @var string|null
     */
    protected $name;

    /**
     * @var integer|null
     */
    protected $garminID;

    public function __construct($name)
    {
        $this->name = $name;
        $this->steps = new ArrayCollection();
    }

    public function steps($steps)
    {
        $repeaterStep = null;

        foreach ($steps as $index => $step) {
            $header = array_key_first($step);
            $parameters = $step[$header];
            $notes = array_key_exists("notes", $step) ? $step["notes"] : "";

            if ($header === 'repeat') {
                $repeaterStep = new RepeaterStep($parameters, $index);
                $this->steps->add($repeaterStep);
                $this->addStepsToRepeater($step["steps"], $repeaterStep, 1);
            } else {
                $stepFactory = StepFactory::build($header, $parameters, $notes, $index);
                $this->steps->add($stepFactory);
            }
        }

        return $this;
    }

    private function addStepsToRepeater($steps, $repeaterStep, $level)
    {
        $subRepeaterStep = null;
        
        foreach ($steps as $index => $step) {
            $header = array_key_first($step);
            $parameters = $step[$header];
            $notes = array_key_exists("notes", $step) ? $step["notes"] : "";

            if ($header === 'repeat') {
                $subRepeaterStep = new RepeaterStep($parameters, $index);
                $repeaterStep->addStep($subRepeaterStep);
                $nextLevel = $level + 1;
                $this->addStepsToRepeater($step["steps"], $subRepeaterStep, $nextLevel);
            }
            else {
                $stepFactory = StepFactory::build($header, $parameters, $notes, $index);
                $repeaterStep->addStep($stepFactory);
            }
        }

        return null;
    }

    abstract protected function getSportTypeId();

    abstract protected function getSportTypeKey();

    public function jsonSerialize()
    {
        return [
            'sportType' => [
                'sportTypeId' => $this->getSportTypeId(),
                'sportTypeKey' => $this->getSportTypeKey()
            ],
            'workoutName' => $this->getName(),
            'workoutSegments' => [[
                'segmentOrder' => 1,
                'sportType' => [
                    'sportTypeId' => $this->getSportTypeId(),
                    'sportTypeKey' => $this->getSportTypeKey()
                ],
                'workoutSteps' => $this->steps->toArray()
            ]]
        ];
    }

    /**
     * @return ArrayCollection
     */
    public function getSteps(): ArrayCollection
    {
        return $this->steps;
    }

    /**
     * @param ArrayCollection $steps
     * @return AbstractWorkout
     */
    public function setSteps(ArrayCollection $steps): AbstractWorkout
    {
        $this->steps = $steps;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return AbstractWorkout
     */
    public function setName(?string $name): AbstractWorkout
    {
        $this->name = $name;
        return $this;
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
     * @return AbstractWorkout
     */
    public function setGarminID(?int $garminID): AbstractWorkout
    {
        $this->garminID = $garminID;
        return $this;
    }

    /**
     * @param array $steps
     * @param array $output
     * @return AbstractStep[]
     */
    public function getAllSteps($output, $steps = [])
    {
        foreach ($steps as $step) {
            $output[] = $step;
            if ($step instanceof RepeaterStep) {
                $output = $this->getAllSteps($output, $step->getSteps());
            }
        }

        return $output;
    }
}
