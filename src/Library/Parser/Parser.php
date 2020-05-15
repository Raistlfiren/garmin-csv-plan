<?php

namespace App\Library\Parser;

use App\Library\Garmin\Client;
use App\Library\Parser\Model\Day;
use App\Library\Parser\Model\PeriodCollection;
use App\Library\Parser\Model\WeekCollection;
use App\Library\Parser\Model\Workout\WorkoutFactory;
use League\Csv\Reader;
use DateTime;

class Parser
{
    /** @var Reader $csv */
    protected $csv;
    /**
     * @var Client
     */
    private $client;

    protected $debugMessages = [];

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function isValidFile($path)
    {
        $addToPath = [DIRECTORY_SEPARATOR, '..', DIRECTORY_SEPARATOR, '..', DIRECTORY_SEPARATOR, '..', DIRECTORY_SEPARATOR];
        $fullPath = __DIR__ . implode('', $addToPath) . $path;
        if (file_exists($fullPath)) {
            $this->csv = Reader::createFromPath($fullPath, 'r');
            return true;
        }

        return false;
    }

    public function getTotalWeeks()
    {
        $this->csv->setHeaderOffset(0);
        $this->csv->skipEmptyRecords();
        return count($this->csv);
    }

    public function parse(DateTime $startDate = null, $prefix = null)
    {
        $this->csv->setHeaderOffset(0);
        $this->csv->skipEmptyRecords();
        $records = $this->csv->getRecords();

        $period = new PeriodCollection();
        $debugCounter = 0;

        $days = Day::WEEK;
        foreach ($records as $record) {
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

                $workout = $record[$day];

                if (! empty($workout)) {
                    $workout = $this->parseWorkout($workout);
                    $name = $prefix . $workout->getName();
                    $workout->setName($name);
                    $this->debugMessages[$debugCounter] .= (empty($workout->getName()) ? 'Workout parsed.' : $workout->getName());
                    $entityDay->addWorkout($workout);
                }
                $debugCounter++;
            }
            $period->addWeek($week);
        }

        return $period;
    }

    /**
     * @return array
     */
    public function getDebugMessages(): array
    {
        return $this->debugMessages;
    }

    /**
     * @param array $debugMessages
     * @return Parser
     */
    public function setDebugMessages(array $debugMessages): Parser
    {
        $this->debugMessages = $debugMessages;
        return $this;
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
