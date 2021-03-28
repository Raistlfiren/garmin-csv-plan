<?php

namespace App\Library\Handler;

use App\Library\Handler\Event\HandlerEvent;
use App\Library\Handler\Event\HandlerEvents;

class ImportHandler extends AbstractHandler
{
    public function handle(HandlerOptions $handlerOptions)
    {
        $this->validateFile($handlerOptions);
//        $workouts = $this->parser->findAllWorkouts();
//        $event = new HandlerEvent($handlerOptions);
//        $this->dispatcher->dispatch($event, HandlerEvents::FILE_VALIDATION_STARTED);
//
//        $this->parser->isValidFile($handlerOptions->getPath());
//
//        $this->dispatcher->dispatch($event, HandlerEvents::FILE_VALIDATION_ENDED);

//        $this->dispatcher->dispatch($event, HandlerEvents::PARSING_WORKOUTS_STARTED);
//
//        $period = $this->parser->parse();
//
//        $debugMessages = $this->parser->getDebugMessages();
//        $event->setDebugMessages($debugMessages);
//        $this->dispatcher->dispatch($event, HandlerEvents::PARSING_WORKOUTS_ENDED);

        $workouts = $this->parseWorkouts($handlerOptions);

//        if ($period === null) {
//            return;
//        }
//
//        $workouts = $period->getWorkouts();
//        $days = $period->getDays();
        $this->overrideClientCredentials($handlerOptions->getEmail(), $handlerOptions->getPassword());

        $this->deleteWorkouts($handlerOptions, $workouts);

//        if ($handlerOptions->getDelete()) {
//            $this->dispatcher->dispatch($event, HandlerEvents::DELETE_WORKOUTS_STARTED);
//        }
//        $debugMessages = $this->deleteWorkouts($workouts, $handlerOptions->getDelete());
//        $event->setDebugMessages($debugMessages);
//        $this->dispatcher->dispatch($event, HandlerEvents::DELETE_WORKOUTS_ENDED);

        $this->createWorkouts($handlerOptions, $workouts);
//        $this->createWorkouts($handlerOptions, $days);

        $this->attachNotes($handlerOptions, $workouts);

//        $event->setDebugMessages($debugMessages);
//        $this->dispatcher->dispatch($event, HandlerEvents::CREATED_WORKOUTS_ENDED);

        //Attach notes to workouts...
    }

    public function supports(string $command)
    {
        return $command === 'import';
    }
}
