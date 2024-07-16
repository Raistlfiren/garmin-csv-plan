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
    public function isValidFile(string $path)
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

    public function getTotalWeeks(): int
    {
        return count($this->records);
    }

    public function findAllWorkouts($prefix = null, $poolSize = null): array
    {
        $workouts = [];

        foreach ($this->records as $row) {
            foreach ($row as $data) {
                //Append newline to end
                $data .= "\n";

                // Find all workouts for a day
                $workoutGroups = $this->splitWorkoutText($data);

                if (! empty($workoutGroups)) {
                    foreach ($workoutGroups as $workoutGroupText) {
                        //Try to parse workout
                        $workout = $this->parseWorkout($workoutGroupText, $poolSize);
                        if ($workout) {
                            //Workout must have been made
                            $name = $workout->getName();
                            $workout->setPrefix($prefix);
                            $workout->setName($name);
                            $workouts[] = $workout;
                        }
                    }
                }
            }
        }

        $workouts = array_unique($workouts);

        foreach ($workouts as $workout) {
            $this->debugMessages[] .= (empty($workout->getName()) ? 'Workout parsed.' : $workout->getName());
        }

        return array_unique($workouts);
    }

    /**
     * @return string[]
     */
    protected function splitWorkoutText($workoutText): array
    {
        $splitLines = explode("\n", (string) $workoutText);

        $headers = $this->parseMultiWorkouts($workoutText);

        $workoutGroups = [];

        if (is_array($headers)) {
            $workoutCounterMax = count($headers);
            $workoutKeys = array_keys($headers);

            for ($workoutCounter = 0; $workoutCounter < $workoutCounterMax; $workoutCounter++) {
                $individualWorkoutText = '';

                // Means only one workout for the day
                if ($workoutCounterMax === 1) {
                    foreach ($splitLines as $line) {
                        $individualWorkoutText .= $line . "\n";
                    }
                    $workoutGroups[] = $individualWorkoutText;
                    // Must be last workout in set
                } elseif ($workoutCounterMax === ($workoutCounter + 1)) {
                    foreach ($splitLines as $line) {
                        $individualWorkoutText .= $line . "\n";
                    }
                    $workoutGroups[] = $individualWorkoutText;
                } else {
                    $nextIndex = $workoutKeys[($workoutCounter + 1)] - $workoutKeys[$workoutCounter];

                    for ($lineCounter = 0; $lineCounter < $nextIndex; $lineCounter++) {
                        $individualWorkoutText .= $splitLines[$lineCounter] . "\n";
                        unset($splitLines[$lineCounter]);
                    }

                    $splitLines = array_values($splitLines);

                    $workoutGroups[] = $individualWorkoutText;
                }
            }
        }

        return $workoutGroups;
    }

    public function scheduleWorkouts($workouts, DateTime $startDate = null): \App\Library\Parser\Model\PeriodCollection
    {
        $period = new PeriodCollection();
        $debugCounter = 0;

        $days = Day::WEEK;

        foreach ($this->records as $record) {
            $week = new WeekCollection();

            foreach ($days as $day) {
                $entityDay = new Day();
                $this->debugMessages[$debugCounter] = '';

                if ($startDate instanceof \DateTime) {
                    $entityDay->setDate(clone $startDate);
                    $this->debugMessages[$debugCounter] = $startDate->format('Y-m-d') . ' - ';
                    //Increment date by 1...
                    $startDate->modify('+1 day');
                }

                $week->addDay($entityDay);

                // Parse first line of workout including name and activity Ex.: running: easy run
                $workoutNames = $this->parseMultiWorkouts($record[$day]);

                if (! empty($workoutNames)) {
                    // Parse out the actual name of te workout Ex.: easy run
                    foreach ($workoutNames as &$workoutName) {
                        $workoutName = $this->parseWorkoutName($workoutName);
                    }
                }

                // Check to see if it is just the workout name
                if ($workoutNames === null) {
                    // Remove /n/t/... from workout name
                    $workoutNames[] = trim($record[$day] ?? '');
                }

//                $foundWorkout = null;


                // Loop through workout names to get all workouts in a day
                foreach ($workoutNames as $workoutFullName) {
                    // Loop through parsed workouts
                    foreach ($workouts as $workout) {
                        // Test to see if the workout name is a legit workout that was imported
                        if ($workout->getName() === $workoutFullName) {
                            // Add the workout to the day
                            $entityDay->addWorkout($workout);
                            break;
                        }
                    }
                }

            }

            $period->addWeek($week);
        }

        return $period;
    }

    public function parseMultiWorkouts($workoutText)
    {
        // Generates regex - /^(running|cycling|swimming|etc...)?:/
        $regex = '/(' . implode('|',WorkoutTypes::WORKOUTS) . ')/';
        $result = preg_grep($regex, explode("\n", (string) $workoutText));

        if ($result) {
            return $result;
        }

        return null;
    }

    public function parseWorkoutType($workoutText): ?string
    {
        // Generates regex - /^(running|cycling|swimming|etc...)?:/
        $regex = '/^(' . implode('|',WorkoutTypes::WORKOUTS) . ')?:/';
        $result = $workoutText && preg_match($regex, (string) $workoutText, $workoutType);
        if (!$result) {
            return null;
        }
        if (!isset($workoutType[1])) {
            return null;
        }
        if (empty($workoutType[1])) {
            return null;
        }
        return trim($workoutType[1]);
    }

    public function parseWorkoutName($workoutText): ?string
    {
        $regex = '/:\s{1,}(.*)/';
        $result = $workoutText && preg_match($regex, (string) $workoutText, $workoutName);
        if (!$result) {
            return null;
        }
        if (!isset($workoutName[1])) {
            return null;
        }
        if (empty($workoutName[1])) {
            return null;
        }
        return trim($workoutName[1]);
    }

    public function removeFirstLine($workoutText): string|array|null
    {
        $regex = '/^.+\n?/';

        return preg_replace($regex, '', $workoutText);
    }

    public function parseSteps($stepsText)
    {
        $regex = '/^(\s*-.*)$/m';

        $result = preg_match_all($regex, (string) $stepsText, $steps);
        if (!$result) {
            return null;
        }
        if (!isset($steps[0])) {
            return null;
        }
        if (empty($steps[0])) {
            return null;
        }
        return $steps[0];
    }

    public function parseWorkout($workoutText, $poolSize = null)
    {
        //Read first line
        $workoutType = $this->parseWorkoutType($workoutText);
        $workoutName = $this->parseWorkoutName($workoutText);

        //Remove first line
        $stepsText = $this->removeFirstLine($workoutText);
        //Read steps into array
        $steps = $this->parseSteps($stepsText);

        return WorkoutFactory::build($workoutType, $workoutName, $steps, $poolSize);
    }
}
