<?php


namespace App\Tests\Library\Handler;


use App\Library\Handler\HandlerFactory;
use App\Library\Handler\HandlerOptions;
use App\Library\Handler\ImportHandler;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class HandlerFactoryTest extends KernelTestCase
{
    protected $handlerFactory;

    protected function setUp() : void
    {
        static::bootKernel();

        $container = self::$container;

        $this->handlerFactory = $container->get(HandlerFactory::class);
    }

    public function testImportBuildCommand()
    {
        $this->markTestIncomplete();
        $handlerOptions = new HandlerOptions();
        $handlerOptions->setCommand('import');
        $handlerOptions->setPath('tests/Resource/test.csv');
        $handlerOptions->setDryrun(true);
        $handlerOptions->setDelete(false);
        $handlerOptions->setDeleteOnly(false);

        $test = $this->handlerFactory->buildCommand($handlerOptions);

        self::assertInstanceOf(ImportHandler::class, $test);
    }
}