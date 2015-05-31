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

        $client = new Client($pami, $eventManager);
        static::assertSame($pami, $client->getConnection());

        // Test attach EventManager

        $eventManager = $client->getEventManager();
        static::assertSame($eventManager, $client->getEventManager());

        $params = ['foo' => 'bar'];
        $client->setParams($params);
        static::assertEquals($params, $client->getParams());
    }
}
