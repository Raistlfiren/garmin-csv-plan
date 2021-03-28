<?php

namespace App\Library\Handler;

use App\Library\Handler\Event\HandlerEvent;
use App\Library\Handler\Event\HandlerEvents;
use DateTime;

class ScheduleHandler extends AbstractHandler
{
    public function handle(HandlerOptions $handlerOptions)
    {
        parent::handle($handlerOptions);

        // Get and parse the inputted start and end date
        $start = $this->convertStringToDate($handlerOptions->getStartDate());
        $end = $this->convertStringToDate($handlerOptions->getEndDate());

        // Find out how many weeks are in the plan
        $totalWeeks = $this->parser->getTotalWeeks();

        // Calculate the timespan of the plan to get the start and end date
        $start = $this->timespan($totalWeeks, $start, $end);

        // Create a collection of workouts depending upon the day
        $period = $this->parser->scheduleWorkouts($start, $this->getWorkouts());

        //Check to see if this should be a dry run or delete only
        if (! $handlerOptions->getDryrun() || ! $handlerOptions->getDeleteOnly()) {
            // Schedule the workouts on Garmin
            $this->scheduleWorkoutsOnGarmin($handlerOptions, $period->getDays());
        }
    }

    public function scheduleWorkoutsOnGarmin(HandlerOptions $handlerOptions, array $days)
    {
        $event = new HandlerEvent($handlerOptions);
        $this->dispatcher->dispatch($event, HandlerEvents::SCHEDULING_WORKOUTS_STARTED);

        $this->garminHelper->scheduleWorkouts($days);

        $debugMessages = $this->garminHelper->getDebugMessages();

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
