<?php

namespace App\Library\Parser\Model\Step;

use Doctrine\Common\Collections\ArrayCollection;

class RepeaterStep implements \JsonSerializable
{
    /**
     * @var ArrayCollection|null
     */
    protected $steps;

    protected $numberOfIterations;

    protected $order;

    /**
     * @var float|null
     */
    protected $garminID;

    public function __construct($numberOfIterations, $stepOrder)
    {
        $this->order = $stepOrder;
        $this->numberOfIterations = $numberOfIterations;
        $this->steps = new ArrayCollection();
    }

    /**
     * @return ArrayCollection|null
     */
    public function getSteps(): ?ArrayCollection
    {
        return $this->steps;
    }

    /**
     * @param ArrayCollection|null $steps
     * @return RepeaterStep
     */
    public function setSteps(?ArrayCollection $steps): RepeaterStep
    {
        $this->steps = $steps;
        return $this;
    }

    /**
     * @param mixed $step
     */
    public function addStep($step)
    {
        if ($this->steps->contains($step)) {
            return;
        }

        $this->steps->add($step);

        return $this;
    }

    /**
     * @param mixed $step
     */
    public function removeStep($step)
    {
        if (!$this->steps->contains($step)) {
            return;
        }

        $this->steps->removeElement($step);
    }

    /**
     * @return mixed
     */
    public function getNumberOfIterations()
    {
        return $this->numberOfIterations;
    }

    /**
     * @param mixed $numberOfIterations
     * @return RepeaterStep
     */
    public function setNumberOfIterations($numberOfIterations)
    {
        $this->numberOfIterations = $numberOfIterations;
        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'type' => 'RepeatGroupDTO',
            'stepId' => null,
            'stepOrder' => $this->order,
            'childStepId' => 1,
            'smartRepeat' => false,
            'numberOfIterations' => $this->numberOfIterations,
            'stepType' => [
                'stepTypeId' => 6,
                'stepTypeKey' => 'repeat'
            ],
            'workoutSteps' => $this->steps->toArray()
        ];
    }

    /**
     * @return float|null
     */
    public function getGarminID(): ?float
    {
        return $this->garminID;
    }

    /**
     * @param float|null $garminID
     * @return RepeaterStep
     */
    public function setGarminID(?float $garminID): RepeaterStep
    {
        $this->garminID = $garminID;
        return $this;
    }
}
