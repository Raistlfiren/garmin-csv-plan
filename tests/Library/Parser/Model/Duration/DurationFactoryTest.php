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

        $this->assertInstanceOf(LapButtonDuration::class, $durationObject, 'Lap button not chosen.');

        $jsonArray = $durationObject->jsonSerialize();

        $this->assertEquals(1, $jsonArray['endCondition']['conditionTypeId']);
        $this->assertEquals('lap.button', $jsonArray['endCondition']['conditionTypeKey']);
    }

    public function testBuildDistance()
    {
        $durationObject = DurationFactory::build('5.25mi');

        $this->assertInstanceOf(DistanceDuration::class, $durationObject, 'Distance duration not chosen.');

        $jsonArray = $durationObject->jsonSerialize();

        $this->assertEquals(3, $jsonArray['endCondition']['conditionTypeId']);
        $this->assertEquals('distance', $jsonArray['endCondition']['conditionTypeKey']);
    }

    public function testTimedDuration()
    {
        $durationObject = DurationFactory::build('7:35');

        $this->assertInstanceOf(TimedDuration::class, $durationObject, 'Timed duration not chosen.');

        $jsonArray = $durationObject->jsonSerialize();

        $this->assertEquals(2, $jsonArray['endCondition']['conditionTypeId']);
        $this->assertEquals('time', $jsonArray['endCondition']['conditionTypeKey']);
    }

    public function testInvalidDuration()
    {
        $durationText = '2:23asdasda';

        $this->expectExceptionMessage('Invalid duration for - ' . $durationText);

        DurationFactory::build($durationText);
    }
}