<?php

declare(strict_types=1);

namespace FondBot\Conversation;

use FondBot\Drivers\Chat;
use FondBot\Drivers\User;
use FondBot\Drivers\Driver;
use FondBot\Channels\Channel;
use Psr\SimpleCache\CacheInterface;
use Psr\Container\ContainerInterface;

class SessionManager
{
    private $container;
    private $cache;

    public function __construct(ContainerInterface $container, CacheInterface $cache)
    {
        $this->container = $container;
        $this->cache = $cache;
    }

    /**
     * Load session.
     *
     * @param Channel $channel
     * @param Driver  $driver
     *
     * @return Session
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function load(Channel $channel, Driver $driver): Session
    {
        $chat = $driver->getChat();
        $user = $driver->getUser();
        $message = $driver->getMessage();
        $key = $this->key($channel, $chat, $user);
        $value = $this->cache->get($key);

        $intent = $value['intent'] !== null ? $this->container->get($value['intent']) : null;
        $interaction = $value['interaction'] !== null ? $this->container->get($value['interaction']) : null;

        return new Session($channel, $chat, $user, $message, $intent, $interaction);
    }

    /**
     * Save session.
     *
     * @param Session $session
     */
    public function save(Session $session): void
    {
        $key = $this->key($session->getChannel(), $session->getChat(), $session->getUser());

        $this->cache->set($key, $session->toArray());
    }

    /**
     * Close session.
     *
     * @param Session $session
     */
    public function close(Session $session): void
    {
        $key = $this->key($session->getChannel(), $session->getChat(), $session->getUser());

        $this->cache->delete($key);
    }

    /**
     * Get key of session.
     *
     * @param Channel $channel
     * @param Chat    $chat
     * @param User    $user
     *
     * @return string
     */
    private function key(Channel $channel, Chat $chat, User $user): string
    {
        return 'session.'.$channel->getName().'.'.$chat->getId().'.'.$user->getId();
    }
}
