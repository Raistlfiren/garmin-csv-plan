<?php

namespace App\Library\Parser\Model;

use Doctrine\Common\Collections\ArrayCollection;
use DateTime;

class Day
{
    const WEEK = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

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

    /**
     * @return ArrayCollection
     */
    public function getWorkouts(): ArrayCollection
    {
        return $this->workouts;
    }

    /**
     * @param ArrayCollection $workouts
     * @return Day
     */
    public function setWorkouts(ArrayCollection $workouts): Day
    {
        $this->workouts = $workouts;
        return $this;
    }

    /**
     * @param mixed $workout
     */
    public function addWorkout($workout)
    {
        if ($this->workouts->contains($workout)) {
            return;
        }

        $this->workouts->add($workout);
        return $this;
    }

    public function updateWorkout($key, $workout)
    {
        $this->workouts->set($key, $workout);

        return;
    }

    /**
     * @param mixed $workout
     */
    public function removeWorkout($workout)
    {
        if (!$this->workouts->contains($workout)) {
            return;
        }

        $this->workouts->removeElement($workout);
    }

    /**
     * @return DateTime|null
     */
    public function getDate(): ?DateTime
    {
        return $this->date;
    }

    /**
     * @param DateTime|null $date
     * @return Day
     */
    public function setDate(?DateTime $date): Day
    {
        $this->date = $date;
        return $this;
    }
}
