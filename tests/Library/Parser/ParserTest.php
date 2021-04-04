<?php

namespace App\Tests\Library\Parser;

use App\Library\Garmin\Client;
use App\Library\Parser\Parser;
use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase {

    public function testIsValidFile()
    {
        $filePath = 'tests/Resource/test.csv';
        $parser = new Parser();

        $this->assertTrue($parser->isValidFile($filePath), 'File not found and is invalid');
    }

    public function testFailIsValidFile()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid file. Please make sure the file exists.');
        $filePath = 'tests/Resource/testasssss.csv';
        $parser = new Parser();
        $parser->isValidFile($filePath);
    }

    /**
     * @dataProvider filePathsDataProvider
     */
    public function testGetRecords($filePath, $expected)
    {
        $parser = new Parser();
        $parser->isValidFile($filePath);

        $this->assertEquals(iterator_count($parser->getRecords()), $expected, 'Invalid record count from get records');
    }

    /**
     * @dataProvider filePathsWorkoutsDataProvider
     */
    public function testfindAllWorkouts($filePath, $workoutsCount)
    {
        $parser = new Parser();
        $parser->isValidFile($filePath);
        $workouts = $parser->findAllWorkouts();

        $this->assertEquals(count($workouts), $workoutsCount, 'Not enough workouts found or too many...');
    }

    /**
     * @dataProvider workoutTypeProvider
     */
    public function testParseWorkoutType($data, $expected)
    {
        $parser = new Parser();
        $this->assertSame($expected, $parser->parseWorkoutType($data), 'REGEX not working properly on workout type.');
    }

    /**
     * @dataProvider workoutNameProvider
     */
    public function testParseWorkoutName($data, $expected)
    {
        $parser = new Parser();
        $this->assertSame($expected, $parser->parseWorkoutName($data), 'REGEX not working properly on workout name.');
    }

    /**
     * @dataProvider removeFirstLineProvider
     */
    public function testRemoveFirstLine($data, $expected)
    {
        $parser = new Parser();
        $this->assertSame($expected, $parser->removeFirstLine($data), 'REGEX not working properly on removing first line.');
    }

    /**
     * @dataProvider parseStepsProvider
     */
    public function testParseSteps($data, $expected)
    {
        $parser = new Parser();
        $stepsText = $parser->removeFirstLine($data);
        $actual = $parser->parseSteps($stepsText);

        if ($expected === null) {
            $this->assertSame($expected, $actual, 'REGEX not working properly on parsing steps.');
        } else {
            $this->assertIsArray($actual, 'REGEX not working properly on parsing steps.');
            $this->assertCount($expected, $actual, 'REGEX not working properly on parsing steps.');
        }
    }

    public function filePathsDataProvider()
    {
        return [
            ['tests/Resource/test.csv', 1],
            ['tests/Resource/ultra-80k-runnersworld.csv', 20]
        ];
    }

    public function filePathsWorkoutsDataProvider()
    {
        return [
            ['tests/Resource/test.csv', 2],
            ['tests/Resource/ultra-80k-runnersworld.csv', 26]
        ];
    }

    public function workoutTypeProvider()
    {
        return [
            ['running: 14k, 4x 1.6k @TMP
- warmup: 2km @z2
- repeat: 4
  - run: 1600m @ 5:00-4:30
  - recover: 900m @z2
- run: 2km
- cooldown: lap-button', 'running'
            ],
            ['running: 8k jog
- run: 8km
- cooldown: lap-button', 'running'],
            ['cycling: 10k bike', 'cycling'],
            ['testing: 10k bike', null],
        ];
    }

    public function workoutNameProvider()
    {
        return [
            ['running: 14k, 4x 1.6k @TMP
- warmup: 2km @z2
- repeat: 4
  - run: 1600m @ 5:00-4:30
  - recover: 900m @z2
- run: 2km
- cooldown: lap-button', '14k, 4x 1.6k @TMP'
            ],
            ['running: 8k jog
- run: 8km
- cooldown: lap-button', '8k jog'],
            ['cycling: ', null],
            ['testing:', null],
        ];
    }

    public function removeFirstLineProvider()
    {
        return [
            ['running: 14k, 4x 1.6k @TMP
- warmup: 2km @z2
- repeat: 4
  - run: 1600m @ 5:00-4:30
  - recover: 900m @z2
- run: 2km
- cooldown: lap-button', '- warmup: 2km @z2
- repeat: 4
  - run: 1600m @ 5:00-4:30
  - recover: 900m @z2
- run: 2km
- cooldown: lap-button'
            ],
            ['running: 8k jog
- run: 8km
- cooldown: lap-button', '- run: 8km
- cooldown: lap-button'],
            ['cycling: ', ''],
            ['testing:', ''],
        ];
    }

    public function parseStepsProvider()
    {
        return [
            ['running: 14k, 4x 1.6k @TMP
- warmup: 2km @z2
- repeat: 4
  - run: 1600m @ 5:00-4:30
  - recover: 900m @z2
- run: 2km
- cooldown: lap-button', 6
            ],
            ['running: 8k jog
- run: 8km
- cooldown: lap-button', 2],
            ['cycling: ', null],
            ['testing:', null],
        ];
    }
}
