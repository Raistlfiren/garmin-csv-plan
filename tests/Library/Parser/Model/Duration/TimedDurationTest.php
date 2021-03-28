<?php


namespace App\Tests\Library\Parser\Model\Duration;


use App\Library\Parser\Model\Duration\TimedDuration;
use PHPUnit\Framework\TestCase;

class TimedDurationTest extends TestCase
{
    /**
     * @dataProvider timeData
     */
    public function testTimed($data, $expected)
    {
        $timedDuration = TimedDuration::testTimed($data);

        $this->assertEquals($timedDuration, $expected);
    }

    public function timeData()
    {
        return [
            ['10:12', new TimedDuration(10, 12)],
            ['10s', false],
            ['5:00', new TimedDuration(5, 00)],
            ['5mi', false],
        ];
    }
}