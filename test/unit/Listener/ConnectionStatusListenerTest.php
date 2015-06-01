<?php

namespace PamiModuleTest\Listener;

use PamiModule\Listener\ConnectionStatusListener;
use Zend\EventManager\Event;

class ConnectionStatusListenerTest extends \PHPUnit_Framework_TestCase
{
    public function testAttach()
    {
        $events = static::getMock('Zend\\EventManager\\EventManagerInterface');

        $events->expects(static::atLeast(4))
            ->method('attach')
            ->withConsecutive(
                ['connect.pre'],
                ['disconnect.pre'],
                ['process.pre'],
                ['sendAction.pre']
            );

        $listener = new ConnectionStatusListener();
        $listener->attach($events);
    }

    public function testOnConnect()
    {
        $listener = new ConnectionStatusListener();

        $event = new Event('connect.pre');
        $listener->onConnect($event);
        static::assertFalse($event->propagationIsStopped());

        $event = new Event('connect.pre');
        $listener->onConnect($event);
        static::assertTrue($event->propagationIsStopped());

        $event = new Event('connect.pre');
        $listener->onConnect($event);
        static::assertTrue($event->propagationIsStopped());
    }

    public function testOnDisconnect()
    {
        $listener = new ConnectionStatusListener();

        $event = new Event('disconnect.pre');
        $listener->onDisconnect($event);
        static::assertTrue($event->propagationIsStopped());

        $listener->onConnect(new Event('connect.pre'));

        $event = new Event('disconnect.pre');
        $listener->onDisconnect($event);
        static::assertFalse($event->propagationIsStopped());

        $event = new Event('disconnect.pre');
        $listener->onDisconnect($event);
        static::assertTrue($event->propagationIsStopped());
    }

    public function testOnProcess()
    {
        $client = static::getMock('PamiModule\\Service\\Client', ['connect'], [], '', false);
        $listener = new ConnectionStatusListener();

        $client->expects(static::once())
            ->method('connect');

        $event = new Event('process.pre', $client);
        $listener->onProcess($event);

        // Should receive a connect event
        $event = new Event('connect.pre');
        $listener->onConnect($event);

        $event = new Event('process.pre', $client);
        $listener->onProcess($event);
    }

    public function testOnSendAction()
    {
        $client = static::getMock('PamiModule\\Service\\Client', ['connect'], [], '', false);
        $listener = new ConnectionStatusListener();

        $client->expects(static::once())
            ->method('connect');

        $event = new Event('sendAction.pre', $client);
        $listener->onSendAction($event);

        // Should receive a connect event
        $event = new Event('connect.pre');
        $listener->onConnect($event);

        $event = new Event('sendAction.pre', $client);
        $listener->onSendAction($event);
    }
}
