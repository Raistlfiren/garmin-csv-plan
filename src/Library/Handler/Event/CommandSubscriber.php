<?php

namespace App\Library\Handler\Event;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CommandSubscriber implements EventSubscriberInterface
{
    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    public function __construct(SymfonyStyle $symfonyStyle)
    {
        $this->symfonyStyle = $symfonyStyle;
    }

    public static function getSubscribedEvents()
    {
        return [
            HandlerEvents::FILE_VALIDATION_STARTED => 'onFileValidationStarted',
            HandlerEvents::FILE_VALIDATION_ENDED => 'onFileValidationEnded',
            HandlerEvents::PARSING_WORKOUTS_STARTED => 'onParsingWorkoutsStarted',
            HandlerEvents::PARSING_WORKOUTS_ENDED => 'onParsingWorkoutsEnded',
            HandlerEvents::DELETE_WORKOUTS_STARTED => 'onDeleteWorkoutsStarted',
            HandlerEvents::DELETE_WORKOUTS_ENDED => 'onDeleteWorkoutsEnded',
            HandlerEvents::CREATED_WORKOUTS_STARTED => 'onCreatedWorkoutsStarted',
            HandlerEvents::CREATED_WORKOUTS_ENDED => 'onCreatedWorkoutsEnded',
            HandlerEvents::SCHEDULING_WORKOUTS_STARTED => 'onSchedulingWorkoutsStarted',
            HandlerEvents::SCHEDULING_WORKOUTS_ENDED => 'onSchedulingWorkoutsEnded',
        ];
    }

    public function onCreatedWorkoutsEnded(HandlerEvent $event)
    {
        $this->symfonyStyle->listing($event->getDebugMessages());
        $this->symfonyStyle->success('Workout import was successful.');
    }

    public function onCreatedWorkoutsStarted(HandlerEvent $event)
    {
        $this->symfonyStyle->section('Creating workouts');
    }

    public function onDeleteWorkoutsEnded(HandlerEvent $event)
    {
        $this->symfonyStyle->listing($event->getDebugMessages());
    }

    public function onDeleteWorkoutsStarted(HandlerEvent $event)
    {
        $this->symfonyStyle->section('Deleting old workouts');
    }

    public function onFileValidationEnded(HandlerEvent $event)
    {
        $this->symfonyStyle->success('File valid');
    }

    public function onFileValidationStarted(HandlerEvent $event)
    {
        $this->symfonyStyle->section('Validating and accessing - ' . $event->getHandlerOptions()->getPath());
    }

    public function onParsingWorkoutsEnded(HandlerEvent $event)
    {
        $this->symfonyStyle->listing($event->getDebugMessages());
        $answer = $this->symfonyStyle->confirm('Does the following look correct?', true);
        if (! $answer) {
            $this->symfonyStyle->note('Stopping import process');
            $event->setStop(true);
        }
    }

    public function onParsingWorkoutsStarted(HandlerEvent $event)
    {
        $this->symfonyStyle->section('Parsing Workouts');
    }

    public function onSchedulingWorkoutsEnded(HandlerEvent $event)
    {
        $this->symfonyStyle->listing($event->getDebugMessages());
        $this->symfonyStyle->success('Workout scheduling and import was successful');
    }

    public function onSchedulingWorkoutsStarted(HandlerEvent $event)
    {
        $this->symfonyStyle->section('Scheduling workouts');
    }
}
