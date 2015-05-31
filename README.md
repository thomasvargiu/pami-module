# PamiModule

[![Build Status](https://travis-ci.org/thomasvargiu/pami-module.svg?branch=master)](https://travis-ci.org/thomasvargiu/pami-module)
[![Code Coverage](https://scrutinizer-ci.com/g/thomasvargiu/pami-module/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/thomasvargiu/pami-module/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/thomasvargiu/pami-module/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/thomasvargiu/pami-module/?branch=master)
[![Dependency Status](https://www.versioneye.com/user/projects/556a836563653200265f1600/badge.svg?style=flat)](https://www.versioneye.com/user/projects/556a836563653200265f1600)


A ZF2 implementation for [PAMI](https://github.com/marcelog/PAMI) library.

Status: *development*
Note: *10% developed as drunk*

## Configuration

```php
return [
    'pami_module' => [
        'connection' => [
            'default' => [
                'host' => 'asterisk.domain.com', // IP or hostname of asterisk server
                'port' => 5038, // (optional) Asterisk AMI port
                'username' => 'admin', // Username for asterisk AMI
                'secret' => 'secret', // Password for asterisk AMI
                'scheme' => 'tcp://', // (optional) Connection scheme (default: tcp://)
                'connect_timeout' => 10000, // (optional) Connection timeout in ms (default: 10000)
                'read_timeout' => 10000 // (optional) Read timeout in ms (default: 10000)
            ]
        ],
        'client' => [
            'default' => [
                'connection' => 'default',
            ]
        ]
    ]
]
```

Then you can access to two services:

- ```pami.connection.default```: PAMI original client
- ```pami.client.default```: PamiModule client

## PamiModule Client

The original ```Pami``` client is injected into the ```PamiModule``` client as connection, and you can access to it:

```php
use PamiModule\Service\Client;
use PAMI\Client\Impl\ClientImpl;

// Getting the PamiModule client
/** @var Client $client */
$client = $serviceLocator->get('pami.client.default');
// Getting the PAMI client
/** @var ClientImpl $connection */
$connection = $client->getConnection();
```

Then you can call any method directly from the connection;

## EventManager

The ```PamiModule``` client has an ```EventManager``` creating only when you try to get it.
When the ```EventManager``` is created, it's attached to the event listener of original PAMI module injected into the client.
The events are instances of ```PamiModule\\PamiEvent``` and the name of the event is the class name of original PAMI event without its (```PAMI\Message\Event```) prefix and ```Event``` suffix.
So, if the original event is an instance of ```PAMI\Message\Event\BridgeEvent```, the name of the event in ```EventManager``` will be ```Bridge```.
Of course, you can acces to the original event to retrieve event data.
The event target is the ```PamiModule``` client.

Example:
```php

use PamiModule\Service\Client;
use PamiModule\PamiEvent;

/** @var Client $client */
$client = $serviceLocator->get('pami.client.default');
$client->getEventManager()->attach('Bridge', function(PamiEvent $event) {
    // Getting the client
    /** @var Client $client */
    $client = $event->getTarget();
    
    // Getting the original Event
    /** @var \PAMI\Message\Event\BridgeEvent $pamiEvent */
    $pamiEvent = $event->getEvent();
});
```
