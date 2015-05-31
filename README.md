# PamiModule

A ZF2 module for [PAMI](https://github.com/marcelog/PAMI) library.

[![Build Status](https://travis-ci.org/thomasvargiu/pami-module.svg?branch=master)](https://travis-ci.org/thomasvargiu/pami-module)
[![Code Coverage](https://scrutinizer-ci.com/g/thomasvargiu/pami-module/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/thomasvargiu/pami-module/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/thomasvargiu/pami-module/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/thomasvargiu/pami-module/?branch=master)
[![Dependency Status](https://www.versioneye.com/user/projects/556a836563653200265f1600/badge.svg?style=flat)](https://www.versioneye.com/user/projects/556a836563653200265f1600)
[![Latest Stable Version](https://poser.pugx.org/thomasvargiu/pami-module/v/stable)](https://packagist.org/packages/thomasvargiu/pami-module)
[![Total Downloads](https://poser.pugx.org/thomasvargiu/pami-module/downloads)](https://packagist.org/packages/thomasvargiu/pami-module)
[![Latest Unstable Version](https://poser.pugx.org/thomasvargiu/pami-module/v/unstable)](https://packagist.org/packages/thomasvargiu/pami-module)
[![License](https://poser.pugx.org/thomasvargiu/pami-module/license)](https://packagist.org/packages/thomasvargiu/pami-module)

## Configuration

```php
return [
    'pami_module' => [
        'connection' => [
            'default' => [
                'host' => '', // IP or hostname of asterisk server
                'port' => 5038, // (optional) Asterisk AMI port (default: 5038)
                'username' => '', // Username for asterisk AMI
                'secret' => '', // Password for asterisk AMI
                'scheme' => 'tcp://', // (optional) Connection scheme (default: tcp://)
                'connect_timeout' => 10000, // (optional) Connection timeout in ms (default: 10000)
                'read_timeout' => 10000 // (optional) Read timeout in ms (default: 10000)
            ]
        ],
    ]
]
```

Then you can retrieve two services from the service locator:
- ```pami.connection.default```: PAMI original client
- ```pami.client.default```: PamiModule client


## PamiModule Client

The original ```Pami``` client (the connection) is injected into the ```PamiModule``` client as connection, and you can access to it:

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

Then you can call any method directly from the connection.


## EventManager

The ```PamiModule``` client has an ```EventManager``` instance injected.  
All PAMI events are forwarded to this event manager that will trigger an event (```PamiModule\Event\PamiEvent```).  
The name of the event will be ```event.<name>``` (example: ```event.ExtensionStatus```).  
Of course, you can acces to the original event to retrieve event data (see example below).  
The event target is the ```PamiModule``` client.  

Example:
```php

use PamiModule\Service\Client;
use PamiModule\Event\PamiEvent;

/** @var Client $client */
$client = $serviceLocator->get('pami.client.default');
$client->getEventManager()->attach('event.Bridge', function(PamiEvent $event) {
    // Getting the client
    /** @var Client $client */
    $client = $event->getTarget();
    
    // Getting the original Event
    /** @var \PAMI\Message\Event\BridgeEvent $pamiEvent */
    $pamiEvent = $event->getEvent();
});
```


## Multiple client

```php
return [
    'pami_module' => [
        'connection' => [
            'default' => [
                // configuration
            ],
            'asterisk2' => [
                // configuration
            ]
        ],
    ]
]
```

You can retrieve connections and clients:
- ```pami.connection.default```
- ```pami.client.default```
- ```pami.connection.asterisk2```
- ```pami.client.asterisk2```


### Multiple client sharing the same connection

You can create another client with the same connection of another one:

```php
return [
    'pami_module' => [
        'connection' => [
            'default' => [
                // configuration
            ]
        ],
        'client' => [
            'default' => [
                'connection' => 'default'
            ],
            'client2' => [
                'connection' => 'default'
            ]
        ]
    ]
]
```

```php
$client1 = $serviceLocator->get('pami.client.default');
$client2 = $serviceLocator->get('pami.client.client2');

$client1->getConnection() === $client2->getConnection(); // true
```

### Passing custom data to clients

Sometimes you need to have some information about the client or connection (sometimes you need them in listeners).

```php
return [
    'pami_module' => [
        // ...
        'client' => [
            'default' => [
                'params' => [
                    'host' => 'host.domain.com'
                ]
            ],
        ]
    ]
]
```

```php
$client = $serviceLocator->get('pami.client.default');
$params = $client->getParams(); // ['host' => 'host.domain.com'] 
```
