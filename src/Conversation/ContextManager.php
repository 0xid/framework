<?php

declare(strict_types=1);

namespace FondBot\Conversation;

use FondBot\Drivers\User;
use FondBot\Drivers\Driver;
use FondBot\Contracts\Cache;
use FondBot\Contracts\Container;

class ContextManager
{
    private $container;
    private $cache;

    public function __construct(Container $container, Cache $cache)
    {
        $this->container = $container;
        $this->cache = $cache;
    }

    /**
     * Resolve context instance.
     *
     * @param string $channel
     * @param Driver $driver
     *
     * @return Context
     */
    public function resolve(string $channel, Driver $driver): Context
    {
        $sender = $driver->getUser();
        $message = $driver->getMessage();
        $key = $this->key($channel, $sender);
        $value = $this->cache->get($key);

        $intent = $value['intent'] !== null ? $this->container->make($value['intent']) : null;
        $interaction = $value['interaction'] !== null ? $this->container->make($value['interaction']) : null;

        return new Context(
            $channel,
            $sender,
            $message,
            $intent,
            $interaction,
            $value['values'] ?? []
        );
    }

    /**
     * Save updated context.
     *
     * @param Context $context
     */
    public function save(Context $context): void
    {
        $key = $this->key($context->getChannel(), $context->getUser());

        $this->cache->store($key, $context->toArray());
    }

    /**
     * Clear context.
     *
     * @param Context $context
     */
    public function clear(Context $context): void
    {
        $key = $this->key($context->getChannel(), $context->getUser());

        $this->cache->forget($key);
    }

    /**
     * Get key of current context in storage (Cache, Memory, etc.).
     *
     * @param string $channel
     * @param User   $sender
     *
     * @return string
     */
    private function key(string $channel, User $sender): string
    {
        return 'context.'.$channel.'.'.$sender->getId();
    }
}
