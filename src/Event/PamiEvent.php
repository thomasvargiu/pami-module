<?php

namespace PamiModule\Event;

use PAMI\Message\Event\EventMessage;
use Zend\EventManager\Event;

/**
 * Class PamiEvent.
 */
class PamiEvent extends Event
{
    /**
     * Get the PAMI event.
     *
     * @return EventMessage
     */
    public function getEvent()
    {
        return $this->getParam('event');
    }

    /**
     * Set the PAMI event.
     *
     * @param EventMessage $event PAMI event
     *
     * @return $this
     */
    public function setEvent(EventMessage $event)
    {
        $this->setParam('event', $event);

        return $this;
    }
}
