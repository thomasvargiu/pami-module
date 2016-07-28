<?php

namespace PamiModule\Log;

use LoggerLoggingEvent;
use LoggerLevel;
use Zend\Log\Logger;

/**
 * Class LogAppender
 * This class enables use of Zend\Log
 *
 * @package PamiModule\Log
 */
class LogAppender extends \LoggerAppender
{
    /**
     * ZF logger.
     *
     * @var Logger
     */
    protected $zendLog;

    /**
     * Tell that the log appender doesn't require a layout.
     *
     * @var bool
     */
    protected $requiresLayout = false;

    /**
     * Return the ZF logger.
     *
     * @return Logger
     */
    public function getZendLog()
    {
        return $this->zendLog;
    }

    /**
     * Set the ZF logger.
     *
     * @param Logger $zendLog Logger
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
     * @param LoggerLoggingEvent $event Logger event
     * @throws \Zend\Log\Exception\InvalidArgumentException
     * @throws \Zend\Log\Exception\RuntimeException
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
