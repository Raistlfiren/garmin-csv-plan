<?php

namespace App\Library\Handler\Event;

final class HandlerEvents
{
    public const FILE_VALIDATION_STARTED = 'handler.validation.started';

    public const FILE_VALIDATION_ENDED = 'handler.validation.ended';

    public const PARSING_WORKOUTS_STARTED = 'handler.parsing.started';

    public const PARSING_WORKOUTS_ENDED = 'handler.parsing.ended';

    public const AUTHENTICATE_GARMIN_STARTED = 'authenticate.garmin.started';

    public const AUTHENTICATE_GARMIN_ENDED = 'authenticate.garmin.ended';

    public const DELETE_WORKOUTS_STARTED = 'handler.delete.workouts.started';

    public const DELETE_WORKOUTS_ENDED = 'handler.delete.workouts.ended';

    public const CREATED_WORKOUTS_STARTED = 'handler.created.workouts.started';

    public const CREATED_WORKOUTS_ENDED = 'handler.created.workouts.ended';

    public const SCHEDULING_WORKOUTS_STARTED = 'handler.scheduling.workouts.started';

    public const SCHEDULING_WORKOUTS_ENDED = 'handler.scheduling.workouts.ended';
}
