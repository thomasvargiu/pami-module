<?php

namespace PamiModule\Event;

use PAMI\Listener\IEventListener;
use PAMI\Message\Event\EventMessage;
use PamiModule\Service\Client;

class EventForwarder implements IEventListener
{
    /**
     * PamiModule client.
     *
     * @var Client
     */
    protected $client;

    /**
     * EventForwarder constructor.
     *
     * @param Client $client PamiModule client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Forward PAMI event to EventManager.
     *
     * @param EventMessage $e PAMI event
     */
    public function handle(EventMessage $e)
    {
        $eventPrefix = 'event.';
        $eventName = $eventPrefix.$e->getName();
        $event = new PamiEvent();
        $event->setName($eventName);
        $event->setTarget($this->client);
        $event->setEvent($e);
        $this->client->getEventManager()->trigger($event);
    }
}
