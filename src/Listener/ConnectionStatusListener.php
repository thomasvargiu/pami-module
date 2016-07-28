<?php

namespace PamiModule\Listener;

use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;

/**
 * Class ConnectionStatusListener.
 */
class ConnectionStatusListener implements ListenerAggregateInterface
{
    use ListenerAggregateTrait;

    /**
     * @var bool
     */
    protected $connected = false;

    /**
     * Attach one or more listeners.
     *
     * Implementors may add an optional $priority argument; the EventManager
     * implementation will pass this to the aggregate.
     *
     * @param EventManagerInterface $events
     * @param int                   $priority
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach('connect.pre', [$this, 'onConnect'], $priority);
        $this->listeners[] = $events->attach('disconnect.pre', [$this, 'onDisconnect'], $priority);
        $this->listeners[] = $events->attach('process.pre', [$this, 'onProcess'], $priority);
        $this->listeners[] = $events->attach('sendAction.pre', [$this, 'onSendAction'], $priority);
    }

    /**
     * Triggered on connect.
     *
     * @param EventInterface $event The triggered event
     */
    public function onConnect(EventInterface $event)
    {
        if ($this->connected) {
            $event->stopPropagation(true);
        }
        $this->connected = true;
    }

    /**
     * Triggered on disconnect.
     *
     * @param EventInterface $event The triggered event
     */
    public function onDisconnect(EventInterface $event)
    {
        if (!$this->connected) {
            $event->stopPropagation(true);
        } else {
            $this->connected = false;
        }
    }

    /**
     * Triggered on process.
     *
     * @param EventInterface $event The triggered event
     */
    public function onProcess(EventInterface $event)
    {
        if (!$this->connected) {
            /** @var \PamiModule\Service\Client $client */
            $client = $event->getTarget();
            $client->connect();
        }
    }

    /**
     * Triggered on sendAction.
     *
     * @param EventInterface $event The triggered event
     */
    public function onSendAction(EventInterface $event)
    {
        if (!$this->connected) {
            /** @var \PamiModule\Service\Client $client */
            $client = $event->getTarget();
            $client->connect();
        }
    }
}
