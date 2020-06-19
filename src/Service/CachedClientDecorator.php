<?php

declare(strict_types=1);

namespace PamiModule\Service;

use PAMI\Message\OutgoingMessage;
use PAMI\Message\Response\ResponseMessage;
use Psr\SimpleCache\CacheInterface;
use function array_diff_key;
use function array_flip;
use function array_key_exists;
use function array_merge;
use function is_array;
use function is_int;
use function is_string;
use function json_encode;
use function ksort;
use function strtolower;
use function unserialize;

final class CachedClientDecorator implements ClientInterface
{
    private ClientInterface $client;
    private CacheInterface $cache;
    /** @var array<string, int> */
    private array $cacheableActions;
    private int $defaultTtl;

    /**
     * @param ClientInterface $client
     * @param CacheInterface $cache
     * @param array<int|string, int|string> $cacheableActions
     * @param int $defaultTtl
     */
    public function __construct(
        ClientInterface $client,
        CacheInterface $cache,
        array $cacheableActions,
        int $defaultTtl = 60
    ) {
        $this->client = $client;
        $this->cache = $cache;
        $this->cacheableActions = $this->sanitizeCacheableActions($cacheableActions);
        $this->defaultTtl = $defaultTtl;
    }

    /**
     * @param array<string|int, string|int> $cacheableActions
     * @return array<string, int>
     */
    private function sanitizeCacheableActions(array $cacheableActions): array
    {
        $new = [];

        foreach ($cacheableActions as $name => $ttl) {
            if (is_string($ttl)) {
                $name = $ttl;
                $ttl = $this->defaultTtl;
            } elseif (! is_string($name) || ! is_int($ttl)) {
                throw new \InvalidArgumentException('Invalid cacheable action configuration');
            }

            $new[$name] = $ttl;
        }

        return $new;
    }

    public function connect(): void
    {
        $this->client->connect();
    }

    public function disconnect(): void
    {
        $this->client->disconnect();
    }

    public function process(): void
    {
        $this->client->process();
    }

    public function sendAction(OutgoingMessage $action): ResponseMessage
    {
        if (! $this->isActionCacheable($action)) {
            return $this->client->sendAction($action);
        }

        $cacheId = $this->generateCacheId($action);

        $response = $response = $this->cache->get($cacheId);

        if (null !== $response) {
            return unserialize($response);
        }

        $response = $this->client->sendAction($action);

        if ($response->isSuccess()) {
            $this->cache->set($cacheId, serialize($response));
        }

        return $response;
    }

    /**
     * Generate a cache ID based on action keys.
     *
     * @param OutgoingMessage $action The action
     * @param string          $prefix Cache ID prefix
     *
     * @return string
     */
    private function generateCacheId(OutgoingMessage $action, string $prefix = ''): string
    {
        $removeKeys = ['actionid'];
        $keys = $action->getKeys();
        $keys = array_diff_key($keys, array_flip($removeKeys));
        /** @var mixed $variables */
        $variables = $action->getVariables();
        $variables = is_array($variables) ? $variables : [];
        ksort($keys);
        ksort($variables);

        return sha1($prefix . json_encode(array_merge($keys, $variables), JSON_THROW_ON_ERROR));
    }

    private function isActionCacheable(OutgoingMessage $action): bool
    {
        $actionName = $action->getKey('Action');

        return array_key_exists(strtolower($actionName), $this->cacheableActions);
    }
}
