<?php

namespace PamiModule\Service;

use PAMI\Client\Impl\ClientImpl;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventsCapableInterface;

class Client implements EventsCapableInterface
{
    /**
     * @var ClientImpl
     */
    protected $connection;
    /**
     * @var array
     */
    protected $params = [];
    /**
     * @var EventManagerInterface
     */
    protected $eventManager;

    /**
     * Client constructor.
     *
     * @param ClientImpl            $pami
     * @param EventManagerInterface $eventManager
     */
    public function __construct(ClientImpl $pami, EventManagerInterface $eventManager)
    {
        $this->connection = $pami;
        $this->eventManager = $eventManager;
    }

    /**
     * @return ClientImpl
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @return EventManagerInterface
     */
    public function getEventManager()
    {
        return $this->eventManager;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param array $params
     *
     * @return $this
     */
    public function setParams(array $params)
    {
        $this->params = $params;

        return $this;
    }
}
