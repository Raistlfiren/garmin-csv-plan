<?php

namespace App\Library\Handler\Event;

final class HandlerEvents
{
    const FILE_VALIDATION_STARTED = 'handler.validation.started';
    const FILE_VALIDATION_ENDED = 'handler.validation.ended';
    const PARSING_WORKOUTS_STARTED = 'handler.parsing.started';
    const PARSING_WORKOUTS_ENDED = 'handler.parsing.ended';
    const DELETE_WORKOUTS_STARTED = 'handler.delete.workouts.started';
    const DELETE_WORKOUTS_ENDED = 'handler.delete.workouts.ended';
    const CREATED_WORKOUTS_STARTED = 'handler.created.workouts.started';
    const CREATED_WORKOUTS_ENDED = 'handler.created.workouts.ended';
    const SCHEDULING_WORKOUTS_STARTED = 'handler.scheduling.workouts.started';
    const SCHEDULING_WORKOUTS_ENDED = 'handler.scheduling.workouts.ended';
}
