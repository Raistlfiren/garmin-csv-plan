<?php


namespace App\Tests\Library\Parser\Model\Step;


use App\Library\Parser\Model\Step\AbstractStep;
use PHPUnit\Framework\TestCase;

class AbstractStepTest extends TestCase
{
    public function testParseTextDuration()
    {
        $abstractStep = $this->getMockForAbstractClass(AbstractStep::class, [], '', false);
        $textDuration = $abstractStep->parseTextDuration('55:00 @z2');

        self::assertEquals($textDuration, '55:00', 'Invalid text parsed for duration');
    }

    public function testParseTextTarget()
    {
        $abstractStep = $this->getMockForAbstractClass(AbstractStep::class, [], '', false);
        $textDuration = $abstractStep->parseTextTarget('55:00 @z2');

        self::assertEquals($textDuration, 'z2', 'Invalid text parsed for target');
    }
}