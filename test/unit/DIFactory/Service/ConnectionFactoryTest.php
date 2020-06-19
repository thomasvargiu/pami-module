<?php

declare(strict_types=1);

namespace PamiModuleTest\DIFactory\Service;

use PamiModule\DIFactory\Service\ConnectionFactory;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use function serialize;
use function unserialize;

class ConnectionFactoryTest extends TestCase
{
    use ProphecyTrait;

    private function getVar(object $obj, string $prop)
    {
        $reflection = new ReflectionClass($obj);
        $property = $reflection->getProperty($prop);
        $property->setAccessible(true);

        return $property->getValue($obj);
    }

    public function testIsSerializable(): void
    {
        $factory = new ConnectionFactory();
        $result = unserialize(serialize($factory));

        $this->assertEquals($factory, $result);
    }

    public function testCreateService(): void
    {
        $config = [
            'pami' => [
                'connection' => [
                    'foo' => [
                        'host' => 'foo.host',
                        'scheme' => 'tcp2://',
                        'port' => 999,
                        'username' => 'admin',
                        'secret' => 'secret',
                        'connect_timeout' => 30,
                        'read_timeout' => 3000,
                        'event_mask' => [],
                    ],
                ],
            ],
        ];

        $container = $this->prophesize(ContainerInterface::class);
        $container->get('config')->willReturn($config);

        $factory = new ConnectionFactory('foo');
        $service = $factory($container->reveal());

        $this->assertSame('foo.host', $this->getVar($service, 'host'));
        $this->assertSame('tcp2://', $this->getVar($service, 'scheme'));
        $this->assertSame(999, $this->getVar($service, 'port'));
        $this->assertSame('admin', $this->getVar($service, 'user'));
        $this->assertSame('secret', $this->getVar($service, 'pass'));
        $this->assertSame(30, $this->getVar($service, 'cTimeout'));
        $this->assertSame(3000, $this->getVar($service, 'rTimeout'));
        $this->assertSame('off', $this->getVar($service, 'eventMask'));
    }

    public function testCreateServiceWithEventMaskArray(): void
    {
        $config = [
            'pami' => [
                'connection' => [
                    'foo' => [
                        'event_mask' => [
                            'foo',
                            'bar',
                        ],
                    ],
                ],
            ],
        ];

        $container = $this->prophesize(ContainerInterface::class);
        $container->get('config')->willReturn($config);

        $factory = new ConnectionFactory('foo');
        $service = $factory($container->reveal());

        $this->assertSame('foo, bar', $this->getVar($service, 'eventMask'));
    }

    public function testCreateServiceWithEventMaskString(): void
    {
        $config = [
            'pami' => [
                'connection' => [
                    'foo' => [
                        'event_mask' => 'foo, bar',
                    ],
                ],
            ],
        ];

        $container = $this->prophesize(ContainerInterface::class);
        $container->get('config')->willReturn($config);

        $factory = new ConnectionFactory('foo');
        $service = $factory($container->reveal());

        $this->assertSame('foo, bar', $this->getVar($service, 'eventMask'));
    }
}
