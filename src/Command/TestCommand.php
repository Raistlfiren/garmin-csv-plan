<?php

namespace App\Command;

use App\Http\GarminClient\GarminClient;
use App\Library\Handler\HandlerFactory;
use App\Library\Handler\HandlerOptions;
use App\Subscriber\WorkoutCommandSubscriber;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'garmin:test',
    description: 'Parses and reads a CSV file into Garmin Connect',
    hidden: false,
)]
class TestCommand extends Command
{
    /**
     * @var HandlerFactory
     */
    private $handlerFactory;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    public function __construct(private GarminClient $garminClient)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setHelp('This command allows you to parse out a CSV file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Starting GARMIN test command');

        $this->garminClient->getWorkoutList(0, 9999);

        return Command::SUCCESS;
    }
}
