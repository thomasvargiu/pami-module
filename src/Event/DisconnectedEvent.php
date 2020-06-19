<?php

declare(strict_types=1);

namespace PamiModule\Event;

use PamiModule\Service\ClientInterface;

class DisconnectedEvent
{
    private ClientInterface $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    public function getClient(): ClientInterface
    {
        return $this->client;
    }
}
