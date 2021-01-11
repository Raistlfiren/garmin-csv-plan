<?php

namespace App\Tests\Command;

use App\Command\WorkoutCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class WorkoutCommandTest extends KernelTestCase
{
    public function testCommand()
    {
        $this->markTestSkipped('must be revisited.');

        $kernel = static::createKernel();
        $application = new Application($kernel);

        $command = $application->find('garmin:workout');

        $this->assertInstanceOf(WorkoutCommand::class, $command, 'Command was not found.');

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'csv' => 'tests/Resources/test.csv',
            '--dry-run' => true,
        ]);
    }
}
