<?php

namespace App\Library\Parser\Model\Workout;

use App\Library\Parser\Model\Step\AbstractStep;
use App\Library\Parser\Model\Step\RepeaterStep;
use App\Library\Parser\Model\Step\StepFactory;
use Doctrine\Common\Collections\ArrayCollection;

abstract class AbstractWorkout implements \JsonSerializable, \Stringable
{
    public $poolSizeLength;
    public $poolSizeUnit;
    /**
     * @var ArrayCollection|[]
     */
    protected $steps;

    /**
     * @var string|null
     */
    protected $prefix;

    /**
     * @var int|null
     */
    protected $garminID;

    /**
     * @param string|null $name
     */
    public function __construct(protected $name)
    {
        $this->steps = new ArrayCollection();
    }

    public function steps($steps, $swimming = false)
    {
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

    public function parseStepResult($whitespaceCount, $repeater, $header, $parameters, $notes, $index): void
    {
        if ($header === 'repeat') {
            $repeaterStep = new RepeaterStep($parameters, $index);
            $this->steps->add($repeaterStep);
        }
    }

    public function calculateWhiteSpace($stepText)
    {
        $regex = '/.+?(?=-)/';

        $result = $stepText && preg_match($regex, (string) $stepText, $whiteText);

        return $result && isset($whiteText[0]) ? strlen($whiteText[0]) : 0;
    }

    public function isRepeaterStep($stepText)
    {
        $regex = '/^\s{1,}-.*$/';

        $result = $stepText && preg_match($regex, (string) $stepText, $stepHeader);

        return $result && isset($stepHeader[0]) && ! empty($stepHeader[0]);
    }

    public function parseStepHeader($stepText)
    {
        $regex = '/-\s*([^:]*)/';
        $result = $stepText && preg_match($regex, (string) $stepText, $stepHeader);
        if (!$result) {
            return null;
        }
        if (!isset($stepHeader[1])) {
            return null;
        }
        if (empty($stepHeader[1])) {
            return null;
        }
        return trim($stepHeader[1]);
    }

    public function parseStepDetails($stepText)
    {
        $regex = '/:\s*([^;]*)/';
        $result = $stepText && preg_match($regex, (string) $stepText, $stepDetails);
        if (!$result) {
            return null;
        }
        if (!isset($stepDetails[1])) {
            return null;
        }
        if (empty($stepDetails[1])) {
            return null;
        }
        return trim($stepDetails[1]);
    }

    public function parseStepNotes($stepText)
    {
        $regex = '/;\s*(.*)/';
        $result = $stepText && preg_match($regex, (string) $stepText, $stepNotes);
        if (!$result) {
            return null;
        }
        if (!isset($stepNotes[1])) {
            return null;
        }
        if (empty($stepNotes[1])) {
            return null;
        }
        return trim($stepNotes[1]);
    }

    abstract protected function getSportTypeId();

    abstract protected function getSportTypeKey();

    public function jsonSerialize(): array
    {
        $name = $this->getName();
        if (! empty($this->getPrefix())) {
            $name = $this->getPrefix() . $this->getName();
        }

        $swimming = [];
        if ($this instanceof SwimmingWorkout) {
            $swimming = [
                'poolLength' => $this->poolSizeLength,
                "poolLengthUnit" => [
                    "factor" => null,
		            "unitId" => null,
		            "unitKey" => $this->poolSizeUnit
                ]
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

    public function getSteps(): ArrayCollection
    {
        return $this->steps;
    }

    public function setSteps(ArrayCollection $steps): AbstractWorkout
    {
        $this->steps = $steps;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): AbstractWorkout
    {
        $this->name = $name;
        return $this;
    }

    public function getGarminID(): ?int
    {
        return $this->garminID;
    }

    public function getPrefix(): ?string
    {
        return $this->prefix;
    }

    public function setPrefix(?string $prefix): AbstractWorkout
    {
        $this->prefix = $prefix;
        return $this;
    }

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

    public function __toString(): string
    {
        return (string) $this->getName();
    }
}
