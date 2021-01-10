<?php

namespace App\Library\Parser;

use App\Library\Parser\Model\Day;
use App\Library\Parser\Model\PeriodCollection;
use App\Library\Parser\Model\WeekCollection;
use App\Library\Parser\Model\Workout\WorkoutFactory;
use League\Csv\Reader;
use Symfony\Component\Yaml\Yaml;
use DateTime;

class Parser
{
    /** @var Reader $csv */
    protected $csv;

    protected $debugMessages = [];

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

                $recordLines = preg_split("/((\r?\n)|(\r\n?))/", $record[$day]);
                $lineNumbers = count($recordLines);
                $lineNumber = 0;
                $workout = "";
                $workoutArr = array();
                foreach ($recordLines as $recordLine) {
                    $lineNumber++;
                    if (! empty($recordLine)) {
                        $regex = '/^(' . implode('|',WorkoutTypes::WORKOUTS) . ')?:/';
                        $result = preg_match($regex, $recordLine, $workoutType);

                        // new workout
                        if ($result && isset($workoutType[1]) && ! empty($workoutType[1])) {
                            // push previous workout to array
                            if ($workout != "") {
                                array_push($workoutArr, $workout);
                            }
                            $workout = $recordLine . "\n";
                        }
                        else {
                            $workout .= $recordLine . "\n";
                            // last line
                            if($lineNumber === $lineNumbers) {
                                array_push($workoutArr, $workout);
                            }
                        }
                    }
                }

                $workoutNumbers = count($workoutArr);
                $workoutNumber = 0;
                foreach ($workoutArr as $workout) {
                    $workoutNumber++;
                    $workout = $this->parseWorkout($workout);
                    $name = $prefix . $workout->getName();
                    $workout->setName($name);
                    if ($workoutNumbers > 1) {
                        $this->debugMessages[$debugCounter] .= $workoutNumber . ". ";
                    }
                    $this->debugMessages[$debugCounter] .= (empty($workout->getName()) ? 'Workout parsed.' : $workout->getName());
                    if ($workoutNumbers > 1 && $workoutNumber < $workoutNumbers) {
                        $this->debugMessages[$debugCounter] .= "  -  ";
                    }
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
        // create a valid parseable Yaml string
        $stepsText = preg_replace("/(( *)- repeat: \d+)\n/", "$1\n$2  steps:\n", $stepsText);
        $stepsText = preg_replace("/(( *)- \w+:.*);\s*(.*)\n/", "$1\n$2  notes: \"$3\"\n", $stepsText);
        $steps = Yaml::parse($stepsText);
        return $steps;
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
