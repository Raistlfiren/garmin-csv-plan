<?php

namespace App\Library\Handler;

use App\Library\Garmin\Client;
use App\Library\Handler\Event\HandlerEvent;
use App\Library\Handler\Event\HandlerEvents;
use App\Library\Parser\Model\Day;
use App\Library\Parser\Model\PeriodCollection;
use App\Library\Parser\Model\Step\AbstractStep;
use App\Library\Parser\Model\Workout\AbstractWorkout;
use App\Library\Parser\Model\Workout\WorkoutFactory;
use App\Library\Parser\Parser;
use dawguk\GarminConnect;
use Symfony\Component\Console\Style\OutputStyle;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

abstract class AbstractHandler implements HandlerInterface
{
    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var bool
     */
    protected $delete;

    /**
     * @var Parser
     */
    protected $parser;

    /**
     * @var GarminConnect   */
    protected $client;

    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @var string
     */
    protected $garminUsername;

    /**
     * @var string
     */
    protected $garminPassword;

    public function __construct(Parser $parser, EventDispatcherInterface $eventDispatcher, $garminUsername, $garminPassword)
    {
        $this->parser = $parser;
        $this->dispatcher = $eventDispatcher;
        $this->garminUsername = $garminUsername;
        $this->garminPassword = $garminPassword;
    }

    abstract public function handle(HandlerOptions $handlerOptions);

    public function overrideClientCredentials($username, $password)
    {
        $credentials = [
            'username' => $username,
            'password' => $password,
        ];

        if (empty($username) && empty($password)) {
            $credentials = [
                'username' => $this->garminUsername,
                'password' => $this->garminPassword,
            ];
        }

        $this->client = new GarminConnect($credentials);
    }

    public function getWorkoutNames(array $workouts)
    {
        $workoutNames = [];

        /** @var AbstractWorkout $workout */
        foreach ($workouts as $workout) {
            $workoutNames[] = $workout->getName();
        }

        return $workoutNames;
    }

    public function findWorkoutSteps($steps, $workoutSteps = [])
    {
        foreach ($steps->workoutSteps as $step) {
            if ($step->type === 'ExecutableStepDTO'){
                $workoutSteps[] = ['type' => 'ExecutableStepDTO', 'id' => $step->stepId];
            } else if ($step->type === 'RepeatGroupDTO') {
                $workoutSteps[] = ['type' => 'RepeatGroupDTO', 'id' => $step->stepId];
                $workoutSteps = $this->findWorkoutSteps($step, $workoutSteps);
            }
        }

        return $workoutSteps;
    }

