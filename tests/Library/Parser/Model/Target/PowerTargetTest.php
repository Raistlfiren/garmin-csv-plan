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
            'watts range' => ['200-220w', null, new PowerTarget(200, 220)],
            'watts range with spaces' => [' 210 - 230watts ', null, new PowerTarget(210, 230)],
            'watts single value' => ['250w', null, new PowerTarget(250, 251)],
            'watts swapped range' => ['300-250w', null, new PowerTarget(250, 300)],

            // Percentage CP Tests
            'cp range' => ['90-95%cp', 300, new PowerTarget(270, 285)],
            'cp range with pcp' => ['100-110pcp', 280, new PowerTarget(280, 308)],
            'cp single value' => ['120%cp', 250, new PowerTarget(300, 301)],
            'cp swapped range' => ['100-90%cp', 300, new PowerTarget(270, 300)],

            // Edge Cases and Invalid Data
            'cp with no critical power provided' => ['90-95%cp', null, false],
            'cp with zero critical power' => ['90-95%cp', 0, false],
            'invalid string' => ['some invalid text', null, false],
            'pace string should be invalid' => ['4:30-5:00', null, false],
            'hr string should be invalid' => ['150-160bpm', null, false],
            'empty string' => ['', null, false],
            'null value' => [null, 250, false],
        ];
    }
}