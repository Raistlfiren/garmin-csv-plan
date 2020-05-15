<?php

namespace App\Library\Parser\Model;

use App\Library\Parser\Model\Step\AbstractStep;
use App\Library\Parser\Model\Workout\AbstractWorkout;
use Doctrine\Common\Collections\ArrayCollection;

class PeriodCollection extends ArrayCollection
{
    /** @var array $weeks */
    protected $weeks;

    public function getWorkouts()
    {
        $workouts = [];

        /** @var WeekCollection $week */
        foreach ($this->weeks as $week) {
            /** @var Day $day */
            foreach ($week->getDays() as $day) {
                /** @var AbstractWorkout $workout */
                foreach ($day->getWorkouts() as $workout) {
                    $workouts[] = $workout;
                }
            }
        }

        return $workouts;
    }

    /**
     * @return AbstractStep[]
     */
    public function getStepsWithNotes()
    {
        $steps = [];

        /** @var WeekCollection $week */
        foreach ($this->weeks as $week) {
            /** @var Day $day */
            foreach ($week->getDays() as $day) {
                /** @var AbstractWorkout $workout */
                foreach ($day->getWorkouts() as $workout) {
                    foreach ($workout->getAllSteps([], $workout->getSteps()) as $step) {
                        if ($step instanceof AbstractStep && ! empty($step->getNotes())) {
                            $steps[] = $step->setWorkout($workout);
                        }
                    }
                }
            }
        }

        return $steps;
    }

    public function getDays()
    {
        $days = [];

        /** @var WeekCollection $week */
        foreach ($this->weeks as $week) {
            /** @var Day $day */
            foreach ($week->getDays() as $day) {
                $days[] = $day;
            }
        }

        return $days;
    }

    /**
     * @return array
     */
    public function getWeeks(): array
    {
        return $this->weeks;
    }

    /**
     * @param array $weeks
     */
    public function setWeeks(array $weeks): void
    {
        $this->weeks = $weeks;
    }

    /**
     * @param mixed $week
     */
    public function addWeek($week)
    {
        $this->weeks[] = $week;
    }
}
