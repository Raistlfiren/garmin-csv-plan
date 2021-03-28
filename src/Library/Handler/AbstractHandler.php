<?php

namespace App\Library\Handler;

use App\Library\Garmin\Client;
use App\Library\Handler\Event\HandlerEvent;
use App\Library\Handler\Event\HandlerEvents;
use App\Library\Parser\Parser;
use App\Service\GarminHelper;
use dawguk\GarminConnect;
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

    protected $garminHelper;

    protected $workouts;

    public function __construct(
        Parser $parser,
        EventDispatcherInterface $eventDispatcher,
        GarminHelper $garminHelper
    ) {
        $this->parser = $parser;
        $this->dispatcher = $eventDispatcher;
        $this->garminHelper = $garminHelper;
    }

    public function handle(HandlerOptions $handlerOptions)
    {
        // Validate the CSV file
        $this->validateFile($handlerOptions);

        // Find all workouts and return an array
        $workouts = $this->parseWorkouts($handlerOptions);

        // Put the workouts in a variable to do something with them later
        $this->setWorkouts($workouts);

        // See if we should send it to Garmin or not
        if (! $handlerOptions->getDryrun()) {
            // Create a new instance of Garmin client based upon .env or specified username/password
            $this->authenticatingToGarmin($handlerOptions);

            // Check to see if we should delete workouts
            if ($handlerOptions->getDelete()) {
                // Delete workouts in Garmin
                $this->deleteGarminWorkouts($handlerOptions, $workouts);
            }

            // Check to see if we only want to delete workouts or not
            if (! $handlerOptions->getDeleteOnly()) {
                // Create workouts in Garmin
                $this->createGarminWorkouts($handlerOptions, $workouts);
            }

        }
    }

    public function validateFile(HandlerOptions $handlerOptions)
    {
        $event = new HandlerEvent($handlerOptions);
        $this->dispatcher->dispatch($event, HandlerEvents::FILE_VALIDATION_STARTED);
        $this->parser->isValidFile($handlerOptions->getPath());
        $this->dispatcher->dispatch($event, HandlerEvents::FILE_VALIDATION_ENDED);
    }

    public function parseWorkouts(HandlerOptions $handlerOptions)
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

    public function authenticatingToGarmin(HandlerOptions $handlerOptions)
    {
        $event = new HandlerEvent($handlerOptions);
        $this->dispatcher->dispatch($event, HandlerEvents::AUTHENTICATE_GARMIN_STARTED);

        $this->garminHelper->createGarminClient($handlerOptions->getEmail(), $handlerOptions->getPassword());

        $this->dispatcher->dispatch($event, HandlerEvents::AUTHENTICATE_GARMIN_ENDED);
    }

    public function createGarminWorkouts($handlerOptions, $workouts)
    {
        $event = new HandlerEvent($handlerOptions);
        $this->dispatcher->dispatch($event, HandlerEvents::CREATED_WORKOUTS_STARTED);

        $this->garminHelper->createWorkouts($workouts);
        $this->garminHelper->attachNotes($workouts);

        $debugMessages = $this->garminHelper->getDebugMessages();

        $event->setDebugMessages($debugMessages);
        $this->dispatcher->dispatch($event, HandlerEvents::CREATED_WORKOUTS_ENDED);
    }

    public function deleteGarminWorkouts($handlerOptions, $workouts)
    {
        $event = new HandlerEvent($handlerOptions);
        $this->dispatcher->dispatch($event, HandlerEvents::DELETE_WORKOUTS_STARTED);

        $this->garminHelper->deleteWorkouts($workouts);

        $debugMessages = $this->garminHelper->getDebugMessages();

        $event->setDebugMessages($debugMessages);
        $this->dispatcher->dispatch($event, HandlerEvents::DELETE_WORKOUTS_ENDED);
    }

    /**
     * @return mixed
     */
    public function getWorkouts()
    {
        return $this->workouts;
    }

    /**
     * @param mixed $workouts
     * @return AbstractHandler
     */
    public function setWorkouts($workouts)
    {
        $this->workouts = $workouts;
        return $this;
    }
}
