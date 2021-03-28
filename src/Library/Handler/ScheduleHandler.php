<?php

namespace App\Library\Handler;

use App\Library\Handler\Event\HandlerEvent;
use App\Library\Handler\Event\HandlerEvents;
use App\Library\Parser\Model\Day;
use App\Library\Parser\Model\Workout\AbstractWorkout;
use DateTime;

class ScheduleHandler extends AbstractHandler
{
    public function handle(HandlerOptions $handlerOptions)
    {
        $this->overrideClientCredentials($handlerOptions->getEmail(), $handlerOptions->getPassword());

        $start = $this->convertStringToDate($handlerOptions->getStartDate());
        $end = $this->convertStringToDate($handlerOptions->getEndDate());

        $this->validateFile($handlerOptions);

//        $this->getLogger()->section('Validating and accessing - ' . $path);
//
//        $this->parser->isValidFile($path);
//
//        $this->getLogger()->success('File valid.');

        $totalWeeks = $this->parser->getTotalWeeks();
        $start = $this->timespan($totalWeeks, $start, $end);

        $period = $this->parseWorkouts($handlerOptions, $start);

        if ($period === null) {
            return;
        }

        $workouts = $period->getWorkouts();
        $days = $period->getDays();
        $this->deleteWorkouts($handlerOptions, $workouts);

        $this->createWorkouts($handlerOptions, $days);

//        $this->getLogger()->text('Starting scheduler on ' . $start->format('Y-m-d') . '.');

//        $this->getLogger()->section('Parsing workouts:');
//
//        $period = $this->parser->parse($start);
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

//        $this->getLogger()->section('Scheduling workouts');

        $this->attachNotes($handlerOptions, $workouts);
        
        $this->scheduleWorkout($handlerOptions, $period->getDays());

//        $this->getLogger()->listing($debugMessages);
//        $this->getLogger()->success('Workout scheduling and import was successful.');
    }

    public function scheduleWorkout(HandlerOptions $handlerOptions, array $days)
    {
        $debugMessages = [];
        $event = new HandlerEvent($handlerOptions);
        $this->dispatcher->dispatch($event, HandlerEvents::SCHEDULING_WORKOUTS_STARTED);

        /** @var Day $day */
        foreach ($days as $day) {
            /** @var AbstractWorkout $workout */
            foreach ($day->getWorkouts() as $workoutKey => $workout) {
                if ($day->getDate() && $workout->getGarminID()) {
                    $formattedDate = $day->getDate()->format('Y-m-d');
                    $data = json_encode(['date' => $formattedDate]);
                    $response = $this->client->scheduleWorkout($workout->getGarminID(), $data);
                    $debugMessages[] = 'Workout - ' . $workout->getName() . ' with id '  . $workout->getGarminID() .' was scheduled on the Garmin website for ' . $formattedDate;
                }
            }
        }

        $event->setDebugMessages($debugMessages);
        $this->dispatcher->dispatch($event, HandlerEvents::SCHEDULING_WORKOUTS_ENDED);
    }

    public function timespan($totalWeeks, DateTime $start = null, DateTime $end = null)
    {
        if ($start === null && $end === null) {
            throw new \Exception('Invalid timespan. Please provide a valid start and/or end date');
        }

        $daysModifier = $totalWeeks * 7;

        if ($start && $end === null) {
            $modifiedDateTime = clone $start;
            $modifiedDateTime->modify('+' . $daysModifier . ' day');
            $end = $modifiedDateTime;
        }

        if ($end && $start === null) {
            $modifiedDateTime = clone $end;
            $modifiedDateTime->modify('-' . $daysModifier . ' day');
            $start = $modifiedDateTime;
        }

        return $start;
    }

    /**
     * @param string $date|null
     * @return DateTime|null
     */
    protected function convertStringToDate(string $date = null) : ?DateTime
    {
        $date = DateTime::createFromFormat('Y-m-d', $date);
        return ($date ? $date : null);
    }

    public function supports(string $command)
    {
        return $command === 'schedule';
    }
}
