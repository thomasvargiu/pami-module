<?php

namespace PamiModuleTest\Service;

use PamiModule\Service\Client;
use Zend\EventManager\Event;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    public function testClient()
    {
        $pami = $this->getMockBuilder('PAMI\\Client\\Impl\\ClientImpl')
            ->disableOriginalConstructor()
            ->getMock();

        $eventManager = $this->getMockBuilder('Zend\\EventManager\\EventManager')
            ->getMock();

        /* @var \PAMI\Client\Impl\ClientImpl $pami */
        /* @var \Zend\EventManager\EventManager $eventManager */

        $client = new Client('host', $pami);
        $client->setEventManager($eventManager);
        static::assertSame($pami, $client->getConnection());
        static::assertEquals('host', $client->getHost());

        // Test attach EventManager

        $eventManager = $client->getEventManager();
        static::assertSame($eventManager, $client->getEventManager());

        $params = ['foo' => 'bar'];
        $client->setParams($params);
        static::assertEquals($params, $client->getParams());
    }

    public function testConnect()
    {
        $pami = $this->getMockBuilder('PAMI\\Client\\Impl\\ClientImpl')
            ->disableOriginalConstructor()
            ->setMethods(['open'])
            ->getMock();

        $eventManager = $this->getMockBuilder('Zend\\EventManager\\EventManager')
            ->setMethods(['trigger'])
            ->getMock();

        $pami->expects(static::once())
            ->method('open');

        $eventResults = $this->getMock('Zend\\EventManager\\ResponseCollection', ['stopped'], [], '', false);
        $eventResults->expects(static::once())->method('stopped')->willReturn(false);

        /* @var \PAMI\Client\Impl\ClientImpl $pami */

        $client = new Client('host', $pami);
        $client->setEventManager($eventManager);

        $eventManager->expects(static::exactly(2))
            ->method('trigger')
            ->withConsecutive(
                ['connect.pre', $client],
                ['connect.post', $client]
            )
            ->will(static::returnValue($eventResults));

        $result = $client->connect();
        static::assertSame($client, $result);
    }

    public function testConnectStopped()
    {
        $pami = $this->getMockBuilder('PAMI\\Client\\Impl\\ClientImpl')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $eventManager = $this->getMockBuilder('Zend\\EventManager\\EventManager')
            ->setMethods(['trigger'])
            ->getMock();

        $eventResults = $this->getMock('Zend\\EventManager\\ResponseCollection', ['stopped'], [], '', false);
        $eventResults->expects(static::once())->method('stopped')->willReturn(true);

        /* @var \PAMI\Client\Impl\ClientImpl $pami */

        $client = new Client('host', $pami);
        $client->setEventManager($eventManager);

        $eventManager->expects(static::once())
            ->method('trigger')
            ->with('connect.pre', $client)
            ->willReturn($eventResults);

        $result = $client->connect();
        static::assertSame($client, $result);
    }

    public function testDisconnect()
    {
        $pami = $this->getMockBuilder('PAMI\\Client\\Impl\\ClientImpl')
            ->disableOriginalConstructor()
            ->setMethods(['close'])
            ->getMock();

        $eventManager = $this->getMockBuilder('Zend\\EventManager\\EventManager')
            ->setMethods(['trigger'])
            ->getMock();

        $pami->expects(static::once())
            ->method('close');

        $eventResults = $this->getMock('Zend\\EventManager\\ResponseCollection', ['stopped'], [], '', false);
        $eventResults->expects(static::once())->method('stopped')->willReturn(false);

        /* @var \PAMI\Client\Impl\ClientImpl $pami */

        $client = new Client('host', $pami);
        $client->setEventManager($eventManager);

        $eventManager->expects(static::exactly(2))
            ->method('trigger')
            ->withConsecutive(
                ['disconnect.pre', $client],
                ['disconnect.post', $client]
            )
            ->willReturn($eventResults);

        $result = $client->disconnect();
        static::assertSame($client, $result);
    }

    public function testDisconnectStopped()
    {
        $pami = $this->getMockBuilder('PAMI\\Client\\Impl\\ClientImpl')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $eventManager = $this->getMockBuilder('Zend\\EventManager\\EventManager')
            ->setMethods(['trigger'])
            ->getMock();

        $eventResults = $this->getMock('Zend\\EventManager\\ResponseCollection', ['stopped'], [], '', false);
        $eventResults->expects(static::once())->method('stopped')->willReturn(true);

        /* @var \PAMI\Client\Impl\ClientImpl $pami */

        $client = new Client('host', $pami);
        $client->setEventManager($eventManager);

        $eventManager->expects(static::once())
            ->method('trigger')
            ->with('disconnect.pre', $client)
            ->willReturn($eventResults);

        $result = $client->disconnect();
        static::assertSame($client, $result);
    }

    public function testProcess()
    {
        $pami = $this->getMockBuilder('PAMI\\Client\\Impl\\ClientImpl')
            ->disableOriginalConstructor()
            ->setMethods(['process'])
            ->getMock();

        $eventManager = $this->getMockBuilder('Zend\\EventManager\\EventManager')
            ->setMethods(['trigger', 'stopped'])
            ->getMock();

        $pami->expects(static::once())
            ->method('process');

        $eventResults = $this->getMock('Zend\\EventManager\\ResponseCollection', ['stopped'], [], '', false);
        $eventResults->expects(static::once())->method('stopped')->willReturn(false);

        /* @var \PAMI\Client\Impl\ClientImpl $pami */

        $client = new Client('host', $pami);
        $client->setEventManager($eventManager);

        $eventManager->expects(static::exactly(2))
            ->method('trigger')
            ->withConsecutive(
                ['process.pre', $client],
                ['process.post', $client]
            )
        ->willReturn($eventResults);

        $result = $client->process();
        static::assertSame($client, $result);
    }

    public function testProcessStopped()
    {
        $pami = $this->getMockBuilder('PAMI\\Client\\Impl\\ClientImpl')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $eventManager = $this->getMockBuilder('Zend\\EventManager\\EventManager')
            ->setMethods(['trigger'])
            ->getMock();

        $eventResults = $this->getMock('Zend\\EventManager\\ResponseCollection', ['stopped'], [], '', false);
        $eventResults->expects(static::once())->method('stopped')->willReturn(true);

        /* @var \PAMI\Client\Impl\ClientImpl $pami */

        $client = new Client('host', $pami);
        $client->setEventManager($eventManager);

        $eventManager->expects(static::once())
            ->method('trigger')
            ->with('process.pre', $client)
            ->willReturn($eventResults);

        $result = $client->process();
        static::assertSame($client, $result);
    }

    public function testSendAction()
    {
        $pami = $this->getMockBuilder('PAMI\\Client\\Impl\\ClientImpl')
            ->disableOriginalConstructor()
            ->setMethods(['send'])
            ->getMock();

        $eventManager = $this->getMockBuilder('Zend\\EventManager\\EventManager')
            ->setMethods(['trigger', 'triggerEventUntil'])
            ->getMock();

        $action = $this->getMockBuilder('PAMI\\Message\\OutgoingMessage')
            ->disableOriginalConstructor()
            ->getMock();

        $pami->expects(static::once())
            ->method('send')
            ->with($action)
            ->willReturn('foo');

        $eventResults = $this->getMock('Zend\\EventManager\\ResponseCollection', ['stopped'], [], '', false);
        $eventResults->expects(static::once())->method('stopped')->willReturn(false);

        /* @var \PAMI\Client\Impl\ClientImpl $pami */

        $client = new Client('host', $pami);
        $client->setEventManager($eventManager);

        $eventManager->expects(static::exactly(1))
            ->method('triggerEventUntil')
            ->with(static::isType('callable'), static::isInstanceOf(Event::class))
            ->willReturn($eventResults);

        $eventManager->expects(static::exactly(1))
            ->method('trigger')
            ->with('sendAction.post', $client, static::isInstanceOf('ArrayObject'));

        $result = $client->sendAction($action);
        static::assertEquals('foo', $result);
    }

    public function testSendActionStopped()
    {
        $pami = $this->getMockBuilder('PAMI\\Client\\Impl\\ClientImpl')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $eventManager = $this->getMockBuilder('Zend\\EventManager\\EventManager')
            ->setMethods(['triggerEventUntil'])
            ->getMock();

        $action = $this->getMockBuilder('PAMI\\Message\\OutgoingMessage')
            ->disableOriginalConstructor()
            ->getMock();

        $response = $this->getMock('PAMI\\Message\\Response\\ResponseMessage', [], [], '', false);

        $eventResults = $this->getMock('Zend\\EventManager\\ResponseCollection', ['stopped', 'last'], [], '', false);
        $eventResults->expects(static::once())->method('stopped')->willReturn(true);
        $eventResults->expects(static::once())->method('last')->willReturn($response);

        /* @var \PAMI\Client\Impl\ClientImpl $pami */

        $client = new Client('host', $pami);
        $client->setEventManager($eventManager);

        $eventManager->expects(static::exactly(1))
            ->method('triggerEventUntil')
            ->with(static::callback(
                function ($callback) use ($response) {
                    static::assertFalse($callback(null));
                    static::assertFalse($callback('string'));
                    static::assertFalse($callback([]));
                    static::assertTrue($callback($response));

                    return true;
                }
            ), static::isInstanceOf(Event::class)
            )
            ->willReturn($eventResults);

        $result = $client->sendAction($action);
        static::assertSame($response, $result);
    }
}
