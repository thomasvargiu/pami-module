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

    public function testConnectAction()
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

        /* @var \PAMI\Client\Impl\ClientImpl $pami */

        $client = new Client('host', $pami, $eventManager);

        $eventManager->expects(static::exactly(2))
            ->method('trigger')
            ->withConsecutive(
                ['connect.pre', $client],
                ['connect.post', $client]
            );

        $result = $client->connect();
        static::assertSame($client, $result);
    }

    public function testDisconnectAction()
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

        /* @var \PAMI\Client\Impl\ClientImpl $pami */

        $client = new Client('host', $pami, $eventManager);

        $eventManager->expects(static::exactly(2))
            ->method('trigger')
            ->withConsecutive(
                ['disconnect.pre', $client],
                ['disconnect.post', $client]
            );

        $result = $client->disconnect();
        static::assertSame($client, $result);
    }

    public function testProcessAction()
    {
        $pami = static::getMockBuilder('PAMI\\Client\\Impl\\ClientImpl')
            ->disableOriginalConstructor()
            ->setMethods(['process'])
            ->getMock();

        $eventManager = static::getMockBuilder('Zend\\EventManager\\EventManager')
            ->setMethods(['trigger'])
            ->getMock();

        $pami->expects(static::once())
            ->method('process');

        /* @var \PAMI\Client\Impl\ClientImpl $pami */

        $client = new Client('host', $pami, $eventManager);

        $eventManager->expects(static::exactly(2))
            ->method('trigger')
            ->withConsecutive(
                ['process.pre', $client],
                ['process.post', $client]
            );

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

        /* @var \PAMI\Client\Impl\ClientImpl $pami */

        $client = new Client('host', $pami, $eventManager);

        $eventManager->expects(static::exactly(2))
            ->method('trigger')
            ->withConsecutive(
                ['sendAction.pre', $client, static::isInstanceOf('Zend\\Stdlib\\ArrayObject')],
                ['sendAction.post', $client, static::isInstanceOf('Zend\\Stdlib\\ArrayObject')]
            );

        $result = $client->sendAction($action);
        static::assertEquals('foo', $result);
    }
}
