<?php

declare(strict_types=1);

namespace PamiModuleTest\Service;

use PAMI\Message\Event\EventMessage;
use PamiModule\Event\PamiEvent;
use PamiModule\Service\ClientInterface;
use PamiModule\Service\EventForwarder;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\EventDispatcher\EventDispatcherInterface;

class EventForwarderTest extends TestCase
{
    use ProphecyTrait;

    public function testShouldDispatchEvent(): void
    {
        $client = $this->prophesize(ClientInterface::class);
        $eventDispatcher = $this->prophesize(EventDispatcherInterface::class);
        $event = $this->prophesize(EventMessage::class);

        $eventDispatcher->dispatch(Argument::allOf(
            Argument::type(PamiEvent::class),
            Argument::that(fn (PamiEvent $e) => $e->getEvent() === $event->reveal())
        ))
            ->shouldBeCalled();

        $eventForwarder = new EventForwarder($client->reveal(), $eventDispatcher->reveal());
        $eventForwarder->handle($event->reveal());
    }
}
