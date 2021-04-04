<?php

namespace App\Library\Parser;

use App\Library\Parser\Model\Day;
use App\Library\Parser\Model\PeriodCollection;
use App\Library\Parser\Model\WeekCollection;
use App\Library\Parser\Model\Workout\WorkoutFactory;
use App\Model\DebugMessages;
use DateTime;
use League\Csv\Reader;

class Parser
{
    use DebugMessages;

    /** @var Reader $csv */
    protected $csv;

    protected $records;

    /**
     * @param $path
     * @return bool
     */
    public function isValidFile($path)
    {
        $addToPath = [DIRECTORY_SEPARATOR, '..', DIRECTORY_SEPARATOR, '..', DIRECTORY_SEPARATOR, '..', DIRECTORY_SEPARATOR];
        $fullPath = __DIR__ . implode('', $addToPath) . $path;
        if (file_exists($fullPath)) {
            $csv = Reader::createFromPath($fullPath, 'r');
            $csv->setHeaderOffset(0);
            $csv->skipEmptyRecords();
            $this->records = $csv;
            return true;
        }

        throw new \Exception('Invalid file. Please make sure the file exists.');
    }

    /**
     * @return mixed
     */
    public function getRecords()
    {
        return $this->records;
    }

    public function getTotalWeeks()
    {
        return count($this->records);
    }

    /**
     * @return array
     */
    public function findAllWorkouts($prefix = null)
    {
        $workouts = [];

        foreach ($this->records as $row) {
            foreach ($row as $data) {
                //Append newline to end
                $data .= "\n";
                //Try to parse workout
                $workout = $this->parseWorkout($data);
                if ($workout) {
                    //Workout must have been made
                    $name = $workout->getName();
                    $workout->setPrefix($prefix);
                    $workout->setName($name);
                    $workouts[] = $workout;
                }
            }
        }

        $workouts = array_unique($workouts);

        foreach ($workouts as $workout) {
            $this->debugMessages[] .= (empty($workout->getName()) ? 'Workout parsed.' : $workout->getName());
        }

        return array_unique($workouts);
    }

    public function scheduleWorkouts(DateTime $startDate = null, $workouts)
    {
        $period = new PeriodCollection();
        $debugCounter = 0;

        $days = Day::WEEK;

        foreach ($this->records as $record) {
            $week = new WeekCollection();

            foreach ($days as $day) {
                $entityDay = new Day();
                $this->debugMessages[$debugCounter] = '';

                if ($startDate) {
                    $entityDay->setDate(clone $startDate);
                    $this->debugMessages[$debugCounter] = $startDate->format('Y-m-d') . ' - ';
                    //Increment date by 1...
                    $startDate->modify('+1 day');
                }

                $week->addDay($entityDay);

                $workoutName = $this->parseWorkoutName($record[$day]);
                if ($workoutName === null) {
                    // Remove /n/t/... from workout name
                    $workoutName = trim($record[$day]);
                }
                $foundWorkout = null;

                foreach ($workouts as $workout) {
                    if ($workout->getName() === $workoutName) {
                        $entityDay->addWorkout($workout);
                        break;
                    }
                }

            }
            $period->addWeek($week);
        }

        return $period;
    }

    public function parseWorkoutType($workoutText)
    {
        // Generates regex - /^(running|cycling|swimming|etc...)?:/
        $regex = '/^(' . implode('|',WorkoutTypes::WORKOUTS) . ')?:/';
        $result = preg_match($regex, $workoutText, $workoutType);

        if ($result && isset($workoutType[1]) && ! empty($workoutType[1])) {
            return trim($workoutType[1]);
        }

        return null;
    }

    public function parseWorkoutName($workoutText)
    {
        $regex = '/:\s{1,}(.*)/';
        $result = preg_match($regex, $workoutText, $workoutName);

        if ($result && isset($workoutName[1]) && ! empty($workoutName[1])) {
            return trim($workoutName[1]);
        }

        return null;
    }

    public function removeFirstLine($workoutText)
    {
        $regex = '/^.+\n?/';

        return preg_replace($regex, '', $workoutText);
    }

    public function parseSteps($stepsText)
    {
        $regex = '/^(\s*-.*)$/m';

        $result = preg_match_all($regex, $stepsText, $steps);

        if ($result && isset($steps[0]) && ! empty($steps[0])) {
            return $steps[0];
        }

        return null;
    }

    public function parseWorkout($workoutText)
    {
        //Read first line
        $workoutType = $this->parseWorkoutType($workoutText);
        $workoutName = $this->parseWorkoutName($workoutText);

        //Remove first line
        $stepsText = $this->removeFirstLine($workoutText);
        //Read steps into array
        $steps = $this->parseSteps($stepsText);

        return WorkoutFactory::build($workoutType, $workoutName, $steps);
    }
}
