<?php

namespace PamiModule\Service;

use PAMI\Client\Impl\ClientImpl;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventsCapableInterface;

class Client implements EventsCapableInterface
{
    /**
     * PAMI client.
     *
     * @var ClientImpl
     */
    protected $connection;
    /**
     * Custom parameters.
     *
     * @var array
     */
    protected $params = [];
    /**
     * Event manager.
     *
     * @var EventManagerInterface
     */
    protected $eventManager;

    /**
     * Client constructor.
     *
     * @param ClientImpl            $pami         PAMI client
     * @param EventManagerInterface $eventManager EventManager
     */
    public function __construct(
        ClientImpl $pami,
        EventManagerInterface $eventManager
    ) {
        $this->connection = $pami;
        $this->eventManager = $eventManager;
    }

    /**
     * Return the PAMI client.
     *
     * @return ClientImpl
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Return the EventManager.
     *
     * @return EventManagerInterface
     */
    public function getEventManager()
    {
        return $this->eventManager;
    }

    /**
     * Return the custom parameters.
     *
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Set the custom parameters.
     *
     * @param array $params Parameters
     *
     * @return $this
     */
    public function setParams(array $params)
    {
        $this->params = $params;

        return $this;
    }
}
