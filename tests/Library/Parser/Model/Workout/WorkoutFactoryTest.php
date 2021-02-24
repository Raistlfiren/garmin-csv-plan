<?php


namespace App\Tests\Library\Parser\Model\Workout;


use App\Library\Parser\Model\Workout\CyclingWorkout;
use App\Library\Parser\Model\Workout\RunningWorkout;
use App\Library\Parser\Model\Workout\WorkoutFactory;
use PHPUnit\Framework\TestCase;

class WorkoutFactoryTest extends TestCase
{
    public function testBuild()
    {
        //Test running
        $workout = WorkoutFactory::build('running', '3.5-4h run', []);
        $workoutData = $workout->jsonSerialize();
        $this->assertInstanceOf(RunningWorkout::class, $workout, 'Invalid running workout instance.');
        $this->assertEquals(1, $workoutData['sportType']['sportTypeId']);
        $this->assertEquals('running', $workoutData['sportType']['sportTypeKey']);
        $this->assertEquals('3.5-4h run', $workoutData['workoutName']);

        //Test cycling
        $workout = WorkoutFactory::build('cycling', '1h cycle', []);
        $workoutData = $workout->jsonSerialize();
        $this->assertInstanceOf(CyclingWorkout::class, $workout, 'Invalid cycling workout instance.');
        $this->assertEquals(2, $workoutData['sportType']['sportTypeId']);
        $this->assertEquals('cycling', $workoutData['sportType']['sportTypeKey']);
        $this->assertEquals('1h cycle', $workoutData['workoutName']);

        //Test invalid workout
        $workout = WorkoutFactory::build(1, '1', []);
        $this->assertEmpty($workout, 'Returning an instance of a workout when it should not.');
    }
}