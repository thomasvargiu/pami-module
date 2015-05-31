<?php

namespace PamiModule\Log;

use LoggerLoggingEvent;
use LoggerLevel;
use Zend\Log\Logger;

class LogAppender extends \LoggerAppender
{
    /**
     * @var Logger
     */
    protected $zendLog;

    protected $requiresLayout = false;

    /**
     * @return Logger
     */
    public function getZendLog()
    {
        return $this->zendLog;
    }

    /**
     * @param Logger $zendLog
     *
     * @return $this
     */
    public function setZendLog(Logger $zendLog)
    {
        $this->zendLog = $zendLog;

        return $this;
    }

    /**
     * Forwards the logging event to the destination.
     *
     * Derived appenders should implement this method to perform actual logging.
     *
     * @param LoggerLoggingEvent $event
     */
    public function append(LoggerLoggingEvent $event)
    {
        $level = $event->getLevel();

        $priority = Logger::DEBUG;

        $map = [
            LoggerLevel::FATAL => Logger::CRIT,
            LoggerLevel::ERROR => Logger::ERR,
            LoggerLevel::WARN => Logger::WARN,
            LoggerLevel::INFO => Logger::INFO,
            LoggerLevel::DEBUG => Logger::DEBUG,
            LoggerLevel::TRACE => Logger::DEBUG,
        ];

        foreach ($map as $value => $zendValue) {
            if ($level->toInt() >= $value) {
                $priority = $zendValue;
                break;
            }
        }

        $message = $event->getMessage();
        $this->getZendLog()->log($priority, $message);
    }
}
