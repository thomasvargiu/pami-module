<?php

namespace PamiModuleTest\Listener;

use PAMI\Message\Response\ResponseMessage;
use PamiModule\Listener\CacheListener;
use Zend\Cache\Storage\Adapter\Memory;
use Zend\EventManager\Event;

class CacheListenerTest extends \PHPUnit_Framework_TestCase
{
    public function testAttach()
    {
        $events = $this->getMock('Zend\\EventManager\\EventManagerInterface');
        $cache = $this->getMock('Zend\\Cache\\Storage\\StorageInterface');

        $events->expects(static::exactly(2))
            ->method('attach')
            ->withConsecutive(
                ['sendAction.pre'],
                ['sendAction.post']
            );

        $listener = new CacheListener($cache, ['Foo']);
        $listener->attach($events);
        static::assertSame($cache, $listener->getCache());
        static::assertEquals(['foo'], $listener->getCacheableActions());
    }

    public function testCacheAction()
    {
        $actionsToCache = ['Foo'];
        $client = $this->getMock('PamiModule\\Service\\Client', ['getHost'], [], '', false);
        $client->expects(static::atLeast(1))->method('getHost')->willReturn('foo.com');

        $action = $this->getMock('PAMI\\Message\\OutgoingMessage', [], [], '', false);
        $action->expects(static::any())->method('getKey')->with('Action')->willReturn('Foo');
        $action->expects(static::any())->method('getKeys')->willReturn(['foo' => 'bar', 'bar' => 'foo']);

        $cache = new Memory();

        $listener = new CacheListener($cache, $actionsToCache);

        $event = new Event('sendAction.pre', $client, ['action' => $action]);
        $ret = $listener->onSendPre($event);
        static::assertNull($ret);

        $response = new ResponseMessage("Event: Response\r\nResponse: Success\r\nFoo: Bar");
        $event = new Event('sendAction.pre', $client, ['action' => $action, 'response' => $response]);
        $listener->onSendPost($event);

        // Requesting again, asserting the response is the previous cached
        $ret = $listener->onSendPre($event);
        static::assertSame($response, $ret);
    }

    public function testNotCacheableAction()
    {
        $actionsToCache = [];
        $client = $this->getMock('PamiModule\\Service\\Client', ['getHost'], [], '', false);

        $action = $this->getMock('PAMI\\Message\\OutgoingMessage', [], [], '', false);
        $action->expects(static::any())->method('getKey')->with('Action')->willReturn('Bar');
        $action->expects(static::any())->method('getKeys')->willReturn(['foo' => 'bar', 'bar' => 'foo']);

        $cache = new Memory();

        $listener = new CacheListener($cache, $actionsToCache);

        $event = new Event('sendAction.pre', $client, ['action' => $action]);
        $ret = $listener->onSendPre($event);
        static::assertNull($ret);

        $response = new ResponseMessage("Event: Response\r\nResponse: Success\r\nFoo: Bar");
        $event = new Event('sendAction.pre', $client, ['action' => $action, 'response' => $response]);
        $listener->onSendPost($event);

        // Requesting again, asserting the response is the previous cached
        $ret = $listener->onSendPre($event);
        static::assertNull($ret);
    }
}
