PamiModule
==========

A ZF2 module for [PAMI](https://github.com/marcelog/PAMI) library.

[![Build Status](https://travis-ci.org/thomasvargiu/pami-module.svg?branch=master)](https://travis-ci.org/thomasvargiu/pami-module)
[![Code Coverage](https://scrutinizer-ci.com/g/thomasvargiu/pami-module/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/thomasvargiu/pami-module/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/thomasvargiu/pami-module/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/thomasvargiu/pami-module/?branch=master)
[![Dependency Status](https://www.versioneye.com/user/projects/556a836563653200265f1600/badge.svg?style=flat)](https://www.versioneye.com/user/projects/556a836563653200265f1600)
[![Latest Stable Version](https://poser.pugx.org/thomasvargiu/pami-module/v/stable)](https://packagist.org/packages/thomasvargiu/pami-module)
[![Total Downloads](https://poser.pugx.org/thomasvargiu/pami-module/downloads)](https://packagist.org/packages/thomasvargiu/pami-module)
[![Latest Unstable Version](https://poser.pugx.org/thomasvargiu/pami-module/v/unstable)](https://packagist.org/packages/thomasvargiu/pami-module)
[![License](https://poser.pugx.org/thomasvargiu/pami-module/license)](https://packagist.org/packages/thomasvargiu/pami-module)

Configuration
-------------

First, you should define connection and clients options in your configuration. Client options are all optional.

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
        'client' => [
            'default' => []
        ]
    ]
]
```

Then you can retrieve two services from the service locator:
- ```pami.client.default```: PamiModule client


PamiModule Client
-----------------

You can get the client from the service locator.

```php
use PamiModule\Service\Client;
use PAMI\Client\Impl\ClientImpl;

// Getting the PamiModule client
/** @var Client $client */
$client = $serviceLocator->get('pami.client.default');
```


### Methods

The original ```Pami``` client (the connection) is injected into the ```PamiModule```, and the ```PamiModule``` actions 
delegates the original client.

*Mapped Actions:*

| PamiModule         | PAMI            |
|--------------------|-----------------|
| ```connect()```    | ```open()```    |
| ```disconnect()``` | ```close()```   |
| ```sendAction()``` | ```send()```    |
| ```process()```    | ```process()``` |


### Events

The ```PamiModule``` client has an ```EventManager``` instance injected into it.  
The following methods will trigger events with the same name of the method and  ```.pre``` and ```.post``` suffix:
 
- ```connect()```
- ```disconnect()```
- ```process()```
- ```sendAction()```

The ```sendAction()``` events have ```action``` param in ```sendAction.pre``` event
and ```action``` and ```response``` params in ```sendAction.post``` event, allowing you to modify the action before it
will be dispatched or to cache responses.


PAMI events
-----------
 
All PAMI events are forwarded to the event manager that will trigger an event (```PamiModule\Event\PamiEvent```).  
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


Multiple clients
----------------

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
        'client' => [
            'default' => [],
            'asterisk2 => []
        ]
    ]
]
```

You can retrieve connections and clients:
- ```pami.connection.default```
- ```pami.client.default```
- ```pami.connection.asterisk2```
- ```pami.client.asterisk2```


### Multiple clients sharing the same connection

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


Getting the original PAMI client
--------------------------------

You can retrieve the original PAMI client in two ways:

From service locator:
```php
use PAMI\Client\Impl\ClientImpl;

/** @var ClientImpl $connection */
$connection = $serviceLocator->get('pami.connection.default');
```

From the ```PamiModule``` client:
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
