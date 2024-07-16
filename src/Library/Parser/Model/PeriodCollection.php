<?php

namespace App\Library\Parser\Model;

use App\Library\Parser\Model\Step\AbstractStep;
use App\Library\Parser\Model\Workout\AbstractWorkout;
use Doctrine\Common\Collections\ArrayCollection;

class PeriodCollection extends ArrayCollection
{
    /** @var array $weeks */
    protected $weeks;

    /**
     * @return mixed[]
     */
    public function getWorkouts(): array
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
    public function getStepsWithNotes(): array
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

    /**
     * @return mixed[]
     */
    public function getDays(): array
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

    public function getWeeks(): array
    {
        return $this->weeks;
    }

    public function setWeeks(array $weeks): void
    {
        $this->weeks = $weeks;
    }

    public function addWeek(mixed $week): void
    {
        $this->weeks[] = $week;
    }
}
