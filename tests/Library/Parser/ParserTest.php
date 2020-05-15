<?php

namespace App\Tests\Library\Parser;

use App\Library\Garmin\Client;
use App\Library\Parser\Parser;
use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase {

    /**
     * @dataProvider workoutTypeProvider
     */
    public function testParseWorkoutType($data, $expected)
    {
        $client = $this->createMock(Client::class);
        $parser = new Parser($client);
        $this->assertSame($expected, $parser->parseWorkoutType($data), 'REGEX not working properly on workout type.');
    }

    /**
     * @dataProvider workoutNameProvider
     */
    public function testParseWorkoutName($data, $expected)
    {
        $client = $this->createMock(Client::class);
        $parser = new Parser($client);
        $this->assertSame($expected, $parser->parseWorkoutName($data), 'REGEX not working properly on workout name.');
    }

    /**
     * @dataProvider removeFirstLineProvider
     */
    public function testRemoveFirstLine($data, $expected)
    {
        $client = $this->createMock(Client::class);
        $parser = new Parser($client);
        $this->assertSame($expected, $parser->removeFirstLine($data), 'REGEX not working properly on removing first line.');
    }

    /**
     * @dataProvider parseStepsProvider
     */
    public function testParseSteps($data, $expected)
    {
        $client = $this->createMock(Client::class);
        $parser = new Parser($client);
        $parser->removeFirstLine($data);
        $actual = $parser->parseSteps($data);

        if ($expected === null) {
            $this->assertSame($expected, $actual, 'REGEX not working properly on parsing steps.');
        } else {
            $this->assertIsArray($actual, 'REGEX not working properly on parsing steps.');
            $this->assertCount($expected, $actual, 'REGEX not working properly on parsing steps.');
        }
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
