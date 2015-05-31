<?php

namespace PamiModule;

use Zend\EventManager\Event;
use PAMI\Message\IncomingMessage;

class PamiEvent extends Event
{
    /**
     * @return IncomingMessage
     */
    public function getEvent()
    {
        return $this->getParam('event');
    }

    /**
     * @param IncomingMessage $event
     *
     * @return $this
     */
    public function setEvent(IncomingMessage $event)
    {
        $this->setParam('event', $event);

        return $this;
    }
}
