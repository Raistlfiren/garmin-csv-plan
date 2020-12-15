<?php

namespace App\Library\Handler;

use App\Library\Garmin\Client;
use App\Library\Handler\Event\HandlerEvent;
use App\Library\Handler\Event\HandlerEvents;
use App\Library\Parser\Model\Day;
use App\Library\Parser\Model\PeriodCollection;
use App\Library\Parser\Model\Workout\AbstractWorkout;
use App\Library\Parser\Model\Workout\WorkoutFactory;
use App\Library\Parser\Parser;
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
     * @var Client
     */
    protected $client;

    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    public function __construct(Client $client, Parser $parser, EventDispatcherInterface $eventDispatcher)
    {
        $this->parser = $parser;
        $this->client = $client;
        $this->dispatcher = $eventDispatcher;
    }

    abstract public function handle(HandlerOptions $handlerOptions);

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

    public function createWorkouts(HandlerOptions $handlerOptions, array $days)
    {
        if ($handlerOptions->getDeleteOnly()) {
            return;
        }

        $debugMessages = [];
        $event = new HandlerEvent($handlerOptions);
        $this->dispatcher->dispatch($event, HandlerEvents::CREATED_WORKOUTS_STARTED);

        /** @var Day $day */
        $workoutList = [];
        foreach ($days as $day) {
            /** @var AbstractWorkout $workout */
            foreach ($day->getWorkouts() as $workoutKey => $workout) {
                $workoutID = '**********';
                $workoutName = $workout->getName();
                if ($handlerOptions->getDryrun()) {
                    $debugMessages[] = 'Workout - ' . $workoutName . ' was created on the Garmin website with the id ' . $workoutID;
                }
                else {
                    // same workout name already created?
                    if ($workoutID = array_search($workoutName, $workoutList, true)) {
                        $workout->setGarminID($workoutID);
                        $debugMessages[] = 'Workout - ' . $workoutName . ' was previously created on the Garmin website with the id ' . $workoutID;
                    }
                    else {
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
                        $day->updateWorkout($workoutKey, $workout);
                    }
                }
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

    public function attachNotes(HandlerOptions $handlerOptions, PeriodCollection $period)
    {
        if ($handlerOptions->getDeleteOnly()) {
            return;
        }
        
        $steps = $period->getStepsWithNotes();

        //Loop through steps and add their notes
        foreach ($steps as $step) {
            if (! $handlerOptions->getDryrun()) {
                // if the step has no GarminID, it means the same workout was already created
                if ($stepID = $step->getGarminID()) {
                    $this->client->createStepNote($stepID, $step->getNotes(), $step->getWorkout()->getGarminID());
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
        $period = $this->parser->parse($startDate, $handlerOptions->getPrefix());
        $debugMessages = $this->parser->getDebugMessages();
        $event->setDebugMessages($debugMessages);
        $this->dispatcher->dispatch($event, HandlerEvents::PARSING_WORKOUTS_ENDED);

        if ($event->getStop()) {
            return null;
        }

        return $period;
    }
}
