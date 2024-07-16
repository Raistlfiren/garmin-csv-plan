<?php

namespace App\Library\Parser\Model;

use Doctrine\Common\Collections\ArrayCollection;

class WeekCollection extends ArrayCollection
{
    /** @var array $days */
    protected $days;

    public function getDays(): array
    {
        return $this->days;
    }

    public function setDays(array $days): void
    {
        $this->days = $days;
    }

    public function addDay(mixed $day): void
    {
        $this->days[] = $day;
    }

    public function removeDay(mixed $day): void
    {
        if (false !== $key = array_search($day, $this->days, true)) {
            array_splice($this->days, $key, 1);
        }
    }
}
