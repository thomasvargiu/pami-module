<?php

declare(strict_types=1);

namespace PamiModuleTest\DIFactory\Service;

use PAMI\Client\IClient;
use PamiModule\DIFactory\Service\ClientFactory;
use PamiModule\Service\Client;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use function serialize;
use function unserialize;

class ClientFactoryTest extends TestCase
{
    use ProphecyTrait;

    public function testIsSerializable(): void
    {
        $factory = new ClientFactory();
        $result = unserialize(serialize($factory));

        $this->assertEquals($factory, $result);
    }

    public function testShouldCreateClient(): void
    {
        $config = [
            'pami' => [
                'client' => [
                ],
            ],
        ];

        $eventDispatcher = $this->prophesize(EventDispatcherInterface::class);
        $connection = $this->prophesize(IClient::class);
        $container = $this->prophesize(ContainerInterface::class);
        $container->get('config')->willReturn($config);
        $container->has(EventDispatcherInterface::class)
            ->willReturn(true);
        $container->get(EventDispatcherInterface::class)
            ->shouldBeCalled()
            ->willReturn($eventDispatcher->reveal());
        $container->has(IClient::class)
            ->willReturn(true);
        $container->get(IClient::class)
            ->shouldBeCalled()
            ->willReturn($connection->reveal());

        $factory = new ClientFactory('foo');

        $service = $factory($container->reveal());

        $this->assertInstanceOf(Client::class, $service);
    }

    public function testShouldCreateClientWithCustomDependencies(): void
    {
        $config = [
            'pami' => [
                'client' => [
                    'foo' => [
                        'connection' => 'PamiConnectionName',
                        'event_dispatcher' => 'EventDispatcherName'
                    ],
                ],
            ],
        ];

        $eventDispatcher = $this->prophesize(EventDispatcherInterface::class);
        $connection = $this->prophesize(IClient::class);
        $container = $this->prophesize(ContainerInterface::class);
        $container->get('config')->willReturn($config);
        $container->has('EventDispatcherName')
            ->willReturn(true);
        $container->get('EventDispatcherName')
            ->shouldBeCalled()
            ->willReturn($eventDispatcher->reveal());
        $container->has('PamiConnectionName')
            ->willReturn(true);
        $container->get('PamiConnectionName')
            ->shouldBeCalled()
            ->willReturn($connection->reveal());

        $factory = new ClientFactory('foo');

        $service = $factory($container->reveal());

        $this->assertInstanceOf(Client::class, $service);
    }

    public function testShouldThrowExceptionOnConnectionNotFound(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Unable to find Pami Client service');

        $config = [
            'pami' => [
                'client' => [
                    'foo' => [
                        'connection' => 'PamiConnectionName',
                    ],
                ],
            ],
        ];

        $container = $this->prophesize(ContainerInterface::class);
        $container->get('config')->willReturn($config);
        $container->has('PamiConnectionName')
            ->willReturn(false);

        $factory = new ClientFactory('foo');

        $factory($container->reveal());
    }

    public function testShouldThrowExceptionOnEventDispatcherNotFound(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Unable to find an event dispatcher service');

        $config = [
            'pami' => [
                'client' => [
                    'foo' => [
                        'event_dispatcher' => 'EventDispatcher',
                    ],
                ],
            ],
        ];

        $connection = $this->prophesize(IClient::class);
        $container = $this->prophesize(ContainerInterface::class);
        $container->get('config')->willReturn($config);
        $container->has('EventDispatcher')
            ->willReturn(false);
        $container->has(IClient::class)
            ->willReturn(true);
        $container->get(IClient::class)
            ->shouldBeCalled()
            ->willReturn($connection->reveal());

        $factory = new ClientFactory('foo');

        $factory($container->reveal());
    }
}
