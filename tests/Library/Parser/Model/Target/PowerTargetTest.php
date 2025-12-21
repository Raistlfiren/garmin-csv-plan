<?php

namespace App\Tests\Library\Parser\Model\Target;

use App\Library\Parser\Model\Target\PowerTarget;
use PHPUnit\Framework\TestCase;

class PowerTargetTest extends TestCase
{
    /**
     * @dataProvider powerData
     */
    public function testPower($powerText, $criticalPower, $expected)
    {
        $powerTarget = PowerTarget::testPower($powerText, $criticalPower);

        self::assertEquals($expected, $powerTarget);
    }

    public static function powerData(): array
    {
        return [

            // Absolute Watts Tests
            ['200-220w', null, new PowerTarget(200, 220)],
//            [' 210 - 230watts ', null, new PowerTarget(210, 230)],
            ['250w', null, new PowerTarget(250, 251)],
            ['300-250w', null, new PowerTarget(250, 300)],

            // Percentage CP Tests
            ['90-95%cp', 300, new PowerTarget(270, 285)],
            ['100-110pcp', 280, new PowerTarget(280, 308)],
            ['120%cp', 250, new PowerTarget(300, 301)],
            ['100-90%cp', 300, new PowerTarget(270, 300)],

            // Edge Cases and Invalid Data
            ['90-95%cp', null, false],
            ['90-95%cp', 0, false],
            ['some invalid text', null, false],
            ['4:30-5:00', null, false],
            ['150-160bpm', null, false],
            ['', null, false],
            [null, 250, false],
        ];
    }
}