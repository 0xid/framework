<?php

declare(strict_types=1);

namespace FondBot;

use FondBot\Drivers\Driver;
use FondBot\Channels\Channel;
use FondBot\Contracts\Container;
use FondBot\Drivers\DriverManager;

class BotFactory
{
    /**
     * Create bot instance.
     *
     * @param Container $container
     * @param Channel   $channel
     * @param array     $request
     * @param array     $headers
     *
     * @return Bot
     */
    public function create(Container $container, Channel $channel, array $request, array $headers)
    {
        /** @var Driver $driver */
        $driver = $container->make(DriverManager::class)->get($channel);

        $driver->fill($channel->getParameters(), $request, $headers);

        Bot::createInstance($container, $channel, $driver, $request, $headers);

        return Bot::getInstance();
    }
}
