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
            $repeater = $this->isRepeaterStep($step);
            $header = $this->parseStepHeader($step);
            $parameters = $this->parseStepDetails($step);
            $notes = $this->parseStepNotes($step);
            if ($header === 'repeat') {
                $repeaterStep = new RepeaterStep($parameters, $index);
                $this->steps->add($repeaterStep);
            } else {
                if ($repeater && $repeaterStep) {
                    $stepFactory = StepFactory::build($header, $parameters, $notes, $index);
                    $repeaterStep->addStep($stepFactory);
                } else {
                    $stepFactory = StepFactory::build($header, $parameters, $notes, $index);
                    $this->steps->add($stepFactory);
                }
            }
        }

        return $this;
    }

    public function isRepeaterStep($stepText)
    {
        $regex = '/^\s{1,}-.*$/';

        $result = preg_match($regex, $stepText, $stepHeader);

        return $result && isset($stepHeader[0]) && ! empty($stepHeader[0]);
    }

    public function parseStepHeader($stepText)
    {
        $regex = '/-\s*([^:]*)/';
        $result = preg_match($regex, $stepText, $stepHeader);

        if ($result && isset($stepHeader[1]) && ! empty($stepHeader[1])) {
            return trim($stepHeader[1]);
        }

        return null;
    }

    public function parseStepDetails($stepText)
    {
        $regex = '/:\s*([^;]*)/';
        $result = preg_match($regex, $stepText, $stepDetails);

        if ($result && isset($stepDetails[1]) && ! empty($stepDetails[1])) {
            return trim($stepDetails[1]);
        }

        return null;
    }

    public function parseStepNotes($stepText)
    {
        $regex = '/;\s*(.*)/';
        $result = preg_match($regex, $stepText, $stepNotes);

        if ($result && isset($stepNotes[1]) && ! empty($stepNotes[1])) {
            return trim($stepNotes[1]);
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

    public function __toString()
    {
        return $this->getName();
    }
}
