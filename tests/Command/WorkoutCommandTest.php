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
        $kernel = static::createKernel();
        $application = new Application($kernel);

        $command = $application->find('garmin:workout');

        $this->assertInstanceOf(WorkoutCommand::class, $command, 'Command was not found.');

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'csv' => 'tests/Resource/test.csv',
            '--dry-run' => true,
        ]);

        $commandTester->assertCommandIsSuccessful();

        $output = $commandTester->getDisplay();

        $this->assertStringContainsString('3.5-4h run', $output);
        $this->assertStringContainsString('14k, 4x 1.6k @TMP', $output);
    }
}
