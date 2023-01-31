<?php


namespace App\Tests\Library\Parser\Model\Target;

use App\Library\Parser\Model\Target\HRZoneTarget;
use App\Library\Parser\Model\Target\PaceTarget;
use App\Library\Parser\Model\Target\TargetFactory;
use PHPUnit\Framework\TestCase;

class TargetFactoryTest extends TestCase
{
    public function testBuildIntervalStep()
    {
        $targetObject = TargetFactory::build('z2');

        self::assertInstanceOf(HRZoneTarget::class, $targetObject, 'HR Target class not chosen.');

        $jsonArray = $targetObject->jsonSerialize();

        self::assertEquals(4, $jsonArray['targetType']['workoutTargetTypeId']);
        self::assertEquals('2', $jsonArray['zoneNumber']);
        self::assertEmpty($jsonArray['targetValueOne']);
        self::assertEquals('heart.rate.zone', $jsonArray['targetType']['workoutTargetTypeKey']);
    }

    public function testBuildPaceTarget()
    {
        $targetObject = TargetFactory::build('6:30-7:00');

        self::assertInstanceOf(PaceTarget::class, $targetObject, 'Pace Target class not chosen.');

        $jsonArray = $targetObject->jsonSerialize();

        self::assertEquals(6, $jsonArray['targetType']['workoutTargetTypeId']);
        self::assertEquals(2.564103, $jsonArray['targetValueOne']);
        self::assertEquals(2.380952, $jsonArray['targetValueTwo']);
        self::assertEmpty($jsonArray['zoneNumber']);
        self::assertEquals('pace.zone', $jsonArray['targetType']['workoutTargetTypeKey']);
    }
}