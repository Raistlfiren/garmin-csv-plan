<?php


namespace App\Tests\Library\Parser\Model\Target;


use App\Library\Parser\Model\Target\PaceTarget;
use PHPUnit\Framework\TestCase;

class PaceTargetTest extends TestCase
{

    /**
     * @dataProvider paceData
     */
    public function testPace($data, $expected)
    {
        $distanceDuration = PaceTarget::testPace($data);

        self::assertEquals($distanceDuration, $expected);
    }

    public function paceData()
    {
        return [
            ['4:30-5:00', new PaceTarget('4:30', '5:00', 'km')],
            ['7:30-8:00mpk', new PaceTarget('7:30', '8:00', 'km')],
            ['6:30-7:00mpm', new PaceTarget('6:30', '7:00', 'mi')],
            ['invalid', false]
        ];
    }
}