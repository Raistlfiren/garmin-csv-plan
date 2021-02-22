<?php


namespace App\Tests\Library\Handler;


use App\Library\Handler\AbstractHandler;
use App\Library\Handler\Event\HandlerEvents;
use App\Library\Handler\HandlerOptions;
use App\Library\Parser\Parser;
use dawguk\GarminConnect;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class AbstractHandlerTest extends TestCase
{
    protected $handlerOptions;
    protected $handler;
    protected $client;
    protected $eventDispatcher;

    public function setUp(): void
    {
        $handlerOptions = new HandlerOptions();
        $handlerOptions->setEmail('');
        $handlerOptions->setPassword('');
        $handlerOptions->setPrefix('');
        $handlerOptions->setDryrun(true);
        $handlerOptions->setDelete(false);
        $handlerOptions->setDeleteOnly(false);
        $handlerOptions->setPath('tests/Resource/test.csv');
        $handlerOptions->setStartDate(null);
        $handlerOptions->setEndDate(null);
        $handlerOptions->setCommand('import');

        $this->handlerOptions = $handlerOptions;

        $parser = new Parser();
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->handler = $this->getMockForAbstractClass(AbstractHandler::class, [$parser, $this->eventDispatcher, 'john', 'doe']);
        $this->client = $this->createPartialMock(GarminConnect::class, ['getWorkoutList']);
    }

    public function testDeleteWorkouts()
    {
        $this->markTestSkipped();

        $json = file_get_contents(__DIR__ . '/../../Resource/response/workouts.json');
        $parsedWorkouts = json_decode($json);
        $this->client->expects($this->any())
            ->method('getWorkoutList')
            ->will($this->returnValue($parsedWorkouts));



        $this->handlerOptions->setDelete(true);

        $this->handler->validateFile($this->handlerOptions);
        $workouts = $this->handler->parseWorkouts($this->handlerOptions);
        $this->handler->setClient($this->client);
        $this->handler->deleteWorkouts($this->handlerOptions, $workouts);
    }

//    public function testCreateWorkouts()
//    {
//
//    }
//
//    public function testAttachNotes()
//    {
//
//    }
}