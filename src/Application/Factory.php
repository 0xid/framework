<?php

declare(strict_types=1);

namespace FondBot\Application;

use League\Container\Container;
use League\Container\ReflectionContainer;
use FondBot\Drivers\DriverServiceProvider;
use FondBot\Conversation\ContextServiceProvider;

class Factory
{
    public static function create(Container $container, string $routesPrefix = ''): Kernel
    {
        $container->delegate(new ReflectionContainer);

        // Load service providers
        $container->addServiceProvider(new LogServiceProvider);
        $container->addServiceProvider(new RouteServiceProvider($routesPrefix));
        $container->addServiceProvider(new DriverServiceProvider);
        $container->addServiceProvider(new ContextServiceProvider);

        // Boot kernel
        $kernel = new Kernel($container);

        $container->add(Kernel::class, $kernel);

        return $kernel;
    }
}
