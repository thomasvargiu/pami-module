<?php

namespace PamiModule\Log;

use Zend\Log\Logger;

class LogAppenderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider logEventProvider
     */
    public function testAppend($levelInt, $zendLogInt)
    {
        $logger = static::getMockBuilder('Zend\\Log\\Logger')
            ->setMethods(['log'])
            ->getMock();

        $loggerLevel = static::getMockBuilder('LoggerLevel')
            ->disableOriginalConstructor()
            ->setMethods(['toInt'])
            ->getMock();

        $event = static::getMockBuilder('LoggerLoggingEvent')
            ->disableOriginalConstructor()
            ->setMethods(['getLevel', 'getMessage'])
            ->getMock();

        $logger->expects(static::once())
            ->method('log')
            ->with($zendLogInt, 'foo');

        $loggerLevel->expects(static::atLeast(1))
            ->method('toInt')
            ->willReturn($levelInt);

        $event->expects(static::atLeast(1))
            ->method('getLevel')
            ->willReturn($loggerLevel);

        $event->expects(static::atLeast(1))
            ->method('getMessage')
            ->willReturn('foo');

        /* @var Logger $logger */
        /* @var \LoggerLoggingEvent $event */

        $appender = new LogAppender();
        $appender->setZendLog($logger);

        $appender->append($event);
    }

    public function logEventProvider()
    {
        return [
            [\LoggerLevel::FATAL, Logger::CRIT],
            [\LoggerLevel::ERROR, Logger::ERR],
            [\LoggerLevel::WARN, Logger::WARN],
            [\LoggerLevel::INFO, Logger::INFO],
            [\LoggerLevel::DEBUG, Logger::DEBUG],
            [\LoggerLevel::TRACE, Logger::DEBUG],
        ];
    }
}
