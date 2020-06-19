<?php

declare(strict_types=1);

namespace PamiModule\Event;

use PAMI\Message\Event\EventMessage;
use PamiModule\Service\ClientInterface;

final class PamiEvent
{
    private ClientInterface $client;
    private EventMessage $event;

    public function __construct(ClientInterface $client, EventMessage $event)
    {
        $this->client = $client;
        $this->event = $event;
    }

    public function getClient(): ClientInterface
    {
        return $this->client;
    }

    /**
     * Get the PAMI event.
     *
     * @return EventMessage
     */
    public function getEvent(): EventMessage
    {
        return $this->event;
    }

    public function getEventName(): string
    {
        return $this->event->getName();
    }
}
