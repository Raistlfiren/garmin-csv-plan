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
     * @var string|null
     */
    protected $prefix;

    /**
     * @var integer|null
     */
    protected $garminID;

    public function __construct($name)
    {
        $this->name = $name;
        $this->steps = new ArrayCollection();
    }

    public function steps($steps, $swimming = false)
    {
        $repeaterStep = null;
        $repStep = [];

        foreach ($steps as $index => $step) {
            $whiteSpaceCount = $this->calculateWhiteSpace($step);
            $header = $this->parseStepHeader($step);
            $parameters = $this->parseStepDetails($step);
            $notes = $this->parseStepNotes($step);

            $stepFactory = StepFactory::build($header, $parameters, $notes, $index, $swimming);

            if ($stepFactory instanceof RepeaterStep) {
                //Store it into array with the index being whitespace to reference children steps later
                $repStep[($whiteSpaceCount+2)] = $stepFactory;
            }

            if (isset($repStep[$whiteSpaceCount])) {
                //Add step to repeater
                $repStep[$whiteSpaceCount]->addStep($stepFactory);
            } else {
                //Store the step into the workout
                $this->steps->add($stepFactory);
            }
        }

        return $this;
    }

    public function parseStepResult($whitespaceCount, $repeater, $header, $parameters, $notes, $index)
    {
        if ($header === 'repeat') {
            $repeaterStep = new RepeaterStep($parameters, $index);
            $this->steps->add($repeaterStep);
        }
    }

    public function calculateWhiteSpace($stepText)
    {
        $regex = '/.+?(?=-)/';

        $result = preg_match($regex, $stepText, $whiteText);

        return $result && isset($whiteText[0]) ? strlen($whiteText[0]) : 0;
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
        $name = $this->getName();
        if (! empty($this->getPrefix())) {
            $name = $this->getPrefix() . $this->getName();
        }

        $swimming = [];
        if ($this instanceof SwimmingWorkout) {
            $swimming = [
                'poolLength' => 25
            ];
        }
        
        $workout = [
            'sportType' => [
                'sportTypeId' => $this->getSportTypeId(),
                'sportTypeKey' => $this->getSportTypeKey()
            ],
            'workoutName' => $name,
            'workoutSegments' => [[
                'segmentOrder' => 1,
                'sportType' => [
                    'sportTypeId' => $this->getSportTypeId(),
                    'sportTypeKey' => $this->getSportTypeKey()
                ],
                'workoutSteps' => $this->steps->toArray()
            ]]
        ];
        
        return array_merge($workout, $swimming);
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
     * @return string|null
     */
    public function getPrefix(): ?string
    {
        return $this->prefix;
    }

    /**
     * @param string|null $prefix
     * @return AbstractWorkout
     */
    public function setPrefix(?string $prefix): AbstractWorkout
    {
        $this->prefix = $prefix;
        return $this;
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
