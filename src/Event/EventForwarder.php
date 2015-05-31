<?php

namespace PamiModule\Event;

use PAMI\Listener\IEventListener;
use PAMI\Message\Event\EventMessage;
use PamiModule\Service\Client;

class EventForwarder implements IEventListener
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * EventForwarder constructor.
     *
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Forward PAMI event to EventManager.
     *
     * @param EventMessage $e
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
