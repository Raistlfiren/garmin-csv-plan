<?php

namespace App\Library\Parser\Model\Step;

use Doctrine\Common\Collections\ArrayCollection;

class RepeaterStep implements \JsonSerializable
{
    /**
     * @var ArrayCollection|null
     */
    protected $steps;

    /**
     * @var int|null
     */
    protected $garminID;

    public function __construct(protected $numberOfIterations, protected $order)
    {
        $this->steps = new ArrayCollection();
    }

    public function getSteps(): ?ArrayCollection
    {
        return $this->steps;
    }

    public function setSteps(?ArrayCollection $steps): RepeaterStep
    {
        $this->steps = $steps;
        return $this;
    }

    public function addStep(mixed $step): ?static
    {
        if ($this->steps->contains($step)) {
            return null;
        }

        $this->steps->add($step);

        return $this;
    }

    public function removeStep(mixed $step): void
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

    public function setNumberOfIterations(mixed $numberOfIterations): static
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

    public function getGarminID(): ?int
    {
        return $this->garminID;
    }

    public function setGarminID(?int $garminID): RepeaterStep
    {
        $this->garminID = $garminID;
        return $this;
    }
}
