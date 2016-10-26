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
        $events = $this->getMockBuilder('Zend\\EventManager\\EventManagerInterface')->getMock();
        $cache = $this->getMockBuilder('Zend\\Cache\\Storage\\StorageInterface')->getMock();

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
        $client = $this->getMockBuilder('PamiModule\\Service\\Client')
            ->setMethods(['getHost'])
            ->disableOriginalConstructor()
            ->getMock();
        $client->expects(static::atLeast(1))->method('getHost')->willReturn('foo.com');

        $action = $this->getMockBuilder('PAMI\\Message\\OutgoingMessage')
            ->disableOriginalConstructor()
            ->getMock();
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
        $client = $this->getMockBuilder('PamiModule\\Service\\Client')
            ->disableOriginalConstructor()
            ->setMethods(['getHost'])
            ->getMock();

        $action = $this->getMockBuilder('PAMI\\Message\\OutgoingMessage')
            ->disableOriginalConstructor()
            ->getMock();
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
