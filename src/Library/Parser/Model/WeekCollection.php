<?php

namespace App\Library\Parser\Model;

use Doctrine\Common\Collections\ArrayCollection;

class WeekCollection extends ArrayCollection
{
    /** @var array $days */
    protected $days;

    /**
     * @return array
     */
    public function getDays(): array
    {
        return $this->days;
    }

    /**
     * @param array $days
     */
    public function setDays(array $days): void
    {
        $this->days = $days;
    }

    /**
     * @param mixed $day
     */
    public function addDay($day)
    {
        $this->days[] = $day;
    }

    /**
     * @param mixed $day
     */
    public function removeDay($day)
    {
        if (false !== $key = array_search($day, $this->days, true)) {
            array_splice($this->days, $key, 1);
        }
    }
}
