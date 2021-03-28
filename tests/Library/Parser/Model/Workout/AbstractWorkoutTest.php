<?php


namespace App\Tests\Library\Parser\Model\Workout;


use App\Library\Parser\Model\Step\CooldownStep;
use App\Library\Parser\Model\Step\IntervalStep;
use App\Library\Parser\Model\Workout\AbstractWorkout;
use PHPUnit\Framework\TestCase;

class AbstractWorkoutTest extends TestCase
{
    public function testSteps()
    {
        $abstractWorkout = $this->getMockForAbstractClass(AbstractWorkout::class, ['Test']);
        $steps = [
            0 => '   - run: 225:00',
            1 => '   - cooldown: lap-button'
        ];

        $abstractWorkout->steps($steps);
        $steps = $abstractWorkout->getSteps();

        $this->assertEquals(2, $steps->count(), 'Not all steps are being counted.');
        $this->assertInstanceOf(IntervalStep::class, $steps[0]);
        $this->assertInstanceOf(CooldownStep::class, $steps[1]);
    }

    public function testIsRepeaterStep()
    {
        $abstractWorkout = $this->getMockForAbstractClass(AbstractWorkout::class, ['Test']);

        $stepText = '  - recover: 900m @z2';
        $result = $abstractWorkout->isRepeaterStep($stepText);
        $this->assertTrue($result, 'Not detecting a repeater step.');
    }

    public function testFailedRepeaterStep()
    {
        $abstractWorkout = $this->getMockForAbstractClass(AbstractWorkout::class, ['Test']);

        $stepText = '- recover: 900m @z2';
        $result = $abstractWorkout->parseStepHeader($stepText);
        $this->assertEquals('recover', $result, 'Invalid step header detected.');
    }

    public function testParseStepHeader()
    {
        $abstractWorkout = $this->getMockForAbstractClass(AbstractWorkout::class, ['Test']);

        $stepText = '- recover: 900m @z2;Hello World';
        $result = $abstractWorkout->parseStepNotes($stepText);
        $this->assertEquals('Hello World', $result, 'Invalid notes detected');
    }

    public function testParseStepDetails()
    {
        $abstractWorkout = $this->getMockForAbstractClass(AbstractWorkout::class, ['Test']);

        $stepText = '- recover: 900m @z2;Hello World';
        $result = $abstractWorkout->parseStepDetails($stepText);
        $this->assertEquals('900m @z2', $result, 'Invalid parsed step detected');
    }

    public function testParseStepNotes()
    {
        $abstractWorkout = $this->getMockForAbstractClass(AbstractWorkout::class, ['Test']);

        $stepText = '- recover: 900m @z2;Hello World';
        $result = $abstractWorkout->parseStepNotes($stepText);
        $this->assertEquals('Hello World', $result, 'Invalid notes detected');
    }
}