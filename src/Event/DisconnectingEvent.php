<?php

declare(strict_types=1);

namespace PamiModule\Event;

use PamiModule\Service\ClientInterface;
use Psr\EventDispatcher\StoppableEventInterface;

class DisconnectingEvent implements StoppableEventInterface
{
    private ClientInterface $client;

    private bool $stopped = false;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    public function getClient(): ClientInterface
    {
        return $this->client;
    }

    public function isPropagationStopped(): bool
    {
        return $this->stopped;
    }

    public function setPropagationStopped(): void
    {
        $this->stopped = true;
    }
}
