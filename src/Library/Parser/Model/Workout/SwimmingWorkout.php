<?php

namespace App\Library\Parser\Model\Workout;

use App\Library\Parser\Helper\DistanceUnit;

class SwimmingWorkout extends AbstractWorkout
{
    protected $poolSizeLength;

    protected $poolSizeUnit;

    public function __construct($name, $poolSize = null)
    {
        if ($poolSize === null) {
            throw new \Exception('Please set the --pool-size option as a parameter when import swimming workouts.');
        }

        $this->parsePoolSize($poolSize);

        parent::__construct($name);
    }

    protected function getSportTypeId()
    {
        return 4;
    }

    protected function getSportTypeKey()
    {
        return 'swimming';
    }

    protected function parsePoolSize($poolSize)
    {
        $regex = '/^(\d+(.\d+)?)\s*(m|yds)$/';

        $result = preg_match($regex, $poolSize, $length);

        if ($result && isset($length[1]) && ! empty($length[1]) && isset($length[3]) && ! empty($length[3])) {
            $this->poolSizeLength = $length[1];
            $this->poolSizeUnit = DistanceUnit::getFullName($length[3]);
        }
    }
}
