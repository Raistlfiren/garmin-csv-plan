<?php

namespace App\Command;

use App\Library\Handler\HandlerFactory;
use App\Library\Handler\HandlerOptions;
use App\Subscriber\WorkoutCommandSubscriber;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class WorkoutCommand extends Command
{
    protected static $defaultName = 'garmin:workout';

    /**
     * @var HandlerFactory
     */
    private $handlerFactory;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    public function __construct(string $name = null, HandlerFactory $handlerFactory, EventDispatcherInterface $dispatcher)
    {
        parent::__construct($name);
        $this->handlerFactory = $handlerFactory;
        $this->dispatcher = $dispatcher;
    }

    protected function configure()
    {
        $this
            ->setDescription('Parses and reads a CSV file into Garmin Connect')
            ->setHelp('This command allows you to parse out a CSV file')
            ->addArgument('csv', InputArgument::REQUIRED, 'The specified CSV file path that you want to import into Garmin connect')
            ->addArgument('type', InputArgument::OPTIONAL, 'Specify import OR schedule to either just import the workouts into Garmin connect 
or import **AND** schedule the workouts.', 'import')
            ->addOption('email', 'm',InputOption::VALUE_REQUIRED, 'Email to login to Garmin', '')
            ->addOption('password', 'p', InputOption::VALUE_REQUIRED, 'Password to login to Garmin', '')
            ->addOption('delete', 'x', InputOption::VALUE_NONE, 'Delete previous workouts from CSV file')
            ->addOption('delete-only', 'X', InputOption::VALUE_NONE, 'ONLY delete workouts that are contained in the CSV file')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Dry run that will prevent anything from being created or deleted from Garmin')
            ->addOption('prefix', 'r', InputOption::VALUE_OPTIONAL, 'A prefix to put before every workout name/title', null)
            ->addOption('pool-size', null, InputOption::VALUE_OPTIONAL, 'The pool size specified for all workouts in the plan Ex.: 25yds OR 100m', null)
            ->addOption('start', 's', InputOption::VALUE_REQUIRED, 'Date of the FIRST day of the first week of the plan Ex.: 2021-01-01 YYYY-MM-DD')
            ->addOption('end', 'd', InputOption::VALUE_REQUIRED, 'Date of the LAST day of the last week of the plan Ex.: 2021-01-31 YYYY-MM-DD');

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Starting workout import/export command');

        $command = $input->getArgument('type');
        $email = $input->getOption('email');
        $password = $input->getOption('password');
        $prefix = $input->getOption('prefix');
        $dryrun = $input->getOption('dry-run');
        $poolSize = $input->getOption('pool-size');
        $delete = $input->getOption('delete');
        $deleteOnly = $input->getOption('delete-only');
        $path = $input->getArgument('csv');
        $start = $input->getOption('start');
        $end = $input->getOption('end');

        $handlerOptions = new HandlerOptions();
        $handlerOptions->setEmail($email);
        $handlerOptions->setPassword($password);
        $handlerOptions->setPrefix($prefix);
        $handlerOptions->setDryrun($dryrun);
        $handlerOptions->setPoolSize($poolSize);
        $handlerOptions->setDelete($delete||$deleteOnly);
        $handlerOptions->setDeleteOnly($deleteOnly);
        $handlerOptions->setPath($path);
        $handlerOptions->setStartDate($start);
        $handlerOptions->setEndDate($end);
        $handlerOptions->setCommand($command);

        $this->registerSubscriber($io, $this->dispatcher);

        $this->handlerFactory->buildCommand($handlerOptions);

        return 0;
    }


    protected function registerSubscriber(SymfonyStyle $symfonyStyle, EventDispatcherInterface $eventDispatcher)
    {
        $subscriber = new WorkoutCommandSubscriber($symfonyStyle);
        $eventDispatcher->addSubscriber($subscriber);
    }
}
