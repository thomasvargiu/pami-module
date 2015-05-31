<?php

namespace PamiModule\Service;

use PAMI\Client\Impl\ClientImpl;
use PAMI\Message\IncomingMessage;
use PamiModule\PamiEvent;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerAwareTrait;

class Client implements EventManagerAwareInterface
{
    use EventManagerAwareTrait;

    /**
     * @var ClientImpl
     */
    protected $connection;
    /**
     * @var string
     */
    protected $pamiListenerId;
    /**
     * @var array
     */
    protected $params;

    /**
     * Client constructor.
     *
     * @param ClientImpl $pami
     */
    public function __construct(ClientImpl $pami)
    {
        $this->connection = $pami;
    }

    /**
     * @return ClientImpl
     */
    public function getConnection()
    {
        return $this->connection;
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

    /**
     * Attach the EventManager trigger to PAMI event listener.
     */
    protected function attachDefaultListeners()
    {
        if ($this->pamiListenerId) {
            $this->getConnection()->unregisterEventListener($this->pamiListenerId);
        }
        $this->pamiListenerId = $this->getConnection()->registerEventListener([$this, 'onConnectionEvent']);
    }

    /**
     * Forward PAMI event to EventManager.
     *
     * @param IncomingMessage $e
     */
    protected function onConnectionEvent(IncomingMessage $e)
    {
        $className = get_class($e);
        $eventName = 'unknown';
        if (0 === strpos($className, 'PAMI\\Message\\Event\\') && substr($className, -5) === 'Event') {
            $exploded = explode('\\', $className);
            $className = array_pop($exploded);
            $eventName = substr($className, 0, -5);
        }
        $event = new PamiEvent();
        $event->setName($eventName);
        $event->setTarget($this);
        $event->setEvent($e);
        $this->getEventManager()->trigger($event);
    }
}
