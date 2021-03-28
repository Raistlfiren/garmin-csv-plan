<?php


namespace App\Tests\Library\Parser\Model\Duration;


use App\Library\Parser\Model\Duration\DistanceDuration;
use PHPUnit\Framework\TestCase;

class DistanceDurationTest extends TestCase
{

    /**
     * @dataProvider durationData
     */
    public function testDistance($data, $expected)
    {
        $distanceDuration = DistanceDuration::testDistance($data);

        $this->assertEquals($distanceDuration, $expected);
    }

    public function durationData()
    {
        return [
            ['2km', new DistanceDuration(2, 'km')],
            ['225:00', false],
            ['10s', false],
            ['10m', new DistanceDuration(10, 'm')],
            ['5mi', new DistanceDuration(5, 'mi')],
            ['10km', new DistanceDuration(10, 'km')]
        ];
    }
}