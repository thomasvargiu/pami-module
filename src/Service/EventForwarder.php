<?php

declare(strict_types=1);

namespace PamiModule\Service;

use PAMI\Listener\IEventListener;
use PAMI\Message\Event\EventMessage;
use PamiModule\Event\PamiEvent;
use Psr\EventDispatcher\EventDispatcherInterface;

final class EventForwarder implements IEventListener
{
    private ClientInterface $client;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(ClientInterface $client, EventDispatcherInterface $eventDispatcher)
    {
        $this->client = $client;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function handle(EventMessage $event): void
    {
        $this->eventDispatcher->dispatch(new PamiEvent($this->client, $event));
    }
}
