<?php

namespace PamiModule\Listener;

use PAMI\Message\OutgoingMessage;
use PamiModule\Service\Client;
use Zend\Cache\Storage\StorageInterface;
use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;

/**
 * Class CacheListener.
 */
class CacheListener implements ListenerAggregateInterface
{
    use ListenerAggregateTrait;

    /**
     * Cache instance.
     *
     * @var StorageInterface
     */
    protected $cache;
    /**
     * Cacheable actions.
     *
     * @var array
     */
    protected $cacheableActions = [];

    /**
     * CacheListener constructor.
     *
     * @param StorageInterface $cache            Cache to use
     * @param array            $cacheableActions Actions to cache
     */
    public function __construct(StorageInterface $cache, $cacheableActions = [])
    {
        $this->setCache($cache);
        $this->setCacheableActions($cacheableActions);
    }

    /**
     * Return the cache storage.
     *
     * @return StorageInterface
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * Set the cache storage to use.
     *
     * @param StorageInterface $cache Cache storage to use
     *
     * @return $this
     */
    public function setCache(StorageInterface $cache)
    {
        $this->cache = $cache;

        return $this;
    }

    /**
     * Return the action classes to cache.
     *
     * @return array
     */
    public function getCacheableActions()
    {
        return $this->cacheableActions;
    }

    /**
     * Set the actions to cache.
     *
     * @param array $cacheableActions Actions to cache
     *
     * @return $this
     */
    public function setCacheableActions(array $cacheableActions)
    {
        $this->cacheableActions = array_map('strtolower', $cacheableActions);

        return $this;
    }

    /**
     * Attach one or more listeners.
     *
     * Implementors may add an optional $priority argument; the EventManager
     * implementation will pass this to the aggregate.
     *
     * @param EventManagerInterface $events   The event manager
     * @param int                   $priority
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach('sendAction.pre', [$this, 'onSendPre'], $priority);
        $this->listeners[] = $events->attach('sendAction.post', [$this, 'onSendPost'], $priority);
    }

    /**
     * Triggered on sendAction.pre.
     *
     * @param EventInterface $event Triggered event
     *
     * @return void|\PAMI\Message\Response\ResponseMessage
     *
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function onSendPre(EventInterface $event)
    {
        /* @var OutgoingMessage $action */
        $action = $event->getParam('action');
        /* @var Client $client */
        $client = $event->getTarget();

        if (!$this->isActionCacheable($action)) {
            return;
        }

        $cacheId = $this->generateCacheId($action, $client->getHost());

        if ($this->cache->hasItem($cacheId)) {
            // If cached item is an instance of \PAMI\Message\Response\ResponseMessage, the execution will be stopped
            return $this->cache->getItem($cacheId);
        }
    }

    /**
     * Triggered on sendAction.post.
     *
     * @param EventInterface $event Triggered event
     */
    public function onSendPost(EventInterface $event)
    {
        /* @var OutgoingMessage $action */
        $action = $event->getParam('action');
        /* @var Client $client */
        $client = $event->getTarget();

        if (!$this->isActionCacheable($action)) {
            return;
        }

        /* @var \PAMI\Message\Response\ResponseMessage $response */
        $response = $event->getParam('response');
        $cacheId = $this->generateCacheId($action, $client->getHost());

        if ($response->isSuccess()) {
            $this->cache->setItem($cacheId, $response);
        }
    }

    /**
     * Return true if we can cache the action.
     *
     * @param OutgoingMessage $action Requested action
     *
     * @return bool
     */
    protected function isActionCacheable(OutgoingMessage $action)
    {
        $actionName = $action->getKey('Action');

        return in_array(strtolower($actionName), $this->cacheableActions, true);
    }

    /**
     * Generate a cache ID based on action keys.
     *
     * @param OutgoingMessage $action The action
     * @param string          $prefix Cache ID prefix
     *
     * @return string
     */
    protected function generateCacheId(OutgoingMessage $action, $prefix = '')
    {
        $removeKeys = ['actionid'];
        $keys = $action->getKeys();
        $keys = array_diff_key($keys, array_flip($removeKeys));
        $variables = $action->getVariables();
        $variables = is_array($variables) ? $variables : [];
        ksort($keys);
        ksort($variables);

        return md5($prefix.json_encode(array_merge($keys, $variables)));
    }
}
