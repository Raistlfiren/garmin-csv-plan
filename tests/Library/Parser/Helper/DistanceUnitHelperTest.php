<?php


namespace App\Tests\Library\Parser\Helper;


use App\Library\Parser\Helper\DistanceUnit;
use PHPUnit\Framework\TestCase;

class DistanceUnitHelperTest extends TestCase
{
    /**
     * @dataProvider fullNameDataProvider
     */
    public function testGetFullName($data, $expected)
    {
        self::assertSame($expected, DistanceUnit::getFullName($data), 'Invalid full name for distance.');
    }

    /**
     * @dataProvider convertToMetersDataProvider
     */
    public function testConvertToMeters($data, $expected)
    {
        self::assertSame($expected, DistanceUnit::convertToMeters($data, 5), 'Invalid meters measurement.');
    }

    /**
     * @dataProvider convertWithPaceUOMDataProvider
     */
    public function testWithPaceUOM($data, $expected)
    {
        self::assertSame($expected, DistanceUnit::withPaceUOM($data), 'Pace UOM is incorrect.');
    }

    /**
     * @dataProvider convertWithSpeedUOMDataProvider
     */
    public function testWithSpeedUOM($data, $expected)
    {
        self::assertSame($expected, DistanceUnit::withSpeedUOM($data), 'Speed UOM is incorrect.');
    }

    /**
     * @dataProvider isValidDataProvider
     */
    public function testIsValid($data, $expected)
    {
        self::assertSame($expected, DistanceUnit::isValid($data), 'Test DistanceUnit isvalid is incorrect.');
    }


    public function testIsValidException()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid distance! It must either be km, mi, or m. You provided - t');
        DistanceUnit::isValid('t');
    }

    public function fullNameDataProvider()
    {
        return [
            ['km', 'kilometer'],
            ['mi', 'mile'],
            ['m', 'meter']
        ];
    }

    public function convertToMetersDataProvider()
    {
        return [
            ['km', 5000],
            ['mi', 8046.72],
            ['m', 5],
        ];
    }

    public function convertWithPaceUOMDataProvider()
    {
        return [
            ['mpk', 'km'],
            ['mpm', 'mi']
        ];
    }

    public function convertWithSpeedUOMDataProvider()
    {
        return [
            ['kph', 'km'],
            ['mph', 'mi']
        ];
    }

    public function isValidDataProvider()
    {
        return [
            ['km', true],
            ['mi', true],
            ['m', true]
        ];
    }
}