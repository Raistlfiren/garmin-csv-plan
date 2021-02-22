<?php


namespace App\Tests\Library\Parser\Model\Duration;


use App\Library\Parser\Model\Duration\DistanceDuration;
use App\Library\Parser\Model\Duration\DurationFactory;
use App\Library\Parser\Model\Duration\LapButtonDuration;
use App\Library\Parser\Model\Duration\TimedDuration;
use PHPUnit\Framework\TestCase;

class DurationFactoryTest extends TestCase
{
    public function testBuildLap()
    {
        $durationObject = DurationFactory::build('lap-button');

        $test = json_encode($durationObject);

        $this->assertInstanceOf(LapButtonDuration::class, $durationObject, 'Lap button not chosen.');
    }

    public function testBuildDistance()
    {
        $durationObject = DurationFactory::build('5.25mi');

        $this->assertInstanceOf(DistanceDuration::class, $durationObject, 'Distance duration not chosen.');
    }

    public function testTimedDuration()
    {
        $durationObject = DurationFactory::build('7:35');

        $this->assertInstanceOf(TimedDuration::class, $durationObject, 'Timed duration not chosen.');
    }
}