    public function createWorkouts(HandlerOptions $handlerOptions, array $workouts)
    {
        if ($handlerOptions->getDeleteOnly()) {
            return;
        }

        $debugMessages = [];
        $event = new HandlerEvent($handlerOptions);
        $this->dispatcher->dispatch($event, HandlerEvents::CREATED_WORKOUTS_STARTED);

        /** @var Day $day */
        $workoutList = [];

        if ($handlerOptions->getDryrun()) {
            /** @var AbstractWorkout $workout */
            $workoutID = '**********';
            foreach ($workouts as $workoutKey => $workout) {
                $workoutName = $workout->getName();
                $debugMessages[] = 'Workout - ' . $workoutName . ' was created on the Garmin website with the id ' . $workoutID;
            }

            $event->setDebugMessages($debugMessages);
            $this->dispatcher->dispatch($event, HandlerEvents::CREATED_WORKOUTS_ENDED);
            return;
        }

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
//                    $day->updateWorkout($workoutKey, $workout);
            }
        }

        $event->setDebugMessages($debugMessages);
        $this->dispatcher->dispatch($event, HandlerEvents::CREATED_WORKOUTS_ENDED);
    }

    public function deleteWorkouts(HandlerOptions $handlerOptions, array $workouts)
    {
        if (! $handlerOptions->getDelete()) {
            return;
        }

        $event = new HandlerEvent($handlerOptions);
        $this->dispatcher->dispatch($event, HandlerEvents::DELETE_WORKOUTS_STARTED);

        $debugMessages = [];

        $workoutNames = $this->getWorkoutNames($workouts);

        $workoutList = [];

        //Grab all workouts from Garmin API
        $workouts = $this->client->getWorkoutList(0, 9999);

        //Store them in an array
        foreach ($workouts as $workout) {
            $workoutList[$workout->workoutId] = $workout->workoutName;
        }

        //Loop through workout names and delete them from Garmin
        foreach ($workoutNames as $workoutName) {
            while ($workoutKey = array_search($workoutName, $workoutList, true)) {
                if (! $handlerOptions->getDryrun()) {
                    $this->client->deleteWorkout($workoutKey);
                }
                unset($workoutList[$workoutKey]);
                $debugMessages[] = 'Workout - ' . $workoutName . ' with id ' . $workoutKey . ' was deleted from the Garmin website.';
            }
        }

        $event->setDebugMessages($debugMessages);
        $this->dispatcher->dispatch($event, HandlerEvents::DELETE_WORKOUTS_ENDED);
    }

    public function attachNotes(HandlerOptions $handlerOptions, $workouts)
    {
        $stepsWithNotes = [];

        if ($handlerOptions->getDeleteOnly()) {
            return;
        }
        
        foreach ($workouts as $workout) {
            foreach ($workout->getAllSteps([], $workout->getSteps()) as $step) {
                if ($step instanceof AbstractStep && ! empty($step->getNotes())) {
                    $stepsWithNotes[] = $step->setWorkout($workout);
                }
            }
        }

        //Loop through steps and add their notes
        foreach ($stepsWithNotes as $stepsWithNote) {
            if (! $handlerOptions->getDryrun()) {
                // if the step has no GarminID, it means the same workout was already created
                if ($stepID = $stepsWithNote->getGarminID()) {
                    $this->client->createStepNote($stepID, $stepsWithNote->getNotes(), $stepsWithNote->getWorkout()->getGarminID());
                }
            }
        }
    }
//
//    public function importProcess(string $path, string $email, string $password, bool $delete)
//    {
//        $this->getLogger()->section('Validating and accessing - ' . $path);
//
//        $this->parser->isValidFile($path);
//
//        $this->getLogger()->success('File valid.');
//        $this->getLogger()->section('Parsing workouts:');
//
//        $period = $this->parser->parse();
//
//        $this->getLogger()->listing($this->parser->getDebugMessages());
//
//        $answer = $this->getLogger()->confirm('Does the following look correct?', true);
//
//        if (! $answer) {
//            $this->getLogger()->note('Stopping import process.');
//            return;
//        }
//
//        $workouts = $period->getWorkouts();
//        $days = $period->getDays();
//        $debugMessages = $this->deleteWorkouts($workouts, $delete);
//
//        $this->getLogger()->listing($debugMessages);
//        $this->getLogger()->section('Creating workouts');
//
//        $debugMessages = $this->createWorkouts($days);
//
//        $this->getLogger()->listing($debugMessages);
//        $this->getLogger()->success('Workout import was successful.');
//
//        return $period;
//    }

    public function validateFile(HandlerOptions $handlerOptions)
    {
        $event = new HandlerEvent($handlerOptions);
        $this->dispatcher->dispatch($event, HandlerEvents::FILE_VALIDATION_STARTED);
        $this->parser->isValidFile($handlerOptions->getPath());
        $this->dispatcher->dispatch($event, HandlerEvents::FILE_VALIDATION_ENDED);
    }

    public function parseWorkouts(HandlerOptions $handlerOptions, \DateTime $startDate = null)
    {
        $event = new HandlerEvent($handlerOptions);
        $this->dispatcher->dispatch($event, HandlerEvents::PARSING_WORKOUTS_STARTED);

        $prefix = $handlerOptions->getPrefix();
        $workouts = $this->parser->findAllWorkouts($prefix);

        $debugMessages = $this->parser->getDebugMessages();
        $event->setDebugMessages($debugMessages);
        $this->dispatcher->dispatch($event, HandlerEvents::PARSING_WORKOUTS_ENDED);

        if ($event->getStop()) {
            return null;
        }

        return $workouts;
    }

    /**
     * @return GarminConnect
     */
    public function getClient(): GarminConnect
    {
        return $this->client;
    }

    /**
     * @param GarminConnect $client
     * @return AbstractHandler
     */
    public function setClient(GarminConnect $client): AbstractHandler
    {
        $this->client = $client;
        return $this;
    }
}
