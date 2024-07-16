<?php

namespace App\Subscriber;

use App\Library\Handler\Event\HandlerEvent;
use App\Library\Handler\Event\HandlerEvents;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class WorkoutCommandSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly \Symfony\Component\Console\Style\SymfonyStyle $symfonyStyle)
    {
    }

    public static function getSubscribedEvents(): array
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

    public function onCreatedWorkoutsEnded(HandlerEvent $event): void
    {
        $this->symfonyStyle->listing($event->getDebugMessages());
        $this->symfonyStyle->success('Workout import was successful.');
    }

    public function onCreatedWorkoutsStarted(HandlerEvent $event): void
    {
        $this->symfonyStyle->section('Creating workouts');
    }

    public function onDeleteWorkoutsEnded(HandlerEvent $event): void
    {
        $this->symfonyStyle->listing($event->getDebugMessages());
    }

    public function onDeleteWorkoutsStarted(HandlerEvent $event): void
    {
        $this->symfonyStyle->section('Deleting old workouts');
    }

    public function onFileValidationEnded(HandlerEvent $event): void
    {
        $this->symfonyStyle->success('File valid');
    }

    public function onFileValidationStarted(HandlerEvent $event): void
    {
        $this->symfonyStyle->section('Validating and accessing - ' . $event->getHandlerOptions()->getPath());
    }

    public function onParsingWorkoutsEnded(HandlerEvent $event): void
    {
        $this->symfonyStyle->listing($event->getDebugMessages());
        $answer = $this->symfonyStyle->confirm('Does the following look correct?', true);
        if (! $answer) {
            $this->symfonyStyle->note('Stopping import process');
            $event->setStop(true);
            die();
        }
    }

    public function onParsingWorkoutsStarted(HandlerEvent $event): void
    {
        $this->symfonyStyle->section('Parsing Workouts');
    }

    public function onSchedulingWorkoutsEnded(HandlerEvent $event): void
    {
        $this->symfonyStyle->listing($event->getDebugMessages());
        $this->symfonyStyle->success('Workout scheduling and import was successful');
    }

    public function onSchedulingWorkoutsStarted(HandlerEvent $event): void
    {
        $this->symfonyStyle->section('Scheduling workouts');
    }
}
