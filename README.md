PamiModule
==========

A Laminas/Mezzio module for [PAMI](https://github.com/marcelog/PAMI) library.

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

First, you should define connection and client options in your configuration. Client options are all optional.

```php
return [
    'pami' => [
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
            'default' => [],
        ],
    ],
];
```

Then you can register your service:

```php
use PamiModule\DIFactory\Service\ConnectionFactory;

return [
    'dependencies' => [
        'factories' => [
            'pami.connection' => new ConnectionFactory('default'),
        ],
    ],
];
```

PamiModule Client
-----------------

Set your client configuration:

```php
use PamiModule\DIFactory\Service\ConnectionFactory;
use PamiModule\DIFactory\Service\ClientFactory;
use Symfony\Component\EventDispatcher\EventDispatcher;

return [
    'pami' => [
        'connection' => [
            'connection1' => [
                // connection params
            ]
        ],
        'client' => [
            'client1' => [
                'connection' => 'pami.connection', // connection service name
                'event_dispatcher' => EventDispatcher::class, // your PSR EventDispatcher service name
            ],
        ],
    ],
    'dependencies' => [
        'factories' => [
            'pami.connection' => new ConnectionFactory('connection1'),
            'pami.client' => new ClientFactory('client1'),
        ],
    ],
];
```

Now you can get the client from your DI Container.

```php
use PamiModule\Service\ClientInterface;

// Getting the PamiModule client
/** @var ClientInterface $client */
$client = $container->get('pami.client');
```


### Methods

The original `Pami` client (the connection) is injected into the `PamiModule`, and the `PamiModule` actions 
delegates the original client.

*Mapped Actions:*

| PamiModule         | PAMI            |
|--------------------|-----------------|
| ```connect()```    | ```open()```    |
| ```disconnect()``` | ```close()```   |
| ```sendAction()``` | ```send()```    |
| ```process()```    | ```process()``` |


### Events

The `PamiModule` client requires an `EventDispatcher` instance and some events are dispatched:

- `PamiModule\Event\ConnectingEvent`
- `PamiModule\Event\ConnectedEvent`
- `PamiModule\Event\DisconnectingEvent`
- `PamiModule\Event\DisconnectedEvent`
- `PamiModule\Event\SendingActionEvent`
- `PamiModule\Event\ResponseReceivedEvent`
- `PamiModule\Event\PamiEvent`

PAMI events
-----------
 
All PAMI events are forwarded and dispatched as `PamiModule\Event\PamiEvent` event.

Example:
```php

use PamiModule\Service\ClientInterface;
use PamiModule\Event\PamiEvent;
use Psr\EventDispatcher\EventDispatcherInterface;

/* @var ClientInterface $client */
$client = $container->get('pami.client.default');
$eventDispatcher = $container->get(EventDispatcherInterface::class);

$listener = static function (PamiEvent $event) {
    $client = $event->getClient();
    $eventName = $event->getEventName();
    $pamiEvent = $event->getEvent();
};
```


### CachedClientDecorator

You can use the CachedClientDecorator to decorate the `PamiModule` client and cache some actions.
The constructor require a cache storage instance and the action names that listener can cache response. 

```php
use Psr\SimpleCache\CacheInterface;
use PamiModule\Service\ClientInterface;
use PamiModule\Service\CachedClientDecorator;

/** @var ClientInterface $client */
$client = $container->get('pami.client');
$cache = $container->get(CacheInterface::class);

$cachedClient = new CachedClientDecorator(
    $client,
    $cache,
    [
        'SIPPeers' => 60, // key is the action name, value is the cache TTL
        'ShowPeer', // use the default cache TTL for ShowPeer action 
    ],
    60 // default cache TTL
);

```
