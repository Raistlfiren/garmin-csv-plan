<?php

namespace App\Service;

use App\Library\Parser\Model\Day;
use App\Library\Parser\Model\Step\AbstractStep;
use App\Library\Parser\Model\Workout\AbstractWorkout;
use App\Model\DebugMessages;
use dawguk\GarminConnect;

class GarminHelper
{
    use DebugMessages;

    /**
     * @var GarminConnect $client
     */
    protected $client;

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $password;

    public function __construct($garminUsername, $garminPassword)
    {
        $this->username = $garminUsername;
        $this->password = $garminPassword;
    }

    public function createGarminClient($username, $password)
    {
        $credentials = [
            'username' => $username,
            'password' => $password,
        ];

        if (empty($username) && empty($password)) {
            $credentials = [
                'username' => $this->username,
                'password' => $this->password,
            ];
        }

        $this->client = new GarminConnect($credentials);
    }

    public function createWorkouts(array $workouts)
    {
        $debugMessages = [];

        /** @var Day $day */
        $workoutList = [];

        /** @var AbstractWorkout $workout */
        foreach ($workouts as $workoutKey => $workout) {
            $workoutName = $workout->getName();
            // same workout name already created?
            if ($workoutID = array_search($workoutName, $workoutList, true)) {
                $workout->setGarminID($workoutID);
                $debugMessages[] = 'Workout - ' . $workoutName . ' was previously created on the Garmin website with the id ' . $workoutID;
            } else {
                $response = $this->client->createWorkout(json_encode($workout));
                $workoutID = $response->workoutId;
                $workoutSteps = $this->findWorkoutSteps($response->workoutSegments[0]);
                $allSteps = $workout->getAllSteps([], $workout->getSteps());
                foreach ($workoutSteps as $index => $workoutStep) {
                    $allSteps[$index]->setGarminID($workoutStep['id']);
                }
                $workout->setGarminID($response->workoutId);
                $workoutList[$response->workoutId] = $workoutName;
                $debugMessages[] = 'Workout - ' . $workoutName . ' was created on the Garmin website with the id ' . $workoutID;
            }
        }

        $this->setDebugMessages($debugMessages);
    }

    public function deleteWorkouts(array $workouts)
    {
        $debugMessages = [];

        $workoutNames = [];

        /** @var AbstractWorkout $workout */
        foreach ($workouts as $workout) {
            $workoutNames[] = $workout->getName();
        }

        $workoutList = [];

        //Grab all workouts from Garmin API
        $workouts = $this->client->getWorkoutList(0, 9999);

        //Store them in an array
        foreach ($workouts as $workout) {
            $workoutList[$workout->workoutId] = $workout->workoutName;
        }

        //Loop through workout names and delete them from Garmin
        foreach ($workoutNames as $workoutName) {
            foreach ($workoutList as $workoutKey => $workout) {
                //Delete all workouts that contain the workout name
                if (strpos($workout, $workoutName) !== false) {
                    $this->client->deleteWorkout($workoutKey);
                    unset($workoutList[$workoutKey]);
                    $debugMessages[] = 'Workout - ' . $workoutName . ' with id ' . $workoutKey . ' was deleted from the Garmin website.';
                }
            }
        }

        $this->setDebugMessages($debugMessages);
    }

    public function attachNotes($workouts)
    {
        $stepsWithNotes = [];

        foreach ($workouts as $workout) {
            foreach ($workout->getAllSteps([], $workout->getSteps()) as $step) {
                if ($step instanceof AbstractStep && ! empty($step->getNotes())) {
                    $stepsWithNotes[] = $step->setWorkout($workout);
                }
            }
        }

        //Loop through steps and add their notes
        foreach ($stepsWithNotes as $stepsWithNote) {
            // if the step has no GarminID, it means the same workout was already created
            if ($stepID = $stepsWithNote->getGarminID()) {
                $this->client->createStepNote($stepID, $stepsWithNote->getNotes(), $stepsWithNote->getWorkout()->getGarminID());
            }
        }
    }

    public function scheduleWorkouts(array $days)
    {
        $debugMessages = [];

        /** @var Day $day */
        foreach ($days as $day) {
            /** @var AbstractWorkout $workout */
            foreach ($day->getWorkouts() as $workoutKey => $workout) {
                if ($day->getDate()) {
                    $formattedDate = $day->getDate()->format('Y-m-d');
                    $data = json_encode(['date' => $formattedDate]);

                    $messageID = ' is going to be scheduled on ';

                    if ($workout->getGarminID()) {
                        $this->client->scheduleWorkout($workout->getGarminID(), $data);
                        $messageID = ' with id '  . $workout->getGarminID() .' was scheduled on the Garmin website for ';
                    }
                    $debugMessages[] = 'Workout - ' . $workout->getName() .  $messageID . $formattedDate;
                }
            }
        }

        $this->setDebugMessages($debugMessages);
    }

    public function findWorkoutSteps($steps, $workoutSteps = [])
    {
        foreach ($steps->workoutSteps as $step) {
            if ($step->type === 'ExecutableStepDTO') {
                $workoutSteps[] = ['type' => 'ExecutableStepDTO', 'id' => $step->stepId];
            } elseif ($step->type === 'RepeatGroupDTO') {
                $workoutSteps[] = ['type' => 'RepeatGroupDTO', 'id' => $step->stepId];
                $workoutSteps = $this->findWorkoutSteps($step, $workoutSteps);
            }
        }

        return $workoutSteps;
    }
}