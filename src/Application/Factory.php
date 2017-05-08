<?php

declare(strict_types=1);

namespace FondBot\Application;

use FondBot\Drivers\Driver;
use FondBot\Channels\Channel;
use FondBot\Drivers\DriverManager;

class Factory
{
    /**
     * Create kernel instance.
     *
     * @param Container $container
     * @param Channel   $channel
     * @param array     $request
     * @param array     $headers
     *
     * @return Kernel
     */
    public function create(Container $container, Channel $channel, array $request, array $headers): Kernel
    {
        /** @var Driver $driver */
        $driver = $container->get(DriverManager::class)->get($channel);

        $driver->fill($channel->getParameters(), $request, $headers);

        Kernel::createInstance($container, $channel, $driver);

        return Kernel::getInstance();
    }
}
