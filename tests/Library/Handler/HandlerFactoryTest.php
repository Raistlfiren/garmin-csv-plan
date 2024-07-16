<?php

namespace App\Tests\Library\Handler;

use App\Library\Handler\HandlerFactory;
use App\Library\Handler\HandlerOptions;
use App\Library\Handler\ImportHandler;
use App\Library\Handler\ScheduleHandler;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class HandlerFactoryTest extends KernelTestCase
{
    protected $handlerFactory;
    protected $importHandler;
    protected $scheduleHandler;

    protected function setUp(): void
    {
        $this->importHandler = $this->getMockBuilder(ImportHandler::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['handle'])
            ->getMock();

        $this->scheduleHandler = $this->getMockBuilder(ScheduleHandler::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['handle'])
            ->getMock();

        $this->handlerFactory = new HandlerFactory([$this->importHandler, $this->scheduleHandler]);
    }

    public function testImportBuildCommand()
    {
        $handlerOptions = new HandlerOptions();
        $handlerOptions->setCommand('import');
        $handlerOptions->setPath('tests/Resource/test.csv');
        $handlerOptions->setDryrun(true);
        $handlerOptions->setDelete(false);
        $handlerOptions->setDeleteOnly(false);

        self::assertTrue($this->importHandler->supports($handlerOptions->getCommand()));
    }

    public function testScheduleBuildCommand()
    {
        $handlerOptions = new HandlerOptions();
        $handlerOptions->setCommand('schedule');
        $handlerOptions->setPath('tests/Resource/test.csv');
        $handlerOptions->setDryrun(true);
        $handlerOptions->setDelete(false);
        $handlerOptions->setDeleteOnly(false);

        self::assertTrue($this->scheduleHandler->supports($handlerOptions->getCommand()));
    }
}
