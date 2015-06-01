<?php

namespace PamiModuleTest\Service;

use PamiModule\Service\Client;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    public function testClient()
    {
        $pami = static::getMockBuilder('PAMI\\Client\\Impl\\ClientImpl')
            ->disableOriginalConstructor()
            ->getMock();

        $eventManager = static::getMockBuilder('Zend\\EventManager\\EventManager')
            ->getMock();

        /* @var \PAMI\Client\Impl\ClientImpl $pami */
        /* @var \Zend\EventManager\EventManager $eventManager */

        $client = new Client('host', $pami, $eventManager);
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
        $pami = static::getMockBuilder('PAMI\\Client\\Impl\\ClientImpl')
            ->disableOriginalConstructor()
            ->setMethods(['open'])
            ->getMock();

        $eventManager = static::getMockBuilder('Zend\\EventManager\\EventManager')
            ->setMethods(['trigger'])
            ->getMock();

        $pami->expects(static::once())
            ->method('open');

        $eventResults = static::getMock('Zend\\EventManager\\ResponseCollection', ['stopped'], [], '', false);
        $eventResults->expects(static::once())->method('stopped')->willReturn(false);

        /* @var \PAMI\Client\Impl\ClientImpl $pami */

        $client = new Client('host', $pami, $eventManager);

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
        $pami = static::getMockBuilder('PAMI\\Client\\Impl\\ClientImpl')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $eventManager = static::getMockBuilder('Zend\\EventManager\\EventManager')
            ->setMethods(['trigger'])
            ->getMock();

        $eventResults = static::getMock('Zend\\EventManager\\ResponseCollection', ['stopped'], [], '', false);
        $eventResults->expects(static::once())->method('stopped')->willReturn(true);

        /* @var \PAMI\Client\Impl\ClientImpl $pami */

        $client = new Client('host', $pami, $eventManager);

        $eventManager->expects(static::once())
            ->method('trigger')
            ->with('connect.pre', $client)
            ->willReturn($eventResults);

        $result = $client->connect();
        static::assertSame($client, $result);
    }

    public function testDisconnect()
    {
        $pami = static::getMockBuilder('PAMI\\Client\\Impl\\ClientImpl')
            ->disableOriginalConstructor()
            ->setMethods(['close'])
            ->getMock();

        $eventManager = static::getMockBuilder('Zend\\EventManager\\EventManager')
            ->setMethods(['trigger'])
            ->getMock();

        $pami->expects(static::once())
            ->method('close');

        $eventResults = static::getMock('Zend\\EventManager\\ResponseCollection', ['stopped'], [], '', false);
        $eventResults->expects(static::once())->method('stopped')->willReturn(false);

        /* @var \PAMI\Client\Impl\ClientImpl $pami */

        $client = new Client('host', $pami, $eventManager);

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
        $pami = static::getMockBuilder('PAMI\\Client\\Impl\\ClientImpl')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $eventManager = static::getMockBuilder('Zend\\EventManager\\EventManager')
            ->setMethods(['trigger'])
            ->getMock();

        $eventResults = static::getMock('Zend\\EventManager\\ResponseCollection', ['stopped'], [], '', false);
        $eventResults->expects(static::once())->method('stopped')->willReturn(true);

        /* @var \PAMI\Client\Impl\ClientImpl $pami */

        $client = new Client('host', $pami, $eventManager);

        $eventManager->expects(static::once())
            ->method('trigger')
            ->with('disconnect.pre', $client)
            ->willReturn($eventResults);

        $result = $client->disconnect();
        static::assertSame($client, $result);
    }

    public function testProcess()
    {
        $pami = static::getMockBuilder('PAMI\\Client\\Impl\\ClientImpl')
            ->disableOriginalConstructor()
            ->setMethods(['process'])
            ->getMock();

        $eventManager = static::getMockBuilder('Zend\\EventManager\\EventManager')
            ->setMethods(['trigger', 'stopped'])
            ->getMock();

        $pami->expects(static::once())
            ->method('process');

        $eventResults = static::getMock('Zend\\EventManager\\ResponseCollection', ['stopped'], [], '', false);
        $eventResults->expects(static::once())->method('stopped')->willReturn(false);

        /* @var \PAMI\Client\Impl\ClientImpl $pami */

        $client = new Client('host', $pami, $eventManager);

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
        $pami = static::getMockBuilder('PAMI\\Client\\Impl\\ClientImpl')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $eventManager = static::getMockBuilder('Zend\\EventManager\\EventManager')
            ->setMethods(['trigger'])
            ->getMock();

        $eventResults = static::getMock('Zend\\EventManager\\ResponseCollection', ['stopped'], [], '', false);
        $eventResults->expects(static::once())->method('stopped')->willReturn(true);

        /* @var \PAMI\Client\Impl\ClientImpl $pami */

        $client = new Client('host', $pami, $eventManager);

        $eventManager->expects(static::once())
            ->method('trigger')
            ->with('process.pre', $client)
            ->willReturn($eventResults);

        $result = $client->process();
        static::assertSame($client, $result);
    }

    public function testSendAction()
    {
        $pami = static::getMockBuilder('PAMI\\Client\\Impl\\ClientImpl')
            ->disableOriginalConstructor()
            ->setMethods(['send'])
            ->getMock();

        $eventManager = static::getMockBuilder('Zend\\EventManager\\EventManager')
            ->setMethods(['trigger'])
            ->getMock();

        $action = static::getMockBuilder('PAMI\\Message\\OutgoingMessage')
            ->disableOriginalConstructor()
            ->getMock();

        $pami->expects(static::once())
            ->method('send')
            ->with($action)
            ->willReturn('foo');

        $eventResults = static::getMock('Zend\\EventManager\\ResponseCollection', ['stopped'], [], '', false);
        $eventResults->expects(static::once())->method('stopped')->willReturn(false);

        /* @var \PAMI\Client\Impl\ClientImpl $pami */

        $client = new Client('host', $pami, $eventManager);

        $eventManager->expects(static::exactly(2))
            ->method('trigger')
            ->withConsecutive(
                ['sendAction.pre', $client, static::isInstanceOf('Zend\\Stdlib\\ArrayObject')],
                ['sendAction.post', $client, static::isInstanceOf('Zend\\Stdlib\\ArrayObject')]
            )
        ->willReturn($eventResults);

        $result = $client->sendAction($action);
        static::assertEquals('foo', $result);
    }

    public function testSendActionStopped()
    {
        $pami = static::getMockBuilder('PAMI\\Client\\Impl\\ClientImpl')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $eventManager = static::getMockBuilder('Zend\\EventManager\\EventManager')
            ->setMethods(['trigger'])
            ->getMock();

        $action = static::getMockBuilder('PAMI\\Message\\OutgoingMessage')
            ->disableOriginalConstructor()
            ->getMock();

        $response = static::getMock('PAMI\\Message\\Response\\ResponseMessage', [], [], '', false);

        $eventResults = static::getMock('Zend\\EventManager\\ResponseCollection', ['stopped', 'last'], [], '', false);
        $eventResults->expects(static::once())->method('stopped')->willReturn(true);
        $eventResults->expects(static::once())->method('last')->willReturn($response);

        /* @var \PAMI\Client\Impl\ClientImpl $pami */

        $client = new Client('host', $pami, $eventManager);

        $eventManager->expects(static::once())
            ->method('trigger')
            ->with(
                'sendAction.pre',
                $client,
                static::isInstanceOf('Zend\\Stdlib\\ArrayObject'),
                static::callback(
                    function ($callback) use ($response) {
                        static::assertFalse($callback(null));
                        static::assertFalse($callback('string'));
                        static::assertFalse($callback([]));
                        static::assertTrue($callback($response));

                        return true;
                    }
                )
            )
            ->willReturn($eventResults);

        $result = $client->sendAction($action);
        static::assertSame($response, $result);
    }
}
