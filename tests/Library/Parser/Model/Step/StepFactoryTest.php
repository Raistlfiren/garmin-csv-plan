<?php


namespace App\Tests\Library\Parser\Model\Step;

use App\Library\Parser\Model\Step\CooldownStep;
use App\Library\Parser\Model\Step\IntervalStep;
use App\Library\Parser\Model\Step\RecoverStep;
use App\Library\Parser\Model\Step\RepeaterStep;
use App\Library\Parser\Model\Step\RestStep;
use App\Library\Parser\Model\Step\StepFactory;
use App\Library\Parser\Model\Step\WarmupStep;
use PHPUnit\Framework\TestCase;

class StepFactoryTest extends TestCase
{
    public function testBuildIntervalStep()
    {
        $stepObject = StepFactory::build('run', '225:00', null, 6);

        self::assertInstanceOf(IntervalStep::class, $stepObject, 'Interval class not chosen.');

        $jsonArray = $stepObject->jsonSerialize();

        self::assertEquals(3, $jsonArray['stepType']['stepTypeId']);
        self::assertEquals(6, $jsonArray['stepOrder']);
        self::assertEquals('interval', $jsonArray['stepType']['stepTypeKey']);
    }

    public function testBuildCooldownStep()
    {
        $stepObject = StepFactory::build('cooldown', 'lap-button', null, 1);

        self::assertInstanceOf(CooldownStep::class, $stepObject, 'Cooldown class not chosen.');

        $jsonArray = $stepObject->jsonSerialize();

        self::assertEquals(2, $jsonArray['stepType']['stepTypeId']);
        self::assertEquals(1, $jsonArray['stepOrder']);
        self::assertEquals('cooldown', $jsonArray['stepType']['stepTypeKey']);
    }

    public function testBuildWarmupStep()
    {
        $stepObject = StepFactory::build('warmup', '2km @z2', 'testing notes', 0);

        self::assertInstanceOf(WarmupStep::class, $stepObject, 'Warmup class not chosen.');

        $jsonArray = $stepObject->jsonSerialize();

        self::assertEquals(1, $jsonArray['stepType']['stepTypeId']);
        self::assertEquals(0, $jsonArray['stepOrder']);
        self::assertEquals('warmup', $jsonArray['stepType']['stepTypeKey']);
    }

    public function testBuildRecoverStep()
    {
        $stepObject = StepFactory::build('recover', '900m @z2', null, 3);

        self::assertInstanceOf(RecoverStep::class, $stepObject, 'Recover class not chosen.');

        $jsonArray = $stepObject->jsonSerialize();

        self::assertEquals(4, $jsonArray['stepType']['stepTypeId']);
        self::assertEquals(3, $jsonArray['stepOrder']);
        self::assertEquals('recover', $jsonArray['stepType']['stepTypeKey']);
    }

    public function testBuildRestStep()
    {
        $stepObject = StepFactory::build('rest', 'lap-button', null, 5);

        self::assertInstanceOf(RestStep::class, $stepObject, 'Rest class not chosen.');

        $jsonArray = $stepObject->jsonSerialize();

        self::assertEquals(5, $jsonArray['stepType']['stepTypeId']);
        self::assertEquals(5, $jsonArray['stepOrder']);
        self::assertEquals('rest', $jsonArray['stepType']['stepTypeKey']);
    }

    public function testBuildRepeatStep()
    {
        $stepObject = StepFactory::build('repeat', null, null, null);

        self::assertInstanceOf(RepeaterStep::class, $stepObject);
    }

    public function testInvalidStep()
    {
        $stepObject = StepFactory::build('asde', null, null, null);

        self::assertEmpty($stepObject);
    }
}