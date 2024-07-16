<?php

namespace App\Library\Parser\Model;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;

class Day
{
    public const WEEK = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

    /**
     * @var DateTime|null
     */
    protected $date;

    /**
     * @var ArrayCollection
     */
    protected $workouts;

    public function __construct()
    {
        $this->workouts = new ArrayCollection();
    }

    public function getWorkouts(): ArrayCollection
    {
        return $this->workouts;
    }

    public function setWorkouts(ArrayCollection $workouts): Day
    {
        $this->workouts = $workouts;
        return $this;
    }

    public function addWorkout(mixed $workout): ?static
    {
        if ($this->workouts->contains($workout)) {
            return null;
        }

        $this->workouts->add($workout);
        return $this;
    }

    public function updateWorkout($key, $workout): void
    {
        $this->workouts->set($key, $workout);
    }

    public function removeWorkout(mixed $workout): void
    {
        if (!$this->workouts->contains($workout)) {
            return;
        }

        $this->workouts->removeElement($workout);
    }

    public function getDate(): ?DateTime
    {
        return $this->date;
    }

    public function setDate(?DateTime $date): Day
    {
        $this->date = $date;
        return $this;
    }
}
